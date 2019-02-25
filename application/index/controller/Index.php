<?php

namespace app\index\controller;

use think\Controller;
use think\Loader;
use think\Request;

class Index extends Common
{
    //首页
    public function index()
    {
        //首页左侧导航专业列表
        $dataMajorList = $this->dataMajorList;
        $this->assign('dataMajorList',$dataMajorList);
        $urlHomeList = curlByPost([],Config('urlHomeList'))['Data'];
        foreach ($urlHomeList as $value){
            if($value['theme'] == '横幅'){
                $banners = $value['course'];
                $this->assign('bannerlist',$banners);
            }elseif($value['theme'] == '考务资讯'){
                $examlist = $value['course'];
                $this->assign('examlist',$examlist);
            }elseif($value['theme'] == '面授培训'){
                $ftflist = $value['course'];
                $this->assign('ftflist',$ftflist);
            }elseif($value['theme'] == '直播预告'){
                if($value['course']){
                    foreach ($value['course'] as $k=>$val){
                        $value['course'][$k]['margin_date'] = getDataFormart($val['startDate']);
                    }
                }
                $liveList = $value['course'];
                $this->assign('livelist',$liveList);
            }
        }

        //首页全部在线课程
        $dataOnlineList = [];
        //图书资源
        $booklist = [];
        //金牌名师
        $teacherlist = [];
        if($dataMajorList){
            foreach ($dataMajorList as $value){
                $dataOnlineList[] = curlByPost(['majorId'=>$value['id']],Config('urlHomeOnlineList'))['Data'];
                $booklist[] = curlByPost(['majorId' => $value['id']], Config('urlHomeBookList'))['Data'];
                $teachers = curlByPost(['majorId' => $value['id']], Config('$urlHomeTeacherListurlHomeTeacherList'))['Data'];
                $teacherlist[] = array_chunk($teachers,4);
            }
        }

        $this->assign('dataOnlineList',$dataOnlineList);
        $this->assign('booklist',$booklist);
        $this->assign('teacherlist',$teacherlist);

        return $this->fetch('/index');
    }

    //专业首页
    public function theme(Request $request){
        $majorId = $request->id;
        $majorName = $request->major_name;
        $majorData = curlByPost(['majorId'=>$majorId],Config('themeList'))['Data'];

        foreach ($majorData as $key=>$value){
            switch ($value['theme']){
                case "横幅":
                    $banners = $value['course'];
                    break;
                case "考务动态":
                    $examinations = $value['course'];
                    break;
                case "面授培训":
                    $ftfs = $value['course'];
                    break;
                case "推荐课程":
                    $onlines = $value['course'];
                    break;
                case "推荐图书":
                    $books = $value['course'];
                    break;
                case "金牌名师":
                    $teachers = array_chunk($value['course'],4);
                    break;
                case "直播教室":
                    $lives = $value['course'];
                    break;
            }
        }
        //$this->assign('banners',$banners);
        $this->assign('examinations',$examinations);
        $this->assign('ftfs',$ftfs);
        $this->assign('onlines',$onlines);
        $this->assign('books',$books);
        $this->assign('teachers',$teachers);
        $this->assign('lives',$lives);
        $this->assign('majorName',$majorName);
        $this->assign('majorId',$majorId);
//        dd($teachers);die;
        return $this->fetch('/theme');
    }









}
