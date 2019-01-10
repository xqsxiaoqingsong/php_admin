<?php

namespace app\admin\validate;

use think\Validate;

class AuthGroupValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'id' => 'require|number',
        'title' => 'require|unique:auth_group|max:50',
        'status' => 'require|number|in:0,1',
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

        //title
        'title.require' => '用户组名称不能为空',
        'title.unique' => '当前用户组已经存在',
        'title.max' => '用户组名称最大长度只能是五十个字符',

        //status
        'status.require' => '状态不能为空',
        'status.number' => '状态只能是数字',
        'status.in' => '状态非法',
    ];

    //场景设置
    protected $scene = [
        'save' => ['title', 'status'],//添加用户组
        'update' => ['id', 'title', 'status'],//编辑用户组
    ];
}
