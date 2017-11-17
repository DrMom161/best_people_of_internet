<?php

namespace Users\Filters;

use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class LoginFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'login',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Обязательное поле.'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Обязательное поле.'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
            ],
        ]);
    }
}