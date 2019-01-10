<?php

namespace app\admin\controller;

use app\admin\model\Profession;
use think\Controller;
use think\Request;

class Professions extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_profession' => 'am-in',   //展开
            '_profession' => 'am-active',   //高亮
        ]);
    }

    // 验证失败是否抛出异常
    protected $failException = true;

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
//        echo \think\facade\App::version();
        $professions = Profession::paginate(10, false, ['query' => request()->param()]);
        $count = $professions->total();
        return view('profession/index', compact('professions', 'count'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('profession/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('ProfessionValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = Profession::create($request->param());
//        return redirect('index');
//        $this->success('恭喜您，新建成功了', 'index');
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，新增成功', 'index');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            $this->error('新增失败');
        }
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
        $profession = Profession::find($id);
        return view('profession/edit', compact('profession'));
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
        $profession = Profession::find($id);

        $validate = Validate('ProfessionValidate');
        if ($request->param('MAJOR_NAME') == ''){
            $this->error('分类名称不能为空');
        }
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $profession->update($request->param());
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
        Profession::where('ID',$id)->delete();
        return redirect('index');
    }
}
