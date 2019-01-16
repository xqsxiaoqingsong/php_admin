<?php

namespace app\admin\controller;

use app\admin\model\Live;
use app\admin\model\LiveStage;
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
        $lives = Live::with('livepro')->where($where)->paginate(10, false, ['query' => request()->param()]);
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
        return view('/live/edit', compact('live', 'content'));
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
        //
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
