<?php

namespace app\admin\validate;

use think\Validate;

class AuthRuleValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'id' => 'require|number',
        'parent_id' => 'require|number',
        'name' => 'require|unique:auth_rule|max:20',
        'title' => 'require|unique:auth_rule|max:20',
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

        //parent_id
        'parent_id.require' => '上级权限不能为空',
        'parent_id.number' => '权限非法',

        //name
        'name.require' => '权限不能为空',
        'name.unique' => '当前权限已经存在',
        'name.max' => '权限最大长度只能是二十个字符',

        //title
        'title.require' => '权限名称不能为空',
        'title.unique' => '当前权限名称已经存在',
        'title.max' => '权限名称的最大长度只能是二十个字符',
    ];

    //场景设置
    protected $scene = [
        'save' => ['parent_id', 'name', 'title'],//添加权限
        'update' => ['id', 'name', 'title'],//更新权限
    ];
}
