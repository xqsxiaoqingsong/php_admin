<?php

namespace app\admin\model;

use think\Model;

class Face extends Model
{
    protected $table = "ss_face";

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    //面授所属专业
    public function facepro()
    {
        return $this->belongsTo('Profession','profession_id');
    }
}
