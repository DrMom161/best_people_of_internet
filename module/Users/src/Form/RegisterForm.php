<?php

namespace Users\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('user');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);
        $this->add([
            'name' => 'login',
            'type' => 'Text',
            'options' => [
                'label' => 'Логин',
            ],
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Пароль',
            ],
            'attributes' => [
                'class' => 'form-control',
                'required' => 'required',
            ],

        ]);
        $this->add([
            'name' => 'avatar',
            'type' => 'File',
            'options' => [
                'label' => 'Аватар',
            ],
        ]);
        $this->add([
            'name' => 'gender',
            'type' => 'Select',
            'options' => [
                'label' => 'Пол',
                'value_options' => [
                    '0' => 'Мужской',
                    '1' => 'Женский',
                ],
            ],
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
                'value' => 'Зарегистрироваться',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);

    }
}