<?php

namespace app\admin\validate;

use think\Validate;

class AdminValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'id' => 'require|number',
        'group_id' => 'require|number',
        'name' => 'require|unique:admins|max:20',
        'email' => 'require|unique:admins|max:20|email',
        'status' => 'require|number|in:0,1',
        'password' => 'require|max:20',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        //id
        'id.require' => 'id不能为空',
        'id.number' => 'id只能是数字',
        //group_id
        'group_id.require' => '所属用户组不能为空',
        'group_id.number' => '用户组非法',
        //username
        'name.require' => '用户名称不能为空',
        'name.unique' => '当前用户已经存在',
        'name.max' => '用户名最大长度只能是二十个字符',
        //email
        'email.require' => '邮箱帐号不能为空',
        'email.unique' => '邮箱已经存在',
        'email.max' => '邮箱的最大长度只能是二十个字符',
        'email.email' => '邮箱格式错误',
        //status
        'status.require' => '状态不能为空',
        'status.number' => '状态只能是数字',
        'status.in' => '状态非法',

        //password
        'password.require' => '密码不能为空',
        'password.max' => '密码的最大长度只能是二十个字符',
    ];

    //场景设置
    protected $scene = [
        'save' => ['group_id', 'name', 'email', 'status', 'password'],//添加用户
        'update' => ['id', 'group_id', 'name', 'email', 'status', 'password'],//编辑用户
    ];
}
