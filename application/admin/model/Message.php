<?php

namespace app\admin\model;

use think\Model;

class Message extends Model
{
    protected $table = "t_zixun";
    protected $pk = 'ID';

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'PUSHTIME';

    //资讯所属分类
    public function messagetype()
    {
        //localKey为关联表的主键,可设置
        return $this->belongsTo('MessageType', 'TYPE', 'ID');
    }

    //咨询所属专业
    public function messagepro()
    {
        return $this->belongsTo('Profession', 'MAJORID', 'ID');
    }
}
