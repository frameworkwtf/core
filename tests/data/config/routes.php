<?php

declare(strict_types=1);

return [
    'DummyController' => [
        'test_route' => [
            'pattern' => '/test/route',
            'rbac' => [
                'admin' => 'get',
                'user' => 'post',
            ],
        ],
    ],
];
