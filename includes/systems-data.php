<?php

declare(strict_types=1);

function systems_portfolio(): array
{
    return [
        'eduflow' => [
            'name' => 'EduFlow',
            'description' => 'Education Management Platform',
            'status' => 'Pilot',
            'path' => 'systems/eduflow.php',
        ],
        'clinicflow' => [
            'name' => 'ClinicFlow',
            'description' => 'Clinic Management System',
            'status' => 'In Development',
            'path' => 'systems/clinicflow.php',
        ],
        'peopleflow' => [
            'name' => 'PeopleFlow',
            'description' => 'HR Management System',
            'status' => 'Prototype',
            'path' => 'systems/peopleflow.php',
        ],
        'learnhub' => [
            'name' => 'LearnHub LMS',
            'description' => 'Learning Management Platform',
            'status' => 'In Development',
            'path' => 'systems/learnhub.php',
        ],
        'ai-assistant' => [
            'name' => 'Business AI Assistant',
            'description' => 'AI-enabled Business Support',
            'status' => 'Prototype',
            'path' => 'systems/ai-assistant.php',
        ],
        'insighthub' => [
            'name' => 'InsightHub',
            'description' => 'Performance and Reporting Dashboard',
            'status' => 'In Development',
            'path' => 'systems/insighthub.php',
        ],
    ];
}

function system_record(string $slug): ?array
{
    return systems_portfolio()[$slug] ?? null;
}
