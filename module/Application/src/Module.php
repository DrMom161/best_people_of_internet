<?php

namespace Application;

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
}
