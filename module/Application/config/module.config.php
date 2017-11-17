<?php

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            'captcha' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/captcha[/:id]',
                    'defaults' => [
                        'controller' => Controller\CaptchaController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\CaptchaController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'htimg' => [
        'filters' => [
            'my_thumbnail' => [ // this is  filter service
                'type' => 'thumbnail', // this is a filter loader
                'options' => [  // filter loader passes these options to a Filter which manipulates the image
                    'width' => 50,
                    'height' => 50,
                    'format' => 'gif' // format is optional and defaults to the format of given image
                ]
            ]
        ]
    ]
];
