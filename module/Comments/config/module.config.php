<?php

namespace Comments;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'comments' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/comments[/:action]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],

    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ]
    ],
];
