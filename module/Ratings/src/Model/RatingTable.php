<?php

namespace Ratings\Model;

use Application\Model\BaseTable;
use Zend\Db\Sql\Select;

class RatingTable extends BaseTable
{
    /**
     * Сохранение рейтинга
     * @param Rating $rating
     * @return int
     */
    public function save(Rating $rating)
    {
        $data = [
            'fromUserId' => $rating->fromUserId,
            'toUserId' => $rating->toUserId,
            'date' => $rating->date,
            'mark' => $rating->mark,
        ];

        $where = [
            'fromUserId' => (int)$rating->fromUserId,
            'toUserId' => (int)$rating->toUserId
        ];

        //если оценка уже была, то сохраняем только при изменениия
        $oldRating = $this->tableGateway->select($where);
        if ($oldRating->current() && $oldRating->current()->mark == $rating->mark) {
            return 0;
        }
        if ($oldRating->current()) {
            $this->tableGateway->update($data, $where);

            //если оценка была проставлена - нам нужно ее отменить и проставить соответствующую
            return $rating->mark == 1 ? 2 : -2;
        } else {
            $this->tableGateway->insert($data);

            return $rating->mark;
        }

    }

    /**
     * Получение оценок пользователя
     * @param int $userId
     * @return Rating[]
     */
    public function getByToUserId($userId)
    {
        /** @var Select $select */
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['date', 'mark']);
        $select->join(
            ['u' => 'users'],
            'u.id = ratings.fromUserId',
            ['author' => 'login', 'gender', 'authorId' => 'id']
        );
        $select->where(['ratings.toUserId' => $userId]);
        $select->order(['date DESC']);

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);

        return $statement->execute();
    }
}