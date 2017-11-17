<?php

namespace Ratings\Controller;

use Application\Controller\BaseController;
use Comments\Form\AddForm;
use Ratings\Model\Rating;
use Ratings\Model\RatingTable;
use Zend\View\Model\JsonModel;

class IndexController extends BaseController
{


    /**
     * IndexController constructor.
     * @param RatingTable $table
     */
    public function __construct(RatingTable $table)
    {
        $this->table = $table;
    }

    /**
     * Получение истории оценок пользователя
     * @return JsonModel
     */
    public function indexAction()
    {
        $userId = $this->getRequest()->getQuery('userId', null);
        $comments = $this->table->getByToUserId($userId);
        $commentsArray = [];
        foreach ($comments as $comment) {
            foreach ($comment as &$value) {
                $value = nl2br(htmlentities($value));
            }
            $commentsArray[] = $comment;
        }

        return new JsonModel([
            'list' => $commentsArray
        ]);
    }

    /**
     * Получение текущего значения рейтинга для пользователя
     * @return JsonModel
     */
    public function getForUserAction()
    {
        $userId = $this->getRequest()->getQuery('userId', null);

        return new JsonModel([
            'rating' => $this->getUserTable()->get($userId)->rating
        ]);
    }

    /**
     * Сохранение оценки
     * @return JsonModel
     * @throws \Exception
     */
    public function saveAction()
    {
        if (!$this->isAuth()) {
            throw new \Exception();
        }

        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new \Exception();
        }

        $form = new AddForm();
        $form->setData($request->getPost());
        if ($form->get('toUserId')->getValue() == $this->getCurrentUser()->id) {
            throw new \Exception();
        }
        if (!$form->isValid()) {
            throw new \Exception();
        }

        $rating = new Rating();
        $rating->toUserId = $form->get('toUserId')->getValue();
        $rating->fromUserId = $this->getCurrentUser()->id;
        $rating->date = time();
        $rating->mark = $request->getPost('mark', null) == 1 ? 1 : -1;

        //чтобы не делать выборки с агрегацией все время - записываем рейтинг в избыточное поле
        $changeValue = $this->table->save($rating);
        if ($changeValue) {
            $this->getUserTable()->updateRating($rating->toUserId, $changeValue);
        }

        return new JsonModel();
    }
}
