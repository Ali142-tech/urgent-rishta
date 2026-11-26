<?php

/**
 * One place for the facts that appear on many public pages (contact details, consultation fee and slots, response time, headline
 * figures). Change them here (or in .env) and every page follows — they used to be typed separately on each page and drifted apart.
 */
return [
    'contact' => [
        'phone'          => env('SITE_PHONE', '+92 304 0227000'),
        'phone_e164'     => env('SITE_PHONE_E164', '+923040227000'),     // tel: links
        'whatsapp'       => env('SITE_WHATSAPP', '923040227000'),         // wa.me/<digits>
        'email'          => env('SITE_EMAIL', 'urgentrishta.co@gmail.com'),
        'hours'          => 'Monday – Saturday, 10:00 AM – 7:00 PM (PKT)',
        // ONE response-time promise, used on every page
        'response_time'  => env('SITE_RESPONSE_TIME', 'within 24 hours'),
    ],

    'consultation' => [
        'fee'       => env('SITE_CONSULTATION_FEE', '£10'),
        'timezone'  => 'PKT',
        // Slots offered on the booking forms (Pakistan time). They match the evening hours the team is actually available.
        'slots'     => ['5:00 PM – 6:00 PM PKT', '6:00 PM – 7:00 PM PKT', '7:00 PM – 8:00 PM PKT', '8:00 PM – 9:00 PM PKT'],
        'payment_note' => 'Submit your request and our team will contact you to confirm the appointment. Payment details are shared only after your slot is confirmed.',
    ],

    // Headline figures shown on the homepage. Only change after the number has been confirmed.
    'stats' => [
        'successful_matches' => (int) env('SITE_SUCCESSFUL_MATCHES', 5000),
        'years'              => (int) env('SITE_YEARS', 16),
    ],
];
