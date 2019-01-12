<?php

namespace app\admin\validate;

use think\Validate;

class CourseCatalogValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'muluName' => 'require',
        'recommendSort' => 'require|number',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'muluName.require' => '二级分类名不能为空',
        'recommendSort.require' => '二级分类排序不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['muluName','recommendSort'],//添加资讯
        'update' => ['muluName','recommendSort'],//编辑资讯
    ];
}
