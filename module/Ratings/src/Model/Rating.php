<?php

namespace Ratings\Model;

class Rating
{
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
     * @var int
     */
    public $mark;

    public function exchangeArray(array $data)
    {
        $this->fromUserId = !empty($data['fromUserId']) ? $data['fromUserId'] : null;
        $this->toUserId = !empty($data['toUserId']) ? $data['toUserId'] : null;
        $this->date = !empty($data['date']) ? $data['date'] : null;
        $this->mark = !empty($data['mark']) ? $data['mark'] : null;
    }

}