$(function () {
  var majorId = getUrlParam('id');
  var majorName = decodeURI(getUrlParam('majorName'));

  // 面包屑导航
  //$('.breadcrumb .active').text(majorName);

  // 进入页面默认运行
  //getThemeList();
  // 获取所有列表
  function getThemeList() {
    $.ajax({
      url: themeList,
      type: "POST",
      data: {
        majorId: majorId
      },
      success: function (res) {
        console.log(res);
        var dataList = res.Data;
        for (var i in dataList) {
          if (dataList[i].theme === "横幅") {
            // banner
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(" .js-banner-box .blank").hide();
            } else {
              $(" .js-banner-box .blank").show();
            }
            var dataBannerList = dataList[i].course;
            var bannerBox = $(" .js-banner-box");
            var bannerModel = bannerBox.find(".item");
            bannerBox.empty();

            for (var j in dataBannerList) {
              var bannerItem = bannerModel.clone(false);
              bannerItem.find('img').attr({
                'src': dataBannerList[j].imgUrl,
                'data-url': dataBannerList[j].contentUrl,
                'title': dataBannerList[j].bannerName
              });
              bannerBox.append(bannerItem);

              // 给元素绑定事件
              bannerItem.find('img').click(function () {
                // if ($(this).attr('data-url')) {
                //   window.location.href = $(this).attr('data-url');
                // } else {
                //   window.location.href = '../index.html';
                // }
              })
            }
            // 给第一个item设置className active，激活轮播图
            bannerBox.find('.item').eq(0).addClass('active');

          } else if (dataList[i].theme === "直播教室") {
            // 直播教室
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(" .live-left .blank").hide();
            } else {
              $(" .live-left .blank").show();
            }
            var dataLiveList = dataList[i].course.slice(0, 3);
            var liveBox = $(".js-live-box");
            var LiveModel = liveBox.find(".js-live-item");
            liveBox.empty();

            for (var k in dataLiveList) {
              var liveItem = LiveModel.clone(false);
              liveItem.find('.content-col').attr('data-id', dataLiveList[k].id);
              liveItem.find('img').attr({
                'src': dataLiveList[k].imgUrl
              });
              liveItem.find('.title').text(dataLiveList[k].liveRoomName);
              liveItem.find('.real-price-number').text('￥' + dataLiveList[k].activityPrice);
              // 打折价格判断显示
              if (dataLiveList[k].originalPrice != 0) {
                liveItem.find('.original-price').text('￥' + dataLiveList[k].originalPrice).show();
              }
              liveItem.find('.class-number').text('课时：' + dataLiveList[k].classNumber);
              liveBox.append(liveItem);

              // 给元素绑定事件
              liveItem.find('.content-col').click(function () {
                liveDetailPath($(this).attr('data-id'));
              })
            }

          } else if (dataList[i].theme === "面授培训") {
            // 面授培训
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(" .ftf-content .blank").hide();
            } else {
              $(" .ftf-content .blank").show();
            }
            var dataFtfList = dataList[i].course.slice(0, 3);
            var ftfBox = $(".js-ftf-box");
            var ftfModel = ftfBox.find(".js-ftf-item");
            ftfBox.empty();

            for (var m in dataFtfList) {
              var ftfItem = ftfModel.clone(false);
              ftfItem.find('.content-col').attr('data-id', dataFtfList[m].id);
              ftfItem.find('img').attr({
                'src': dataFtfList[m].pictureUrl
              });
              ftfItem.find('.title').text(dataFtfList[m].courseName);
              ftfItem.find('.real-price-number').text('￥' + dataFtfList[m].activityPrice);
              // 打折价格判断显示
              if (dataFtfList[m].originalPrice != 0) {
                ftfItem.find('.original-price').text('￥' + dataFtfList[m].originalPrice).show();
              }
              ftfBox.append(ftfItem);

              // 给元素绑定事件
              ftfItem.find('.content-col').click(function () {
                ftfDetailPath($(this).attr('data-id'));
              })
            }

          } else if (dataList[i].theme === "考务动态") {
            // 考务动态
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(".exam-content .blank").hide();
            } else {
              $(".exam-content .blank").show();
            }
            var dataExamList = dataList[i].course;
            var examBox = $(".js-exam-box");
            var examModel = examBox.find(".js-exam-item");
            examBox.empty();

            for (var n in dataExamList) {
              var examItem = examModel.clone(false);
              examItem.attr('data-id', dataExamList[n].id);
              examItem.find('.des').text(dataExamList[n].ziXunName);
              examBox.append(examItem);

              // 给元素绑定事件
              examItem.click(function () {
                examDetailPath($(this).attr('data-id'));
              })
            }

          } else if (dataList[i].theme === "推荐课程") {
            // 推荐课程
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(".online-content .blank").hide();
            } else {
              $(".online-content .blank").show();
            }
            var dataOnlineList = dataList[i].course.slice(0, 4);
            var onlineBox = $(".js-online-box");
            var onlineModel = onlineBox.find(".js-online-item");
            onlineBox.empty();

            for (var s in dataOnlineList) {
              var onlineItem = onlineModel.clone(false);
              onlineItem.find('.content-col').attr('data-id', dataOnlineList[s].id);
              onlineItem.find('img').attr({
                'src': dataOnlineList[s].imgUrl
              });
              onlineItem.find('.title').text(dataOnlineList[s].courseName);
              onlineItem.find('.real-price-number').text('￥' + dataOnlineList[s].activityPrice);
              // 打折价格判断显示
              if (dataOnlineList[s].originalPrice != 0) {
                onlineItem.find('.original-price').text('￥' + dataOnlineList[s].originalPrice).show();
              }
              onlineItem.find('.class-number').text('课时：' + dataOnlineList[s].classNumber);
              onlineItem.find('.speaker').text('主讲人：' + dataOnlineList[s].speaker);
              onlineItem.find('.learn-members').text(dataOnlineList[s].learnMembers + '人在学');
              onlineBox.append(onlineItem);

              // 给元素绑定事件
              onlineItem.find('.content-col').click(function () {
                onlineDetailPath($(this).attr('data-id'));
              })
            }

          } else if (dataList[i].theme === "推荐图书") {
            // 推荐图书
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(".book-content .blank").hide();
            } else {
              $(".book-content .blank").show();
            }
            var dataBookList = dataList[i].course.slice(0, 4);
            var bookBox = $(".js-book-box");
            var bookModel = bookBox.find(".js-book-item");
            bookBox.empty();

            for (var t in dataBookList) {
              var bookItem = bookModel.clone(false);
              bookItem.find('.content-col').attr('data-id', dataBookList[t].id);
              bookItem.find('img').attr('src', dataBookList[t].imgUrl);
              bookItem.find('.title').text(dataBookList[t].bookName);
              bookItem.find('.real-price-number').text('￥' + dataBookList[t].realPrice);
              // 打折价格判断显示
              if (dataBookList[t].originalPrice != 0) {
                bookItem.find('.original-price').text('￥' + dataBookList[t].originalPrice).show();
              }
              bookBox.append(bookItem);

              // 给元素绑定事件
              bookItem.find('.content-col').click(function () {
                bookdetailPath($(this).attr('data-id'));
              })
            }

          } else if (dataList[i].theme === "金牌名师") {
            // 金牌名师 轮播图
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(".teacher-carousel .blank").hide();
            } else {
              $(".teacher-carousel .blank").show();
            }
            var dataTeacherList = dataList[i].course;
            var teacherTotal = dataList[i].course.length;
            var teacherSlideNum = Math.ceil(teacherTotal / 4);

            // 获取模板
            var teacherBox = $(".js-teacher-box");
            var teacherSlideModel = teacherBox.find(".item").eq(0).removeClass('active');
            var teacherItemModel = teacherBox.find(".js-teacher-item").eq(0);

            teacherSlideModel.empty();
            teacherBox.empty();
            // 渲染列表
            for (var u = 0; u < teacherSlideNum; u++) {
              var teacherSlideItem = teacherSlideModel.clone(false);
              for (var v in dataTeacherList) {
                var teacherItem = teacherItemModel.clone(false);
                if (v >= u * 4 && v < (u + 1) * 4) {
                  // 1234 5678 
                  teacherItem.find("img").attr('src', dataTeacherList[v].imgUrl);
                  teacherItem.find('.content-col').attr('data-id', dataTeacherList[v].id);
                  teacherItem.find(".name").text(dataTeacherList[v].teacherName);
                  // t.brief.length>30?t.brief.substring(0,34):t.brief
                  var techerDesData = dataTeacherList[v].brief;
                  var techerDes = techerDesData.length > 30 ? techerDesData.substring(0, 31) : techerDesData;
                  teacherItem.find(".des").text(techerDes + '...');
                  // 给元素绑定事件
                  teacherItem.find('.content-col').click(function () {
                    console.log($(this).attr("data-id") + '沒有 teacher 详情页')
                  });
                  teacherSlideItem.append(teacherItem);
                }
              }
              teacherBox.append(teacherSlideItem);
            }
            // 轮播图不渲染的原因是得有一个 item 存在 active 的 className
            // 那么渲染的时候也要先去掉 active 
            teacherBox.find(".item").eq(0).addClass('active').show();

          }
        }
      }
    });
  }

  // 按钮 '更多' ，去列表页
  // 传参 majorId ,列表页自动筛选
  $('.live-left .more').click(function () {
    window.location.href = "./live.html?id=" + majorId;
  })
  $('.ftf-content .more').click(function () {
    window.location.href = "./ftf.html?id=" + majorId;
  })
  $('.right-exam .more').click(function () {
    window.location.href = "./examination.html?id=" + majorId;
  })
  $('.online-content .more').click(function () {
    window.location.href = "./online.html?id=" + majorId;
  })
  $('.book-content .more').click(function () {
    window.location.href = "./books.html?id=" + majorId;
  })



})