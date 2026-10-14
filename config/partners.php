<?php

/**
 * Partner websites shown on the Partners page (/partners).
 *
 * This is also the server-side allowlist for /go/{key}: a visitor can only ever be sent to a URL listed here (the key in the
 * address is looked up, never used as a destination). Each click is recorded in `partner_clicks` (see
 * App\Http\Controllers\PartnerRedirectController) and the visitor is redirected with the referral tags below added.
 * The tags only identify UrgentRishta as the source of the visit — they do not track purchases or commission.
 *
 * Page content per partner (all optional except name/url): category, tagline, description, logo (a file under public/),
 * benefit (the "Exclusive Benefit for UR Members" line — leave it out when the partner has not agreed one), theme (card colour).
 */
return [
    'referral' => [
        'source' => 'urgentrishta',
        'medium' => 'partner',
    ],

    'links' => [
        'mishis' => [
            'name' => "Mishi's Collection",
            'url' => 'https://mishiscollection.com/',
            'category' => 'Fashion & Clothing',
            'tagline' => 'Elegant Styles for Every Occasion',
            'description' => 'Trendy and modest clothing for every occasion. Discover elegant and modern styles.',
            'logo' => 'images/partners/mishis-collection.webp',
            'benefit' => 'Special Discounts',
            'theme' => 'rose',
        ],
        'lumina' => [
            'name' => 'Lumina Clinic',
            'url' => 'https://www.instagram.com/lumina_clinic.pk',
            'category' => 'Health & Beauty',
            'tagline' => 'Skin & Laser Clinic',
            'description' => 'A skin and laser clinic offering professional skincare and laser treatments.',
            'logo' => 'images/partners/lumina-clinic.jpg',
            'benefit' => null,
            'theme' => 'gold',
        ],
        'smartroute' => [
            'name' => 'SmartRoute Systems',
            'url' => 'https://www.smartroutesystems.co.uk/',
            'category' => 'Technology & Services',
            'tagline' => 'Technology for a Smarter Tomorrow',
            'description' => 'Innovative technology and IT solutions for businesses in the UK and beyond.',
            'logo' => null,
            'benefit' => 'Special Offers',
            'theme' => 'blue',
        ],
    ],
];
