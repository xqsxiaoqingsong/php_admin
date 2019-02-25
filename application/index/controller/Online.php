<?php
namespace app\index\controller;

use think\Controller;
use think\paginator\driver\Bootstrap;
use think\Request;

class Online extends Common{

    //在线课程
    public function index(Request $request){
        $majorId = intval($request->id) ? intval($request->id) : '0';
        $dataMajorList = $this->dataMajorList;
        $page = $request->page ? $request->page : '1';
        $this->assign('dataMajorList',$dataMajorList);
        $this->assign('majorId',$majorId ? $majorId : 0);
        $online = curlByPost(['majorId' => $majorId,'page'=>$page], Config('urlOnlineList'))['Data'];
        $list = isset($online['courseManage']) ? $online['courseManage'] : [];
        $count = isset($online['total']) ? $online['total'] : 0;
        $p = Bootstrap::make($list, 16, $page, $count, false, [
            'var_page' => 'page',
            'path'     => '/online/'.$majorId,//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);

        $p->appends($_GET);
        $this->assign('list',$list);
        $this->assign('plistpage', $p->render());
        $this->assign('majorId',$majorId);

        return $this->fetch('/online/online');
    }

    public function onlineDetail(Request $request){
        $id = $request->id;
        $memberId = session('userId') ? session('userId') : "";
        $online = curlByPost(['memberId' => $memberId,'courseId'=>$id], Config('urlOnlineDetail'))['Data'];
        $this->assign('online',$online);
//        dd($online);die;
        $classList = curlByPost(['memberId' => $memberId,'courseId'=>$id], Config('_ClassList'))['Data'];

        $this->assign('classList',$classList);
        return $this->fetch('onlineDetail');
    }

}