<?php

return [
    'bootstrap' => \Arck\Oauth\Bootstrap::class,
    'entities' => [
		[
			'type' => 'object',
			'subtype' => \Arck\Oauth\AccessToken::SUBTYPE,
			'class' => \Arck\Oauth\AccessToken::class,
			'searchable' => false,
		],
        [
            'type' => 'object',
            'subtype' => \Arck\Oauth\AuthCode::SUBTYPE,
            'class' => \Arck\Oauth\AuthCode::class,
            'searchable' => false,
        ],
        [
            'type' => 'object',
            'subtype' => \Arck\Oauth\Client::SUBTYPE,
            'class' => \Arck\Oauth\Client::class,
            'searchable' => false,
        ],
        [
            'type' => 'object',
            'subtype' => \Arck\Oauth\RefreshToken::SUBTYPE,
            'class' => \Arck\Oauth\RefreshToken::class,
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