<?php

return [
    // 1. Top Header Links (Visible to everyone)
    'main' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Browse', 'route' => 'browse'],
        ['label' => 'Workflows', 'route' => 'workflows'], // Added
        ['label' => 'FAQ', 'route' => 'faq'],
        ['label' => 'About', 'route' => 'about'],
    ],

    // 2. Sidebar Main Links (For Creators)
    'creator' => [
        [
            'label' => 'Dashboard',
            'route' => 'creator.dashboard',
            'icon'  => 'layout-dashboard',
        ],
        [
            'label' => 'My Services',
            'route' => 'creator.services.index',
            'icon'  => 'briefcase',
        ],
        [
            'label' => 'Open Offers',
            'route' => 'creator.openoffers.index',
            'icon'  => 'megaphone',
        ],
        [
            'label' => 'Workflows',
            'route' => 'creator.workflows.index',
            'icon'  => 'git-merge',
        ],
    ],

    // 3. Profile Popover Links (Bottom Left)
    'creator_profile' => [
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon'  => 'user',
        ],
        [
            'label' => 'Verifications',
            'route' => 'verification.status', // Defined in your web.php
            'icon'  => 'shield-check',
        ],
    ],

     'creator_space' => [
        [
            'label' => 'Creator Space',
            'route' => 'creator.dashboard',
            'icon'  => 'layout-dashboard',
        ],
    ],
];