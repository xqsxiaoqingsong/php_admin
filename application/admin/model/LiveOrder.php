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

    //订单所属用户
    public function userlive()
    {
        return $this->belongsTo('Member', 'MEMBERID');
//        return $this->belongsTo('Member', 'MEMBERID')->bind('PHONE,REALNAME');
    }

    //订单所属用户
    public function userliveclass()
    {
        return $this->belongsTo('Live', 'LIVEROOMID');
    }

    public function selectLiveOrder($where = '')
    {
        return $this->query('select * from t_order c INNER JOIN t_member d on c.MEMBERID=d.ID INNER JOIN l_liveroom f on c.LIVEROOMID=f.id '.$where);
    }
}
