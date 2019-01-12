<?php

namespace app\admin\validate;

use think\Validate;

class CourseStageValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'stageName' => 'require',
        'recommendSort' => 'require|number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'stageName.require' => '权限分类名不能为空',
        'recommendSort.require' => '权限排序不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['stageName','recommendSort'],//添加资讯
        'update' => ['stageName','recommendSort'],//编辑资讯
    ];
}
