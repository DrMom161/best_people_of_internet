<?php

namespace Application\Controller;

use Ratings\Form\AddForm;

class IndexController extends BaseController
{
    /**
     * Главная страница
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return $this->renderView([
            'ratingForm' => new AddForm(),
            'authUser' => $this->getCurrentUser()
        ]);

    }
}
