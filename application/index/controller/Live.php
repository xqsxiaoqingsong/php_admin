<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Live extends Common{

    //直播列表
    public function index(Request $request){
        $majorId = intval($request->id);
        $dataMajorList = $this->dataMajorList;

        $liveList = [];
        if($dataMajorList){
            foreach ($dataMajorList as $value) {
                $liveList[] = curlByPost(['majorId' => $value['id']], Config('urlLiveList'))['Data'];
            }
        }

        $this->assign('liveList',$liveList);
        $this->assign('majorName','直播课程');
        $this->assign('dataMajorList',$dataMajorList);
        $this->assign('majorId',$majorId ? $majorId : 1);
//        dd($liveList);die;
        return $this->fetch('live');
    }

    //直播详情
    public function liveDetail(Request $request){
        $liveId = $request->id;
        $res = curlByPost(['liveRoomId' => $liveId], Config('urlLiveDetail'))['Data'];
        $this->assign('liveInfo',$res);

        $memberId = session('userId') ? session('userId') : '';
        $liveClassList = curlByPost(['liveRoomId' => $liveId,'memberId'=>$memberId], Config('_LiveClassList'))['Data'];
        $this->assign('liveClassList',$liveClassList);

        $this->assign('liveId',$liveId);
//        dd($liveClassList);die;

        return $this->fetch('liveDetail');
    }

    //验证直播权限，获取看课信息
    public function checkLiveRole(Request $request){

        //登录检测
        $memberId = session('userId');
        $userInfo = session('userInfo');
        $liveId = $request->liveId;
        $classId = $request->id;
        if(!$memberId){
            return json(['Code'=>5,'Msg'=>'请先登录！']);
        }
        $liveClassInfo = curlByPost(['liveRoomId' => $liveId,'classId'=>$classId,'memberId'=>$memberId], Config('_LiveRoomUrl'));

        if($liveClassInfo['Code'] == '0'){
            if($liveClassInfo['Data']['state'] == 0){
                return json(['Code'=>0,'Msg'=>'直播尚未开始，请稍后。']);
            }elseif(($liveClassInfo['Data']['state'] == 3 || $liveClassInfo['Data']['state'] == 2) && $liveClassInfo['Data']['playbackUrl'] == ''){
                return json(['Code'=>1,'Msg'=>'回看课件正在加紧剪辑中...']);
            }elseif(($liveClassInfo['Data']['state'] == 3 || $liveClassInfo['Data']['state'] == 2) && $liveClassInfo['Data']['playbackUrl'] != ''){
                return json(['Code'=>2,'Msg'=>'本地回看！','entry'=>['playbackUrl'=>$liveClassInfo['Data']['playbackUrl'],'liveClassName'=>$liveClassInfo['Data']['liveClassName']]]);
            }elseif($liveClassInfo['Data']['state'] == 1){

                //直播链接拼接
                if($userInfo['memberName']){
                    $nickName = $userInfo['memberName'];
                }elseif($userInfo['realName']){
                    $nickName = $userInfo['realName'];
                }else{
                    $nickName = '新方向用户';
                }
                $nickName = $nickName . '('.$memberId.')';
                $url = $liveClassInfo['Data']['studentJoinUrl'];
                $token = md5($liveClassInfo['Data']['studentToken']);
                $genseeUid = '1';
                $length = 16 - strlen($memberId)-1;
                if($length>0){
                    for($i=0;$i<$length;$i++){
                        $genseeUid .= '0';
                    }
                }
                $genseeUid .= $memberId;
                $fullUrl=$url."?token=".$token."&nickname=".$nickName."&sec=md5&uid=".$genseeUid;

                return json(
                    [
                        'Code'=>3,
                        'Msg'=>'直播中',
                        'entry'=>['fullUrl'=>$fullUrl,'liveClassName'=>$liveClassInfo['Data']['liveClassName']]
                    ]
                );

            }else{
                return json(['Code'=>1,'Msg'=>'没有权限观看！']);
            }
        }



    }
}