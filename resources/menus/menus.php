<?php

define("ADMIN_MENU", [
    'Server' => [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />'
        ],
        'link' => '/adminx/server',
    ],
    'Users' => [
        'link' => '/adminx/users',
    ],
    'Organizations' => [
        'link' => '/adminx/organizations',
    ],
    'Memberships' => [
        'link' => '/adminx/memberships',
    ],
    'Firewall' => [
        'link' => '/adminx/firewall'
    ],
    'Action Log' => [
        'link' => '/adminx/action-log'
    ],
    'System Log' => [
        'link' => '/adminx/system-log'
    ],
    'API Keys' => [
        'link' => '/adminx/api-keys'
    ],
    'IP Reputation' => [
        'link' => '/adminx/ip-reputation'
    ]
]);

/* Menu Settings */

define("MAIN_MENU", [
    'Docs' => [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'
        ],
        'link' => '/docs',
    ],
    '404' => [
        'link' => '/404'
    ]
]);

/* Username drop down menu */

define("USERNAME_DROPDOWN_MENU", [
    'User Settings' => [
        'path' => '/user-settings',
        'admin' => false
    ],
    'Admin' => [
        'path' => '/adminx',
        'admin' => true,
    ],
    'Logout' => [
        'path' => '/logout',
        'admin' => false,
    ]
]);
