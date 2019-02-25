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



function curlByPost($parameters, $url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    return json_decode($result,true);
}


function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
    if(function_exists("mb_substr")){
        if($suffix)
            return mb_substr($str, $start, $length, $charset)."...";
        else
            return mb_substr($str, $start, $length, $charset);
    }elseif(function_exists('iconv_substr')) {
        if($suffix)
            return iconv_substr($str,$start,$length,$charset)."...";
        else
            return iconv_substr($str,$start,$length,$charset);
    }

    $re['utf-8']   = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
    $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
    $re['gbk']    = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
    $re['big5']   = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    if($suffix) return $slice."…";
    return $slice;
}


//当前日期到给定日期的天/时/分
function getDataFormart($date){
    $return = [
        'days' => 0,
        'hours' => 0,
        'minutes' => 0,
    ];
    $margin_date = strtotime($date) - time();
    if($margin_date > 0){
        $days = floor($margin_date/3600/24);    //倒计时还有多少天
        $hours = floor($margin_date/3600%24);     //倒计时还有多少小时（%取余数）
        $mins = floor($margin_date/60%60);
        $return['days'] = $days;
        $return['hours'] = $hours;
        $return['minutes'] = $mins;
    }
    return $return;
}

function dd($data){
    echo "<pre>";
    var_dump($data);
    echo "</pre>";

}