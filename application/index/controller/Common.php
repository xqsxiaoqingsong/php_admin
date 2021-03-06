<?php

namespace app\index\controller;

use think\Controller;

class Common extends Controller
{
    public $dataMajorList=null;
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $navList = curlByPost(['page'=>1],Config('urlNavList'))['Data'];

        $userInfo = session('userInfo') ? session('userInfo') : false;
        $userId = session('userId') ? session('userId') : '';

        $this->assign('userId',$userId);
        $this->assign('userInfo',$userInfo);

        $this->assign('navList',$navList);
        $this->dataMajorList = curlByPost(['page'=>1],Config('urlShowCnmedicineMajor'))['Data'];
    }

}
