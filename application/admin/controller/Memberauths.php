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
use think\Db;
use think\Loader;
use think\Model;
use think\Request;

class Memberauths extends Common
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
    public function index(Request $request)
    {
//        return json($request->param());
        //网课搜索条件,因为关联表的不同,需要设置两个搜索条件
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('d.REALNAME', 'like', $search);
            }
            //按分类搜索
//            if ($request->param('category_id') and $request->param('category_id') != '') {
////                $search = "%" . $request->param('category_id') . "%";
//                $query->where('m.MAJOR_NAME', $request->param('category_id'));
//            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('d.PHONE', 'like', $search);
            }
            //按课程名称
            if ($request->param('coursename') and $request->param('coursename') != '') {
                $search = "%" . $request->param('coursename') . "%";
                $query->where('f.courseName', 'like', $search);
            }
            //按备注
            if ($request->param('courseordercode') and $request->param('courseordercode') != '') {
                $search = "%" . $request->param('courseordercode') . "%";
                $query->where('c.ORDERCODE', 'like', $search);
            }
            //按创建时间
            if ($request->param('coursestartdate') and $request->param('courseenddate')) {
                $query->whereBetweenTime('c.CREATTIME', $request->param('coursestartdate'), $request->param('courseenddate'));
            }
            if ($request->param('coursestartdate') and $request->param('courseenddate') == '') {
                $query->whereTime('c.CREATTIME', '>=', $request->param('coursestartdate'));
            }
            if ($request->param('coursestartdate') == '' and $request->param('courseenddate')) {
                $query->whereTime('c.CREATTIME', '<', $request->param('courseenddate'));
            }
        };

        //直播搜索条件
        $map = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('d.REALNAME', 'like', $search);
            }
            //按分类搜索
//            if ($request->param('category_id') and $request->param('category_id') != '') {
////                $search = "%" . $request->param('category_id') . "%";
//                $query->where('m.MAJOR_NAME', $request->param('category_id'));
//            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('d.PHONE', 'like', $search);
            }
            //按课程名称
            if ($request->param('livename') and $request->param('livename') != '') {
                $search = "%" . $request->param('livename') . "%";
                $query->where('b.LiveRoomName', 'like', $search);
            }
            //按备注
            if ($request->param('liveordercode') and $request->param('liveordercode') != '') {
                $search = "%" . $request->param('liveordercode') . "%";
                $query->where('a.ORDERCODE', 'like', $search);
            }
            //按创建时间
            if ($request->param('livestartdate') and $request->param('liveenddate')) {
                $query->whereBetweenTime('a.CREATTIME', $request->param('livestartdate'), $request->param('liveenddate'));
            }
            if ($request->param('livestartdate') and $request->param('liveenddate') == '') {
                $query->whereTime('a.CREATTIME', '>=', $request->param('livestartdate'));
            }
            if ($request->param('livestartdate') == '' and $request->param('liveenddate')) {
                $query->whereTime('a.CREATTIME', '<', $request->param('liveenddate'));
            }
        };

//        $coursestages = CourseOrder::with('usercourse')->where($where)->paginate(10, false, ['query' => request()->param()]);
//        $coursestages = Model('CourseOrder')->selectCourseOrder();
        $allcoursestages = DB::table('t_courseorder c')
            //设置需要查询的字段,不然主键字段相同
            ->field('c.*,d.REALNAME,d.PHONE,f.*,m.MAJOR_NAME')
            ->join("t_member d", "c.MEMBERID=d.ID")
            ->join("c_course f", "c.COURSEMANAGEID=f.id")
            ->join("t_cnmedicinemajor m", "f.majorId=m.ID")
            ->where($where)
            ->paginate(10, false, ['query' => request()->param()]);
        //分页出来的数据不是纯数组的格式,在循环的时候需要用数据对象的形式进行处理
        $coursestages = $allcoursestages->items();
//        echo CourseOrder::getLastSql();die;
        foreach ($coursestages as $key => $value) {
            $coursestageids = explode(',', $value['STAGEID']);
            $coursestage = CourseStage::whereIn('id', $coursestageids)->select();
            $coursestages[$key]['STAGEID'] = $coursestage;
        }
//        return json($coursestages);

//        $livestages = LiveOrder::with('userlive')->where($where)->paginate(10, false, ['query' => request()->param()]);
//        $livestages = Model('LiveOrder')->selectLiveOrder();
        $alllivestages = DB::table('t_order a')
            //设置需要查询的字段,不然主键字段相同
            ->field('a.*,d.REALNAME,d.PHONE,b.*,m.MAJOR_NAME')
            ->join("t_member d", "a.MEMBERID=d.ID", "LEFT")
            ->join("l_liveroom b", "a.LIVEROOMID=b.id", "LEFT")
            ->join("t_cnmedicinemajor m", "b.majorId=m.ID", "LEFT")
            ->where($map)
            ->paginate(10, false, ['query' => request()->param()]);
        //分页出来的数据不是纯数组的格式,在循环的时候需要用数据对象的形式进行处理
        $livestages = $alllivestages->items();
        foreach ($livestages as $key => $liveval) {
            $stageids = explode(',', $liveval['STAGEID']);
            $stage = LiveStage::whereIn('id', $stageids)->select();
            $livestages[$key]['STAGEID'] = $stage;
        }
//        return json($livestages);
//        $allstages = array_merge($coursestages,$livestages);
//        $usercourse = CourseOrder::column('MEMBERID');
//        $userlive = LiveOrder::column('MEMBERID');
        $condition = $request->param();
        return view('memberauth/index', compact('coursestages', 'livestages', 'condition', 'allcoursestages', 'alllivestages'));
    }

    public function indexlive(Request $request)
    {
//        return json($request->param());
        //网课搜索条件,因为关联表的不同,需要设置两个搜索条件
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('d.REALNAME', 'like', $search);
            }
            //按分类搜索
//            if ($request->param('category_id') and $request->param('category_id') != '') {
////                $search = "%" . $request->param('category_id') . "%";
//                $query->where('m.MAJOR_NAME', $request->param('category_id'));
//            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('d.PHONE', 'like', $search);
            }
            //按课程名称
            if ($request->param('coursename') and $request->param('coursename') != '') {
                $search = "%" . $request->param('coursename') . "%";
                $query->where('f.courseName', 'like', $search);
            }
            //按备注
            if ($request->param('courseordercode') and $request->param('courseordercode') != '') {
                $search = "%" . $request->param('courseordercode') . "%";
                $query->where('c.ORDERCODE', 'like', $search);
            }
            //按创建时间
            if ($request->param('coursestartdate') and $request->param('courseenddate')) {
                $query->whereBetweenTime('c.CREATTIME', $request->param('coursestartdate'), $request->param('courseenddate'));
            }
            if ($request->param('coursestartdate') and $request->param('courseenddate') == '') {
                $query->whereTime('c.CREATTIME', '>=', $request->param('coursestartdate'));
            }
            if ($request->param('coursestartdate') == '' and $request->param('courseenddate')) {
                $query->whereTime('c.CREATTIME', '<', $request->param('courseenddate'));
            }
        };

        //直播搜索条件
        $map = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('d.REALNAME', 'like', $search);
            }
            //按分类搜索
//            if ($request->param('category_id') and $request->param('category_id') != '') {
////                $search = "%" . $request->param('category_id') . "%";
//                $query->where('m.MAJOR_NAME', $request->param('category_id'));
//            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('d.PHONE', 'like', $search);
            }
            //按课程名称
            if ($request->param('livename') and $request->param('livename') != '') {
                $search = "%" . $request->param('livename') . "%";
                $query->where('b.LiveRoomName', 'like', $search);
            }
            //按备注
            if ($request->param('liveordercode') and $request->param('liveordercode') != '') {
                $search = "%" . $request->param('liveordercode') . "%";
                $query->where('a.ORDERCODE', 'like', $search);
            }
            //按创建时间
            if ($request->param('livestartdate') and $request->param('liveenddate')) {
                $query->whereBetweenTime('a.CREATTIME', $request->param('livestartdate'), $request->param('liveenddate'));
            }
            if ($request->param('livestartdate') and $request->param('liveenddate') == '') {
                $query->whereTime('a.CREATTIME', '>=', $request->param('livestartdate'));
            }
            if ($request->param('livestartdate') == '' and $request->param('liveenddate')) {
                $query->whereTime('a.CREATTIME', '<', $request->param('liveenddate'));
            }
        };

//        $coursestages = CourseOrder::with('usercourse')->where($where)->paginate(10, false, ['query' => request()->param()]);
//        $coursestages = Model('CourseOrder')->selectCourseOrder();
        $allcoursestages = DB::table('t_courseorder c')
            //设置需要查询的字段,不然主键字段相同
            ->field('c.*,d.REALNAME,d.PHONE,f.*,m.MAJOR_NAME')
            ->join("t_member d", "c.MEMBERID=d.ID")
            ->join("c_course f", "c.COURSEMANAGEID=f.id")
            ->join("t_cnmedicinemajor m", "f.majorId=m.ID")
            ->where($where)
            ->paginate(10, false, ['query' => request()->param()]);
        //分页出来的数据不是纯数组的格式,在循环的时候需要用数据对象的形式进行处理
        $coursestages = $allcoursestages->items();
//        echo CourseOrder::getLastSql();die;
        foreach ($coursestages as $key => $value) {
            $coursestageids = explode(',', $value['STAGEID']);
            $coursestage = CourseStage::whereIn('id', $coursestageids)->select();
            $coursestages[$key]['STAGEID'] = $coursestage;
        }
//        return json($coursestages);

//        $livestages = LiveOrder::with('userlive')->where($where)->paginate(10, false, ['query' => request()->param()]);
//        $livestages = Model('LiveOrder')->selectLiveOrder();
        $alllivestages = DB::table('t_order a')
            //设置需要查询的字段,不然主键字段相同
            ->field('a.*,d.REALNAME,d.PHONE,b.*,m.MAJOR_NAME')
            ->join("t_member d", "a.MEMBERID=d.ID", "LEFT")
            ->join("l_liveroom b", "a.LIVEROOMID=b.id", "LEFT")
            ->join("t_cnmedicinemajor m", "b.majorId=m.ID", "LEFT")
            ->where($map)
            ->paginate(10, false, ['query' => request()->param()]);
        //分页出来的数据不是纯数组的格式,在循环的时候需要用数据对象的形式进行处理
        $livestages = $alllivestages->items();
        foreach ($livestages as $key => $liveval) {
            $stageids = explode(',', $liveval['STAGEID']);
            $stage = LiveStage::whereIn('id', $stageids)->select();
            $livestages[$key]['STAGEID'] = $stage;
        }
//        return json($livestages);
//        $allstages = array_merge($coursestages,$livestages);
//        $usercourse = CourseOrder::column('MEMBERID');
//        $userlive = LiveOrder::column('MEMBERID');
        $condition = $request->param();
        return view('memberauth/indexlive', compact('coursestages', 'livestages', 'condition', 'allcoursestages', 'alllivestages'));
    }

    //删除课程权限
    public function delcoursestage(Request $request)
    {
//        return json($request->param());
        $results = $request->param('result');
        foreach ($results as $key => $value) {
            $stageid[] = $value['stageid'];
            $userid = $value['userid'];
            $id = $value['id'];
            $classid = $value['classid'];
            $result = CourseOrder::where(['MEMBERID' => $userid, 'ID' => $id, 'COURSEMANAGEID' => $classid])->find();
            $sidarr = explode(",", $result['STAGEID']);
            $saveid = implode(',', array_diff($sidarr, $stageid));
            $newstage = CourseOrder::where(['MEMBERID' => $userid, 'ID' => $id, 'COURSEMANAGEID' => $classid])->setField('STAGEID', $saveid);
        }
        if ($newstage) {
            $info = array('status' => 1, 'msg' => '删除成功');
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }

    //删除直播权限
    public function dellivestage(Request $request)
    {
//        return json($request->param());
        $results = $request->param('result');
        foreach ($results as $key => $value) {
            $stageid[] = $value['stageid'];
            $userid = $value['userid'];
            $id = $value['id'];
            $classid = $value['classid'];
            $result = LiveOrder::where(['MEMBERID' => $userid, 'ID' => $id, 'LIVEROOMID' => $classid])->find();
            $sidarr = explode(",", $result['STAGEID']);
            $saveid = implode(',', array_diff($sidarr, $stageid));
            $newstage = LiveOrder::where(['MEMBERID' => $userid, 'ID' => $id, 'LIVEROOMID' => $classid])->setField('STAGEID', $saveid);
        }
        if ($newstage) {
            $info = array('status' => 1, 'msg' => '删除成功');
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
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
