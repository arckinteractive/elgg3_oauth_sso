<?php

return [
    'bootstrap' => '\\Arck\\Oauth\\Bootstrap',
    'entities' => [
		[
			'type' => 'object',
			'subtype' => 'oauth_access_token',
			'class' => '\\Arck\\Oauth\\AccessToken',
			'searchable' => false,
		],
        [
            'type' => 'object',
            'subtype' => 'oauth_authorization_code',
            'class' => '\\Arck\\Oauth\\AuthCode',
            'searchable' => false,
        ],
        [
            'type' => 'object',
            'subtype' => 'oauth_client',
            'class' => '\\Arck\\Oauth\\Client',
            'searchable' => false,
        ],
        [
            'type' => 'object',
            'subtype' => 'oauth_refresh_token',
            'class' => '\\Arck\\Oauth\\RefreshToken',
            'searchable' => false,
        ]
    ],
    'actions' => [
        'oauth/client/edit' => [
            'access' => 'admin',
            'filename' => __DIR__ . '/actions/oauth/client/edit.php',
        ],
        'oauth/applications/delete' => [
            'access' => 'admin',
            'filename' => __DIR__ . '/actions/oauth/client/delete.php',
        ],
        'oauth/login' => [
            'access' => 'public',
            'filename' => __DIR__ . '/actions/oauth/login.php',
        ]
    ]
];