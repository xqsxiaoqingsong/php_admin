<?php

namespace app\api\model;

use think\Model;

class Course extends Model
{
    protected $table = "ss_course";

    //课程属于老师
    public function courseteacher()
    {
        return $this->belongsTo('Teacher','teacher_id');
    }
}
