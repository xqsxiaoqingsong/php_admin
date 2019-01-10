<?php

namespace app\lib\exception;

use think\Exception;

//服务器内部错误
class ErrorException extends Exception
{
    public $status;
    public $message;
    public $http_code;

    public function __construct($message = "", $http_code = 0, $status=0)
    {
        parent::__construct();
        $this->status = $status;
        $this->http_code = $http_code;
        $this->message = $message;
    }
}
