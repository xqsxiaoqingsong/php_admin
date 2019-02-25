<?php

namespace app\api\controller;

use app\api\model\Activityuser;
use think\Controller;
use think\Request;
use think\Validate;

class Activity extends Controller
{
    //添加用户信息
    public function createuserinfo(Request $request)
    {
        if ($request->isPost()) {
            $name = $request->param('name');
            $phone = $request->param('phone');
            $province = $request->param('province');
            $major = $request->param('major');
            $type = $request->param('activityType');
            $activityname = $request->param('activityName');
            $validate = Validate('UserValidate');
            if (!$validate->scene('save')->check($request->param())) {
//                $this->error($validate->getError());
//                $info = $this->error();
//                return json($info);
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '1',
                    'Data' => array(),
                    'Msg' => $validate->getError(),
                    'Time' => time());
                return json($info);
            };
            $result = Activityuser::create([
                'USERNAME' => $name,
                'PHONE' => $phone,
                'ADDRESS' => $province,
                'MAJORNAME' => $major,
                'ACTIVITYTYPE' => $type,
                'ACTIVITYNAME' => $activityname,
            ]);
            if (false !== $result) {
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '0',
                    'Data' => array(),
                    'Msg' => '添加成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '1',
                    'Data' => array('name' => ''),
                    'Msg' => '添加失败',
                    'Time' => time());
                return json($info);
            }
        }
    }

    //下载文件
    public function downloadfile(Request $request)
    {
        if ($request->isPost()) {
            $download = new \think\response\Download('推荐承诺书.doc');
            return $download->name('推荐承诺书.doc');
        }
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
