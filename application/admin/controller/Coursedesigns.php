<?php

namespace app\admin\controller;

use app\admin\model\CountPricebook;
use app\admin\model\CountPricenobook;
use app\admin\model\Course;
use app\admin\model\CourseCatalog;
use app\admin\model\CourseClass;
use app\admin\model\CourseStage;
use app\admin\model\StagePeicebook;
use app\admin\model\StagePeicenobook;
use think\Controller;
use think\Request;
use think\response\Json;

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
            if ($request->param('pid')) {
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
            } else {
                $info = array('status' => 0, 'msg' => '无法直接添加二级分类,请先添加一级分类');
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

    //编辑课程分类
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

    //课程管理选择一级目录分类二级目录分类联动
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
            $count = CourseCatalog::where('pid', $id)->count();
//            return json($count);
            if ($count) {
                $info = array('status' => 0, 'msg' => '该一级分类下有二级分类,不能删除!!!');
            } else {
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
        $course = Course::find($id);
        $catalogs = CourseCatalog::where('courseId', $id)->select();
        //循环出2级目录
        foreach ($catalogs as $key => $value) {
            //查出二级目录
            $ids = $value['id'];
            $catalog = CourseCatalog::where('pid', $ids)->order('recommendSort asc')->select();
            $catalogs[$key]['sub'] = $catalog;
        }
        return view('coursedesign/editlessons', compact('course', 'catalogs', ''));
    }

    //课节管理选择一级目录分类二级目录分类联动
    public function editlessonsajax(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id');
            $courseid = $request->param('courseId');
//                        return json($courseid);
            if ($id) {
                //查出所有课节
                $classes = CourseClass::where('catalogId', $id)->order('recommendSort asc')->select();
                //查出二级目录
                $twocatalog = CourseCatalog::where('pid', $id)->order('recommendSort asc')->select();
                //查出所有阶段权限
                $stages = CourseStage::where('courseId', $request->param('courseId'))->order('recommendSort asc')->select();
                $info = array('erjimulu' => $twocatalog, 'lessons' => $classes, 'stages' => $stages);
                return json($info); //返回json格式的数据
            } else {
                $classes = CourseClass::where('courseId', $courseid)->order('recommendSort asc')->select();
                //查出所有阶段权限
                $stages = CourseStage::where('courseId', $request->param('courseId'))->order('recommendSort asc')->select();
                $info = array('lessons' => $classes, 'stages' => $stages);
                return json($info);
            }
        }
    }

    //新增课节
    public function saveclass(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $validate = Validate('CourseClassValidate');
            if (!$validate->scene('save')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
            $result = CourseClass::create($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，新增成功', 'index');
                $info = array('status' => 1, 'msg' => '添加课节成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('新增失败');
                $info = array('status' => 0, 'msg' => '添加课节失败');
            }
            return json($info);
        }
    }

    //更新编辑的课节
    public function editclass(Request $request)
    {
        if ($request->isAjax()) {
            $validate = Validate('CourseClassValidate');
            if (!$validate->scene('update')->check($request->param())) {
                $this->error($validate->getError());
                $info = $this->error();
                return json($info);
            };
//            return json($request->param());
            $result = CourseClass::where('id', $request->param('id'))->update($request->param());
            if ($result) {
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
                $info = array('status' => 1, 'msg' => '编辑课节成功');
            } else {
                //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
                $info = array('status' => 0, 'msg' => '编辑课节失败');
            }
            return json($info);
//            return json($request->param());
        }
    }

    //删除课节
    public function deleteclass(Request $request,$id)
    {
//        return json($request->param());
        $result = CourseClass::destroy($id);
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
//                $this->success('恭喜您，更新成功', 'index');
            $info = array('status' => 1, 'msg' => '删除课节成功');
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
//                $this->error('更新失败');
            $info = array('status' => 0, 'msg' => '删除课节失败');
        }
        return json($info);
    }

    //编辑价格
    public function editprice($id)
    {
        $course = Course::find($id);
        $catalogs = CourseCatalog::where('courseId', $id)->select();
        //循环出2级目录
        foreach ($catalogs as $key => $value) {
            //查出二级目录
            $ids = $value['id'];
            $catalog = CourseCatalog::where('pid', $ids)->order('recommendSort asc')->select();
            $catalogs[$key]['sub'] = $catalog;
        }
        return view('coursedesign/editprice', compact('course', 'catalogs', ''));
    }

    //价格有没有图书联动
    public function editpriceajax(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id');
            $courseid = $request->param('courseId');
//            return json($request->param());
            //查出课程的阶段权限
            $stages = CourseStage::where('courseId', $courseid)->select();
            if ($id == 1) {
                foreach ($stages as $key => $value) {
                    //查出阶段价格
                    $ids = $value['id'];
                    $price = StagePeicebook::where('stageId', $ids)->value('price');
                    $stages[$key]['price'] = $price;
                }
                $counts = CountPricebook::where('courseId', $courseid)->select();
                $info = array('counts' => $counts, 'stages' => $stages);
                return json($info);
            }
            if ($id == 2) {
                foreach ($stages as $key => $value) {
                    //查出阶段价格
                    $ids = $value['id'];
                    $price = StagePeicenobook::where('stageId', $ids)->value('price');
                    $stages[$key]['price'] = $price;
                }
                $counts = CountPricenobook::where('courseId', $courseid)->select();
                $info = array('counts' => $counts, 'stages' => $stages);
                return json($info);
            }
        }
    }

    //新增数量价格
    public function savecountprice(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $id = $request->param('typeid');
            $courseid = $request->param('courseId');
            $bookcountpriceid = $request->param('id');

            $stages = CourseStage::where('courseId', $courseid)->select();
            if ($id == 1) {
//                return json($request->param());
                $onlycount = CountPricebook::where(['courseId' => $courseid, 'count' => $request->param('count')])->find();
                if ($onlycount and !$bookcountpriceid) {
                    $info = array('status' => 0, 'msg' => '该规则已存在!');
                    return json($info);
                }
                if ($bookcountpriceid) {
                    $result = CountPricebook::where('id', $bookcountpriceid)->update(['count' => $request->param('count'), 'price' => $request->param('price')]);
                    if ($result !== false) {
                        $info = array('status' => 1, 'msg' => '更新成功', 'stages' => $stages);
                    } else {
                        $info = array('status' => 0, 'msg' => '更新失败');
                    }
                } else {
                    $counts = CountPricebook::create($request->param());
                    $info = array('status' => 1, 'msg' => '插入成功', 'stages' => $stages);
                }
                return json($info);
            }
            if ($id == 2) {
//                return json($request->param());
                $onlycountnobook = CountPricenobook::where(['courseId' => $courseid, 'count' => $request->param('count')])->find();
                if ($onlycountnobook and !$bookcountpriceid) {
                    $info = array('status' => 0, 'msg' => '该规则已存在!');
                    return json($info);
                }
                if ($bookcountpriceid) {
                    $result = CountPricenobook::where('id', $bookcountpriceid)->update(['count' => $request->param('count'), 'price' => $request->param('price')]);
                    if ($result !== false) {
                        $info = array('status' => 1, 'msg' => '更新成功', 'stages' => $stages);
                    } else {
                        $info = array('status' => 0, 'msg' => '更新失败');
                    }
                } else {
                    $counts = CountPricenobook::create($request->param());
                    $info = array('status' => 1, 'msg' => '插入成功', 'stages' => $stages);
                }
                return json($info);
            }
        }
    }

    //保存编辑的权限价格
    public function updatestageprice(Request $request)
    {
//        return json($request->param());
        $type = $request->param('typeid');
        $id =$request->param('id');
        if ($type==1){
            $stagebookprice =StagePeicebook::where('stageId', $id)->find();
            if ($stagebookprice){
                $result =StagePeicebook::where('stageId', $id)->setField('price',$request->param('price'));
<<<<<<< HEAD
                if ($result !== false) {
=======
//                return json($result);
                if ($result) {
>>>>>>> parent of 99fb2a7... 20190116 17.35
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            }else{
                $result =StagePeicebook::create(['stageId'=>$id,'price'=>$request->param('price')]);
//            return json($result);
                if ($result) {
                    
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            }

        }
        if ($type==2){
            $stagenobookprice = StagePeicenobook::where('stageId', $id)->find();
            if ($stagenobookprice){
                $result =StagePeicenobook::where('stageId', $id)->setField('price',$request->param('price'));
//            return json($result);
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            }else{
                $result =StagePeicenobook::create(['stageId'=>$id,'price'=>$request->param('price')]);
//            return json($result);
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            }

        }
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
