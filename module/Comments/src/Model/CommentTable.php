<?php

namespace Comments\Model;

use Application\Model\BaseTable;
use Zend\Db\Sql\Select;

class CommentTable extends BaseTable
{
    /**
     * Сохранение комментария
     * @param Comment $comment
     * @return int
     */
    public function save(Comment $comment)
    {
        $data = [
            'fromUserId' => $comment->fromUserId,
            'toUserId' => $comment->toUserId,
            'date' => $comment->date,
            'comment' => $comment->comment,
        ];

        return parent::save($data, (int)$comment->id);
    }

    /**
     * Получение списка комментариев для пользователя
     * @param int $userId
     * @return Comment[]
     */
    public function getByToUserId($userId)
    {
        /** @var Select $select */
        $select = $this->tableGateway->getSql()->select();
        $select->columns(['date', 'comment']);
        $select->join(
            ['u' => 'users'],
            'u.id = comments.fromUserId',
            ['author' => 'login', 'gender', 'authorId' => 'id']
        );
        $select->where(['comments.toUserId' => $userId]);
        $select->order(['date DESC']);

        $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);

        return $statement->execute();
    }
}