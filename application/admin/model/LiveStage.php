<?php

namespace app\admin\model;

use think\Model;

class LiveStage extends Model
{
    protected $table = "l_livestage";

    //课程所属专业
    public function stagelive()
    {
        return $this->belongsTo('Live', 'liveRoomId');
    }
}
