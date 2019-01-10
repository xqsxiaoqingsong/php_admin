<?php

namespace app\admin\controller;

use app\admin\model\LiveUrl;
use app\api\model\Live;
use think\Controller;
use think\Request;

class Liveurls extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
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
    public function index()
    {
        $lives = LiveUrl::paginate(10, false, ['query' => request()->param()]);
        $count = $lives->total();
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

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $live = LiveUrl::find($id);
//        $content = htmlspecialchars_decode($live['content']);
        //        return json($content);exit();
        return view('/liveurl/edit', compact('live','content'));
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
