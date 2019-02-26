$(function () {
  var goodsId = getUrlParam('id');
  var userId = '';

  // getExamDetail();

  // 获取产品数据
  function getExamDetail() {
    var goodsData = {
      id: goodsId,
      memberId: userId,
    }
    $.ajax({
      url: urlFtfDetail,
      type: "POST",
      data: goodsData,
      success: function (res) {
        console.log(res)
        var dataFtfDetail = res.Data;
        $('.buy-des .title').text(dataFtfDetail.title)
        $('.buy-des p').text(dataFtfDetail.faceTrainDetails)

        $('.escape-box').show();
        // 课程介绍渲染
        if (dataFtfDetail.faceTrainWord){
//        var olDeatil=dataFtfDetail.faceTrainWord + `<head><style>img{max-width:100% !important;} table{max-width:100% !important;}</style></head>`
          var olDeatil=dataFtfDetail.faceTrainWord;
          var bookDesData = escape2Html(olDeatil)
          $('.escape-box').html(bookDesData).show();
        }
      }
    });
  }

})