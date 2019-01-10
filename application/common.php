<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//截取md5加密的密码
function set_password($password)
{
    return substr(md5($password), '6', '-6');
}

//截取文章
//function set_content($contents){
//    return substr($contents,'0','100');
//}

/****
 * 返回状态
 */
function return_status($status)
{
    $arr = [
        '-1' => "<a type='button' class='am-btn am-btn-danger'>删除</a>",
        '0' => "<a type='button' class='am-btn am-btn-warning'>禁用</a>",
        '1' => "<a type='button' class='am-btn am-btn-success'>正常</a>"
    ];
    return $arr[$status];
}


/***
 * 改变属性
 * @param $model
 * @param $attr
 * @return string
 */
function is_something($model, $attr)
{
    if ($model->$attr) {
        return '<span class="am-icon-check change_is_show" data-attr="' . $attr . '"></span>';
    }

    return '<span class="am-icon-close change_is_show" data-attr="' . $attr . '"></span>';
}

function img_empty($content){
    $content=preg_replace("/src=\"(.+?)\"/","src=\"$1\" width=\"100%\"",$content);
    return $content;
}