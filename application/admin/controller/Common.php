<?php

namespace app\admin\controller;

use app\admin\model\Profession;
use think\Controller;
use think\Request;

class Common extends Controller
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            'adminname'=>session('admin.username'),
            //共享专业方向.
            'categories'=>Profession::all(),
//            'counts' => [
//                'message' => Message::count(),
//                'messagecate' => MessageCategory::count(),
//                'course' => Course::count(),
//                'coursecate' => CourseCategory::count(),
//                'guide' => Guide::count(),
//                'guidecate' => GuideCategory::count(),
//                'book' => Book::count(),
//                'bookcate' => BookCategory::count(),
//                'student' => Student::count(),
//                'studentcate' => StudentCategory::count(),
//                'team' => Team::count(),
//                'teamcate' => TeamCategory::count(),
//                'live' => Live::count(),
//                'livecate' => LiveCategory::count(),
//                'admin' => Admin::count(),
//                'group' => AuthGroup::count(),
//                'rule' => AuthRule::count(),
//                'ad' => Ad::count(),
//                'adcate' => AdCategory::count(),
//            ],
//            "_count" => $this->sidebarCount(),//侧边栏数据统计
        ]);
    }

    //检测是否登录
    public function initialize()
    {
        if(!session('admin')){
            $this->error('请先登录！',url('/admin/Logins/index'));
        }

        //检查权限
//        $auth = new Auth();
//        //获取当前控制器和方法
//        $con = request()->controller();//获取控制器
//        $action = request()->action();//获取当前方法
//        $name = $con . '/' . $action;
////        return json($name);exit();
//        if (!$auth->check($name, session('admin.id'))) {
//            $this->error('抱歉,您没有权限执行此操作!');
//        }
    }
}
