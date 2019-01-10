<?php

namespace app\api\controller;

use app\api\model\Login;
use app\api\model\Profession;
use app\api\model\School;
use app\lib\exception\ApiException;
use Qiniu\Auth;
use think\Controller;
use think\Request;
use think\facade\Cache;
use think\facade\Session;

//require 'vendor/autoload.php';
require '../vendor/autoload.php';
//require 'autoload.php';

class Logins extends Common
{

    //发送验证码
    public function code(Request $request)
    {
//        $phone =13476182464;
        $phone = $request->param('phone');
        $type = $request->param('type');
//            return json($request->param());
//
        $validate = Validate('LoginValidate');
        if (!$validate->scene('code')->check($request->param())) {
//                $this->error($validate->getError());
            if ($phone == null) {
                $phone = '';
            }
            $info = array(
                'ApiUrl' => 'http://test.xfxerj.com/api/logins/code',
                'Code' => '1',
                'Data' => array('phone' => $phone),
                'Msg' => $validate->getError(),
                'Time' => time());
            return json($info);
        };
        //判断手机号是否注册过
        if ($type == 1) {
            $user = Login::where('phone', $phone)->find();
            if (isset($user)) {
                if ($phone == null) {
                    $phone = '';
                }
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/logins/code',
                    'Code' => '1',
                    'Data' => array('phone' => $phone),
                    'Msg' => '此手机已经注册过了,请更换手机号',
                    'Time' => time());
                return json($info);
            }
        }
//            $code=rand(1000,9999);
//            Cache::clear(); //清除缓存
//            return json(Cache('sms'));
        if ($code = rand(1000, 9999)) {
            Cache::set('sms_phone', $phone, 300); //设置缓存手机号
            Cache::set('sms_code', $code, 300); //设置缓存验证码
            Cache::set('sms_type', $type, 300); //设置短信type.1为注册,2为忘记密码,3为绑定第三方
            $this->sendSms($phone, $code);
//                Cache::set('sms_phone',$phone,30); //设置缓存手机号
//                Cache::set('sms_code',$code,30); //设置缓存手机号
//                        return json(Cache('sms_code'));

            $info = array(
                'ApiUrl' => 'http://test.xfxerj.com/api/logins/code',
                'Code' => '0',
                'Data' => array('code' => $code, 'type' => $type),
                'Msg' => '发送成功',
                'Time' => time());
        } else {
            $info = array(
                'ApiUrl' => 'http://test.xfxerj.com/api/logins/code',
                'Code' => '1',
                'Data' => array('code' => json_encode($code)),
                'Msg' => '发送验证码失败',
                'Time' => time());
        }
        return json($info);
//        $result = $this->sendSms($phone,$code);
//        return json($result);exit();
    }

    //注册
    public function register(Request $request)
    {
//        return json($request->param());
//        return json(Cache('sms_code'));
        $phone = $request->param('phone');
        $code = $request->param('code');
        $school = $request->param('school');
        $password = $request->param('password');
        $validate = Validate('LoginValidate');
        if (!$validate->scene('check_login')->check($request->param())) {
            if ($phone == null) {
                $phone = '';
            }
            if ($code == null) {
                $code = '';
            }
            $info = array(
                'ApiUrl' => 'http://test.xfxerj.com/api/logins/register',
                'Code' => '1',
                'Data' => array('phone' => $phone, 'code' => $code),
                'Msg' => $validate->getError(),
                'Time' => time());
            return json($info);
        };
        if ($request->param('code') == Cache('sms_code') && $request->param('phone') == Cache('sms_phone') && $request->param('type') == 1) {
            $user = Login::create(['phone' => $phone, 'password' => $password,'school'=>$school]);
//            $newuserinfo = Login::
//                return json($user);
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/register',
                'Code' => '0',
                'Data' => $user,
                'Msg' => '注册成功',
                'Time' => time());
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/register',
                'Code' => '1',
                'Data' => array('phone' => $phone),
                'Msg' => '注册失败',
                'Time' => time());
        }
        return json($info);
    }

    //登陆
    public function check_login(Request $request)
    {
//        return json($request->param());
//                    return json(Cache('sms_code'));
        $phone = $request->param('phone');
        $password = $request->param('password');

        //判断是否为空
        if (!$phone || !$password) {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/check_login',
                'Code' => '1',
                'Data' => array('phone' => json_encode($phone), 'password' => json_encode($password)),
                'Msg' => '用户名或密码不能为空',
                'Time' => time());
            return json($info);
        }

        if ($phone) {
            $user = Login::where('phone', $phone)->where('password', $password)->find();
            if (isset($user)) {
//                Session::set('admin', $user);
                Login::where(['phone' => $phone, 'password' => $password])->update(['updated_at' => date("Y-m-d H:i:s")]);
                $id = $user['id'];
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/check_login',
                    'Code' => '0',
                    'Data' => array('phone' => $phone, 'id' => json_encode($id)),
                    'Msg' => '用户登录成功',
                    'Time' => time());
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/check_login',
                    'Code' => '1',
                    'Data' => array('user' => ''),
                    'Msg' => '用户名或密码错误',
                    'Time' => time());
            }
        }
        return json($info);
    }

    //修改密码
    public function change_password(Request $request)
    {
//                            return json(Cache('sms_code'));
        $phone = $request->param('phone');
        $code = $request->param('code');
        $newpassword = $request->param('password');
        $user = Login::where('phone', $phone)->find();
        $userphone = $user['phone'];
//        return json($userphone);
        if (isset($user)) {
            if ($request->param('code') == Cache('sms_code') && $request->param('phone') == $userphone && $request->param('type') == 2) {
                $user->where('phone', $userphone)->update(['password' => $newpassword]);
//                return json($user);
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/change_password',
                    'Code' => '0',
                    'Data' => array('name' => ''),
                    'Msg' => '密码修改成功!',
                    'Time' => time());
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/change_password',
                    'Code' => '1',
                    'Data' => array('name' => ''),
                    'Msg' => '密码修改失败',
                    'Time' => time());
            }
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/change_password',
                'Code' => '1',
                'Data' => array('phone' => json_encode($phone)),
                'Msg' => '此账号未注册',
                'Time' => time());
        }
        return json($info);
    }

    //第三方授权
    public function thirdlogin(Request $request)
    {
        $openid = $request->param('openid');
        $user = Login::where('openid', $openid)->find();
//        $useropenid = $user['openid'];
        if (isset($user)) {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/thirdlogin',
                'Code' => '0',
                'Data' => array('phone' => $user['phone'], 'id' => $user['id'], 'is_binding' => $user['is_binding']),
                'Msg' => '',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/thirdlogin',
                'Code' => '0',
                'Data' => array('is_binding' => 0),
                'Msg' => '',
                'Time' => time());
            return json($info);
        }
    }

    //绑定第三方
    public function binding(Request $request)
    {
        $phone = $request->param('phone');
        $openid = $request->param('openid');
        $code = $request->param('code');
        $type = $request->param('type');
        $opentype = $request->param('opentype');
        if ($request->param('code') == Cache('sms_code') && $request->param('type') == 3) {
            Login::where('phone', $phone)->update(['openid' => $openid, 'is_binding' => 1, 'type' => $opentype]);
            $newuserinfo = Login::where('phone', $phone)->find();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/binding',
                'Code' => '0',
//                'Data' => array('name' => str_replace("null", '""', $newuserinfo)),
                'Data' => array('phone' => $newuserinfo['phone'], 'id' => json_encode($newuserinfo['id'])),
                'Msg' => '绑定成功',
                'Time' => time());
            return json($info);
        } else {
            $newuserinfo = Login::where('phone', $phone)->find();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/binding',
                'Code' => '1',
//                'Data' => array('name' => str_replace("null", '""', $newuserinfo)),
                'Data' => array('phone' => '', 'id' => ''),
                'Msg' => '绑定失败',
                'Time' => time());
            return json($info);
        }
    }

    //检查是否注册
    public function check_register(Request $request)
    {
        $phone = $request->param('phone');
        $openid = $request->param('openid');
        $user = Login::where('phone', $phone)->find();
        if (isset($user)) {
//            $newuserinfo = Login::where('phone', $phone)->find();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/check_register',
                'Code' => '0',
//                'Data' => array('name' => str_replace("null", '""', $newuserinfo)),
                'Data' => array('isregister' => 1),
                'Msg' => '已经注册',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/check_register',
                'Code' => '0',
                'Data' => array('isregister' => 0),
                'Msg' => '',
                'Time' => time());
            return json($info);
        }
    }

    //选择分校
    public function school_list(Request $request)
    {
        $schools = School::all();
        $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/school_list',
            'Code' => '0',
            'Data' => array('name' => $schools),
            'Msg' => '',
            'Time' => time());
        return json($info);
    }

    //选择专业
    public function profession_list(Request $request)
    {
        $profession = Profession::all();
        $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/profession_list',
            'Code' => '0',
            'Data' => array('name' => $profession),
            'Msg' => '',
            'Time' => time());
        return json($info);
    }

    //提交个人信息
    public function change_info(Request $request)
    {
        $headimg = $request->param('headimg');
        $id = $request->param('id');
        $profession = $request->param('profession');
        $name = $request->param('name');
        $nickname = $request->param('nickname');
        $school = $request->param('school');
//        return json($request->param());
        if (!$headimg){
            $result = Login::where('id', $id)->update(['profession' => $profession, 'name' => $name, 'nickname' => $nickname,'school'=>$school]);
        }else{
            $result = Login::where('id', $id)->update($request->param());
        }
        if (false !== $result) {
            $userinfo = Login::where('id', $id)->find();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/change_info',
                'Code' => '0',
                'Data' => array('name' => $userinfo),
                'Msg' => '修改成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/change_info',
                'Code' => '1',
                'Data' => array('name' => ''),
                'Msg' => '修改失败',
                'Time' => time());
            return json($info);
        }
    }

    //返回个人信息
    public function show_info(Request $request)
    {
        $id= $request->param('id');
//        return json($userinfo);
        if (isset($id)) {
            $userinfo = Login::find($id);
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/show_info',
                'Code' => '0',
                'Data' => array('name' => $userinfo),
                'Msg' => '',
                'Time' => time());
            return json($info);
        }else{
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/show_info',
                'Code' => '1',
                'Data' => array('name' => ''),
                'Msg' => '缺少参数id',
                'Time' => time());
            return json($info);
        }
    }

    //七牛云token
    public function qiniu_token()
    {
// 需要填写你的 Access Key 和 Secret Key
        $accessKey = "FiZadnKLi1OZXDQpUT1y8HmfFU37979Ox76ZV4Ap";
        $secretKey = "c79gEFIkOutGYyc_6XtUteomt8zPyYYWkWj6ER_2";
        $bucket = "whxfx";
// 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
// 生成上传 Token
        $token = $auth->uploadToken($bucket);

        if (isset($token)) {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/qiniu_token',
                'Code' => '0',
                'Data' => array('name' => $token),
                'Msg' => '',
                'Time' => time());
            return json($info);
        }else{
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/logins/qiniu_token',
                'Code' => '1',
                'Data' => array('name' => ''),
                'Msg' => '获取token失败',
                'Time' => time());
            return json($info);
        }
// 上传到七牛后保存的文件名
//        $key = basename($filePath);
//// 初始化 UploadManager 对象并进行文件的上传。
//        $uploadMgr = new UploadManager();
//// 调用 UploadManager 的 putFile 方法进行文件的上传。
//        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

        ////删除本地图片
//        unlink($filePath);
    }
}
