<?php
//配置文件
$baseUrl = 'https://api.xfxerj.com';

return [

// 登录
'urlLogin' => $baseUrl . '/wrdp-web/face/memberPC/login',
// 获取个人信息
'urlUserInfo' => $baseUrl . '/wrdp-web/face/memberPC/showInfo',

// 获取主页nav-list
'urlNavList' => $baseUrl . '/wrdp-web/face/cnmedicineMajorPC/showCnmedicineMajor',
// 获取所有专业
'urlShowCnmedicineMajor' => $baseUrl . '/wrdp-web/face/cnmedicineMajorPC/showMajorNojkgls',
// 获取主页列表
'urlHomeList' => $baseUrl . '/wrdp-web/face/homePagePC/PChomePage',
// 获取主页在线课程列表
'urlHomeOnlineList' => $baseUrl . '/wrdp-web/face/homePagePC/PChomeCours',
// 获取主页图书资源列表
'urlHomeBookList' => $baseUrl . '/wrdp-web/face/homePagePC/PChomeBook',
// 获取主页名师列表
'$urlHomeTeacherListurlHomeTeacherList' => $baseUrl . '/wrdp-web/face/TeacherPC/showTeacher',

// 获取线上课程列表
'urlOnlineList' => $baseUrl . '/wrdp-web/face/coursePC/courseList',
// 获取图书列表
'urlBooksList' => $baseUrl . '/wrdp-web/face/bookPC/showBooksAll',
// 获取面授列表
'urlFtfList' => $baseUrl . '/wrdp-web/face/faceTrainPC/showFaceTrainList',
// 获取直播教室列表
'urlLiveList' => $baseUrl . '/wrdp-web/face/livePC/showLiveRoom',
// 获取已支付的直播（需先登录）
'urlPayLive' => $baseUrl . '/wrdp-web/face/liveRoomPC/shouLiveRoomNotice',
// 获取直播预告列表
//$urlPreLive => $baseUrl . 'xxx';

// 获取线上课程详情
'urlOnlineDetail' => $baseUrl . '/wrdp-web/face/coursePC/courseDetails',
// 获取线上课程目录
'urlOnlineDetailCatalog' => $baseUrl . '/wrdp-web/face/OnlineCoursePC/courseManageList',
// 获取图书详情
'urlBookDetail' => $baseUrl . '/wrdp-web/face/bookPC/showBookInfo',
// 获取面授培训详情
'urlFtfDetail' => $baseUrl . '/wrdp-web/faceTrain/showFaceClasseParticulars',
// 我的面授报名列表
'urlFaceEnrollList' => $baseUrl . '/wrdp-web/face/faceEnrollPC/faceEnrollList',
// 获取直播详情
'urlLiveDetail' => $baseUrl . '/wrdp-web/face/livePC/showLiveRoomInfo',
// 获取直播回放和预告列表
'urlLiveBroadcastList' => $baseUrl . '/wrdp-web/face/liveRoomPC/showLiveBroadcastList',
// 获取考务资讯类型
'urlZiXunTypeList' => $baseUrl . '/wrdp-web/face/educmanagePC/ziXunTypeList',
// 获取考务资讯列表
'urlExaminationList' => $baseUrl . '/wrdp-web/face/educmanagePC/ziXunList',
// 获取主题页列表
'themeList' => $baseUrl . '/wrdp-web/face/homePagePC/homePage',

'_LiveClassList' => $baseUrl . '/wrdp-web/face/livePC/liveClass',
'_LiveRoomUrl'  =>$baseUrl . '/wrdp-web/face/livePC/classInfo',

'_ClassList' =>$baseUrl. '/wrdp-web/face/coursePC/catalog',
];