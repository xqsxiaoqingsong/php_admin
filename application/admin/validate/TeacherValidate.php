<?php

namespace app\admin\validate;

use think\Validate;

class TeacherValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'CNMEDICINEMAJORID' => 'require',
        'TEACHERNAME' => 'require|unique:t_teacher',
        'PHONE' => 'require|mobile',
        'image' => 'require',
        'BRIEF' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'CNMEDICINEMAJORID.require' => '主讲分类不能为空',
        'PHONE.require' => '联系方式不能为空',
        'PHONE.mobile' => '电话格式不正确',
        'image.require' => '图片不能为空',
        'BRIEF.require' => '老师简介不能为空',

        'TEACHERNAME.require' => '老师姓名不能为空',
        'TEACHERNAME.unique' => '此老师已存在',
    ];

    //场景设置
    protected $scene = [
        'save' => ['CNMEDICINEMAJORID','TEACHERNAME','PHONE','image','BRIEF'],//添加
        'update' => ['CNMEDICINEMAJORID','PHONE','image','BRIEF'],//编辑用户
    ];
}
