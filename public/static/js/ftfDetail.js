$(function () {
  var goodsId = getUrlParam('id');
  var userId = '';
  getFtfDetail();

  // 获取产品数据
  function getFtfDetail() {
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
        $('.buy-content .img-box img').attr('src', dataFtfDetail.pictureUrl)
        $('.buy-des .title').text(dataFtfDetail.title)
        $('.buy-des p').text(dataFtfDetail.faceTrainDetails)
        $('.price-table .real-price').text('￥' + dataFtfDetail.activityPrice)
        if (dataFtfDetail.originalPrice != 0) {
          $('.price-table .original-price').text('￥' + dataFtfDetail.originalPrice)
        } else {
          $('.price-table .real-price').css({
            'line-height': '36px'
          });
        }

        // 购买情况
        if (dataFtfDetail.pay != '1') {
          $('.buy-btns .buy-now').show();
          $('.buy-btns .buy-done').hide();
          $('.buy-btns .buy-ask').show();
        } else {
          $('.buy-btns .buy-done').show().siblings().hide();
        }

        $('.escape-box').show();
        // 课程介绍渲染
//         if (dataFtfDetail.faceTrainWord){
// //        var olDeatil=dataFtfDetail.faceTrainWord + `<head><style>img{max-width:100% !important;} table{max-width:100% !important;}</style></head>`
//           var olDeatil=dataFtfDetail.faceTrainWord;
//           var bookDesData = escape2Html(olDeatil)
//           $('.escape-box').html(bookDesData).show();
//         }
      }
    });
  }

})