<?php

namespace Comments;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    const VERSION = '3.0.3-dev';

    /**
     * Получение конфига модуля
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Получение конфига сервисов для модуля
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\CommentTable::class => function ($container) {
                    $tableGateway = $container->get('CommentTableGateway');
                    return new Model\CommentTable($tableGateway);
                },
                'CommentTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Comment());
                    return new TableGateway('comments', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    /**
     * Получение конфига контроллеров для модуля
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(Model\CommentTable::class)
                    );
                },
            ],
        ];
    }
}
