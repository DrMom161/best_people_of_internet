<?php

namespace Users\Filters;

use Zend\InputFilter\InputFilter;
use Zend\Validator\InArray;

class EditFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'gender',
            'required' => true,
            'filters' => [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [0, 1],
                        'messages' => [
                            InArray::NOT_IN_ARRAY => 'Нужно выбрать значение из списка'
                        ]
                    ],
                ],
            ],
        ]);
    }
}