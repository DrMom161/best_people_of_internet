<?php

namespace Users\Controller;

use Application\Controller\BaseController;
use Comments\Form\AddForm;
use Users\Filters\EditFilter;
use Users\Filters\LoginFilter;
use Users\Form\EditForm;
use Users\Form\LoginForm;
use Users\Form\RegisterForm;
use Users\Model\User;
use Zend\Session\Container;
use Zend\Validator\Db\NoRecordExists;

class IndexController extends BaseController
{
    /**
     * Получение списка пользователей с рейтингом от текущего пользователя
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        //для упрощения работы с токенами отдаю готовый html на ajax-запрос
        return $this->renderView([
            'users' => $this->getUserTable()->getAllWithCurrentUserRatings($this->getCurrentUser()->id),
            'ratingForm' => new \Ratings\Form\AddForm()
        ],
            true
        );
    }

    /**
     * Авторизация
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function loginAction()
    {
        if ($this->isAuth()) {
            return $this->redirect()->toUrl('/');
        }

        $form = new LoginForm();
        $form->setInputFilter(new LoginFilter());
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();
                $this->getAuthService()
                    ->getAdapter()
                    ->setIdentity($data['login'])
                    ->setCredential($data['password']);
                $result = $this->getAuthService()->authenticate();

                if ($result->isValid()) {
                    $session = new Container('User');
                    //сохраняем на сутки 24*60*60
                    $session->setExpirationSeconds(86400);
                    $session->offsetSet('login', $data['login']);

                    $this->redirect()->toUrl('/');
                } else {
                    $form->get('login')->setMessages([
                        NoRecordExists::ERROR_NO_RECORD_FOUND => 'Неверный логин/пароль.'
                    ]);
                }
            }
        }

        return $this->renderView(['form' => $form]);
    }

    /**
     * Регистрация
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function registerAction()
    {
        if ($this->isAuth()) {
            return $this->redirect()->toUrl('/');
        }

        $form = new RegisterForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = new User();
            $dbAdapter = $this->getPluginManager()->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $user->setDbAdapter($dbAdapter);
            $form->setInputFilter($user->getRegisterFilter());

            //добавляем файлы
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);

            if ($form->isValid()) {
                $user->exchangeArray($form->getData());
                $password = $user->password;
                //хэшируем пароль перед сохранением
                $user->password = password_hash($user->password, PASSWORD_BCRYPT);
                //Сохраняем аватар
                if ($post['avatar']['name']) {
                    $destination = './data/images/avatars/';
                    $fileName = $post['avatar']['name'];
                    $httpadapter = new \Zend\File\Transfer\Adapter\Http();
                    $httpadapter->setDestination($destination);
                    $httpadapter->addFilter('Rename', [
                        'target' => $destination . md5($fileName . microtime(true)) . '.'
                            . pathinfo($fileName, PATHINFO_EXTENSION),
                    ]);

                    if ($httpadapter->isValid() && $httpadapter->isUploaded() && $httpadapter->receive('avatar')) {
                        $user->avatar = $httpadapter->getFileName();
                    } else {
                        //если не удалось загрузить файл - прекращаем обработку формы
                        $form->get('avatar')->setMessages(['upload_error' => 'Не удалось загрузить файл']);

                        return $this->renderView(['form' => $form]);
                    }
                }

                //сохраняем и авторизуем пользователя
                $this->getUserTable()->save($user);
                $this->getAuthService()
                    ->getAdapter()
                    ->setIdentity($user->login)
                    ->setCredential($password);
                $this->getAuthService()->authenticate();

                $session = new Container('User');
                //сохраняем на сутки 24*60*60
                $session->setExpirationSeconds(86400);
                $session->offsetSet('login', $user->login);

                return $this->redirect()->toUrl('/users/edit');
            }
        }

        return $this->renderView(['form' => $form]);
    }

    /**
     * Редактирование пользователя
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        if (!$this->isAuth()) {
            return $this->redirect()->toUrl('/');
        }

        $user = $this->getUserTable()->getByLogin($this->getAuthService()->getIdentity());
        $form = new EditForm();
        $form->get('gender')->setValue($user->gender);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new EditFilter());
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();
                $user->gender = $data['gender'];
                //сохраняем автарук
                if ($post['avatar']['name']) {
                    $httpadapter = new \Zend\File\Transfer\Adapter\Http();
                    $httpadapter->setDestination('./data/images/avatars/');
                    $httpadapter->addFilter('Rename', [
                        'target' => './data/images/avatars/' . md5($post['avatar']['name'] . microtime(true)) . '.' . pathinfo($post['avatar']['name'],
                                PATHINFO_EXTENSION),
                    ]);
                    if ($httpadapter->isValid() && $httpadapter->isUploaded() && $httpadapter->receive('avatar')) {
                        //удалем старую аватарку
                        if (file_exists($user->avatar)) {
                            @unlink($user->avatar);
                        }
                        $user->avatar = $httpadapter->getFileName();
                    } else {
                        $form->get('avatar')->setMessages(['upload_error' => 'Не удалось загрузить файл']);

                        return $this->renderView(['form' => $form]);
                    }
                }
                $this->getUserTable()->save($user);
            }
        }

        return $this->renderView([
            'form' => $form,
            'user' => $user
        ]);
    }

    /**
     * Просмотр профиля
     * @return \Zend\View\Model\ViewModel
     * @throws \Exception
     */
    public function showAction()
    {
        $user = $this->getUserTable()->getWithCurrentUserRating($this->params()->fromRoute('id'),
            $this->getCurrentUser()->id);

        if (!$user) {
            throw new \Exception();
        }

        return $this->renderView([
            'commentForm' => new AddForm(),
            'ratingForm' => new \Ratings\Form\AddForm(),
            'user' => $user,
        ]);
    }

    /**
     * Завершение сессии
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        $session = new Container('User');
        $session->getManager()->destroy();
        $this->getAuthService()->clearIdentity();

        return $this->redirect()->toUrl('/');
    }
}
