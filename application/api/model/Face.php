<?php

namespace app\api\model;

use think\Model;

class Face extends Model
{
    protected $table = "ss_face";

    //面授属于用户收藏
//    public function facecollect()
//    {
//        return $this->belongsTo('CustomerCollect','id');
//    }
}
