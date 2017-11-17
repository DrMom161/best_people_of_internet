<?php

namespace Ratings;

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
     * Получение конфига сервисов модуля
     * @return array
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\RatingTable::class => function ($container) {
                    $tableGateway = $container->get('RatingTableGateway');

                    return new Model\RatingTable($tableGateway);
                },
                'RatingTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Rating());

                    return new TableGateway('ratings', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    /**
     * Получение конфига контроллеров модуля
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(Model\RatingTable::class)
                    );
                },
            ],
        ];
    }
}
