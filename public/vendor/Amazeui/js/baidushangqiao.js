//百度商桥启用代码
var _hmt = _hmt || [];
(function () {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?410b158c572c7d0c12f441e5b20fae3b";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();

//点击按钮时判断 百度商桥代码中的“我要咨询”按钮的元素是否存在，存在的话就执行一次点击事件<a class="shangqiao" href="javascript:void(0);">我要咨询</a>
$(".shangqiao").on('click',function () {
    if ($('#nb_invite_ok').length > 0) {
        $('#nb_invite_ok').click();
    }
});