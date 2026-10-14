<?php

namespace App\Services;

use App\Appointment;
use App\Mail\AppointmentInvite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Emails a standard iCalendar (.ics) invitation for an admin-scheduled
 * consultation to the client and the scheduling admin. No Google account
 * or API needed: Gmail/Outlook/Apple Mail recognise the invite and offer
 * Yes/No/Add-to-calendar. The same UID is reused with a rising SEQUENCE so
 * a reschedule UPDATES the existing event, and a cancel sends METHOD:CANCEL.
 */
class AppointmentInviteService
{
    /** Send (or re-send, after a reschedule) the invitation. Returns recipients emailed. */
    public function sendInvite(Appointment $a, ?string $adminEmail): array
    {
        return $this->dispatch($a, 'REQUEST', $adminEmail);
    }

    /** Send a cancellation — only if an invite was actually sent before. */
    public function sendCancel(Appointment $a, ?string $adminEmail): array
    {
        if (!$a->calendar_invite_sent_at) {
            return [];
        }
        $sent = $this->dispatch($a, 'CANCEL', $adminEmail);
        $a->calendar_invite_sent_at = null;
        $a->save();
        return $sent;
    }

    private function dispatch(Appointment $a, string $method, ?string $adminEmail): array
    {
        $recipients = array_values(array_unique(array_filter([
            strtolower(trim((string) $a->contact_email)),
            strtolower(trim((string) $adminEmail)),
        ], fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))));

        if (!$recipients) {
            return [];
        }

        $ics = $this->ics($a, $method, $recipients);
        $sent = [];
        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new AppointmentInvite($a, $ics, $method));
                $sent[] = $email;
            } catch (\Throwable $e) {
                Log::error("Appointment #{$a->id} calendar invite to {$email} failed: " . $e->getMessage());
            }
        }

        if ($method === 'REQUEST' && $sent) {
            $a->calendar_invite_sent_at = now();
            $a->save();
        }
        return $sent;
    }

    /**
     * Turn the appointment's free-text time into [start, end] Carbon times.
     * Handles "4:00 PM", "14:00", and slot labels such as
     * "Afternoon (12pm - 4pm)" (uses the window itself). Falls back to
     * 10:00 for a label with no parseable time.
     */
    public function window(Appointment $a): array
    {
        $tz = config('services.appointment_calendar.timezone');
        $date = $a->appointment_date->format('Y-m-d');
        $raw = trim((string) $a->appointment_time);
        $minutes = (int) config('services.appointment_calendar.duration_minutes', 30);

        preg_match_all('/(\d{1,2})(?::(\d{2}))?\s*(am|pm)?/i', $raw, $m, PREG_SET_ORDER);
        $times = [];
        foreach ($m as $hit) {
            $h = (int) $hit[1];
            $hasMinutes = isset($hit[2]) && $hit[2] !== '';
            $min = $hasMinutes ? (int) $hit[2] : 0;
            $mer = strtolower($hit[3] ?? '');
            if ($mer === '' && !$hasMinutes) continue;          // bare number, ignore
            if ($mer === 'pm' && $h < 12) $h += 12;
            if ($mer === 'am' && $h === 12) $h = 0;
            if ($h > 23 || $min > 59) continue;
            $times[] = [$h, $min];
        }

        if (count($times) >= 2) {
            $start = Carbon::parse($date, $tz)->setTime($times[0][0], $times[0][1]);
            $end = Carbon::parse($date, $tz)->setTime($times[1][0], $times[1][1]);
            if ($end->lte($start)) $end = $start->copy()->addMinutes($minutes);
        } elseif (count($times) === 1) {
            $start = Carbon::parse($date, $tz)->setTime($times[0][0], $times[0][1]);
            $end = $start->copy()->addMinutes($minutes);
        } else {
            $start = Carbon::parse($date, $tz)->setTime(10, 0);
            $end = $start->copy()->addMinutes($minutes);
        }
        return [$start, $end];
    }
    /** RFC 5545 text. */
    public function ics(Appointment $a, string $method, array $attendees): string
    {
        [$start, $end] = $this->window($a);
        $utc = fn ($c) => $c->copy()->utc()->format('Ymd\THis\Z');

        $name = $a->contact_name ?: 'Client';
        $organizer = config('mail.from.address');
        $organizerName = config('mail.from.name') ?: 'Urgent Rishta';

        $desc = 'Urgent Rishta consultation with ' . $name . '.'
            . ($a->subject ? '\n' . 'Reason: ' . $a->subject : '')
            . ($a->contact_phone ? '\n' . 'Phone: ' . $a->contact_phone : '')
            . ($a->appointment_time ? '\n' . 'Requested slot: ' . $a->appointment_time : '');

        $lines = [
            'BEGIN:VCALENDAR',
            'PRODID:-//Urgent Rishta//Consultations//EN',
            'VERSION:2.0',
            'CALSCALE:GREGORIAN',
            'METHOD:' . $method,
            'BEGIN:VEVENT',
            'UID:appointment-' . $a->id . '@urgentrishta.com',
            'DTSTAMP:' . $utc(now()),
            'SEQUENCE:' . max(0, $a->updated_at ? $a->updated_at->timestamp - 1700000000 : 0),
            'DTSTART:' . $utc($start),
            'DTEND:' . $utc($end),
            'SUMMARY:' . $this->esc('Consultation – ' . $name),
            'DESCRIPTION:' . $this->escKeepNewlines($desc),
            'ORGANIZER;CN=' . $this->param($organizerName) . ':mailto:' . $organizer,
        ];
        foreach ($attendees as $email) {
            $lines[] = 'ATTENDEE;CN=' . $this->param($email === strtolower((string) $a->contact_email) ? $name : $email)
                . ';ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE:mailto:' . $email;
        }
        $lines[] = 'STATUS:' . ($method === 'CANCEL' ? 'CANCELLED' : 'CONFIRMED');
        $lines[] = 'TRANSP:OPAQUE';
        if ($method !== 'CANCEL') {
            $lines[] = 'BEGIN:VALARM';
            $lines[] = 'TRIGGER:-PT30M';
            $lines[] = 'ACTION:DISPLAY';
            $lines[] = 'DESCRIPTION:Consultation reminder';
            $lines[] = 'END:VALARM';
        }
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", array_map([$this, 'fold'], $lines)) . "\r\n";
    }

    private function esc(string $s): string
    {
        return str_replace(["\\", ";", ",", "\r\n", "\n"], ["\\\\", "\\;", "\\,", "\\n", "\\n"], $s);
    }

    /** Escape everything except the literal \n sequences we inserted ourselves. */
    private function escKeepNewlines(string $s): string
    {
        return implode('\n', array_map([$this, 'esc'], explode('\n', $s)));
    }

    private function param(string $s): string
    {
        return '"' . str_replace('"', "'", $s) . '"';
    }

    /** Fold lines longer than 75 octets (RFC 5545 §3.1). */
    private function fold(string $line): string
    {
        if (strlen($line) <= 75) return $line;
        $out = '';
        while (strlen($line) > 75) {
            $out .= mb_strcut($line, 0, 75, 'UTF-8') . "\r\n ";
            $line = mb_strcut($line, 75, null, 'UTF-8');
            if ($line === '') break;
        }
        return $out . $line;
    }
}
