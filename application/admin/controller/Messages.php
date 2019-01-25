<?php

namespace app\admin\controller;

use app\admin\model\Message;
use app\admin\model\MessageType;
use think\Controller;
use think\Request;

class Messages extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            'types' => MessageType::select(),
            '_xfx_eduadmin' => 'am-in',   //展开
            '_message' => 'am-active',   //高亮
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
            if ($request->param('title') and $request->param('title') != '') {
                $search = "%" . $request->param('title') . "%";
                $query->where('ZIXUNNAME', 'like', $search);
            }
            //按分类搜索
            if ($request->param('category_id') and $request->param('category_id') != '-1') {
                $query->where('MAJORID', $request->param('category_id'));
            }
            //按类型
            if ($request->param('type_id') and $request->param('type_id') != '-1') {
                $query->where('TYPE', $request->param('type_id'));
            }
        };

        $messages =Message::with('messagetype')->with('messagepro')->where($where)->paginate(10, false, ['query' => request()->param()]);
        $count = $messages->total();
        //返回搜索条件,然后在前端进行 empty +if 判断显示出来
        $condition=$request->param();
//        return json($condition);
        return view('message/index', compact('messages', 'count','condition'));
//        return json($messages);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view('message/create');
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $validate = Validate('MessageValidate');
        if (!$validate->scene('save')->check($request->param())) {
            $this->error($validate->getError());
        };
//        return 123;
//        return json($request->param());
        $result = Message::create($request->param());
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
        $message = Message::find($id);
        return view('message/edit', compact('message'));
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
        $message = Message::find($id);
        $validate = Validate('MessageValidate');
        if (!$validate->scene('update')->check($request->param())) {
            $this->error($validate->getError());
        };

//        return json($request->param());
        $result = $message->where('ID',$id)->update([
            'MAJORID' => $request->param('MAJORID'),
            'TYPE' => $request->param('TYPE'),
            'ZIXUNNAME' => $request->param('ZIXUNNAME'),
            'CONTENT' => $request->param('CONTENT'),
            'ZIXUNURL' => $request->param('ZIXUNURL')]);
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
        //这里直接用destroy是因为模型那里设置了主键为大写的ID,如果没有设置主键,那么就要用delete方法
        Message::destroy($id);
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
