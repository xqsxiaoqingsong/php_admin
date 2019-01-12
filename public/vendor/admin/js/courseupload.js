// 设置事件绑定按钮
$(".upload_img").click(function () {
    $("#image_upload").click();
});

//文件上传
var opts = {
    url: "/admin/Uploadfile/upload",
    type: "POST",
    beforeSend: function () {
        $("#loading").attr('class', 'am-icon-spinner am-icon-spin');
    },
    success: function (result, status, xhr) {
        // console.log(result);return false;
        if (result.status == 0) {
            alert(result.msg);
            return false;
        }
        //返回js返回的值
        $("input[name='imgUrl']").val(result.msg);

        // console.log(result);
        $("#img_show").attr("src", result.msg);
        $("#loading").attr('class', 'am-icon-cloud-upload');
    },
    error: function (result, status, errorThrown) {
        alert('文件上传失败');
        $("#loading").attr('class', 'am-icon-cloud-upload');
    }
};

$('#image_upload').fileUpload(opts);