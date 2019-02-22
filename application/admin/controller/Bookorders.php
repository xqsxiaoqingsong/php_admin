<?php

namespace app\admin\controller;

use app\admin\model\BookOrder;
use think\Controller;
use think\Db;
use think\Request;

class Bookorders extends Common
{
    public function __construct()
    {
        //分类共享
        parent::__construct();
        return $this->assign([
            '_xfx_book' => 'am-in',   //展开
            '_bookorder' => 'am-active',   //高亮
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
                $query->where('c.BOOKNAME', 'like', $search);
            }
            //按学员姓名
            if ($request->param('username') and $request->param('username') != '') {
                $search = "%" . $request->param('username') . "%";
                $query->where("d.REALNAME", 'like', $search);
            }
            //按学员电话
            if ($request->param('phone') and $request->param('phone') != '') {
                $search = "%" . $request->param('phone') . "%";
                $query->where('c.PHONE', 'like', $search);
            }
            //按创建时间
            if ($request->param('startdate') and $request->param('enddate')) {
                $query->whereBetweenTime('c.creatTime', $request->param('startdate'), $request->param('enddate'));
            }
            if ($request->param('startdate') and $request->param('enddate') == '') {
                $query->whereTime('c.creatTime', '>=', $request->param('startdate'));
            }
            if ($request->param('startdate') == '' and $request->param('enddate')) {
                $query->whereTime('c.creatTime', '<', $request->param('enddate'));
            }
            //按状态
            if ($request->param('category_id') and $request->param('category_id') != "-1") {
                $query->where('c.STATUS', $request->param('category_id'));
            }
            if ($request->param('category_id') == "0" and $request->param('category_id') != "-1") {
                $query->where('c.STATUS', $request->param('category_id'));
            }
        };

//        return json($request->param());
//        $orders = BookOrder::with(['bookorderuser'=>$map])->where($where)->order('CREATTIME DESC')->paginate(10, false, ['query' => request()->param()]);
//        $orders = BookOrder::with('bookorderuser')->where($where)->order('CREATTIME DESC')->paginate(10, false, ['query' => request()->param()]);
        $orders = DB::table('t_booksorder c')
            //设置需要查询的字段,不然主键字段相同
            ->field('c.*,d.REALNAME,d.PHONE as userPHONE')
            ->join("t_member d", "c.MEMBERID=d.ID")
            ->where($where)
            ->order('CREATTIME DESC')
            ->paginate(10, false, ['query' => request()->param()]);
//        return json($orders);
        $count = $orders->total();
        //返回搜索条件,然后在前端进行 empty +if 判断显示出来
        $condition = $request->param();
//        return json($condition);
        //判断是进行导出excel还是展示页面
//        return json($request->param());
        if ($orders and $request->param('export') == 'export') {
            $order = DB::table('t_booksorder c')
                //设置需要查询的字段,不然主键字段相同
                ->field('c.*,d.REALNAME,d.PHONE as userPHONE')
                ->join("t_member d", "c.MEMBERID=d.ID")
                ->where($where)
                ->order('CREATTIME DESC')
                ->select();
            $this->exportorder($order);
        } else {
            return view('bookorder/index', compact('orders', 'count', 'condition'));
        }
//        return json($teachers);
    }

    //更改发货状态
    public function change_show(Request $request)
    {
        //后台循环更改,需要前端传过来的是需要更改的字段名字,而不是值,否则无法做到无刷新交替更改
        $id = $request->param('id');
        $show = $request->param('attr');
        $status = BookOrder::find($id);
        $status->$show = !$status->$show;
        $status->save();
    }

    //导出excel
    public function exportorder($order)
    {
        //导出所有数据
//        $innerdata = DB::table('t_booksorder c')
//            //设置需要查询的字段,不然主键字段相同
//            ->field('c.*,d.REALNAME,d.PHONE as userPHONE')
//            ->join("t_member d", "c.MEMBERID=d.ID")
//            ->order('CREATTIME DESC')
//            ->select();
//        return json($innerdata);

        $table = '';
        $table .= "<table>
            <thead>
                <tr>
                    <th class='name'>编号</th>
                    <th class='name'>图书名称</th>
                    <th class='name'>订单详情</th>
                    <th class='name'>数量</th>
                    <th class='name'>支付订单</th>
                    <th class='name'>学员姓名</th>
                    <th class='name'>学员电话</th>
                    <th class='name'>收货地址</th>
                    <th class='name'>备注信息</th>
                    <th class='name'>创建时间</th>
                    <th class='name'>发货状态</th>
                </tr>
            </thead>
            <tbody>";
        foreach ($order as $v) {
            $status = $v['STATUS'];
            if ($status == '0') {
                $text = '未发货';
                $status = $text;
            } else {
                $text = '已发货';
                $status = $text;
            }
            $table .= "<tr>
                    <td class='name'>{$v['ID']}</td>
                    <td class='name'>{$v['BOOKNAME']}</td>
                    <td class='name'>{$v['STAGEID']}</td>
                    <td class='name'>{$v['NUMBER']}</td>
                    <td class='name'>{$v['ORDERCODE']}</td>
                    <td class='name'>{$v['REALNAME']}</td>
                    <td class='name'>{$v['userPHONE']}</td>
                    <td class='name'>{$v['PROVINCE']}{$v['CITY']}{$v['AREA']}{$v['DETAILADDRESS']}</td>
                    <td class='name'>{$v['REMARK']}</td>
                    <td class='name'>{$v['creatTime']}</td>
                    <td class='name'>{$status}
                            </td>
                </tr>";
        }

        $table .= "</tbody>
        </table>";
//        return json($table);
//通过header头控制输出excel表格
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="图书订单.xls"');
        header("Content-Transfer-Encoding:binary");
        echo $table;
    }

    public function daochu()
    {
//        $head = array('编号','姓名','年龄','出生年月');
//
//        $data = array(
//            array('001','zs',10,'1991-1-1'),
//            array('002','李四',10,'1991-1-1'),
//            array('003','王五',10,'1991-1-1'),
//        );
//        $this->putCsv('putCsv.csv', $data, $head);
    }

    public function putCsv($csvFileName, $data, $head = '', $line = 0, $offset = 0)
    {
        $handle = fopen($csvFileName, "w");//写方式打开
        if (!$handle) {
            return '文件打开失败';
        }
        //判断是否定义头标题
        if (!empty($head)) {
            $re = fputcsv($handle, $head);//该函数返回写入字符串的长度。若出错，则返回 false。。
        }
        foreach ($data as $key => $value) {
            $re = fputcsv($handle, $value);//该函数返回写入字符串的长度。若出错，则返回 false。。
        }
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
    public function save(Request $request)
    {
        //
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
        //
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
}
