<?php

namespace app\admin\controller;

use app\admin\model\Live;
use think\Controller;
use think\Request;

class Lives extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_live' => 'am-in',   //展开
            '_live' => 'am-active',   //高亮
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $lives = Live::paginate(10, false, ['query' => request()->param()]);
        $count = $lives->total();
<<<<<<< HEAD
        return view('live/index', compact('lives', 'count'));
=======
        $condition = $request->param();
//        return json($lives);
        return view('live/index', compact('lives', 'count','condition'));
>>>>>>> parent of 99fb2a7... 20190116 17.35
        return json($lives);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('live/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
<<<<<<< HEAD
        //
=======
        $validate = Validate('LiveRoomValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        return json($request->param());
        $result = Live::create($request->param());
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，新增成功', 'index');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
>>>>>>> parent of 99fb2a7... 20190116 17.35
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
        return view('/live/edit', compact('live','content'));
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
