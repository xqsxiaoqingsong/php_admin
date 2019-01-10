<?php

namespace app\api\model;

use think\Model;

class Login extends Model
{
    protected $table = "ss_customer";
    protected $primaryKey = 'uid';
    //自动写入标准时间
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
}
