<?php

namespace app\admin\controller;

use app\admin\model\LiveclassBelongLiveStage;
use app\admin\model\LiveStage;
use app\admin\model\LiveUrl;
use app\admin\model\Live;
use think\Controller;
use think\Request;
use think\Db;

class Liveurls extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        $this->login_to_gensee = array('loginName' => 'admin@xfxerj.com', 'password' => '888888', 'sec' => 'false');
        return $this->assign([
            '_xfx_live' => 'am-in',   //展开
            '_liveurl' => 'am-active',   //高亮
            'alllives' => Live::all(),
            'alllivestages' => Live::with('livestage')->all(),
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
                $query->where('a.liveClassName', 'like', $search);
            }

            if ($request->param('speaker') and $request->param('speaker') != '') {
                $search = "%" . $request->param('speaker') . "%";
                $query->where('a.speaker', 'like', $search);
            }

            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('d.majorId', $request->param('category_id'));
            }
        };

        $lives = DB::table('l_liveclass a')
            ->field('a.*,GROUP_CONCAT(d.liveRoomName) as liveRoomName')
            ->join("l_liveclass_bind_livestage b", "a.id = b.liveclassId", "LEFT")
            ->join("l_livestage c", "b.livestageId = c.id", "LEFT")
            ->join("l_liveroom d", "c.liveRoomId = d.id", "LEFT")
            ->where($where)
            ->group("a.id")
            ->paginate(10, false, ['query' => request()->param()]);

        $count = $lives->total();
        $condition = $request->param();
        $this->assign('condition', $condition);
        return view('liveurl/index', compact('lives', 'count'));
        return json($lives);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('liveurl/create');
    }

    //新建直播间接口
    public function saveliveclass(Request $request)
    {
        if ($request->isPost()) {
//            return json($request->param());
            $liveroomid = $request->param('liveRoomId');
            $stageid = $request->param('stageId');
            $data['room_start_datetime'] = $request->param('startDate');
            $data['room_end_datetime'] = $request->param('endDate');
            $data['teacher_token_for_room'] = rand(100000, 999999);
            $data['assistant_token_for_room'] = rand(100000, 999999);
            $data['student_web_token_for_room'] = rand(100000, 999999);
            $data['student_client_token_for_room'] = rand(100000, 999999);
            $name = $request->param('subject');
            $parameters = array(
                'subject' => $name, //实时课堂主题（长度：1-250）
                'startDate' => $data['room_start_datetime'], //开始日期
                'invalidDate' => $data['room_end_datetime'], //失效时间
                'duration' => '', //课堂持续时长（单位为分钟）
                'uiMode' => '2', //Web端学生界面设置(1是三分屏，2是文档/视频为主，3是两分屏，4：互动增加)
                'uiColor' => 'default', //三分屏颜色选择（blue, default, green），默认是default
                'uiVideo' => '1', //uiMode等于2时候，设置是否视频为主
                'uiWindow' => '1', //uiMode等于2时候，设置是否显示小窗口。
                'upgrade' => true, //是否允许web升级到客户端
                'teacherToken' => $data['teacher_token_for_room'], //老师加入口令（长度：6-15）（会自动生成随机数）
                'assistantToken' => $data['assistant_token_for_room'], //助教加入口令（长度：6-15）（会自动生成随机数）
                'studentToken' => $data['student_web_token_for_room'], //Web端学生加入口令（长度：最大15）
                'studentClientToken' => $data['student_client_token_for_room'], //客户端学生加入口令
                'webJoin' => 'true', //是否支持Web端学生加入,值为true或者false。默认值为true, 当scene的值为2时该属性值为false,该值得设置无效
                'clientJoin' => 'true', //是否支持客户端端学生加入,值为true或者false。默认值为true，当scene的值为2时该属性值为true，该值得设置无效
                'scheduleInfo' => $name, //课程介绍
                'speakerInfo' => '', //老师介绍
                'scene' => '0', //0:大讲堂，1：小班课，2：私教课，默认值：0。当值scene为2，clientJoin,必须为true,同时webJoin为false
                'description' => $name, //课堂介绍
                'realTime' => false
            );

            $baseUrl = "http://xfxerj.gensee.com/integration/site";

            $url = $baseUrl . "/training/room/created";

            $parameters = array_merge($parameters, $this->login_to_gensee);

            $return = $this->curlByPost($parameters, $url);
            $result = json_decode($return, true);
            if ($result['code'] === '0') {
                $datas['roomId'] = $result['id'];
                $datas['roomNumber'] = $result['number'];
                $datas['startDate'] = $request->param('startDate');
                $datas['endDate'] = $request->param('endDate');
                $datas['teacherToken'] = $result['teacherToken'];
                $datas['tutorToken'] = $result['assistantToken'];
                $datas['studentToken'] = $result['studentToken'];
                $datas['studentClientToken'] = $result['studentClientToken'];
                $datas['teacherJoinUrl'] = $result['teacherJoinUrl'];
                $datas['studentJoinUrl'] = $result['studentJoinUrl'];
                $datas['subject'] = $request->param('subject');
                $datas['liveClassName'] = $request->param('liveClassName');
                $datas['speaker'] = $request->param('speaker');
                $datas['majorId'] = $request->param('majorId');
                $datas['liveRoomId'] = $request->param('liveRoomId');
                $datas['state'] = $request->param('state');
                $datas['playbackUrl'] = $request->param('playbackUrl');
                $datas['imgUrl'] = $request->param('imgUrl');
                //$data['room_duration'] = $duration;


                $liveurl = LiveUrl::create($datas);

                foreach ($stageid as $item) {
//                return $item;
                    $liveclassstage = LiveclassBelongLiveStage::create(['livestageId' => $item, 'liveclassId' => $liveurl->id]);
                }
//                $liveclassstage = LiveclassBelongLiveStage::create(['liveclassId' => $liveurl->id, 'livestageId' => $request->param('stageId')]);
                if ($liveurl) {
                    //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                    $this->success('恭喜您，新增成功', 'index', '', '1');
                } else {
                    //错误页面的默认跳转页面是返回前一页，通常不需要设置
                    $this->error('更新失败');
                }
                return true;
            } else {
                return false;
            }
        }
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
        $live = LiveUrl::find($id);
        $stageid = LiveclassBelongLiveStage::where('liveclassId', $id)->value('livestageId');
        $liveroomid = LiveStage::where('id', $stageid)->value('liveRoomId');
        $stages = db::query("SELECT b.id,b.stageName,b.liveRoomId,c.liveRoomName
                                        FROM
                                            l_liveclass_bind_livestage  a
                                         JOIN l_livestage b on a.livestageId=b.id
                                         JOIN l_liveroom c on b.liveRoomId=c.id
                                        WHERE a.liveclassId=$id;"
        );
//        foreach ($stages as $key => $value){
//            $ids = $value['id'];
//            $liveroomids = LiveStage::where('id',$ids)->value('liveRoomId');
//            $stages[$key]['liveroomid'] = $liveroomids;
//        }
//                return json($stages);
        return view('/liveurl/edit', compact('live', 'stageid', 'liveroomid', 'stages'));
    }

    //选择一级目录分类二级目录分类联动
    public function choosestage(Request $request)
    {
        if ($request->isAjax()) {
            $stages = LiveStage::where('liveRoomId', $request->param('id'))->select();
            $info = array('stages' => $stages);
            return json($info);
//            return json($request->param());
        }
    }

    //新增时候直播间与权限联动
    public function liveroomstage(Request $request)
    {
        if ($request->isAjax()){
            //查出所有直播间
            $liverooms = Live::all();
            $stages = LiveStage::all();
            $info = array('liverooms'=>$liverooms,'stages' => $stages);
            return json($info);
        }
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
//        return json($request->param());

        $liveroomid = $request->param('liveRoomId');
        $stageid = $request->param('stageId');
//        if (count($liveroomid) !== count($stageid)){
//            $this->error('权限不能为空', "edit", '', '1');
//        }
//        return json($liveroomid);
        $broadcastRoomId = LiveUrl::where('id', $id)->value('roomId');

        $parameters = array(
            'subject' => $request->param('subject'), //实时课堂主题（长度：1-250）
            'startDate' => $request->param('startDate'), //开始日期
            'invalidDate' => $request->param('endDate'), //失效时间
            'teacherToken' => $request->param('teacherToken'), //老师加入口令（长度：6-15）（会自动生成随机数）
            'assistantToken' => $request->param('tutorToken'), //助教加入口令（长度：6-15）（会自动生成随机数）
            'studentToken' => $request->param('studentToken'), //Web端学生加入口令（长度：最大15）***
            'studentClientToken' => $request->param('studentClientToken'), //客户端学生加入口令
//            'scheduleInfo'		 => $data['course_name'], //课程介绍
            'scene' => '0', //0:大讲堂，1：小班课，2：私教课，默认值：0。当值scene为2，clientJoin,必须为true,同时webJoin为false
//            'description'		 => $data['course_description'], //课堂介绍
            'id' => $broadcastRoomId,
            'realTime' => false
        );

        $baseUrl = "http://xfxerj.gensee.com/integration/site";

        $url = $baseUrl . "/training/room/modify";

        $parameters = array_merge($parameters, $this->login_to_gensee);

        //$return = curlPost($parameters,$url);
        $return = $this->curlByPost($parameters, $url);

        $result = json_decode($return, true);

        if ($result['code'] === '0') {
            $datas['startDate'] = $request->param('startDate');
            $datas['endDate'] = $request->param('endDate');
            $datas['subject'] = $request->param('subject');
            $datas['liveClassName'] = $request->param('liveClassName');
            $datas['speaker'] = $request->param('speaker');
//            $datas['majorId'] = $request->param('majorId');
//            $datas['liveRoomId'] = $request->param('liveRoomId');
            $datas['state'] = $request->param('state');
            $datas['playbackUrl'] = $request->param('playbackUrl');
            $datas['imgUrl'] = $request->param('imgUrl');

            $liveclass = LiveUrl::where('id', $id)->update($datas);
            LiveclassBelongLiveStage::where('liveclassId', $id)->delete();

//            return json($liveclass);
            foreach ($stageid as $item) {
//                return $item;
                $liveclassstage = LiveclassBelongLiveStage::create(['livestageId' => $item, 'liveclassId' => $id]);
            }
            if ($liveclass || $liveclassstage) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('恭喜您，更新成功', 'index', '', '1');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
                $this->error('更新失败', '', '', '1');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if (LiveUrl::destroy($id)) {
            $info = array('status' => 1, 'msg' => '删除成功');
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);

    }

    public function delete_all(Request $request)
    {
        $ids = $request->param('checked_id');
        if (LiveUrl::destroy($ids)) {
            $info = array('status' => 1, 'msg' => '删除成功');
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }
}
