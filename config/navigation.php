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
            'icon' => 'layout-dashboard',
        ],
        [
            'label' => 'My Services',
            'route' => 'creator.services.index',
            'icon' => 'briefcase',
        ],
        [
            'label' => 'Orders',
            'route' => 'orders.index',
            'icon' => 'shopping-cart',
        ],
        // [
        //     'label' => 'Work',
        //     'route' => 'creator.work-dashboard',
        //     'icon'  => 'tasks',
        // ],
        [
            'label' => 'Open Offers',
            'route' => 'creator.openoffers.index',
            'icon' => 'megaphone',
        ],
        [
            'label' => 'Workflows',
            'route' => 'creator.workflows.index',
            'icon' => 'git-merge',
        ],
    ],

    // 3. Profile Popover Links (Bottom Left)
    'creator_profile' => [
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon' => 'user',
        ],
        [
            'label' => 'Verifications',
            'route' => 'verification.status', // Defined in your web.php
            'icon' => 'shield-check',
        ],
    ],

    'creator_space' => [
        [
            'label' => 'Creator Space',
            'route' => 'creator.dashboard',
            'icon' => 'layout-dashboard',
        ],
    ],

    // 4. Admin Sidebar Links
    'admin' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'layout-dashboard',
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users.index',
            'icon' => 'users',
        ],
        [
            'label' => 'Services',
            'route' => 'admin.listings.index',
            'icon' => 'briefcase',
        ],
        [
            'label' => 'Open Offers',
            'route' => 'admin.openoffers.index',
            'icon' => 'megaphone',
        ],
        [
            'label' => 'Orders',
            'route' => 'admin.orders.index',
            'icon' => 'shopping-cart',
        ],
        [
            'label' => 'Payments',
            'route' => 'admin.payments.index',
            'icon' => 'credit-card',
        ],
        [
            'label' => 'Refunds',
            'route' => 'admin.refunds.index',
            'icon' => 'reply-all',
        ],
        [
            'label' => 'Categories',
            'route' => 'admin.categories.index',
            'icon' => 'layers',
        ],
        [
            'label' => 'Flags',
            'route' => 'admin.flags.index',
            'icon' => 'flag',
        ],
        [
            'label' => 'Verifications',
            'route' => 'admin.verifications.index',
            'icon' => 'shield-check',
        ],
        [
            'label' => 'Activity Logs',
            'route' => 'admin.activity-logs.index',
            'icon' => 'activity',
        ],
        [
            'label' => 'Settings',
            'route' => 'admin.settings.index',
            'icon' => 'settings',
        ],
    ],

    // 5. Admin Profile Popover Links
    'admin_profile' => [
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon' => 'user',
        ],
    ],
];
