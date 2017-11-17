<?php

namespace Users\Form;

use Zend\Captcha\Image;
use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {

        parent::__construct('user');
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
        $captchaImage = new Image([
                'font' => './data/fonts/Roboto-Black.ttf',
                'width' => 200,
                'wordLen' => 6,
                'useNumbers' => true,
                'height' => 100,
                'messages' => [
                    Image::BAD_CAPTCHA => 'Текст с картинки введен неверно.',
                ],
            ]
        );
        $captchaImage->setImgDir('./data/captcha');
        $captchaImage->setImgUrl('/captcha');

        $this->add([
            'type' => 'Captcha',
            'name' => 'captcha',
            'options' => [
                'label' => '',
                'captcha' => $captchaImage,
            ],
            'attributes' => [
                'class' => 'form-control'
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
                'value' => 'Войти',
                'id' => 'submitbutton',
                'class' => 'btn btn - primary',
            ],
        ]);

    }
}