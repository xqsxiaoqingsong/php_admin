<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\paginator\driver\Bootstrap;


class Ftf extends Common{

    //面授培训
    public function index(Request $request){
        $majorId = intval($request->id) ? intval($request->id) : '0';
        $dataMajorList = $this->dataMajorList;
        $page = $request->page ? $request->page : '1';
        $this->assign('dataMajorList',$dataMajorList);
        $this->assign('majorId',$majorId ? $majorId : 0);
        $ftf = curlByPost(['majorId' => $majorId,'faceTrainAddress'=>'全部','page'=>$page], Config('urlFtfList'))['Data'];
        $list = isset($ftf['courseManage']) ? $ftf['courseManage'] : [];
        $count = isset($ftf['total']) ? $ftf['total'] : 0;
        $p = Bootstrap::make($list, 16, $page, $count, false, [
            'var_page' => 'page',
            'path'     => '/ftf/'.$majorId,//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);

        $p->appends($_GET);
        $this->assign('list',$list);
        $this->assign('plistpage', $p->render());
        $this->assign('majorId',$majorId);

        return $this->fetch('ftf');
    }

    public function detail(Request $request){
        $id = $request->id;
        $majorId = intval($request->id) ? intval($request->id) : '0';
        $ftf = curlByPost(['id'=>$id,'memberId'=>$majorId], Config('urlFtfDetail'))['Data'];
        $this->assign('ftf',$ftf);
        return $this->fetch('ftfDetail');
    }
}