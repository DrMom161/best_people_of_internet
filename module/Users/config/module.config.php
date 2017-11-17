<?php

namespace Users;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'users' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/users[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'users' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
];
