<?php

//全局异常处理类
namespace app\lib\exception;

use think\exception\Handle;
use think\facade\Log;

class ApiHandleException extends Handle
{
    public $http_code = 500;

    public function render(\Exception $e)
    {
        if (config('app_debug')==true){
            return parent::render($e);
        }

        if ($e instanceof ApiException) {
            $this->http_code=$e->http_code;
        }

        //服务器内部错误 写入日志
        if ($e instanceof ErrorException){
            $this->http_code=$e->http_code;
            $this->recordErrorLog($e->getMessage());
        }
        
        return show(0, $e->getMessage(), [], $this->http_code);
    }


    //服务器内部错误 记录日志
    public function recordErrorLog($message){
          Log::init([
              'level'=>['error'],
              'close'=>false
          ]);
          Log::write($message,'error');
    }

}
