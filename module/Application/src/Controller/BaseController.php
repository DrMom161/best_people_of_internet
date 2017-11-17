<?php

namespace Application\Controller;

use Users\Model\User;
use Users\Model\UserTable;
use Zend\Authentication\AuthenticationService;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BaseController extends AbstractActionController
{
    /**
     * @var Adapter
     */
    private $dbAdapter;
    /**
     * @var User
     */
    private $currentUser;
    /**
     * @var AuthenticationService
     */
    protected $authservice;
    /**
     * @var object - зависит от контроллера
     */
    protected $table;
    /**
     * @var UserTable
     */
    protected $userTable;

    /**
     * Геттер для сервиса авторизации
     * @return AuthenticationService
     */
    protected function getAuthService()
    {
        if (!$this->authservice) {
            $this->authservice = $this->getPluginManager()->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }

    /**
     * Геттер для таблицы пользователей
     * @return UserTable
     */
    protected function getUserTable()
    {
        if (!$this->userTable) {
            $this->userTable = $this->getPluginManager()->getServiceLocator()->get('Users\Model\UserTable');
        }

        return $this->userTable;
    }

    /**
     * Проверка авторизованности
     * @return bool
     */
    protected function isAuth()
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * Геттер адаптера базы данных
     * @return Adapter
     */
    protected function getDbAdapter()
    {
        if (!$this->dbAdapter) {
            $this->dbAdapter = $this->getPluginManager()->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        }

        return $this->dbAdapter;
    }

    /**
     * Геттер авторизованного пользователя
     * @return User
     */
    public function getCurrentUser()
    {
        if (!$this->currentUser && $this->getAuthService()->hasIdentity()) {
            $this->currentUser = $this->getUserTable()->getByLogin($this->getAuthService()->getIdentity());
        }

        return $this->currentUser;
    }

    /**
     * Обертка рендера шаблонов - добавляет поля, общие для всех контроллеров
     * @param array $data
     * @param bool $withoutLayout
     * @return ViewModel
     */
    protected function renderView($data, $withoutLayout = false)
    {
        $this->layout()->setVariable('currentUser', $this->getCurrentUser());
        $this->layout()->setVariable('action', $this->params()->fromRoute('action'));
        $this->layout()->setVariable('showLinkToHome', $this->request->getUri()->getPath() != '/');
        $data['currentUser'] = $this->getCurrentUser();
        $result = new ViewModel();
        $result->setTerminal($withoutLayout);
        $result->setVariables($data);

        return $result;
    }
}
