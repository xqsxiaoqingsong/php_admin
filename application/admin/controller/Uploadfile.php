<?php

namespace app\Admin\controller;

use think\Controller;
//use think\Request;
use Qiniu\Auth as Auth;

use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\admin\model\Message;

class Uploadfile extends Controller
{
    /**
     * 上传
     */
    public function upload()
    {

        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/uploads/ 目录下
        $info = $file->move( './uploads');

        ////拼接本地上传的完整路径
//        $filePath = str_replace('\\','/',getcwd() . '/uploads/' . $info->getSaveName());

        $filePath= getcwd().'/uploads/'.$info->getSaveName();
//        return $filePath;

//        var_dump($filePath);
        ///调用七牛sdk执行上传七牛
        $this->qiniu_upload($filePath);
        ////前端显示本地图片地址
//        $result['msg'] = '/uploads/' . $info->getSaveName();
        //前端显示七牛云图片地址
        $result['msg'] = 'http://pjx6k6jor.bkt.clouddn.com/' . $info->getSaveName();
        $result['status']=1;
//
//
//        //以下为直接上传图片至七牛，并返回值到表单
        //js存数据库
//        $result['msg'] = $filePath . $info->getFilename();
//        $photo = Message::create(['image' => $result['msg']]);
//
//        赋值
        $result['image']=$result['msg'];
//
        return json($result);
//        return $result;
    }


    public function qiniu_upload($filePath)
    {

// 需要填写你的 Access Key 和 Secret Key
        $accessKey = "FiZadnKLi1OZXDQpUT1y8HmfFU37979Ox76ZV4Ap";
        $secretKey = "c79gEFIkOutGYyc_6XtUteomt8zPyYYWkWj6ER_2";
        $bucket = "whxfx";
// 构建鉴权对象
        $auth = new Auth($accessKey, $secretKey);
// 生成上传 Token
        $token = $auth->uploadToken($bucket);

// 上传到七牛后保存的文件名
        $key = strstr($filePath,'20');
// 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
// 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

        ////删除本地图片
//        unlink($filePath);
    }
}
