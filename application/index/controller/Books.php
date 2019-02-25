<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\paginator\driver\Bootstrap;


class Books extends Common{

    //图书
    public function index(Request $request){
        $majorId = intval($request->id) ? intval($request->id) : '0';
        $type = isset($request->type) ? $request->type : '0';
        $dataMajorList = $this->dataMajorList;
        $page = $request->page ? $request->page : '1';
        $this->assign('type',$type);
        $this->assign('dataMajorList',$dataMajorList);
        $this->assign('majorId',$majorId ? $majorId : 0);
        $book = curlByPost(['majorId' => $majorId,'type'=>$type,'page'=>$page], Config('urlBooksList'))['Data'];

        $list = isset($book['booksList']) ? $book['booksList'] : [];
        $count = isset($book['total']) ? $book['total'] : 0;
        $p = Bootstrap::make($list, 16, $page, $count, false, [
            'var_page' => 'page',
            'path'     => '/books/'.$type.'/'.$majorId,//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);

        $p->appends($_GET);
        $this->assign('list',$list);
        $this->assign('plistpage', $p->render());
        $this->assign('majorId',$majorId);

        return $this->fetch('books');
    }

    //图书详情
    public function bookDetail(Request $request){
        $id = intval($request->id);
        if($id < 1) {
            $this->redirect('/books');
        }

        return $this->fetch('bookDetail');
    }
}