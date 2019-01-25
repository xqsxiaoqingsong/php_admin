<?php

namespace app\admin\model;

use think\Model;

class LiveOrder extends Model
{
    protected $table = "t_order";
    protected $pk = 'ID';

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'CREATTIME';
    protected $updateTime = 'CREATTIME';
}
