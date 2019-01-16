<?php

namespace app\admin\validate;

use think\Validate;

class CourseClassValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'className' => 'require',
        'trySeeUrl' => 'require',
        'positiveUrl' => 'require',
        'stageId' => 'require',
        'duration' => 'require',
        'speaker' => 'require',
        'recommendSort' => 'require|number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'className.require' => '课节名不能为空',
        'trySeeUrl.require' => '试看地址不能为空',
        'positiveUrl.require' => '正片地址不能为空',
        'stageId.require' => '权限不能为空',
        'duration.require' => '时长不能为空',
        'speaker.require' => '讲师不能为空',
        'recommendSort.require' => '课节排序不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['className','trySeeUrl','positiveUrl','recommendSort','stageId','duration','speaker','recommendSort'],//添加资讯
        'update' => ['className','trySeeUrl','positiveUrl','recommendSort','stageId','duration','speaker','recommendSort'],//编辑资讯
    ];
}
