$(function () {
  // 进入页面默认运行
  var urlId = getUrlParam('id')?getUrlParam('id'):null;
  //getShowCnmedicineMajor();
  // 获取所有专业
  function getShowCnmedicineMajor(page) {
    $.ajax({
      url: urlShowCnmedicineMajor,
      type: "POST",
      data: {
        page: 1
      },
      success: function (res) {
        // console.log(res);
        var dataMajorList = res.Data;

        //获取模板
        var majorBox = $(".js-major-id");
        // var majorItem = majorBox.find("li");
        majorBox.empty();

        for (var i in dataMajorList) {
          var majorItem = $('<li data-id="' + dataMajorList[i].id + '">' + dataMajorList[i].majorName + '</li>');
          majorBox.append(majorItem);
        }

        // 给元素绑定事件，根据图书内容id，获取列表数据
        $('.js-major-id li').click(function () {
          $(".js-major-id").find('li').removeClass('active');
          $(this).addClass('active').siblings().removeClass('active');
          var majorId = $(this).attr('data-id');
          // console.log(majorId);
          getLiveList(majorId);
        })
        // 给第一个按钮添加类名，设置默认
        if(urlId){
          $(".js-major-id").find('li[data-id='+urlId+']').addClass('active');
        }else{
          $(".js-major-id").find('li').eq(0).addClass('active');
        }
      }
    });
  }

  // 进入页面自动运行
  //getLiveList(urlId);
  // 获取列表数据
  function getLiveList(majorId) {
    var filterData = {
      majorId: majorId || 1,
    }
    $.ajax({
      url: urlLiveList,
      type: "POST",
      data: filterData,
      success: function (res) {
           console.log(res)
        if (res.Data.length != 0) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.live-carousel .blank').hide();
          $('.js-col-box').show();

          var dataLiveList = res.Data;
          if (dataLiveList.length <= 4) {
            $('.live-carousel .iconfont').hide();
          } else {
            $('.live-carousel .iconfont').show();
          }
          // 轮播图
          var total = res.Data.length;
          var slideNum = Math.ceil(total / 4);

          // 获取模板
          var liveBox = $(".js-col-box");
          var slideModel = liveBox.find(".item").eq(0).removeClass('active');
          var itemModel = liveBox.find(".js-col-item").eq(0);

          slideModel.empty();
          liveBox.empty();
          // 渲染列表
          for (var i = 0; i < slideNum; i++) {
            var slideItem = slideModel.clone(false);
            for (var j in dataLiveList) {
              var colItem = itemModel.clone(false);
              if (j >= i * 4 && j < (i + 1) * 4) {
                // 1234 5678
                colItem.find(".imgbox img").attr('src', dataLiveList[j].imgUrl);
                colItem.find('.content-col').attr('data-id', dataLiveList[j].id);
                colItem.find(".title").text(dataLiveList[j].liveRoomName);
                colItem.find(".real-price-number").text('￥' + dataLiveList[j].activityPrice);
                if (dataLiveList[j].originalPrice != 0) {
                  item.find(".original-price").text('￥' + dataLiveList[j].originalPrice);
                }
                colItem.find(".class-number").text('课时:' + dataLiveList[j].classNumber);
                // 给元素绑定事件
                colItem.find('.content-col').click(function () {
                  liveDetailPath($(this).attr("data-id"));
                });
                slideItem.append(colItem);
              }
            }
            liveBox.append(slideItem);
          }
          // 轮播图不渲染的原因是得有一个 item 存在 active 的 className
          // 那么渲染的时候也要先去掉 active
          liveBox.find(".item").eq(0).addClass('active').show();

        } else {
          $('.live-carousel .blank').show();
          $('.js-col-box').hide();
          $('.live-carousel .iconfont').hide();
        }
      }
    });
  }

  // 获取已购买列表，需登录


  // 获取直播预告列表
  function getPreLiveList() {
    var filterData = {
      // page: pageNo,
      // majorId: majorId,
      // charge: chargeType
    }

    $.ajax({
      url: urlPreLive,
      type: "POST",
      data: filterData,
      success: function (res) {
        console.log(res)
        if (res.Data) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.pre-live-content .blank').hide();
          $('.js-col-box').show();

          // var dataOnlineList = res.Data.courseManage;
          // var total = res.Data.total;

          // $('.total-number').text('共找到 ' + total + ' 门课程');

          // //获取模板
          // var onlineBox = $(".js-col-box");
          // var onlineItem = onlineBox.find(".js-col-item").eq(0);
          // onlineBox.empty();
          // // 渲染列表
          // for (var i in dataOnlineList) {
          //   var item = onlineItem.clone(false);
          //   item.find(".imgbox img").attr('src', dataOnlineList[i].imgUrl);
          //   item.attr('data-id', dataOnlineList[i].id);
          //   item.find(".title").text(dataOnlineList[i].title);
          //   item.find(".real-price-number").text('￥' + dataOnlineList[i].activityPrice);
          //   if (dataOnlineList[i].originalPrice != 0) {
          //     item.find(".original-price").text('￥' + dataOnlineList[i].originalPrice);
          //   }
          //   item.find(".class-number").text('课时:' + dataOnlineList[i].classNumber);
          //   item.find(".speaker").text('主讲人：' + dataOnlineList[i].speaker);
          //   item.find(".learn-members").text(dataOnlineList[i].learnMembers + '人在学');
          //   // 给元素绑定事件
          //   item.click(function () {
          //     onlineDetailPath($(this).attr("data-id"));
          //   });
          //   onlineBox.append(item);
          // }

          // // 计算分页总页数
          // var pages = Math.ceil(total / 16);
          // if (pages > 1) {
          //   $('.pagination').show();
          //   // 渲染分页
          //   createPaginate(pageNo, pages);
          // } else {
          //   $('.pagination').hide();
          // }

        } else {
          $('.pre-live-content .blank').show();
          $('.js-col-box').hide();
          $('.pagination').hide();
          $('.total-number').text('没有找到！');
        }
      }
    })
  }

  // 直播预告分页
  // 预告没有接口，分页逻辑暂时还不清晰
  function createPaginate(pageNo, pages) {
    $('.pagination').pagination({
      // totalData：数据总条数（默认值：0）
      // showData：每页展示条数（默认值：0）
      // count：当前页前后页数（默认值：3）
      current: pageNo, // current：当前第几页（默认值：1）
      pageCount: pages, // pageCount：总页数（默认值：9）
      jump: false,
      isHide: true,
      coping: true,  // coping：是否开启首页尾页（默认值：false）
      keepShowPN: true,   //是否一直显示上一页下一页
      prevContent: '上页',// prevContent：上一页节点内容（默认值：'<'）
      nextContent: '下页',
      homePage: '首页',
      endPage: '末页',
      callback: function (api) {
        // api.getCurrent() 获取当前页
        console.log('当前页码 '+api.getCurrent());
        var pageIndex = api.getCurrent();
        // var majorId = $('.js-major-id').find('.active').attr('data-id');
        // var chargeType = $('.js-charge-type').find('.active').attr('data-id');
        // 获取数据
        // getOnline(pageIndex, majorId, chargeType);
        // 返回顶部
        // goTop($(this)[0], 300);
      }
    });
  }



})