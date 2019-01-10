<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    //指定数据表
    protected $table = "t_adminuser";
    //自动写入标准时间
    protected $autoWriteTimestamp = 'datetime';
    protected $dateFormat = 'Y-m-d H:i:s';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
}
