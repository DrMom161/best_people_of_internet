<?php

namespace Users\Model;

use Application\Model\BaseTable;
use RuntimeException;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

/**
 * Class UserTable
 * @package Users\Model
 */
class UserTable extends BaseTable
{
    /**
     * Получение пользователя по логину
     * @param string $login
     * @return User
     */
    public function getByLogin($login)
    {
        $rowset = $this->tableGateway->select(['login' => $login]);

        return $rowset->current();
    }

    /**
     * Сохранение пользователя
     * @param User $user
     * @return int
     */
    public function save(User $user)
    {
        $data = [
            'login' => $user->login,
            'password' => $user->password,
            'avatar' => $user->avatar,
            'gender' => $user->gender,
            'rating' => $user->rating,
        ];

        $id = (int)$user->id;

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

    /**
     * Обновление рейтинга пользователя
     * @param int $id
     * @param int $mark
     */
    public function updateRating($id, $mark)
    {
        $this->tableGateway->update(
            ['rating' => new Expression('rating' . ($mark > 0 ? ' + ' . (int)$mark : ' - ' . abs((int)$mark)))],
            ['id' => $id]
        );
    }

    /**
     * Получение списка пользователей с оценками от другого пользователя
     * @param int $id
     * @param int $limit
     * @return mixed
     */
    public function getAllWithCurrentUserRatings($id, $limit = 15)
    {
        /** @var Select $select */
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['login', 'avatar', 'rating', 'id']);

        $select->join(
            ['r' => 'ratings'],
            new Expression('r.toUserId = users.id AND r.fromUserId = ' . (int)$id),
            ['mark'],
            'left'
        );
        $select->order(['rating DESC']);
        $select->limit((int)$limit);

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);

        return $statement->execute();
    }

    /**
     * Получение пользователя с оценкой от другого
     * @param int $id
     * @param $currentUserId
     * @return mixed
     */
    public function getWithCurrentUserRating($id, $currentUserId)
    {
        /** @var Select $select */
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['login', 'avatar', 'rating', 'id']);

        $select->join(
            ['r' => 'ratings'],
            new Expression('r.toUserId = users.id AND r.fromUserId = ' . (int)$currentUserId),
            ['mark'],
            'left'
        );
        $select->where(['id' => $id]);
        $select->order(['rating DESC']);
        $select->limit(15);

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);

        return $statement->execute()->current();
    }
}