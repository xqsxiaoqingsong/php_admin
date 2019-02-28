<?php

namespace app\admin\validate;

use think\Validate;

class FaceValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'MAJORID' => 'require',
        'COURSENAME' => 'require',
        'FACETRAINDETAILS' => 'require',
        'COUNSELPHONE' => 'require',
        'city' => 'require',
        'RECOMMENDSORT' => 'require',
        'imgUrl' => 'require',
        'detailsImgUrl' => 'require',
        'FACETRAINWORD' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'MAJORID.require' => '请选择专业方向',
        'COURSENAME.require' => '面授名称不能为空',
        'FACETRAINDETAILS.require' => '面授简介不能为空',
        'COUNSELPHONE.require' => '咨询电话不能为空',
        'RECOMMENDSORT.require' => '推荐排序不能为空',
        'imgUrl.require' => '列表图片不能为空',
        'detailsImgUrl.require' => '详情图片不能为空',
        'FACETRAINWORD.require' => '面授详情不能为空',
        'city.require' => '请选面授地址',
    ];

    //场景设置
    protected $scene = [
        'save' => ['MAJORID', 'COURSENAME', 'FACETRAINDETAILS', 'COUNSELPHONE', 'city', 'RECOMMENDSORT', 'imgUrl', 'detailsImgUrl', 'FACETRAINWORD'],//添加
        'update' => ['MAJORID', 'COURSENAME', 'FACETRAINDETAILS', 'COUNSELPHONE', 'city', 'RECOMMENDSORT', 'imgUrl', 'detailsImgUrl', 'FACETRAINWORD'],//编辑
    ];
}
