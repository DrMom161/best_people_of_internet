<?php

namespace Ratings;

use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'ratings' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/ratings[/:action]',
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
