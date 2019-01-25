<?php

namespace app\admin\validate;

use think\Validate;

class MemberValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'REALNAME' => 'require',
        'MEMBERNAME' => 'require',
        'PHONE' => 'require|mobile',
        'PASSWORD' => 'require',
        'MAJOR_NAME' => 'require',
//        'TEACHERNAME' => 'require|unique:t_teacher',
        'image' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'REALNAME.require' => '真实姓名不能为空',
        'MEMBERNAME.require' => '昵称不能为空',
        'PHONE.require' => '手机号不能为空',
        'PHONE.mobile' => '电话格式不正确',
        'PASSWORD.require' => '密码不能为空',
        'MAJOR_NAME.require' => '请选择主讲分类',
        'image.require' => '图片不能为空',

//        'TEACHERNAME.require' => '老师姓名不能为空',
//        'TEACHERNAME.unique' => '此老师已存在',
    ];

    //场景设置
    protected $scene = [
        'save' => ['REALNAME','MEMBERNAME','PHONE','PASSWORD','MAJOR_NAME','image'],//添加
        'update' => ['REALNAME','MEMBERNAME','PHONE','PASSWORD','MAJOR_NAME','image'],//编辑用户
    ];
}