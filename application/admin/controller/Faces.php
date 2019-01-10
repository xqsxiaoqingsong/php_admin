<?php

namespace app\admin\controller;

use app\admin\model\Face;
use app\admin\model\Profession;
use think\Controller;
use think\Request;

class Faces extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            'professions' => Profession::all(),
            '_xfx_face' => 'am-in',   //展开
            '_face' => 'am-active',   //高亮
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $faces = Face::with('facepro')->paginate(10, false, ['query' => request()->param()]);
//        return json($faces);
        $count = $faces->total();
        return view('face/index', compact('faces', 'count'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('face/create');
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
        //
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
