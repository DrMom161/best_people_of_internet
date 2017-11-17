<?php

namespace Application\Model;

use Zend\Db\Exception\RuntimeException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGatewayInterface;

/**
 * Базовая таблица - в ней собраны типовые операции
 * Class BaseTable
 * @package Application\Model
 */
class BaseTable
{
    /**
     * Гейтвей таблицы
     * @var TableGatewayInterface
     */
    protected $tableGateway;

    /**
     * BaseTable constructor.
     * @param TableGatewayInterface $tableGateway
     */
    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Получение всех записей из таблицы
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Получение записи по идентификатору
     * @param int $id
     * @return mixed
     */
    public function get($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    /**
     * Сохранение набора данных
     * @param array $data
     * @param int $id
     * @return int
     */
    public function save(array $data, $id = 0)
    {
        if ($id === 0) {
            $this->tableGateway->insert($data);

            return $this->tableGateway->lastInsertValue;
        }

        if (!$this->get($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);

        return $id;
    }
}