<?php

namespace app\admin\model;

use think\Model;

class Course extends Model
{
    protected $table = "c_course";

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'createTime';

    //课程所属专业
    public function coursepro()
    {
        return $this->belongsTo('Profession', 'majorId', 'ID');
    }
}
