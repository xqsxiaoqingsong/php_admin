<?php

namespace app\admin\validate;

use think\Validate;

class LiveRoomValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'liveRoomName' => 'require',
        'brief' => 'require',
        'classHour' => 'require',
//        'speaker' => 'require',
        'price' => 'require',
        'recommendSort' => 'require|number',
        'imgUrl' => 'require',
        'detailsImgUrl' => 'require',
        'details'=>'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'liveRoomName.require' => '直播间名称不能为空',
        'brief.require' => '简介不能为空',
        'classHour.require' => '课时不能为空',
        'recommendSort.require' => '排序不能为空',
        'recommendSort.number' => '排序只能是数字',
//        'speaker.require' => '权限不能为空',
        'price.require' => '价格不能为空',
        'imgUrl.require' => '列表图片不能为空',
        'detailsImgUrl.require' => '详情图片不能为空',
        'details.require' => '详情不能为空',
    ];

    //场景设置
    protected $scene = [
        'save' => ['liveRoomName','brief','classHour','recommendSort','price','imgUrl','detailsImgUrl','details'],//添加资讯
        'update' => ['liveRoomName','brief','classHour','recommendSort','price','imgUrl','detailsImgUrl','details'],//编辑资讯
    ];
}
