<?php

namespace app\admin\model;

use think\Model;

class Teacher extends Model
{
    protected $table = "t_teacher";
    protected $pk = 'ID';

    //面授所属专业
    public function teacherpro()
    {
        //localKey为关联表的主键,可设置
        return $this->belongsTo('Profession','CNMEDICINEMAJORID','ID');
    }
}
