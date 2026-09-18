<?php

declare(strict_types=1);

function service_categories(): array
{
    return [
        'custom-software' => [
            'number' => '01',
            'title' => 'Custom Software Development',
            'summary' => 'Purpose-built systems, portals and workflow tools shaped around the way an organisation operates.',
            'helps' => 'Businesses and organisations moving beyond spreadsheets, paper records or disconnected tools.',
            'outcomes' => [
                'Structured internal workflows',
                'Role-appropriate digital workspaces',
                'Reliable capture and record management',
                'A modular foundation that can evolve',
            ],
        ],
        'web-development' => [
            'number' => '02',
            'title' => 'Website & E-commerce Development',
            'summary' => 'Professional websites, online catalogues and commerce experiences that make an organisation clear and accessible online.',
            'helps' => 'Growing businesses that need a credible digital presence or a better way to present services and products.',
            'outcomes' => [
                'Responsive company websites',
                'Clear information architecture',
                'Accessible conversion journeys',
                'Hosting and maintenance planning',
            ],
        ],
        'learning-platforms' => [
            'number' => '03',
            'title' => 'Learning Platforms & Moodle',
            'summary' => 'Structured learning environments with considered branding, course organisation and ongoing platform support.',
            'helps' => 'Schools, colleges, training providers and organisations delivering structured learning online.',
            'outcomes' => [
                'Moodle installation and configuration',
                'Course and dashboard structures',
                'Branded learning experiences',
                'LMS guidance and support',
            ],
        ],
        'data-automation' => [
            'number' => '04',
            'title' => 'Dashboards, Data & Automation',
            'summary' => 'Focused tools that reduce repetitive work and turn operational information into useful reporting views.',
            'helps' => 'Teams that need clearer capture, reporting, monitoring or workflow automation.',
            'outcomes' => [
                'KPI and management dashboards',
                'Data-capture workflows',
                'Reports and export tools',
                'Practical automation opportunities',
            ],
        ],
        'ict-support' => [
            'number' => '05',
            'title' => 'ICT Support & Systems Integration',
            'summary' => 'Practical technical support, configuration and integration that helps digital tools work together reliably.',
            'helps' => 'Small teams and organisations needing responsive assistance without unnecessary complexity.',
            'outcomes' => [
                'Technical troubleshooting',
                'Software and device configuration',
                'System integration planning',
                'Ongoing technical assistance',
            ],
        ],
    ];
}

function service_category(string $slug): ?array
{
    return service_categories()[$slug] ?? null;
}
