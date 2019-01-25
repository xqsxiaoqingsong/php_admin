<?php

namespace app\admin\controller;

use app\admin\model\CourseOrder;
use app\admin\model\LiveOrder;
use app\admin\model\Member;
use think\Controller;
use think\Request;

class Memberauths extends Controller
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_eduadmin' => 'am-in',   //展开
            '_memberauth' => 'am-active',   //高亮
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        $coursestages = CourseOrder::select()->toArray();
//        $livestages = LiveOrder::select()->toArray();
//        $allstages = array_merge($coursestages,$livestages);
        $usercourse = CourseOrder::column('MEMBERID');
        $userlive = LiveOrder::column('MEMBERID');
        return json($usercourse);
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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
