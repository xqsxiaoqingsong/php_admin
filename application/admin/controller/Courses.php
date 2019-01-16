<?php

namespace app\admin\controller;

use app\admin\model\Course;
use think\Controller;
use think\Request;

class Courses extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_course' => 'am-in',   //展开
            '_course' => 'am-active',   //高亮
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
                $query->where('courseName', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('majorId', $request->param('category_id'));
            }
        };

        $courses = Course::with('coursepro')->where($where)->paginate(10, false, ['query' => request()->param()]);
        $count = $courses->total();
        $condition = $request->param();
//        return json($courses);
        return view('course/index', compact('courses', 'count', 'condition'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('course/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
//        return json($request->param());

        $validate = Validate('CourseValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = Course::create($request->param());
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
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $course = Course::find($id);
        return view('course/edit', compact('course'));
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);
//        return json($request->param());
        $validate = Validate('CourseValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $course->update($request->param());
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
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        Course::destroy($id);
        return redirect('index');
    }

    //多条删除
    public function delete_all(Request $request)
    {
        $ids = $request->param();
//        return json($ids);
        Course::destroy($ids['checked_id']);
    }

    //排序
    public function sort_order(Request $request)
    {
        $id = $request->param('id');
        $sort_order = $request->param('sort_order');
//        return $sort_order;
        Course::where("id = '$id'")->setField('recommendSort', $sort_order);
    }

    //是否显示
    public function change_show(Request $request)
    {
        $id = $request->param('id');
        $show = !$request->param('attr');
//        return $show;
        Course::where('id',$id)->setField('state', $show);
    }
}
