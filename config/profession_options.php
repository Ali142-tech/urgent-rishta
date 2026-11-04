<?php

/*
 * Professions the client asked for, in the client's order. They are shown first in the member profession dropdown (signup form and
 * "Complete your profile" form); the professions that were already offered follow, without repeating a name — see
 * App\Services\ProfessionOptions. The profession is saved as text, so an older / typed one keeps working: it is shown as
 * "Other (Please Specify)" with the member's own words.
 */
return [
    'other' => 'Other (Please Specify)',

    'client' => [
        'Doctor / Healthcare Professional',
        'Engineer',
        'IT / Software Professional',
        'Business Owner / Entrepreneur',
        'Government Officer / Civil Servant',
        'Banker / Finance Professional',
        'Chartered Accountant / Accountant',
        'Lawyer / Legal Professional',
        'Teacher / Professor',
        'Armed Forces / Police',
        'Pilot / Aviation Professional',
        'Real Estate / Property',
        'Sales / Marketing Professional',
        'Manager / Corporate Executive',
        'Architect / Designer',
        'Freelancer / Self-Employed',
        'Skilled Worker / Technician',
        'Agriculture / Landlord',
        'Religious Scholar',
        'Student',
        'Homemaker',
        'Retired',
        'Unemployed',
    ],
];
