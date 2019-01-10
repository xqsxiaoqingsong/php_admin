<?php

namespace app\api\controller;

use app\api\model\Banner;
use app\api\model\Book;
use app\api\model\Ceshi;
use app\api\model\Course;
use app\api\model\CourseCatalog;
use app\api\model\CourseClass;
use app\api\model\CustomerCollect;
use app\api\model\Face;
use app\api\model\Live;
use app\api\model\LiveUrl;
use app\api\model\Message;
use app\api\model\Order;
use app\api\model\SearchWord;
use app\api\model\Teacher;
use think\config\driver\Json;
use think\Console;
use think\Controller;
use think\facade\Session;
use think\Request;

class Index extends Common
{
    public function __construct()
    {
        parent::__construct();
        //PC端应用
//        $this->AppID = 'wxc63cd024029c06f1';
//        $this->AppSecret = '5d36545fa403d799053318ea9b86c41f';
        //微信公众号应用
        $this->zshdusername = 'admin@whxfx.com';
        $this->zshdpassword = '888888';
//        $this->callback = 'http://www.xfxerj.com/index/wechat/WxCallback';
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    //首页
    public function index()
    {
        return 123;
    }

    //首页接口
    public function all_info(Request $request)
    {
        $id = $request->param('profession_id');
        if (isset($id)) {
            $message = Message::where('profession_id', $id)->field('id,title,des,created_at')->select();
            $live = Live::where('profession_id', $id)->field('id,title,class,thumb,price,real_price')->limit(2)->select();
            $course = Course::where('p_id', 0)->where('profession_id', $id)->field('id,title,teacher_id,class,thumb,price,real_price')->limit(2)->select();
            $book = Book::where('profession_id', $id)->field('id,title,author,price,thumb,real_price')->limit(2)->select();
            $banner = Banner::all();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/all_info',
                'Code' => '0',
                'Data' => array('message' => $message, 'live' => $live, 'course' => $course, 'book' => $book, 'banner' => $banner),
                'Msg' => '获取成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/all_info',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取首页失败,缺少参数profession_id',
                'Time' => time());
            return json($info);
        }
    }

    //搜索
    public function searchall(Request $request)
    {
        $condition = $request->param('search');
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('search') and $request->param('search') != '') {
                $search = "%" . $request->param('search') . "%";
                $query->where('title', 'like', $search);
            }
        };
        if (isset($condition)) {
            $message = Message::where($where)->select();
            $live = Live::where($where)->select();
            $book = Book::where($where)->select();
            $course = Course::where($where)->select();
            $word = SearchWord::where('name', $condition)->find();
            if ($word) {
                $word->setInc('view');
            } else {
                SearchWord::create(['name' => $condition]);
            }
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/searchall',
                'Code' => '0',
                'Data' => [
                    array('course' => $course, 'theme' => '在线课程'),
                    array('book' => $book, 'theme' => '图书'),
                    array('live' => $live, 'theme' => '直播'),
                    array('message' => $message, 'theme' => '考务动态'),
                ],
                'Msg' => '搜索成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/searchall',
                'Code' => '1',
                'Data' => '',
                'Msg' => '缺少搜索参数search',
                'Time' => time());
            return json($info);
        }
    }

    //搜索前十条词
    public function search_list()
    {
        $words  = SearchWord::order('view desc')->limit(10)->select();
        $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/search_list',
            'Code' => '0',
            'Data' => $words,
            'Msg' => '获取word成功',
            'Time' => time());
        return json($info);
    }

    //面授列表
    public function face_list(Request $request)
    {
        $site = $request->param('site');
        $profession_id = $request->param('profession_id');
        $page = $request->param('page');

        if ($request->param('site') == '全部') {
            $face = Face::page($page, 5)->field('id,title,site,price,thumb')->select();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/face_list',
                'Code' => '0',
                'Data' => $face,
//            'Data' => array('id' => json_encode($face['id']),'title'=>$face['title'],'site'=>$face['site'],'price'=>$face['price'],'thumb'=>$face['thumb']),
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        }

        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('site') and $request->param('site') != '') {
                $search = "%" . $request->param('site') . "%";
                $query->where('site', 'like', $search);
            }
            //按分类搜索
            if ($request->param('profession_id') and $request->param('profession_id') != '-1') {
                $query->where('profession_id', $request->param('profession_id'));
            }
        };

        if (isset($profession_id)) {
            $face = Face::where($where)->page($page, 5)->field('id,title,site,price,thumb')->select();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/face_list',
                'Code' => '0',
                'Data' => $face,
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/face_list',
                'Code' => '0',
                'Data' => '',
                'Msg' => '获取列表失败,缺少参数profession_id',
                'Time' => time());
            return json($info);
        }
    }

    //面授详情
    public function show_face(Request $request)
    {
        $id = $request->param('id');
        $uid = $request->param('u_id');
        $face = Face::field('id,title,site,price,img,des,detail')->find($id);
        if (isset($uid)) {
            $collect = CustomerCollect::where('u_id', $uid)->where('type', 3)->where('b_id', $id)->count();
            $face['is_collect'] = $collect;
//            return $collect;
//            if ($collect == 0){
//                $collect = "";
//            }
            if (isset($face)) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_face',
                    'Code' => '0',
                    'Data' => $face,
                    'Msg' => '获取详情成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_face',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取详情失败',
                    'Time' => time());
                return json($info);
            }
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_face',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取用户信息失败,缺少参数u_id',
                'Time' => time());
            return json($info);
        }
    }

    //收藏和取消收藏
    public function collect(Request $request)
    {
        $id = $request->param('id');
        $uid = $request->param('u_id');
        $type = $request->param('type');
        $iscollect = $request->param('iscollect');
        if ($iscollect == 1) {
            $result = CustomerCollect::where('u_id', $uid)->where('type', $type)->where('b_id', $id)->find();
            if ($result) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/collect',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '请勿重复操作',
                    'Time' => time());
                return json($info);
            } else {
                CustomerCollect::create(['u_id' => $uid, 'b_id' => $id, 'type' => $type, 'is_collect' => 1]);
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/collect',
                    'Code' => '0',
                    'Data' => '',
                    'Msg' => '收藏成功',
                    'Time' => time());
                return json($info);
            }
        }
        if ($iscollect == 0) {
            $result = CustomerCollect::where('u_id', $uid)->where('type', $type)->where('b_id', $id)->find();
            if ($result) {
                CustomerCollect::where('u_id', $uid)->where('type', $type)->where('b_id', $id)->delete();
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/collect',
                    'Code' => '0',
                    'Data' => '',
                    'Msg' => '取消收藏成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/collect',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '请勿重复操作',
                    'Time' => time());
                return json($info);
            }
        }
    }

    //图书列表
    public function book_list(Request $request)
    {
        $profession_id = $request->param('profession_id');
        $page = $request->param('page');

        $where = function ($query) use ($request) {
            //按分类搜索
            if ($request->param('profession_id') and $request->param('profession_id') != '-1') {
                $query->where('profession_id', $request->param('profession_id'));
            }
        };

        if (isset($profession_id)) {
            $book = Book::where($where)->page($page, 5)->field('id,title,author,price,thumb,real_price')->select();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/book_list',
                'Code' => '0',
                'Data' => $book,
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/book_list',
                'Code' => '0',
                'Data' => '',
                'Msg' => '获取列表失败,缺少参数profession_id',
                'Time' => time());
            return json($info);
        }
    }

    //图书详情
    public function show_book(Request $request)
    {
        $id = $request->param('id');
        $uid = $request->param('u_id');
        $book = Book::field('id,title,author,price,img,real_price,des,detail')->find($id);
        if (isset($uid)) {
            $collect = CustomerCollect::where('u_id', $uid)->where('type', 4)->where('b_id', $id)->count();
            $book['is_collect'] = $collect;
//            return $collect;
//            if ($collect == 0){
//                $collect = "";
//            }
            if (isset($book)) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_book',
                    'Code' => '0',
                    'Data' => $book,
                    'Msg' => '获取详情成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_book',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取详情失败',
                    'Time' => time());
                return json($info);
            }
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_book',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取用户信息失败,缺少参数u_id',
                'Time' => time());
            return json($info);
        }
    }

    //考务动态列表
    public function message_list(Request $request)
    {
        $page = $request->param('page');
        $profession_id = $request->param('profession_id');
        $message = Message::page($page, 5)->field('id,title,des,created_at')->select();
        if (isset($message)) {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/message_list',
                'Code' => '0',
                'Data' => $message,
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/message_list',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取列表失败',
                'Time' => time());
            return json($info);
        }
    }

    //考务动态详情
    public function show_message(Request $request)
    {
        $id = $request->param('id');
        $message = Message::field('id,title,school,des,detail,created_at')->find($id);
        if (isset($message)) {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_message',
                'Code' => '0',
                'Data' => $message,
                'Msg' => '获取详情成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_message',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取详情失败',
                'Time' => time());
            return json($info);
        }
    }

    //课程列表
    public function course_list(Request $request)
    {
        //接受参数
        $page = $request->param('page');
        $profession_id = $request->param('profession_id');

        $where = function ($query) use ($request) {
            //按分类搜索
            if ($request->param('profession_id') and $request->param('profession_id') != '-1') {
                $query->where('profession_id', $request->param('profession_id'));
            }
        };

        if ($profession_id) {
            //查出课程列表
            $course = Course::where('p_id', 0)->where($where)->page($page, 5)->field('id,title,teacher_id,class,thumb,price,real_price')->select();
            //讲老师姓名插入$course里
            foreach ($course as $key => $value) {
                $id = $value['teacher_id'];
//            $teacher = Teacher::where('id',$id)->field('name')->find();
                $teacher = Teacher::where('id', $id)->value('name');
                $course[$key]['teacher'] = $teacher;
            }
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/course_list',
                'Code' => '0',
                'Data' => $course,
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/course_list',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取列表失败,缺少参数profession_id',
                'Time' => time());
            return json($info);
        }
    }

    //课程详情
    public function show_course(Request $request)
    {
        $id = $request->param('id');
        $uid = $request->param('u_id');
        $course = Course::field('id,title,teacher_id,price,img,des,detail,real_price')->find($id);
        //查出老师的名字插入$course
        $teacher = Teacher::where('id', $course['teacher_id'])->value('name');
        $course['teacher'] = $teacher;
        if (isset($uid)) {
            $collect = CustomerCollect::where('u_id', $uid)->where('type', 1)->where('b_id', $id)->count();
            $course['is_collect'] = $collect;
//            return $collect;
//            if ($collect == 0){
//                $collect = "";
//            }
            if (isset($course)) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_course',
                    'Code' => '0',
                    'Data' => $course,
                    'Msg' => '获取详情成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_course',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取详情失败',
                    'Time' => time());
                return json($info);
            }
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_course',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取用户信息失败,缺少参数u_id',
                'Time' => time());
            return json($info);
        }
    }

    //课程目录
    public function course_catalog(Request $request)
    {
        //获取课程id
        $course_id = $request->param('id');
        if (isset($course_id)) {
            //通过课程id查询目录
            $course = CourseCatalog::where('course_id', $course_id)->field('id,p_id,title,course_id,level,class_id')->select();

            //循环出2级目录
            foreach ($course as $key => $value) {
                //查出是否有url
                $classes_ids = $value['class_id'];
                $catelog_url = CourseClass::where('id', $classes_ids)->find();

                $course[$key]['freeurl'] = $catelog_url['freeurl'];
                $course[$key]['url'] = $catelog_url['url'];
                if ($course[$key]['freeurl'] == null) {
                    $course[$key]['freeurl'] = "";
                }
                if ($course[$key]['url'] == null) {
                    $course[$key]['url'] = "";
                }
                $course[$key]['isExpand'] = false;

                //查出二级目录
                $ids = $value['id'];
                $catalog = CourseCatalog::where('p_id', $ids)->field('id,p_id,title,course_id,level,class_id')->select();
                $course[$key]['sub'] = $catalog;

                //循环出3级目录
                foreach ($catalog as $k => $v) {
                    //查出是否有url
                    $class_ids = $v['class_id'];
                    $cate_url = CourseClass::where('id', $class_ids)->find();
                    $catalog[$k]['freeurl'] = $cate_url['freeurl'];
                    $catalog[$k]['url'] = $cate_url['url'];
                    if ($catalog[$k]['freeurl'] == null) {
                        $catalog[$k]['freeurl'] = "";
                    }
                    if ($catalog[$k]['url'] == null) {
                        $catalog[$k]['url'] = "";
                    }
                    $catalog[$k]['isExpand'] = false;

                    //查出三级目录
                    $id = $v['id'];
                    $cate = CourseCatalog::where('p_id', $id)->field('id,p_id,title,course_id,level,class_id')->select();
                    $catalog[$k]['sub'] = $cate;

                    //循环出三级目录下面的url
                    foreach ($cate as $k1 => $v1) {
                        $class_id = $v1['class_id'];
                        $c_url = CourseClass::where('id', $class_id)->find();
                        $cate[$k1]['freeurl'] = $c_url['freeurl'];
                        $cate[$k1]['url'] = $c_url['url'];
                        if ($cate[$k1]['freeurl'] == null) {
                            $cate[$k1]['freeurl'] = "";
                        }
                        if ($cate[$k1]['url'] == null) {
                            $cate[$k1]['url'] = "";
                        }
                        $cate[$k1]['isExpand'] = false;
                    }
                }
            }
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/course_catalog',
                'Code' => '0',
                'Data' => $course,
                'Msg' => '获取目录成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/course_catalog',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取目录失败,缺少参数id',
                'Time' => time());
            return json($info);
        }
    }

    //直播列表
    public function live_list(Request $request)
    {
        //接受参数
        $profession_id = $request->param('profession_id');
        $u_id = $request->param('u_id');

        $where = function ($query) use ($request) {
            //按分类搜索
            if ($request->param('profession_id') and $request->param('profession_id') != '-1') {
                $query->where('profession_id', $request->param('profession_id'));
            }
        };

        if ($profession_id) {
            //查出直播列表
            $live = Live::where($where)->field('id,title,class,thumb,price,real_price')->select();
            //讲老师姓名插入$course里
//            foreach ($live as $key => $value) {
//                $id = $value['teacher_id'];
////            $teacher = Teacher::where('id',$id)->field('name')->find();
//                $teacher = Teacher::where('id', $id)->value('name');
//                $live[$key]['teacher'] = $teacher;
//            }
            $pay_order = Order::where('u_id', $u_id)->where('type', 2)->where('status', 1)->find();
            $pay_id = $pay_order['live_id'];
            $pay_live = Live::where('id', $pay_id)->field('id,title,class,thumb,price,real_price,teacher_id')->select();
            //讲老师姓名插入$course里
            foreach ($pay_live as $key => $value) {
                $id = $value['teacher_id'];
//            $teacher = Teacher::where('id',$id)->field('name')->find();
                $teacher = Teacher::where('id', $id)->value('name');
                $pay_live[$key]['teacher'] = $teacher;
            }
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/live_list',
                'Code' => '0',
                'Data' => array('live' => $live, 'pay_live' => $pay_live),
                'Msg' => '获取列表成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/live_list',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取列表失败,缺少参数profession_id',
                'Time' => time());
            return json($info);
        }
    }

    //直播详情
    public function show_live(Request $request)
    {
        $id = $request->param('id');
        $uid = $request->param('u_id');
        if (isset($id)) {
            $live = Live::where('id', $id)->field('id,title,des,detail,price,real_price,img')->find();
            $collect = CustomerCollect::where('u_id', $uid)->where('type', 2)->where('b_id', $id)->count();
            $live['is_collect'] = $collect;
            if (isset($live)) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_live',
                    'Code' => '0',
                    'Data' => $live,
                    'Msg' => '获取详情成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_live',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取详情失败,未找到对应数据',
                    'Time' => time());
                return json($info);
            }
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/show_live',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取详情失败,缺少参数id',
                'Time' => time());
            return json($info);
        }
    }

    //直播预告/回放
    public function play_live(Request $request)
    {
        $id = $request->param('id');
        $phone = $request->param('phone');
        $uid = $request->param('u_id');
        if (isset($id)) {
            $lives = LiveUrl::where('live_id', $id)->field('id,title,url,status,livetime,overtime')->select();
//            return json($lives);
            if (isset($lives)) {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/play_live',
                    'Code' => '0',
                    'Data' => $lives,
                    'Msg' => '获取成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/play_live',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取详情失败,未找到对应数据',
                    'Time' => time());
                return json($info);
            }
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/play_live',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取详情失败,缺少参数id',
                'Time' => time());
            return json($info);
        }
    }

    //直播地址
    public function liveroom_url()
    {
        $number = rand(100, 99999);
        $id = time();
        $uid = "$id$number";
        $nickname = '朱';
        $token = md5(123456);
        $url = "http://whxfx.gensee.com/training/site/s/48009482?nickname=$nickname&token=$token&sec=md5";
//        return $url;
        return json($url);
//        return $this->redirect($url);
    }

    //获取直播间状态
    public function live_callback(Request $request)
    {
        $classid = $_GET['ClassNo'];
        $operator = $_GET['Operator'];
        $action = $_GET['Action'];
        $affected = $_GET['Affected'];
        $totalusernum = $_GET['totalusernum'];
        if ($action == 105) {
            LiveUrl::where('sdkid', $classid)->setField('status', 2);
            Ceshi::create(['ClassNo' => $classid, 'Action' => $action, 'Operator' => $operator, 'Affected' => $affected, 'totalusernum' => $totalusernum]);
            $this->lookback_url();
        } else {
            LiveUrl::where('sdkid', $classid)->setField('status', 1);
            Ceshi::create(['ClassNo' => $classid, 'Action' => $action, 'Operator' => $operator, 'Affected' => $affected, 'totalusernum' => $totalusernum]);
        }
    }

    //获取课件地址,添加课件地址
    public function lookback_url()
    {
        //模拟post请求获json数据
        $url = "http://whxfx.gensee.com/integration/site/training/courseware/list";
        $postData = array(
            'roomId' => 'tz6izG3yLm',
            'loginName' => $this->zshdusername,
            'password' => $this->zshdpassword,
            'sec' => '',
        );
        $postData = http_build_query($postData); //做一层过滤
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($ch);
        curl_close($ch);
        //讲json数据转换成数组
        if ($result) {
            $arr = json_decode($result, true);
            //获取课件回放地址
            $lat = $arr['coursewares'][0]['url'];
            //更改回放地址
            $url = LiveUrl::where('sdkid', 'tz6izG3yLm')->setField('lookbackurl', $lat);
//            if ($url !== false){
//                return '更新成功';
//            }else{
//                return '更新失败';
//            }
//            return $this->lookback_url();
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/lookback_url',
                'Code' => '0',
                'Data' => $lat,
                'Msg' => '获取地址成功',
                'Time' => time());
            return json($info);
        } else {
            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/Index/lookback_url',
                'Code' => '1',
                'Data' => '',
                'Msg' => '获取数据失败',
                'Time' => time());
            return json($info);
        }
    }

    public function live_over(Request $request)
    {
        return 123;
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
