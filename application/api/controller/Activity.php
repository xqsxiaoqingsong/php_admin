<?php

namespace app\api\controller;

use app\api\model\Activitydowonload;
use app\api\model\Activityinfo;
use app\api\model\Activityuser;
use think\Controller;
use think\Request;
use think\Validate;

class Activity extends Controller
{
    //添加用户信息
    public function createuserinfo(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许a.com发起的跨域请求
        if ($request->isPost()) {
            $name = $request->param('name');
            $phone = $request->param('phone');
            $province = $request->param('province');
            $major = $request->param('major');
            $type = $request->param('activityType');
            $activityname = $request->param('activityName');
            $validate = Validate('UserValidate');
            if (!$validate->scene('save')->check($request->param())) {
//                $this->error($validate->getError());
//                $info = $this->error();
//                return json($info);
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '1',
                    'Data' => array(),
                    'Msg' => $validate->getError(),
                    'Time' => time());
                return json($info);
            };
            $result = Activityuser::create([
                'USERNAME' => $name,
                'PHONE' => $phone,
                'ADDRESS' => $province,
                'MAJORNAME' => $major,
                'ACTIVITYTYPE' => $type,
                'ACTIVITYNAME' => $activityname,
            ]);
            if ($result) {
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '0',
                    'Data' => array(),
                    'Msg' => '添加成功',
                    'Time' => time());
                return json($info);
            } else {
                $info = array(
                    'ApiUrl' => 'http://test.xfxerj.com/api/activity/createuserinfo',
                    'Code' => '1',
                    'Data' => array('name' => ''),
                    'Msg' => '添加失败',
                    'Time' => time());
                return json($info);
            }
        }
    }

    //下载文件
    public function downloadfile(Request $request)
    {
//        header("Access-Control-Allow-Origin: http://a.com"); // 允许a.com发起的跨域请求
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        header('Access-Control-Expose-Headers: filename="推荐承诺书.doc"');

        $download = new \think\response\Download('推荐承诺书.doc');
        return $download->name('推荐承诺书.doc');
    }

    //选项卡数据
    public function tabinfo(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许a.com发起的跨域请求
        if ($request->isPost()) {
            $id = $request->param('id');
            if ($id) {
                $activitys = Activityinfo::field(['title','titletwo','titlethree'],true)->find($id);
                $titleone = Activityinfo::where('id', $id)->value('title');
                $titletwo = Activityinfo::where('id', $id)->value('titletwo');
                $titlethree = Activityinfo::where('id', $id)->value('titlethree');
                $array1 = array('listId' => 1, 'content' => $titleone);
                $array2 = array('listId' => 2, 'content' => $titletwo);
                $array3 = array('listId' => 3, 'content' => $titlethree);
                $activitys['infolist'] = array_merge(array($array1, $array2, $array3));
                $downloads = Activitydowonload::where('activityId', $id)->select();
                if (isset($activitys)) {
                    $info = array('ApiUrl' => 'http://test.xfxerj.com/api/activity/tabinfo',
                        'Code' => '0',
                        'Data' => array('left' => $activitys, 'right' => $downloads),
                        'Msg' => '获取详情成功',
                        'Time' => time());
                    return json($info);
                } else {
                    $info = array('ApiUrl' => 'http://test.xfxerj.com/api/activity/tabinfo',
                        'Code' => '1',
                        'Data' => '',
                        'Msg' => '获取详情失败,没有相关数据',
                        'Time' => time());
                    return json($info);
                }
            } else {
                $info = array('ApiUrl' => 'http://test.xfxerj.com/api/activity/tabinfo',
                    'Code' => '1',
                    'Data' => '',
                    'Msg' => '获取列表失败,缺少参数id',
                    'Time' => time());
                return json($info);
            }
        }
    }

    //选项卡下载
    //西药1
    public function xiyaoyi1(Request $request)
    {
//        $activitys = Activityinfo::field(['title','titletwo','titlethree'],true)->find(1);
//        $titleone = Activityinfo::where('id', 1)->value('title');
//        $titletwo = Activityinfo::where('id', 1)->value('titletwo');
//        $titlethree = Activityinfo::where('id', 1)->value('titlethree');
//        $array1 = array('listId' => 1, 'content' => $titleone);
//        $array2 = array('listId' => 2, 'content' => $titletwo);
//        $array3 = array('listId' => 3, 'content' => $titlethree);
//        $activitys['infolist'] = array_merge(array($array1, $array2, $array3));
//        $downloads = Activitydowonload::where('activityId', 1)->select();
//        if (isset($activitys)) {
//            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/activity/tabinfo',
//                'Code' => '0',
//                'Data' => array('left' => $activitys, 'rigit' => $downloads),
//                'Msg' => '获取详情成功',
//                'Time' => time());
//            return json($info);
//        } else {
//            $info = array('ApiUrl' => 'http://test.xfxerj.com/api/activity/tabinfo',
//                'Code' => '1',
//                'Data' => '',
//                'Msg' => '获取详情失败,没有相关数据',
//                'Time' => time());
//            return json($info);
//        }

        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoyi/2018考前最后一卷（西药一）.pdf");
        return $download->name('2018考前最后一卷（西药一）.pdf');
    }

    public function xiyaoyi2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoyi/2018年真题（西药一）.pdf");
        return $download->name('2018年真题（西药一）.pdf');
    }

    public function xiyaoyi3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoyi/历年真题.pdf");
        return $download->name('历年真题（西药一）.pdf');
    }

    //西药二
    public function xiyaoer1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoer/2018年真题（西药二）.pdf");
        return $download->name('2018年真题（西药二）.pdf');
    }

    public function xiyaoer2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoer/2018考前最后一卷.pdf");
        return $download->name('2018考前最后一卷（西药二）.pdf');
    }

    public function xiyaoer3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaoer/历年真题.pdf");
        return $download->name('历年真题（西药二）.pdf');
    }

    //法规
    public function fagui1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/fagui/2018考前最后一卷.pdf");
        return $download->name('2018考前最后一卷(法规).pdf');
    }

    public function fagui2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/fagui/2018年真题（法规）.pdf");
        return $download->name('2018年真题（法规）.pdf');
    }

    public function fagui3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/fagui/历年真题.pdf");
        return $download->name('历年真题(法规).pdf');
    }

    //西药综合
    public function xiyaozonghe1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaozonghe/西药综合2018考前最后一卷.pdf");
        return $download->name('西药综合2018考前最后一卷.pdf');
    }

    public function xiyaozonghe2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaozonghe/2018年西药综合真题.pdf");
        return $download->name('2018年西药综合真题.pdf');
    }

    public function xiyaozonghe3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/xiyaozonghe/历年真题（西药综合）.pdf");
        return $download->name('历年真题（西药综合）.pdf');
    }

    //中药1
    public function zhongyaoyi1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoyi/中药一2018年考前最后一卷（含解析）.pdf");
        return $download->name('中药一2018年考前最后一卷（含解析）.pdf');
    }

    public function zhongyaoyi2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoyi/2018年真题（中药一）.pdf");
        return $download->name('2018年真题（中药一）.pdf');
    }

    public function zhongyaoyi3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoyi/历年真题（中药一）.pdf");
        return $download->name('历年真题（中药一）.pdf');
    }

    //中药二
    public function zhongyaoer1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoer/中药二2018年考前最后一卷（含解析）.pdf");
        return $download->name('中药二2018年考前最后一卷（含解析）.pdf');
    }

    public function zhongyaoer2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoer/2018年真题（中药二）.pdf");
        return $download->name('2018年真题（中药二）.pdf');
    }

    public function zhongyaoer3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaoer/历年真题（中药二）.pdf");
        return $download->name('历年真题（中药二）.pdf');
    }

    //中药综合
    public function zhongyaozonghe1(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaozonghe/2018中药综合考前最后一卷（含解析）.pdf");
        return $download->name('2018中药综合考前最后一卷（含解析）.pdf');
    }

    public function zhongyaozonghe2(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaozonghe/2018年真题（中药综合）.pdf");
        return $download->name('2018年真题（中药综合）.pdf');
    }

    public function zhongyaozonghe3(Request $request)
    {
        header("Access-Control-Allow-Origin:*"); // 允许任意域名发起的跨域请求
        $download = new \think\response\Download("download/zhongyaozonghe/历年真题（中药综合）.pdf");
        return $download->name('历年真题（中药综合）.pdf');
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
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
