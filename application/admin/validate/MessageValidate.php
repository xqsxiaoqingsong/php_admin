<?php

namespace app\admin\validate;

use think\Validate;

class MessageValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'MAJORID' => 'require',
        'TYPE' => 'require',
        'ZIXUNNAME' => 'require',
        'CONTENT' => 'require',
        'ZIXUNURL' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'MAJORID.require' => '主讲分类不能为空',
        'TYPE.require' => '类型不能为空',
        'ZIXUNNAME.require' => '标题不能为空',
        'CONTENT.require' => '简介不能为空',
        'ZIXUNURL.require' => '详情不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['MAJORID', 'TYPE', 'ZIXUNNAME', 'CONTENT', 'ZIXUNURL'],//添加资讯
        'update' => ['MAJORID', 'TYPE', 'ZIXUNNAME', 'CONTENT', 'ZIXUNURL'],//编辑资讯
    ];
}
