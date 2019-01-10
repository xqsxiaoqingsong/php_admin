<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Session;
use think\Request;
use app\admin\model\Login;
use think\captcha\Captcha;
use think\Validate;


class Logins extends Controller
{
    // 验证失败是否抛出异常
    protected $failException = true;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('login/login');
    }

    //设置验证码
    public function verify()
    {
        $captcha = new Captcha();
        $captcha->fontSize = 18;  //验证码字体大小
        $captcha->length = 4;    //验证码长度
        $captcha->useNoise = false;    //是否开启杂点
        $captcha->useCurve = false;     //是否开启混淆曲线
        $captcha->codeSet = '0123456789';  //设置全为数字
        return $captcha->entry();
    }

    public function login(Request $request)
    {
        if ($request->isAjax()) {
//            验证数据
            $validate = Validate('LoginValidate');
            if (!$validate->scene('login')->check($request->param())) {
                $this->error($validate->getError());
            };


            $captcha = $request->param('captcha');
//            return json($captcha);
            //检查验证码是否正确
            $checkCaptcha = new  Captcha();
            if (!$checkCaptcha->check($captcha)) {
                $info = array('status' => 0, 'mes' => '验证码错误');
                return json($info);
            }

            //获取用户名
            $username = $request->param('username');
            //加密截取后的密码
            $password = set_password($request->param('password'));
//        return $password;exit();

            if (!$username || !$password) {
                $info = array('status' => 0, 'mes' => '用户名或密码不能为空');
            }
            if ($username) {
                $user = Login::where('username', $username)->where('password', $password)->find();
//                return json($user);
                if (isset($user)) {
                    Session::set('admin',$user);
//                    return json(Session('admin'));
                    Login::where(['username' => $username, 'password' => $password])->update(['updated_at' => date("Y-m-d H:i:s")]);
                    $info = array('status' => 1, 'mes' => '恭喜您，登录成功');
                    $this->success('登录成功', '/admin/index');
                } else {
                    $info = array('status' => 0, 'mes' => '用户名或密码错误');
                }
                return json($info);
            }
            return json($info);
        }
    }

    public function logout()
    {
        session(null);
        $this->success('登出成功！', url('/admin/Logins/index'));
    }


    /***
     * @param $captcha
     * 验证验证码是否正确
     */
    public function checkCaptcha($captcha)
    {
        $checkCaptcha = new  Captcha();
        return $checkCaptcha->check($captcha);
//        if (!$checkCaptcha->check($captcha)) {
//            $this->error('验证码错误!');
//        }
    }

}
