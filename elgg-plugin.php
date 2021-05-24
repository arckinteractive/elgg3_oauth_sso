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
    ]
];