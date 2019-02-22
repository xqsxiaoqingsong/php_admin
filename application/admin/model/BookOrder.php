<?php

namespace app\admin\model;

use think\Model;

class BookOrder extends Model
{
    protected $table = "t_booksorder";
    protected $pk = 'ID';

    //自动写入标准时间，数据库增加 create_time和update_time字段，类型为datetime
    protected $autoWriteTimestamp = 'datetime';

    protected $dateFormat = 'Y-m-d H:i:s';

    // 定义时间戳字段名
    protected $createTime = 'creatTime';
//    protected $updateTime = 'CREATTIME';

    //订单属于用户
    public function bookorderuser()
    {
        return $this->belongsTo('Member', 'MEMBERID', 'ID');
//        return $this->belongsTo('Member', 'MEMBERID', 'ID')->bind(['MEMBERNAME','USERPHONE'=>'PHONE']);
//        return $this->hasOne('Member', 'ID', 'MEMBERID')->bind(['MEMBERNAME','USERPHONE'=>'PHONE']);
    }
}
