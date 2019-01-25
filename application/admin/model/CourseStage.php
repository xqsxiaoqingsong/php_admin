<?php

namespace app\admin\model;

use think\Model;

class CourseStage extends Model
{
    protected $table = "c_courseStage";

    //课程所属专业
    public function stagecourse()
    {
        return $this->belongsTo('Course', 'courseId');
    }
}
