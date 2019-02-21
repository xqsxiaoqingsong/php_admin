<?php

namespace app\admin\validate;

use think\Validate;

class BookValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'BOOKNAME' => 'require',
        'BRIEF' => 'require',
        'MAJORID' => 'require',
        'RECOMMENDSORT' => 'require',
        'PRICE' => 'require',
        'imgUrl' => 'require',
        'detailsImgUrl' => 'require',
        'DETAILS' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'BOOKNAME.require' => '图书名称不能为空',
        'BRIEF.require' => '图书简介不能为空',
        'MAJORID.require' => '请选择专业方向',
        'RECOMMENDSORT.require' => '推荐排序不能为空',
        'PRICE.require' => '价格不能为空',
        'imgUrl.require' => '列表图片不能为空',
        'detailsImgUrl.require' => '详情图片不能为空',
        'DETAILS.require' => '图书详情不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['BOOKNAME','BRIEF', 'MAJORID', 'RECOMMENDSORT', 'PRICE','imgUrl', 'detailsImgUrl','DETAILS'],//添加资讯
        'update' => ['BOOKNAME','BRIEF', 'MAJORID', 'RECOMMENDSORT', 'PRICE','imgUrl', 'detailsImgUrl','DETAILS'],//编辑资讯
    ];
}
