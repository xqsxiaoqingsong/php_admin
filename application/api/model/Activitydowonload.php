<?php

namespace app\api\model;

use think\Model;

class Activitydowonload extends Model
{
    protected $table = "a_downloadfile";

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
//    protected $autoWriteTimestamp = 'datetime';

//    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
//    protected $createTime = 'CREATETIME';
//    protected $updateTime = 'CREATTIME';
}
