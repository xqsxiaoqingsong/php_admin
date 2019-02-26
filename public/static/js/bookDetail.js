$(function () {
  var goodsId = getUrlParam('id');
  var userId = '';
  getBookDetail();

  // 获取产品数据
  function getBookDetail() {
    var goodsData = {
      id: goodsId,
      memberId: userId,
    }
    $.ajax({
      url: urlBookDetail,
      type: "POST",
      data: goodsData,
      success: function (res) {
        console.log(res)
        var dataBookDetail = res.Data;
        $('.buy-content .img-box img').attr('src', dataBookDetail.imgUrl)
        $('.buy-des .title').text(dataBookDetail.bookName)
        $('.buy-des p').text(dataBookDetail.brief)
        $('.price-table .real-price').text('￥' + dataBookDetail.realPrice)
        if (dataBookDetail.originalPrice != 0) {
          $('.price-table .original-price').text('￥' + dataBookDetail.originalPrice)
        } else {
          $('.price-table .real-price').css({
            'line-height': '36px'
          });
        }

        // 购买情况
        if (dataBookDetail.pay != '1') {
          $('.buy-btns .buy-now').show();
          $('.buy-btns .buy-done').hide();
          $('.buy-btns .buy-ask').show();
        } else {
          $('.buy-btns .buy-done').show().siblings().hide();
        }

        // 课程介绍渲染
        // if (dataBookDetail.details){
        //   var olDeatil=dataBookDetail.details;
        //   var bookDesData = escape2Html(olDeatil)
        //   $('.escape-box').html(bookDesData).show();
        // }
      }
    });
  }


})