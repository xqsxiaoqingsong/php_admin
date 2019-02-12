<?php

namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\CourseOrder;
use app\admin\model\CourseStage;
use app\admin\model\Live;
use app\admin\model\LiveOrder;
use app\admin\model\LiveStage;
use app\admin\model\Member;
use app\admin\model\Profession;
use think\Controller;
use think\Request;

class Members extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_eduadmin' => 'am-in',   //展开
            '_member' => 'am-active',   //高亮
            'alllives' => Live::all(),
            'alllivestages' => Live::with('livestage')->all(),
            'allcourses' => Course::all(),
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('REALNAME', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '') {
                $search = "%" . $request->param('category_id') . "%";
                $query->where('MAJORNAME', 'like', $search);
            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('PHONE', 'like', $search);
            }
        };

        $users = Member::where($where)->order('id desc')->paginate(10, false, ['query' => request()->param()]);
        $count = $users->total();
        //返回搜索条件,然后在前端进行 empty +if 判断显示出来
        $condition = $request->param();
//        return json($users);
        return view('member/index', compact('users', 'count', 'condition'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('member/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('MemberValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };

        if ($request->isPost()) {
//            return json($request->param());
            $parameters = array(
                'realName' => $request->param('realName'), //真实姓名
                'memberName' => $request->param('memberName'), //昵称
                'phone' => $request->param('phone'), //手机
                'password' => $request->param('password'), //密码
                'majorName' => $request->param('majorName'), //专业方向
            );

//            $baseUrl = "http://xfxerj.gensee.com/integration/site";

            $url = "https://www.xfxerj.com/wrdp-web/face/memberPC/registerphp";
            $return = $this->curlByPost($parameters, $url);
            $result = json_decode($return, true);
//            return json($result);
            if ($result['Code'] === '0') {
                $this->success('恭喜您，新增成功', 'index', '', '1');
            } else {
                $this->error('更新失败');
            }
        }

//        $result = Member::create([
//            'REALNAME' => $request->param('REALNAME'),
//            'MEMBERNAME' => $request->param('MEMBERNAME'),
//            'PHONE' => $request->param('PHONE'),
//            'PASSWORD' => $request->param('PASSWORD'),
//            'MAJORNAME' => $request->param('MAJOR_NAME'),
//            'HEADURL' => $request->param('image')
//        ]);
//        if ($result) {
//            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//            $this->success('恭喜您，新增成功', 'index', '', 1);
//        } else {
//            //错误页面的默认跳转页面是返回前一页，通常不需要设置
//            $this->error('新增失败', '', '', 1);
//        }
    }

    public function curlByPost($parameters, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
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
        $user = Member::find($id);
        return view('member/edit', compact('user'));
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
        $user = Member::find($id);
        $validate = Validate('MemberValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $user->where('ID', $id)->update([
            'REALNAME' => $request->param('REALNAME'),
            'MEMBERNAME' => $request->param('MEMBERNAME'),
            'PHONE' => $request->param('PHONE'),
            'PASSWORD' => $request->param('PASSWORD'),
            'MAJORNAME' => $request->param('MAJOR_NAME'),
            'HEADURL' => $request->param('image')
        ]);
//        return redirect('index');
//        $this->success('恭喜您，更新成功了', 'index');
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，更新成功', 'index', '', 1);
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('更新失败', '', '', 1);
        }
    }

    //编辑用户权限
    public function editauth(Request $request, $id)
    {
        $user = Member::find($id);
        $livestage = LiveOrder::where('MEMBERID', $id)->column('STAGEID');
        $livestageid = implode(',', $livestage);
        $livestages = LiveStage::with('stagelive')->whereIn("id", $livestageid)->select();

        $coursestage = CourseOrder::where('MEMBERID', $id)->column('STAGEID');
        $coursestageid = implode(',', $coursestage);
        $coursestages = CourseStage::with('stagecourse')->whereIn("id", $coursestageid)->select();
//        return json($livestages);
        return view('member/editauth', compact('user', 'coursestages', 'livestages'));
    }

    //新增用户权限
    public function createauth(Request $request, $id)
    {
        $user = Member::find($id);

        return view('member/createauth', compact('user', 'mid'));
    }

    //获取四级联动select框
    public function createauthselect(Request $request)
    {
        if ($request->isAjax()) {
            if ($request->param('typeid') == 1) {
                $majorid = Course::column('majorId');
                //去重并且重新修复键
                $majorida = array_flip($majorid);
                $majorids = array_keys($majorida);

                $profession = Profession::whereIn('ID', $majorids)->select();
                $info = array('profession' => $profession);

                if ($request->param('professionid')) {
                    $courses = Course::where('majorId', $request->param('professionid'))->select();
                    $info = array('profession' => $profession, 'courses' => $courses);
                    if ($request->param('classid')) {
                        $coursestages = CourseStage::where('courseId', $request->param('classid'))->select();
                        $userstages = CourseOrder::where('MEMBERID', $request->param('userid'))->where('COURSEMANAGEID', $request->param('classid'))->value('STAGEID');
                        $userstage = explode(',', $userstages);
                        $info = array('profession' => $profession, 'courses' => $courses, 'coursestages' => $coursestages, 'userstage' => $userstage);
                        return json($info);
                    }
//                    return json($info);
                }
//            return json($request->param());
                return json($info);

            }
            if ($request->param('typeid') == 2) {
                $majorid = Live::column('majorId');
                //去重并且重新修复键
                $majorida = array_flip($majorid);
                $majorids = array_keys($majorida);

                $profession = Profession::whereIn('ID', $majorids)->select();
                $info = array('profession' => $profession);
                if ($request->param('professionid')) {
//                    return json($request->param());
                    $lives = Live::where('majorId', $request->param('professionid'))->select();
                    $info = array('profession' => $profession, 'lives' => $lives);
                    if ($request->param('classid')) {
                        $livestages = LiveStage::where('liveRoomId', $request->param('classid'))->select();
                        $userstages = LiveOrder::where('MEMBERID', $request->param('userid'))->where('LIVEROOMID', $request->param('classid'))->value('STAGEID');
                        $userstage = explode(',', $userstages);
                        $info = array('profession' => $profession, 'lives' => $lives, 'livestages' => $livestages, 'userstage' => $userstage);
                        return json($info);
                    }
//                    return json($info);
                }
                return json($info);
                return json($majorids);
            }
        }
    }

    //保存权限
    public function savestage(Request $request, $id)
    {
        $classid = $request->param('classid');
//        return json($request->param());

        if (!$request->param('checked_id')){
            $this->error('请勾选选择项', "/admin/members/createauth/id/$id", '', 1);
        }
        if ($request->param('coursetype') == 1) {
            $ids = implode(',', $request->param('checked_id'));
            $userstage = CourseOrder::where('MEMBERID', $id)->where('COURSEMANAGEID', $classid)->find();
            if ($userstage) {
                $result = $userstage->save([
                    'STAGEID' => $ids,
                    'ORDERCODE' => $request->param('ORDERCODE'),
                ]);
                if ($result) {
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('恭喜您，更新成功', "/admin/members/editauth/id/$id", '', 1);
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('更新失败', "/admin/members/createauth/id/$id", '', 1);
                }
            } else {
                $result = CourseOrder::create([
                    'MEMBERID' => $id,
                    'COURSEMANAGEID' => $classid,
                    'ORDERCODE' => $request->param('ORDERCODE'),
                    'STAGEID' => $ids,
                ]);
                if ($result) {
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('恭喜您，更新成功', "/admin/members/editauth/id/$id", '', 1);
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('更新失败', "/admin/members/createauth/id/$id", '', 1);
                }
            }
        }
        if ($request->param('coursetype') == 2) {
            $ids = implode(',', $request->param('checked_id'));
            $userstage = LiveOrder::where('MEMBERID', $id)->where('LIVEROOMID', $classid)->find();
            if ($userstage) {
                $result = $userstage->save([
                    'STAGEID' => $ids,
                    'ORDERCODE' => $request->param('ORDERCODE'),
                ]);
                if ($result) {
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('恭喜您，更新成功', "/admin/members/editauth/id/$id", '', 1);
//                    /admin/members/editauth/id/$id
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('更新失败', "/admin/members/createauth/id/$id", '', 1);
                }
            } else {
                $result = LiveOrder::create([
                    'MEMBERID' => $id,
                    'LIVEROOMID' => $classid,
                    'ORDERCODE' => $request->param('ORDERCODE'),
                    'STAGEID' => $ids,
                ]);
                if ($result) {
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('恭喜您，更新成功', "/admin/members/editauth/id/$id", '', 1);
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('更新失败', "/admin/members/createauth/id/$id", '', 1);
                }
            }
        }
    }


    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete(Request $request, $id)
    {
//        return json($request->param());
        $id = array($request->param('id'));
        if ($request->param('typeid') == 1) {
            //查出字符串的阶段id
            $stageid = CourseOrder::where('MEMBERID', $request->param('userid'))->where("COURSEMANAGEID", $request->param('classid'))->value('STAGEID');
            //将字符串分割成数组的形式
            $sidarr = explode(",", $stageid);
            //按值删除数组中的值,注意后面一个也需要转成数组
            $saveid = implode(',', array_diff($sidarr, $id));
            $result = CourseOrder::where('MEMBERID', $request->param('userid'))->where("COURSEMANAGEID", $request->param('classid'))->update(['STAGEID' => $saveid]);
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '删除成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '删除失败');
            }
            return json($info);
        }
        if ($request->param('typeid') == 2) {
            //查出字符串的阶段id
            $stageid = LiveOrder::where('MEMBERID', $request->param('userid'))->where("LIVEROOMID", $request->param('classid'))->value('STAGEID');
            //将字符串分割成数组的形式
            $sidarr = explode(",", $stageid);
            //按值删除数组中的值,注意后面一个也需要转成数组
            $saveid = implode(',', array_diff($sidarr, $id));
            $result = LiveOrder::where('MEMBERID', $request->param('userid'))->where("LIVEROOMID", $request->param('classid'))->update(['STAGEID' => $saveid]);
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '删除成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '删除失败');
            }
            return json($info);
        }
    }
}
