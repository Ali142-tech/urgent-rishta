<?php

namespace App\Mail;

use App\Appointment;
use App\Services\AppointmentInviteService;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;

/**
 * Calendar invitation (or cancellation) email carrying an .ics part with
 * the right METHOD so Gmail/Outlook/Apple show Yes/No/Add-to-calendar.
 * Sent immediately (not queued) so the admin sees failures right away.
 */
class AppointmentInvite extends Mailable
{
    use SerializesModels;

    public function __construct(public Appointment $appointment, public string $ics, public string $method = 'REQUEST')
    {
    }

    public function build()
    {
        [$start, $end] = app(AppointmentInviteService::class)->window($this->appointment);
        $cancel = $this->method === 'CANCEL';
        $ics = $this->ics;
        $method = $this->method;

        return $this->subject(($cancel ? 'Cancelled: ' : 'Invitation: ') . 'Urgent Rishta consultation – ' . $start->format('D, d M Y H:i'))
            ->view('mail.appointment-invite')
            ->with(['start' => $start, 'end' => $end, 'cancel' => $cancel, 'appointment' => $this->appointment])
            ->withSymfonyMessage(function ($message) use ($ics, $method) {
                // Symfony builds Content-Type lazily, so add the iTIP "method"
                // parameter (what makes mail apps treat it as an invitation
                // rather than a plain file) when the headers are prepared.
                $part = new class($ics, 'invite.ics', 'text/calendar', $method) extends DataPart {
                    public function __construct(string $body, string $name, string $type, private string $itipMethod)
                    {
                        parent::__construct($body, $name, $type);
                    }

                    public function getPreparedHeaders(): \Symfony\Component\Mime\Header\Headers
                    {
                        $headers = parent::getPreparedHeaders();
                        $ct = $headers->get('Content-Type');
                        $ct->setParameter('method', $this->itipMethod);
                        $ct->setParameter('charset', 'UTF-8');
                        return $headers;
                    }
                };
                $message->addPart($part);
            });
    }
}
