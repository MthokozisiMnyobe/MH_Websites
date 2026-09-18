<?php

declare(strict_types=1);

function founder_profile(): array
{
    return [
        'name' => 'Mthokozisi Hlomela Mnyobe',
        'role' => 'Founder and Managing Director',
        'credentials' => ['IT Graduate', 'CompTIA Network+ certified'],
        'image' => 'images/director-mthokozisi.webp',
        'image_width' => 1280,
        'image_height' => 1280,
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
        'phone_display' => '+27 69 841 8354',
        'phone_uri' => '+27698418354',
        'email' => 'mhweb36@gmail.com',
        'location' => 'East London, Eastern Cape',
        'hours' => 'Monday–Friday, 08:00–17:00',
    ];
}
