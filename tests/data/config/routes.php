<?php

declare(strict_types=1);

return [
    '/' => [
        'test_route' => [
            'pattern' => '',
            'rbac' => [
                'admin' => 'get',
                'user' => 'post',
            ],
        ],
    ],
];
