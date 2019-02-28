<?php

namespace app\admin\controller;

use app\admin\model\Face;
use app\admin\model\FaceStage;
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
    public function index(Request $request)
    {
        $where = function ($query) use ($request) {
            //按名称
            if ($request->param('name') and $request->param('name') != '') {
                $search = "%" . $request->param('name') . "%";
                $query->where('COURSENAME', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('MAJORID', $request->param('category_id'));
            }
            //按地址
            if ($request->param('province') and $request->param('province') != '') {
                $search = "%" . $request->param('province') . "%";
                $query->where('FACETRAINADDRESS', 'like', $search);
            }
            //按地址
            if ($request->param('city') and $request->param('city') != '') {
                $search = "%" . $request->param('city') . "%";
                $query->where('FACETRAINADDRESS', 'like', $search);
            }
        };

        $faces = Face::with('facepro')->where($where)->order('CREATETIME desc')->paginate(10, false, ['query' => request()->param()]);
//        return json($faces);
        $count = $faces->total();
        $condition = $request->param();
        return view('face/index', compact('faces', 'count','condition'));
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
//        return json($request->param());
        $validate = Validate('FaceValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
        $province = $request->param('province');
        $city = $request->param('city');
        $address ="$province".','."$city";
//        return 123;
        $result = Face::create([
            'MAJORID' => $request->param('MAJORID'),
            'COURSENAME' => $request->param('COURSENAME'),
            'FACETRAINDETAILS' => $request->param('FACETRAINDETAILS'),
            'COUNSELPHONE' => $request->param('COUNSELPHONE'),
            'RECOMMENDSORT' => $request->param('RECOMMENDSORT'),
            'FACETRAINADDRESS' => $address,
            'IMGURL' => $request->param('imgUrl'),
            'DETAILSIMGURL' => $request->param('detailsImgUrl'),
            'FACETRAINWORD' => $request->param('FACETRAINWORD'),
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
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $face = Face::with('facepro')->find($id);
        $address = explode(',',$face['FACETRAINADDRESS']);
        $province = $address[0];
        $city = $address[1];
        $stages = FaceStage::where('faceId',$id)->select();
//        return json($face);
        return view('face/read', compact('face','province','city','stages'));
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $face = Face::find($id);
        $address = explode(',',$face['FACETRAINADDRESS']);
        $province = $address[0];
        $city = $address[1];
        $stages = FaceStage::where('faceId',$id)->select();
//        return json($face);
        return view('face/edit', compact('face','province','city','stages'));
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
//        return json($request->param());
        $face = Face::find($id);
        $validate = Validate('FaceValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };
        $province = $request->param('province');
        $city = $request->param('city');
        $address ="$province".','."$city";
        $result = $face->save([
            'MAJORID' => $request->param('MAJORID'),
            'COURSENAME' => $request->param('COURSENAME'),
            'FACETRAINDETAILS' => $request->param('FACETRAINDETAILS'),
            'COUNSELPHONE' => $request->param('COUNSELPHONE'),
            'RECOMMENDSORT' => $request->param('RECOMMENDSORT'),
            'FACETRAINADDRESS' => $address,
            'IMGURL' => $request->param('imgUrl'),
            'DETAILSIMGURL' => $request->param('detailsImgUrl'),
            'FACETRAINWORD' => $request->param('FACETRAINWORD'),
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

    //保存选择项
    public function savestage(Request $request)
    {
//        return json($request->param());
        $price = $request->param('price');
        $stageid = $request->param('id');
        $name = $request->param('name');
        $faceid = $request->param('faceId');
        if ($request->isAjax()) {
            if ($name == '') {
                $info = array('code' => 0, 'msg' => '名称不能为空');
                return json($info);
            }
            if ($price == '') {
                $info = array('code' => 0, 'msg' => '价格不能为空');
                return json($info);
            }
            $onlystage = FaceStage::where(['name' => $name, 'faceId' => $faceid])->find();
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
                $result = FaceStage::update($request->param());
                if ($result) {
                    $info = array('status' => 1, 'msg' => '更新成功');
                } else {
                    $info = array('status' => 0, 'msg' => '更新失败');
                }
            } else {
                $result = FaceStage::create($request->param());
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
            $result = FaceStage::destroy($id);
            if ($result) {
                $info = array('status' => 1, 'msg' => '删除成功');
            } else {
                $info = array('status' => 0, 'msg' => '删除失败');
            }
            return json($info);
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
        $result = Face::destroy($id);
        if ($result) {
            $info = array('status' => 1, 'msg' => '删除成功');
//            FaceStage::where('faceId',$id)->delete();
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }

    //多条删除
    public function delete_all(Request $request)
    {
        $ids = $request->param();
//        return json($ids);
        $result = Face::destroy($ids['checked_id']);
        if ($result) {
            $info = array('status' => 1, 'msg' => '删除成功');
//            FaceStage::where('faceId',$ids['checked_id'])->delete();
        } else {
            $info = array('status' => 0, 'msg' => '删除失败');
        }
        return json($info);
    }
}
