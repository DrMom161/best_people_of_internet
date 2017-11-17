<?php

namespace Users\Model;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\File\Extension;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\InArray;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

class User
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $avatar;
    /**
     * @var int
     */
    public $gender;
    /**
     * @var int
     */
    public $rating;

    /**
     * @var InputFilter
     */
    protected $inputFilter;
    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * Формирование объекта из массива
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->login = !empty($data['login']) ? $data['login'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->avatar = !empty($data['avatar']) ? $data['avatar'] : null;
        $this->gender = !empty($data['gender']) ? $data['gender'] : 0;
        $this->rating = !empty($data['rating']) ? $data['rating'] : 0;
    }

    /**
     * Сеттер для фильтра
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Сеттер для адаптера
     * @param Adapter $dbAdapter
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Получение яильтра для регистрации
     * @return InputFilter
     */
    public function getRegisterFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'id',
                'required' => true,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                'name' => 'login',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 4,
                            'max' => 15,
                            'messages' => [
                                StringLength::TOO_LONG => 'Логин должен быть не короче 4 и не длиннее 15 символов.',
                                StringLength::TOO_SHORT => 'Логин должен быть не короче 4 и не длиннее 15 символов.',
                                StringLength::INVALID => 'Логин должен быть не короче 4 и не длиннее 15 символов.',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\Db\NoRecordExists',
                        'options' => [
                            'table' => 'users',
                            'field' => 'login',
                            'adapter' => $this->dbAdapter,
                            'exclude' => $this->login,
                            'messages' => [
                                NoRecordExists::ERROR_RECORD_FOUND => 'Пользователь с таким логином уже существует',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            //@TODO если обязательно должна быть хотя бы одна буква - валидатор не подойдет
                            'pattern' => '/(\w|\d){4,15}/i',
                            'messages' => [
                                Regex::NOT_MATCH => 'Логин должен состоять из букв латинского алфавита и цифр'
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'password',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 25,
                            'messages' => [
                                StringLength::TOO_LONG => 'Пароль должен быть не короче  5 и не длиннее 25 символов.',
                                StringLength::TOO_SHORT => 'Пароль должен быть не короче 5 и не длиннее 25 символов.',
                                StringLength::INVALID => 'Пароль должен быть не короче 5 и не длиннее 25 символов.',
                            ],

                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/\d/',
                            'messages' => [
                                Regex::NOT_MATCH => 'Пароль должен содержать хотя бы одну цифру'
                            ],
                        ],
                    ],
                ],
            ]);
            $inputFilter->add([
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
            $inputFilter->add([
                'name' => 'avatar',
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\IsImage',
                        'options' => [
                            'messages' => [
                                IsImage::FALSE_TYPE => 'Файл должен быть изображением'
                            ]
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'max' => 5242880,//5MB
                            'messages' => [
                                Size::TOO_BIG => 'Максимальный размер файла 5МБ'
                            ]
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => 'jpeg,jpg,gif,png',
                            'messages' => [
                                Extension::FALSE_EXTENSION => 'Допустимые форматы изображения jpeg,jpg,gif,png'
                            ]
                        ],
                    ],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}