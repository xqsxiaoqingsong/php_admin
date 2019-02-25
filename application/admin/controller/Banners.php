<?php

namespace app\admin\controller;

use app\admin\model\Banner;
use think\Controller;
use think\Request;

class Banners extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_eduadmin' => 'am-in',   //展开
            '_banner' => 'am-active',   //高亮
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $banners = Banner::with('bannerpro')->all();
        $count = $banners->count();
//        return json($banner);
        return view('banner/index', compact('banners', 'count'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('banner/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
//        return json($request->param());
        $validate = Validate('BannerValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = Banner::create(['BANNERNAME' => $request->param('BANNERNAME'), 'IMGURL' => $request->param('image'),'MAJORID' => $request->param('majorId')]);
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，新增成功', 'index');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
//        return json($request->param());
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
        $banner = Banner::find($id);
        return view('banner/edit', compact('banner'));
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
        $banner = Banner::find($id);
        $validate = Validate('BannerValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $banner->where('ID',$id)->update(['BANNERNAME' => $request->param('BANNERNAME'), 'IMGURL' => $request->param('image'),'MAJORID' => $request->param('majorId')]);
//        return redirect('index');
//        $this->success('恭喜您，更新成功了', 'index');
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，更新成功', 'index');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('更新失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        Banner::where('ID',$id)->delete();
        return redirect('index');
    }
}
