<?php

namespace app\admin\validate;

use think\Validate;

class BannerValidate extends Validate
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
        'MAJOR_NAME' => 'require|unique:t_cnmedicinemajor|max:20',
        'email' => 'require|unique:admins|max:20|email',
        'status' => 'require|number|in:0,1',
        'password' => 'require|max:20',
        'MAJOR_ABBREVIATION' => 'require',
        'BANNERNAME' => 'require',
        'image' => 'require',
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
        //name
        'MAJOR_NAME.require' => '分类不能为空',
        'MAJOR_NAME.unique' => '此分类已存在',
        'MAJOR_NAME.max' => '分类最大长度只能是二十个字符',
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

        //des
        'MAJOR_ABBREVIATION.require' => '简介不能为空',

        //name
        'BANNERNAME.require' => '名称不能为空',
        'image.require' => '图片不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['BANNERNAME','image'],//添加
        'update' => ['BANNERNAME'],//编辑用户
    ];
}
