<?php

namespace Comments\Model;

use Zend\Db\Adapter\Adapter;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\RecordExists;

class Comment
{
    /**
     * @var int
     */
    public $id;
    /**
     * @var int
     */
    public $fromUserId;
    /**
     * @var int
     */
    public $toUserId;
    /**
     * @var int
     */
    public $date;
    /**
     * @var string
     */
    public $comment;

    /**
     * @var Adapter
     */
    protected $dbAdapter;
    /**
     * @var InputFilter
     */
    protected $inputFilter;

    /**
     * Формирует объект из массива данных
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->fromUserId = !empty($data['fromUserId']) ? $data['fromUserId'] : null;
        $this->toUserId = !empty($data['toUserId']) ? $data['toUserId'] : null;
        $this->date = !empty($data['date']) ? $data['date'] : null;
        $this->comment = !empty($data['comment']) ? $data['comment'] : null;
    }

    /**
     * Сеттер для адаптера базы данных
     * @param Adapter $dbAdapter
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    /**
     * Получение фильтра для формы регистрации
     * @return InputFilter
     */
    public function getRegisterFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add([
                'name' => 'toUserId',
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\Db\RecordExists',
                        'options' => [
                            'table' => 'users',
                            'field' => 'id',
                            'adapter' => $this->dbAdapter,
                            'exclude' => $this->toUserId,
                            'messages' => [
                                RecordExists::ERROR_NO_RECORD_FOUND => 'Пользователь с таким ID не существует',
                            ],
                        ],
                    ],
                ],
            ]);

            $inputFilter->add([
                'name' => 'comment',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}