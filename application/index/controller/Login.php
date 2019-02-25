<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Login extends Common {

    //注册页面
    public function register(){
        return $this->fetch('login/register');
    }

    //注册提交
    public function registerDo(Request $request){
        var_dump($_POST);die;
    }

    //登陆
    public function login(Request $request){
        $loginRes = curlByPost(['phone'=>$request->phone,'password'=>$request->password],Config('urlLogin'))['Data'];
        if(isset($loginRes['name']['uid']) && intval($loginRes['name']['uid']) > 0){
            $userInfo = curlByPost(['id'=>intval($loginRes['name']['uid'])],Config('urlUserInfo'))['Data'];
            if($userInfo['id']){
                session('userInfo',$userInfo);
                session('userId',$userInfo['id']);
                return json(['Code'=>'0','userInfo'=>$userInfo,'returnUrl'=>$request->returnUrl]);
            }else{
                return json(['Code'=>1,'Msg'=>$userInfo['Msg']]);
            }
        }
    }

    //登出
    public function logout(Request $request){
        if($request->logout){
            session('userInfo',null);
            session('userId',null);
            return json(['Code'=>1,'Msg'=>'登出成功']);
        }
    }

    
}