<?php

declare(strict_types=1);

function founder_profile(): array
{
    return [
        'name' => 'Mthokozisi Hlomela Mnyobe',
        'role' => 'Founder and Managing Director',
        'credentials' => ['IT Graduate', 'CompTIA Network+ certified'],
        'image' => 'images/director-mthokozisi.png',
        'image_width' => 1122,
        'image_height' => 1402,
    ];
}

function collaborating_organisations(): array
{
    return [
        ['monogram' => 'MH', 'name' => 'MH Websites (Pty) Ltd'],
        ['monogram' => 'BB', 'name' => 'Basic Blue Trading 773 CC'],
        ['monogram' => 'HG', 'name' => 'Halisi Group (Pty) Ltd'],
    ];
}

function company_contact(): array
{
    return [
        'phone_display' => '067 202 1923',
        'phone_uri' => '+27672021923',
        'email' => 'mhweb36@gmail.com',
        'location' => 'East London, Eastern Cape',
        'hours' => 'Monday–Friday, 08:00–17:00',
    ];
}

function company_legal_information(): array
{
    return [
        'legal_name' => 'MH WEBSITES (Pty) Ltd',
        'registration_number' => '2024 / 407724 / 07',
        'established' => '2024',
        'phone_display' => '067 202 1923',
        'phone_uri' => '+27672021923',
        'email' => 'mhweb36@gmail.com',
        'address_lines' => [
            '4 Lancaster Palace',
            'Vincent',
            'East London',
            'Eastern Cape',
            'South Africa',
        ],
        'service_area' => 'South Africa nationwide',
    ];
}
