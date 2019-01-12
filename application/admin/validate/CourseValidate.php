<?php

namespace app\admin\validate;

use think\Validate;

class CourseValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'majorId' => 'require',
        'courseName' => 'require',
        'brief' => 'require',
        'classHour' => 'require',
        'studyNumber' => 'require',
        'imgUrl' => 'require',
        'detailsImgUrl' => 'require',
        'details' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'majorId.require' => '专业方向不能为空',
        'courseName.require' => '课程名称不能为空',
        'brief.require' => '课程简介不能为空',
        'classHour.require' => '课时数不能为空',
        'studyNumber.require' => '在学人数不能为空',
        'imgUrl.require' => '列表图片不能为空',
        'detailsImgUrl.require' => '详情图片不能为空',
        'details.require' => '课程详情不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['majorId','courseName', 'brief', 'classHour', 'studyNumber', 'imgUrl','detailsImgUrl','details'],//添加资讯
        'update' => ['majorId','courseName', 'brief', 'classHour', 'studyNumber', 'imgUrl','detailsImgUrl','details'],//编辑资讯
    ];
}
