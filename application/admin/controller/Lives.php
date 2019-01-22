<?php

namespace app\admin\controller;

use app\admin\model\Live;
use app\admin\model\LiveCountprice;
use app\admin\model\LiveStage;
use app\admin\model\LiveStageprice;
use app\admin\model\LiveUrl;
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
    public function index(Request $request)
    {
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('liveRoomName', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('majorId', $request->param('category_id'));
            }
        };
        $lives = Live::with('livepro')->where($where)->order('recommendSort asc')->paginate(10, false, ['query' => request()->param()]);
        $count = $lives->total();
        $condition = $request->param();
//        return json($lives);
        return view('live/index', compact('lives', 'count', 'condition'));
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
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('LiveRoomValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
//        return json($request->param());
        $result = Live::create($request->param());
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
        $live = Live::find($id);
        $stages = LiveStage::where('liveRoomId', $id)->order('recommendSort asc')->select();
        return view('/live/edit', compact('live', 'stages'));
    }

    //编辑阶段页面
    public function editstage($id)
    {
        $live = Live::find($id);
        $stages = LiveStage::where('liveRoomId', $id)->order('recommendSort asc')->select();
        return view('/live/createstage', compact('live', 'stages'));
    }

    //保存阶段
    public function savestage(Request $request)
    {
//        return json($request->param());
        $sort_order = $request->param('recommendSort');
        $stageid = $request->param('id');
        $name = $request->param('stageName');
        $liveroomid = $request->param('liveRoomId');
        if ($request->isAjax()) {
            if ($sort_order == '') {
                $info = array('code' => 0, 'msg' => '排序不能为空');
                return json($info);
            }
            if ($name == '') {
                $info = array('code' => 0, 'msg' => '名称不能为空');
                return json($info);
            }
            $onlystage = LiveStage::where(['stageName' => $name, 'liveRoomId' => $liveroomid])->find();
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
                $result = LiveStage::update($request->param());
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
            } else {
                $result = LiveStage::create($request->param());
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
            $result = LiveStage::destroy($id);
            if ($result) {
                $info = array('status' => 1, 'msg' => '删除成功');
            } else {
                $info = array('status' => 0, 'msg' => '删除失败');
            }
            return json($info);
        }
    }

    //编辑价格页面
    public function editprice($id)
    {
        $live = Live::find($id);
        $stages = LiveStage::where('liveRoomId', $id)->order('recommendSort asc')->select();
        foreach ($stages as $key => $value) {
            //查出阶段价格
            $ids = $value['id'];
            $price = LiveStageprice::where('stageId', $ids)->value('price');
            $stages[$key]['price'] = $price;
        }
        $counts = LiveCountprice::where('liveRoomId', $id)->select();
//        return json($counts);
        return view('/live/createprice', compact('live', 'stages', 'counts'));
    }

    //更新阶段价格
    public function updatestageprice(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id');
//            return json($request->param());
            if ($request->param('price') == "") {
                $info = array('status' => 0, 'msg' => '价格不能为空');
                return json($info);
            }
            $livestageprice = LiveStageprice::where('stageId', $id)->find();
            if ($livestageprice) {
                $result = LiveStageprice::where('stageId', $id)->setField('price', $request->param('price'));
//                return json($result);
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
                return json($info);
            } else {
                $result = LiveStageprice::create(['stageId' => $id, 'price' => $request->param('price')]);
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

    //保存数量价格
    public function savecountprice(Request $request)
    {
        if ($request->isAjax()) {
//            return json($request->param());
            $liveroomid = $request->param('liveRoomId');
            $id = $request->param('id');
            $stages = LiveStage::where('liveRoomId', $liveroomid)->count();
            //判断逻辑,先判断权限阶段是否存在,存在就继续判断是否存在发送过来的id,存在就是更新,不存在就是新建,然后判断输入数量是否超出范围,没有超出范围就继续执行常规操作.
            //注,新建的时候需要判断规则是否存在,更新暂时有纰漏
            if ($stages > 0) {
                if (!$id) {
                    if ($request->param('count') < $stages + 1 and $request->param('count') > 1) {
                        $onlycountprice = LiveCountprice::where(['liveRoomId' => $liveroomid, 'count' => $request->param('count')])->find();
                        if ($onlycountprice) {
                            $info = array('status' => 0, 'msg' => '该规则已存在!');
                            return json($info);
                        }
                        if ($request->param('price') == "") {
                            $info = array('status' => 0, 'msg' => '价格不能为空');
                            return json($info);
                        }
                        $result = LiveCountprice::create($request->param());
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
                        $result = LiveCountprice::update($request->param());
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
                $info = array('status' => 0, 'msg' => '请先添加权限阶段');
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
//        return json($request->param());
        $live = Live::find($id);
//        return json($request->param());
        $validate = Validate('LiveRoomValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $result = $live->update($request->param());
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
        //
    }

    //多条删除
    public function delete_all(Request $request)
    {
        $ids = $request->param('checked_id');
        foreach ($ids as $id){
            $liveurl = LiveStage::where("liveRoomId",$id)->find();
        }
        if ($liveurl){
            $info = array('status' => 0, 'msg' => '此直播间下面有阶段,删除失败,');
            return json($info);
        }
//        return json($ids);
        if (Live::destroy($ids)) {
            $info = array('status' => 1, 'msg' => '删除成功');
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }

    //排序
    public function sort_order(Request $request)
    {
        $id = $request->param('id');
        $sort_order = $request->param('sort_order');
//        return $sort_order;
        Live::where("id = '$id'")->setField('recommendSort', $sort_order);
    }

    //是否显示
    public function change_show(Request $request)
    {
//        $product = Product::find($request->id);
//        $attr = $request->attr;
//        $product->$attr = !$product->$attr;
//        $product->save();

        $id = $request->param('id');
        $show = !$request->param('attr');
//        return json($show);
//        return $show;
        Live::where('id', $id)->setField('state', $show);
    }
}
