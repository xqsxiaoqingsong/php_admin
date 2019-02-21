<?php

namespace app\admin\controller;

use app\admin\model\Book;
use app\admin\model\BookCountPrice;
use app\admin\model\BookStage;
use app\admin\model\BookStagePrice;
use think\Controller;
use think\Request;

class Books extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_book' => 'am-in',   //展开
            '_book' => 'am-active',   //高亮
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
                $query->where('BOOKNAME', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('MAJORID', $request->param('category_id'));
            }
        };

        $books = Book::with('bookpro')->where($where)->order('RECOMMENDSORT')->order('CREATTIME DESC')->paginate(10, false, ['query' => request()->param()]);
//        return json($books);
        $count = $books->total();
        //返回搜索条件,然后在前端进行 empty +if 判断显示出来
        $condition = $request->param();
//        return json($teachers);
        return view('book/index', compact('books', 'count', 'condition'));
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('book/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('BookValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = Book::create([
            'BOOKNAME' => $request->param('BOOKNAME'),
            'BRIEF' => $request->param('BRIEF'),
            'MAJORID' => $request->param('MAJORID'),
            'RECOMMENDSORT' => $request->param('RECOMMENDSORT'),
            'PRICE' => $request->param('PRICE'),
            'STATE' => $request->param('STATE'),
            'IMGURL' => $request->param('imgUrl'),
            'DETAILSIMGURL' => $request->param('detailsImgUrl'),
            'DETAILS' => $request->param('DETAILS'),
        ]);
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，新增成功', 'index', '', 1);
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
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $book = Book::find($id);
        return view('book/edit', compact('book'));
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
        $book = Book::find($id);
        $validate = Validate('BookValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $book->save([
            'BOOKNAME' => $request->param('BOOKNAME'),
            'BRIEF' => $request->param('BRIEF'),
            'MAJORID' => $request->param('MAJORID'),
            'RECOMMENDSORT' => $request->param('RECOMMENDSORT'),
            'PRICE' => $request->param('PRICE'),
            'STATE' => $request->param('STATE'),
            'IMGURL' => $request->param('imgUrl'),
            'DETAILSIMGURL' => $request->param('detailsImgUrl'),
            'DETAILS' => $request->param('DETAILS'),
        ]);
//        return redirect('index');
//        $this->success('恭喜您，更新成功了', 'index');
        if ($result) {
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('恭喜您，更新成功', 'index', '', 1);
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
        //
    }

    //多条删除
    public function delete_all(Request $request)
    {
        $ids = $request->param();
//        return json($ids);
        Book::destroy($ids['checked_id']);
    }

    //排序
    public function sort_order(Request $request)
    {
        $id = $request->param('id');
        $sort_order = $request->param('sort_order');
//        return $sort_order;
        Book::where("ID", $id)->setField('RECOMMENDSORT', $sort_order);
    }

    //设置选择项页面
    public function editstage($id)
    {
        $book = Book::find($id);
        $stages = BookStage::where("bookId", $id)->order('recommendSort asc')->select();
        return view('book/editstage', compact('book', 'stages'));
    }

    //保存选择项
    public function savestage(Request $request)
    {
        $sort_order = $request->param('recommendSort');
        $stageid = $request->param('id');
        $name = $request->param('stageName');
        $bookid = $request->param('bookId');
        if ($request->isAjax()) {
            if ($name == '') {
                $info = array('code' => 0, 'msg' => '名称不能为空');
                return json($info);
            }
            if ($sort_order == '') {
                $info = array('code' => 0, 'msg' => '排序不能为空');
                return json($info);
            }
            $onlystage = BookStage::where(['stageName' => $name, 'bookId' => $bookid])->find();
            if ($onlystage and !$stageid) {
                $info = array('status' => 0, 'msg' => '该规则已存在,请不要重复添加!');
                return json($info);
            }
            if ($stageid) {
//                $onlystage = LiveStage::where(['stageName' => $name, 'liveRoomId' => $liveroomid,'recommendSort'=>$sort_order])->find();
//                if($onlystage){
//                    $info = array('status' => 0, 'msg' => '该规则已存在,请不要重复添加!');
//                    return json($info);
//                }
                $result = BookStage::update($request->param());
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
            } else {
                $result = BookStage::create($request->param());
                if ($result) {
                    $info = array('status' => 1, 'msg' => '创建成功');
                } else {
                    $info = array('status' => 0, 'msg' => '创建失败');
                }
            }
            return json($info);
        }
    }

    //删除阶段
    public function deletestage(Request $request, $id)
    {
        if ($request->isAjax()) {
            $result = BookStage::destroy($id);
            if ($result) {
                $info = array('status' => 1, 'msg' => '删除成功');
            } else {
                $info = array('status' => 0, 'msg' => '删除失败');
            }
            return json($info);
        }
    }

    //设置价格首页
    public function editprice($id)
    {
        $book = Book::find($id);
        $stages = BookStage::where("bookId", $id)->order('recommendSort asc')->select();
        foreach ($stages as $key => $value) {
            //查出阶段价格
            $ids = $value['id'];
            $price = BookStagePrice::where('stageId', $ids)->value('price');
            $stages[$key]['price'] = $price;
        }
        $counts = BookCountPrice::where('bookId', $id)->select();
        return view('book/editprice', compact('book', 'stages', 'counts'));
    }

    //更新选择项价格
    public function updatestageprice(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id');
//            return json($request->param());
            if ($request->param('price') == "") {
                $info = array('status' => 0, 'msg' => '价格不能为空');
                return json($info);
            }
            if (!is_numeric($request->param('price'))) {
                $info = array('status' => 0, 'msg' => '价格只能是数字');
                return json($info);
            }
            $bookstageprice = BookStagePrice::where('stageId', $id)->find();
            if ($bookstageprice) {
                $result = BookStagePrice::where('stageId', $id)->setField('price', $request->param('price'));
//                return json($result);
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            } else {
                $result = BookStagePrice::create(['stageId' => $id, 'price' => $request->param('price')]);
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

    //更改数量优惠价
    public function savecountprice(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $bookid = $request->param('bookId');
            $id = $request->param('id');
            $stages = BookStage::where('bookId', $bookid)->count();
            //判断逻辑,先判断权限阶段是否存在,存在就继续判断是否存在发送过来的id,存在就是更新,不存在就是新建,然后判断输入数量是否超出范围,没有超出范围就继续执行常规操作.
            //注,新建的时候需要判断规则是否存在,更新暂时有纰漏
            if ($stages > 0) {
                if (!$id) {
                    if ($request->param('count') < $stages + 1 and $request->param('count') > 1) {
                        $onlycountprice = BookCountPrice::where(['bookId' => $bookid, 'count' => $request->param('count')])->find();
                        if ($onlycountprice) {
                            $info = array('status' => 0, 'msg' => '该规则已存在!');
                            return json($info);
                        }
                        if ($request->param('price') == "") {
                            $info = array('status' => 0, 'msg' => '价格不能为空');
                            return json($info);
                        }
                        if (!is_numeric($request->param('price'))) {
                            $info = array('status' => 0, 'msg' => '价格只能是数字');
                            return json($info);
                        }
                        $result = BookCountPrice::create($request->param());
                        if ($result) {
                            $info = array('status' => 1, 'msg' => '插入成功');
                        } else {
                            $info = array('status' => 0, 'msg' => '插入失败');
                        }
                    } else {
                        $info = array('status' => 0, 'msg' => "单次购买数量超过限制(必须是2到$stages)");
                    }
                    return json($info);
                } else {
                    if ($request->param('count') < $stages + 1 and $request->param('count') > 1) {
                        if ($request->param('price') == "") {
                            $info = array('status' => 0, 'msg' => '价格不能为空');
                            return json($info);
                        }
                        if (!is_numeric($request->param('price'))) {
                            $info = array('status' => 0, 'msg' => '价格只能是数字');
                            return json($info);
                        }
                        $result = BookCountPrice::update($request->param());
                        if ($result) {
                            $info = array('status' => 1, 'msg' => '更新成功');
                        } else {
                            $info = array('status' => 0, 'msg' => '更新失败');
                        }
                        return json($info);
                    } else {
                        $info = array('status' => 0, 'msg' => "单次购买数量超过限制(必须是2到$stages)");
                    }
                    return json($info);
                    return json($request->param());
                }
            } else {
                $info = array('status' => 0, 'msg' => '请先添加选择项');
                return json($info);
            }
        }
    }
}
