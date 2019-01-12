<?php

namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\CourseCatalog;
use app\admin\model\CourseStage;
use think\Controller;
use think\Request;

class CourseDesigns extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_course' => 'am-in',   //展开
            '_coursedesign' => 'am-active',   //高亮
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
        };
        $courses = Course::where($where)->paginate(10, false, ['query' => request()->param()]);
        $count = $courses->total();
        $condition = $request->param();
//        return json($courses);
        return view('coursedesign/index', compact('courses', 'count', 'condition'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    //新建权限阶段
    public function savestage(Request $request)
    {
        if ($request->isAjax()) {
            $validate = Validate('CourseStageValidate');
            if (!$validate->scene('save')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
            $result = CourseStage::create($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，新增成功', 'index');
                $info = array('status' => 1, 'msg' => '添加权限成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('新增失败');
                $info = array('status' => 0, 'msg' => '添加权限失败');
            }
            return json($info);
        }
    }

    //新建二级目录
    public function savecatalog(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $validate = Validate('CourseCatalogValidate');
            if (!$validate->scene('save')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
            $result = CourseCatalog::create($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，新增成功', 'index');
                $info = array('status' => 1, 'msg' => '添加二级分类成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('新增失败');
                $info = array('status' => 0, 'msg' => '添加二级分类失败');
            }
            return json($info);
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
        //
    }

    //编辑分类
    public function editprofession($id)
    {
        $course = Course::find($id);
        //查出课程的阶段权限
        $stages = CourseStage::where('courseId', $id)->order('recommendSort asc')->select();
        //查出课程的目录
        $catalogs = CourseCatalog::where('courseId', $id)->select();
        //循环出2级目录
        foreach ($catalogs as $key => $value) {
            //查出二级目录
            $ids = $value['id'];
            $catalog = CourseCatalog::where('pid', $ids)->order('recommendSort asc')->select();
            $catalogs[$key]['sub'] = $catalog;
        }
//        return json($catalogs);
        return view('coursedesign/editprofession', compact('course', 'stages', 'catalogs'));
    }

    //选择一级目录分类二级目录分类联动
    public function editproajax(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id');
//                        return json($id);
            if (isset($id)) {
                $catalog = CourseCatalog::where('pid', $id)->select();
//                $ajaxcatalog = $catalog->toArray()['data'];//转换为数据,讲要存的信息保存成一个数组
//                $data['data'] = $ajaxcatalog; //这一段是将数据赋值给一个数组，这个数组用于ajax请求返回给前端
//                $data['status'] = 1; //状态码
                return json($catalog); //返回json格式的数据
            } else {
                $info = array('status' => 0, 'msg' => '获取二级目录失败');
                return json($info);
            }
        }
    }

    //编辑分类里的编辑权限
    public function editstage(Request $request)
    {
        if ($request->isAjax()) {
            $validate = Validate('CourseStageValidate');
            if (!$validate->scene('update')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
            $result = CourseStage::where('id', $request->param('id'))->update($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '编辑权限成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '编辑权限失败');
            }
            return json($info);
//            return json($request->param());
        }
    }

    //编辑分类里的编辑二级目录
    public function editcatalog(Request $request)
    {
        if ($request->isAjax()) {
            $validate = Validate('CourseCatalogValidate');
            if (!$validate->scene('update')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
//            return json($request->param());
            $result = CourseCatalog::where('id', $request->param('id'))->update($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '编辑二级分类成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '编辑二级分类失败');
            }
            return json($info);
//            return json($request->param());
        }
    }

    //删除权限
    public function deletestage(Request $request, $id)
    {
        if ($request->isAjax()) {
            CourseStage::destroy($id);
            return redirect('index');
        }
    }

    //删除二级目录
    public function deletecatalog(Request $request, $id)
    {
        if ($request->isAjax()) {
            CourseCatalog::destroy($id);
            return redirect('index');
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    //编辑一级目录分类
    public function edit(Request $request, $id)
    {
//        return json($request->param());
        $course = Course::find($id);
        $catalogs = CourseCatalog::where('courseId', $id)->order('recommendSort asc')->select();
        return view('coursedesign/editcatalog', compact('course', 'catalogs', ''));
    }

    //更新编辑的一级目录分类
    public function editfirstcatalog(Request $request)
    {
        if ($request->isAjax()) {
            $validate = Validate('CourseCatalogValidate');
            if (!$validate->scene('update')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
//            return json($request->param());
            $result = CourseCatalog::where('id', $request->param('id'))->update($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '编辑一级分类成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '编辑一级分类失败');
            }
            return json($info);
//            return json($request->param());
        }
    }

    //新建一级目录分类
    public function savefirstcatalog(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $validate = Validate('CourseCatalogValidate');
            if (!$validate->scene('save')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
            $result = CourseCatalog::create($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，新增成功', 'index');
                $info = array('status' => 1, 'msg' => '添加一级分类成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('新增失败');
                $info = array('status' => 0, 'msg' => '添加一级分类失败');
            }
            return json($info);
        }
    }

    //删除一级分类
    public function deletefirstcatalog(Request $request, $id)
    {
        if ($request->isAjax()) {
            $count = CourseCatalog::where('pid',$id)->count();
//            return json($count);
            if ($count){
                $info = array('status' => 0, 'msg' => '该一级分类下有二级分类,不能删除!!!');
            }else{
                $result = CourseCatalog::destroy($id);
                if ($result) {
                    $info = array('status' => 1, 'msg' => '删除一级分类成功');
                } else {
                    $info = array('status' => 0, 'msg' => '添加一级分类失败');
                }
            }
            return json($info);
        }
    }

    //编辑课节
    public function editlessons($id)
    {
        //
        return 2;
    }

    //编辑价格
    public function editprice($id)
    {
        //
        return 3;
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
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //deletecatalog
    }


}
