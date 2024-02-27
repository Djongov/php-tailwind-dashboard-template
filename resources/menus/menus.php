<?php

define("ADMIN_MENU", [
    'Admin Home' => [
        'link' => '/adminx',
    ],
    'Server' => [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />'
        ],
        'link' => '/adminx/server',
    ],
    'System Logs' => [
        'link' => '/adminx/system-logs',
    ],
    'Users' => [
        'link' => '/adminx/users',
    ],
    'Cache' => [
        'link' => '/adminx/cache',
    ],
    'CSP' => [
        'link' => [
            'CSP Reports' => [
                'sub_link' => '/adminx/csp-reports'
            ],
            'CSP Approved Domains' => [
                'sub_link' => '/adminx/csp-approved-domains'
            ]
        ]
    ],
    'Firewall' => [
        'link' => '/adminx/firewall',
    ],
    'Queries' => [
        'link' => '/adminx/queries',
    ],
    'Mailer' => (SENDGRID) ? [
        'icon' => [
            'type' => 'svg',
            'src' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />'
        ],
        'link' => '/adminx/mailer',
    ] : null,
    'Tools' => [
        'link' => [
            'Base64' => [
                'sub_link' => '/adminx/base64'
            ]
        ]
    ],
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
    'Charts' => [
        'link' => '/charts',
    ],
    'Forms' => [
        'link' => '/forms',
    ],
    'DataGrid' => [
        'link' => '/datagrid',
    ],
    '404' => [
        'link' => '/blablabla'
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
