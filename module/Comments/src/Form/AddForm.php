<?php

namespace Comments\Form;

use Zend\Form\Form;

class AddForm extends Form
{
    /**
     * AddForm constructor.
     * @param string $name
     */
    public function __construct($name = null)
    {

        parent::__construct('comment');

        $this->add([
            'name' => 'toUserId',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
                'hidden' => true,
                'id' => 'userId'
            ],
        ]);
        $this->add([
            'name' => 'comment',
            'type' => 'Text',
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Сохранить',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}