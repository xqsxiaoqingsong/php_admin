<?php

namespace app\admin\model;

use think\Model;

class Live extends Model
{
    protected $table = "l_liveroom";

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'createTime';

    //直播所属专业
    public function livepro()
    {
        return $this->belongsTo('Profession', 'majorId', 'ID');
    }
}
