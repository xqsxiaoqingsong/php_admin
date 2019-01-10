<?php

namespace app\lib\exception;

use think\Exception;

class ApiException extends Exception
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
