<?php

namespace Comments\Controller;

use Application\Controller\BaseController;
use Comments\Form\AddForm;
use Comments\Model\Comment;
use Comments\Model\CommentTable;
use Zend\View\Model\JsonModel;

class IndexController extends BaseController
{
    /**
     * IndexController constructor.
     * @param CommentTable $table
     */
    public function __construct(CommentTable $table)
    {
        $this->table = $table;
    }

    /**
     * Получение списка комментариев для пользователя
     * @return JsonModel
     */
    public function indexAction()
    {
        $userId = $this->getRequest()->getQuery('userId', null);
        $comments = $this->table->getByToUserId($userId);
        $commentsArray = [];
        //преобразуем ResultSet в массив
        foreach ($comments as $comment) {
            foreach ($comment as &$value) {
                //эскейпим и приводим переводы строк к HTML
                $value = nl2br(htmlentities($value));
            }
            $commentsArray[] = $comment;
        }

        return new JsonModel([
            'list' => $commentsArray
        ]);
    }

    /**
     * Сохранение комментария
     * @return JsonModel
     * @throws \Exception
     */
    public function saveAction()
    {
        if (!$this->isAuth()) {
            throw new \Exception();
        }
        $dbAdapter = $this->getPluginManager()->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new \Exception();
        }

        $comment = new Comment();
        $form = new AddForm();
        $comment->setDbAdapter($dbAdapter);
        $form->setData($request->getPost());
        $form->setInputFilter($comment->getRegisterFilter());
        if (!$form->isValid()) {
            throw new \Exception();
        }

        $user = $this->getUserTable()->getByLogin($this->getAuthService()->getIdentity());
        $comment->toUserId = $form->get('toUserId')->getValue();
        $comment->fromUserId = $user->id;
        $comment->date = time();
        $comment->comment = $form->get('comment')->getValue();
        $this->table->save($comment);

        return new JsonModel();
    }
}
