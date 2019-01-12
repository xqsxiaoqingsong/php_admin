// 设置事件绑定按钮
$(".upload_img1").click(function () {
    $("#image_upload1").click();
});

//文件上传 原始图
var optsabc = {
    url: "/admin/Uploadfile/upload",
    type: "POST",
    beforeSend: function () {
        $("#loading_img").attr('class', 'am-icon-spinner am-icon-spin');
    },
    success: function (result, status, xhr) {
        // console.log(result);return false;
        if (result.status == 0) {
            alert(result.msg);
            return false;
        }
        //返回js返回的值
        $("input[name='detailsImgUrl']").val(result.msg);

        // console.log(result);
        $("#img_show1").attr("src", result.msg);
        $("#loading_img").attr('class', 'am-icon-cloud-upload');
    },
    error: function (result, status, errorThrown) {
        alert('文件上传失败');
        $("#loading_img").attr('class', 'am-icon-cloud-upload');
    }
};

$('#image_upload1').fileUpload(optsabc);