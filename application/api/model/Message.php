<?php

namespace app\api\model;

use think\Model;

class Message extends Model
{
    protected $table = "ss_messages";

    protected $dateFormat = 'Y-m-d';
    protected $type = [
        'created_at'  =>  'datetime',
    ];
}
