<?php

namespace app\admin\controller;

use app\admin\model\Teacher;
use think\Controller;
use think\Request;

class Teachers extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_eduadmin' => 'am-in',   //展开
            '_teacher' => 'am-active',   //高亮
        ]);
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('TEACHERNAME', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('CNMEDICINEMAJORID', $request->param('category_id'));
            }
            //按电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('PHONE', 'like', $search);
            }
        };

        $teachers = Teacher::with('teacherpro')->where($where)->paginate(10, false, ['query' => request()->param()]);
        $count = $teachers->total();
        //返回搜索条件,然后在前端进行 empty +if 判断显示出来
        $condition=$request->param();
//        return json($teachers);
        return view('teacher/index', compact('teachers', 'count','condition'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('teacher/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('TeacherValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = Teacher::create([
            'CNMEDICINEMAJORID' => $request->param('CNMEDICINEMAJORID'),
            'TEACHERNAME' => $request->param('TEACHERNAME'),
            'PHONE' => $request->param('PHONE'),
            'IMGURL' => $request->param('image'),
            'BRIEF' => $request->param('BRIEF')
            ]);
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
        $teacher = Teacher::find($id);
        return view('teacher/edit', compact('teacher'));
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
        $teacher = Teacher::find($id);
        $validate = Validate('TeacherValidate');
        if ($request->param('TEACHERNAME') == ''){
            $this->error('老师姓名不能为空');
        }
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $teacher->where('ID',$id)->update(['CNMEDICINEMAJORID' => $request->param('CNMEDICINEMAJORID'),
            'TEACHERNAME' => $request->param('TEACHERNAME'),
            'PHONE' => $request->param('PHONE'),
            'IMGURL' => $request->param('image'),
            'BRIEF' => $request->param('BRIEF')]);
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
        //这里如果用destroy需要去模型那里设置了主键为大写的ID,没有设置主键,那么就要用delete方法
        Teacher::where('ID',$id)->delete();
        return redirect('index');
    }

    //多条删除
    public function delete_all(Request $request)
    {
        $ids = $request->param();
//        return json($ids);
        Teacher::destroy($ids['checked_id']);
    }
}
