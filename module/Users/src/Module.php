<?php

namespace Users;

use Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
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
                Model\UserTable::class => function ($container) {
                    $tableGateway = $container->get('UserTableGateway');

                    return new Model\UserTable($tableGateway);
                },
                'UserTableGateway' => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\User());

                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                'AuthService' => function ($container) {
                    $adapter = $container->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter = new CallbackCheckAdapter($adapter, 'users', 'login', 'password',
                        function ($dbCredential, $requestCredential) {
                            return (new Bcrypt())->verify($requestCredential, $dbCredential);
                        });

                    $auth = new AuthenticationService();
                    $auth->setAdapter($dbAuthAdapter);

                    return $auth;
                }
            ],
        ];
    }

    /**
     * Получение конфига контроллеров содуля
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\IndexController::class => function ($container) {
                    return new Controller\IndexController(
                        $container->get(Model\UserTable::class)
                    );
                },
            ],
        ];
    }
}
