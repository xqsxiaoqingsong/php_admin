<?php

namespace app\admin\controller;

use app\admin\model\LiveUrl;
use app\api\model\Live;
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
            ->join("l_liveclass_bind_livestage b", "a.id = b.liveclassId","LEFT")
            ->join("l_livestage c","b.livestageId = c.id","LEFT")
            ->join("l_liveroom d","c.liveRoomId = d.id","LEFT")
            ->where($where)
            ->group("a.id")
            ->paginate(10, false, ['query' => request()->param()]);

        $count = $lives->total();
        $condition = $request->param();
        $this->assign('condition',$condition);
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
        if ($request->isPost()){
            return json($request->param());
            $data['room_start_datetime']=$request->param('startDate');
            $data['room_end_datetime']=$request->param('endDate');
            $data['teacher_token_for_room']=rand(100000,999999);
            $data['assistant_token_for_room']=rand(100000,999999);
            $data['student_web_token_for_room']=rand(100000,999999);
            $data['student_client_token_for_room']=rand(100000,999999);
            $name = $request->param('subject');
            $parameters = array(
                'subject' 			 => $name, //实时课堂主题（长度：1-250）
                'startDate'			 => $data['room_start_datetime'], //开始日期
                'invalidDate' 		 => $data['room_end_datetime'], //失效时间
                'duration' 			 => '', //课堂持续时长（单位为分钟）
                'uiMode' 			 => '2', //Web端学生界面设置(1是三分屏，2是文档/视频为主，3是两分屏，4：互动增加)
                'uiColor'			 => 'default', //三分屏颜色选择（blue, default, green），默认是default
                'uiVideo'			 => '1', //uiMode等于2时候，设置是否视频为主
                'uiWindow'			 => '1', //uiMode等于2时候，设置是否显示小窗口。
                'upgrade' 			 => true, //是否允许web升级到客户端
                'teacherToken'       => $data['teacher_token_for_room'], //老师加入口令（长度：6-15）（会自动生成随机数）
                'assistantToken'     => $data['assistant_token_for_room'], //助教加入口令（长度：6-15）（会自动生成随机数）
                'studentToken' 		 => $data['student_web_token_for_room'], //Web端学生加入口令（长度：最大15）
                'studentClientToken' => $data['student_client_token_for_room'], //客户端学生加入口令
                'webJoin' 			 => 'true', //是否支持Web端学生加入,值为true或者false。默认值为true, 当scene的值为2时该属性值为false,该值得设置无效
                'clientJoin'		 => 'true', //是否支持客户端端学生加入,值为true或者false。默认值为true，当scene的值为2时该属性值为true，该值得设置无效
                'scheduleInfo'		 => $name, //课程介绍
                'speakerInfo' 		 => '', //老师介绍
                'scene' 			 => '0', //0:大讲堂，1：小班课，2：私教课，默认值：0。当值scene为2，clientJoin,必须为true,同时webJoin为false
                'description'		 => $name, //课堂介绍
                'realTime'			 => false
            );

            $baseUrl ="http://xfxerj.gensee.com/integration/site";

            $url = $baseUrl."/training/room/created";

            $parameters = array_merge($parameters,$this->login_to_gensee);

            $return = $this->curlByPost($parameters,$url);
            $result  = json_decode($return,true);
            if($result['code'] === '0')
            {
                $data['roomId'] = $result['id'];
                $data['roomNumber'] = $result['number'];
                $data['startDate'] = $result['startDate'];
                $data['endDate'] = $result['invalidDate'];
                $data['teacherToken'] = $result['teacherToken'];
                $data['tutorToken'] = $result['assistantToken'];
                $data['studentToken'] = $result['studentToken'];
                $data['studentClientToken'] = $result['studentClientToken'];
                $data['teacherJoinUrl'] = $result['teacherJoinUrl'];
                $data['studentJoinUrl'] = $result['studentJoinUrl'];
                //$data['room_duration'] = $duration;

                LiveUrl::create($data);
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function curlByPost($parameters,$url){

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
//        $content = htmlspecialchars_decode($live['content']);
        //        return json($content);exit();
        return view('/liveurl/edit', compact('live', 'content'));
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
        if(LiveUrl::destroy($id)){
            $info = array('status' => 1, 'msg' => '删除成功');
        }else{
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);

    }

    public function delete_all(Request $request){
        $ids = $request->param('checked_id');
        if(LiveUrl::destroy($ids)){
            $info = array('status' => 1, 'msg' => '删除成功');
        }else{
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }
}
