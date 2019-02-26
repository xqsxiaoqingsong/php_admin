$(function () {
  // 进入页面默认运行
  //getNavList();

  // 获取导航栏 
  // top-nav 有健康管理师
/*
  function getNavList(page) {
    $.ajax({
      url: urlNavList,
      type: "POST",
      data: {
        page: 1
      },
      success: function (res) {
        // console.log(res);
        var dataMajorList = res.Data;

        //获取模板
        var majorBox = $(".nav-box");
        // var majorItem = majorBox.find("li");
        majorBox.empty();

        for (var i in dataMajorList) {
          var majorItem = $('<li><a href="javascript:;" data-id="' + dataMajorList[i].id + '">' + dataMajorList[i].majorName + '</a></li>');
          majorBox.append(majorItem);
          // 给元素绑定事件
          majorItem.click(function () {
            majorItem.find('a').removeClass('active');
            var majorId = $(this).find('a').attr('data-id');
            var majorName = $(this).find('a').text();
            console.log(majorId, majorName);
            if (majorName == '健康管理师') {
              window.location.href = "./html/health.html" ;
            } else if (majorName == '学历提升') {
              window.location.href = "./html/eduImprove.html" ;
            } else {
              window.location.href = "./html/theme.html?id=" + majorId + "&majorName=" + encodeURI(encodeURI(majorName));
            }
          })
        }
      }
    });
  }
*/

  //getShowCnmedicineMajor()
  // 获取专业
/*
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

        // 筛选最长的字符串，计算元素宽度
        var longestMajor = '';
        for (var index in dataMajorList) {
          if (dataMajorList[index].majorName.length > longestMajor.length) {
            longestMajor = dataMajorList[index].majorName
          }
        }
        $('.side-popover .side-popover-list').css({
          'width': (longestMajor.length + 1) * 16 * dataMajorList.length
        })

        //获取模板
        var sideMajorBox = $(".side-popover-list");
        sideMajorBox.empty();
        var onlineSubnav = $('.online-subnav');
        var booksSubnav = $('.book-subnav');

        for (var i in dataMajorList) {
          var majorItem = $('<li><a href="javascript:;" data-id="' + dataMajorList[i].id + '">' + dataMajorList[i].majorName + '</a></li>');
          // sideMajorBox.append(majorItem);
          $('.side-popover-list,.online-subnav,.book-subnav,.teacher-subnav').append(majorItem);
        }
        // 线上课程
        $(".online-subnav").find('li').eq(0).addClass('active');
        $('.online-subnav li').click(function () {
          $(this).addClass('active').siblings().removeClass('active');
          var majorId = $(this).find('a').attr('data-id');
          getHomeOnlineList(majorId);
        })
        // 图书资源
        $(".book-subnav").find('li').eq(0).addClass('active');
        $('.book-subnav li').click(function () {
          $(this).addClass('active').siblings().removeClass('active');
          var majorId = $(this).find('a').attr('data-id');
          getHomeBookList(majorId);
        })
        // 图书资源
        $(".teacher-subnav").find('li').eq(0).addClass('active');
        $('.teacher-subnav li').click(function () {
          $(this).addClass('active').siblings().removeClass('active');
          var majorId = $(this).find('a').attr('data-id');
          getHomeTeacherList(majorId);
        })
        // 侧边栏去列表页
        $('.side-popover-list li').click(function () {
          var majorId = $(this).find('a').attr('data-id');
          var htmlType = $(this).closest('.side-popover').prev('a').attr('data-htmltype');
          console.log(htmlType)
          // htmlType 1在线课程 2直播教室 3面授培训 4图书资源
          if (htmlType === '1') {
            window.location.href = "./html/online.html?id=" + majorId;
          } else if (htmlType === '2') {
            window.location.href = "./html/live.html?id=" + majorId;
          } else if (htmlType === '3') {
            window.location.href = "./html/ftf.html?id=" + majorId;
          } else if (htmlType === '4') {
            window.location.href = "./html/books.html?id=" + majorId;
          }
        })

      }
    });
  }
*/

  //getHomeList()
  // 获取所有列表
/*
  function getHomeList() {
    $.ajax({
      url: urlHomeList,
      type: "POST",
      data: {},
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
                'title': dataBannerList[j].bannerName,
                'data-contentUrl': dataBannerList[j].contentUrl,
                'data-majorId': dataBannerList[j].majorId,
              });
              bannerBox.append(bannerItem);

              // 给元素绑定事件
              bannerItem.find('img').click(function () {
                // 1 落地页  2主题页  3列表页
                if ($(this).attr('data-contentUrl') == '1') {
                  window.location.href = './html/readingPartner.html';
                } else if ($(this).attr('data-contentUrl') == '2') {
                  window.location.href = './html/theme.html?id=' + 8 + "&majorName=" + encodeURI(encodeURI('中医技术'));
                } else if ($(this).attr('data-contentUrl') == '3') {
                  window.location.href = './html/online.html?' + 'id=0';
                }
              })
            }
            // 给第一个item设置className active，激活轮播图
            bannerBox.find('.item').eq(0).addClass('active');
          } else if (dataList[i].theme === "考务资讯") {
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

            for (var k in dataExamList) {
              var examItem = examModel.clone(false);
              examItem.attr('data-id', dataExamList[k].id);
              examItem.find('.title').text(dataExamList[k].ziXunName);
              examItem.find('.exam-content').text(dataExamList[k].ziXunName);
              examBox.append(examItem);

              // 给元素绑定事件
              examItem.click(function () {
                window.location.href = "./html/examDetail.html?id=" + $(this).attr('data-id');
              })
            }
          } else if (dataList[i].theme === "面授培训") {
            // 判断显示无数据
            if (dataList[i].course.length != 0) {
              $(".ftf-content .blank").hide();
            } else {
              $(".ftf-content .blank").show();
            }
            var dataFtfList = dataList[i].course;
            var ftfBox = $(".js-ftf-box");
            var ftfModel = ftfBox.find(".js-ftf-item");
            ftfBox.empty();

            for (var k in dataFtfList) {
              var ftfItem = ftfModel.clone(false);
              ftfItem.find(".imgbox img").attr('src', dataFtfList[k].pictureUrl);
              ftfItem.attr('data-id', dataFtfList[k].id);
              ftfItem.find(".title").text(dataFtfList[k].courseName);
              ftfItem.find(".real-price-number").text('￥' + dataFtfList[k].activityPrice);
              if (dataFtfList[k].originalPrice != 0) {
                ftfItem.find(".original-price").text('￥' + dataFtfList[k].originalPrice);
              }
              ftfBox.append(ftfItem);

              // 给元素绑定事件
              ftfItem.click(function () {
                window.location.href = "./html/ftfDetail.html?id=" + $(this).attr('data-id');
              })
            }
          } else if (dataList[i].theme === "直播预告") {

          }

        }
      }
    });
  }
*/

  //getHomeOnlineList()
  // 获取主页在线课程列表
/*
  function getHomeOnlineList(majorId) {
    var filterData = {
      majorId: majorId || 1,
    }
    $.ajax({
      url: urlHomeOnlineList,
      type: "POST",
      data: filterData,
      success: function (res) {
        // console.log(res)
        if (res.Data) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.online-content .blank').hide();
          $('.online-content .js-online-box').show();

          var dataOnlineList = res.Data;

          //获取模板
          var onlineBox = $(".js-online-box");
          var onlineItem = onlineBox.find(".js-online-item").eq(0);
          onlineBox.empty();
          // 渲染列表
          for (var i in dataOnlineList) {
            var item = onlineItem.clone(false);
            item.find(".imgbox img").attr('src', dataOnlineList[i].imgUrl);
            item.find('.content-col').attr('data-id', dataOnlineList[i].id);
            item.find(".title").text(dataOnlineList[i].courseName);
            item.find(".real-price-number").text('￥' + dataOnlineList[i].activityPrice);
            if (dataOnlineList[i].originalPrice != 0) {
              item.find(".original-price").text('￥' + dataOnlineList[i].originalPrice);
            }
            item.find(".class-number").text('课时：' + dataOnlineList[i].classNumber);
            item.find(".speaker").text('主讲人：' + dataOnlineList[i].speaker);
            item.find(".learn-members").text(dataOnlineList[i].learnMembers + '人在学');

            // 给元素绑定事件
            item.find('.content-col').click(function () {
              window.location.href = "./html/onlineDetail.html?id=" + $(this).attr('data-id');
            });
            onlineBox.append(item);
          }
        } else {
          $('.online-content .blank').show();
          $('.online-content .js-online-box').hide();
        }
      }
    })
  }
*/

  //getHomeBookList()
  // 获取主页图书资源列表
/*
  function getHomeBookList(majorId) {
    var filterData = {
      majorId: majorId || 1,
    }
    $.ajax({
      url: urlHomeBookList,
      type: "POST",
      data: filterData,
      success: function (res) {
        // console.log(res)
        if (res.Data.length != 0) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.book-content .blank').hide();
          $('.book-content .js-book-box').show();

          var dataBookList = res.Data;

          //获取模板
          var bookBox = $(".js-book-box");
          var bookItem = bookBox.find(".js-book-item").eq(0);
          bookBox.empty();
          // 渲染列表
          for (var i = 0; i < 3; i++) {
            var item = bookItem.clone(false);
            item.find(".imgbox img").attr('src', dataBookList[i].imgUrl);
            item.find('.content-col').attr('data-id', dataBookList[i].id);
            item.find(".title").text(dataBookList[i].bookName);
            item.find(".real-price-number").text('￥' + dataBookList[i].realPrice);
            if (dataBookList[i].originalPrice != 0) {
              item.find(".original-price").text('￥' + dataBookList[i].originalPrice);
            }

            // 给元素绑定事件
            item.find('.content-col').click(function () {
              window.location.href = "./html/bookDetail.html?id=" + $(this).attr('data-id');
            });
            bookBox.append(item);
          }
        } else {
          $('.book-content .blank').show();
          $('.book-content .js-book-box').hide();
        }
      }
    })
  }
*/

  //getHomeTeacherList();
  // 获取主页金牌名师列表
  function getHomeTeacherList(majorId) {
    var filterData = {
      majorId: majorId || 1,
    }
    $.ajax({
      url: urlHomeTeacherList,
      type: "POST",
      data: filterData,
      success: function (res) {
        // console.log(res)
        if (res.Data.length != 0) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.teacher-carousel .blank').hide();
          $('.js-teacher-box').show();

          var dataTeacherList = res.Data;
          if (dataTeacherList.length <= 4) {
            $('.teacher-carousel .iconfont').hide();
          } else {
            $('.teacher-carousel .iconfont').show();
          }
          // 轮播图
          var total = res.Data.length;
          var slideNum = Math.ceil(total / 4);

          // 获取模板
          var teacherBox = $(".js-teacher-box");
          var slideModel = teacherBox.find(".item").eq(0).removeClass('active');
          var itemModel = teacherBox.find(".js-teacher-item").eq(0);

          slideModel.empty();
          teacherBox.empty();
          // 渲染列表
          for (var i = 0; i < slideNum; i++) {
            var slideItem = slideModel.clone(false);
            for (var j in dataTeacherList) {
              var colItem = itemModel.clone(false);
              if (j >= i * 4 && j < (i + 1) * 4) {
                // 1234 5678 
                colItem.find(".imgbox img").attr('src', dataTeacherList[j].imgUrl);
                colItem.find('.content-col').attr('data-id', dataTeacherList[j].id);
                colItem.find(".name").text(dataTeacherList[j].teacherName);
                var techerDesData = dataTeacherList[j].brief;
                var techerDes = techerDesData.length > 30 ? techerDesData.substring(0, 31) : techerDesData;
                colItem.find(".des").text(techerDes + '...');
                // 给元素绑定事件
                colItem.find('.content-col').click(function () {
                  console.log($(this).attr("data-id") + '沒有 teacher 详情页')
                });
                slideItem.append(colItem);
              }
            }
            teacherBox.append(slideItem);
          }
          // 轮播图不渲染的原因是得有一个 item 存在 active 的 className
          // 那么渲染的时候也要先去掉 active 

        } else {
          $('.teacher-carousel .blank').show();
          $('.js-teacher-box').hide();
          $('.teacher-carousel .iconfont').hide();
        }
      }
    })
  }

  // 按钮 '更多' ，去列表页
  $('.exam-content .more').click(function () {
    window.location.href = "./html/examination.html";
  })
  $('.online-content .more').click(function () {
    window.location.href = "./html/online.html";
  })
  $('.live-content .more').click(function () {
    window.location.href = "./html/live.html";
  })
  $('.ftf-content .more').click(function () {
    window.location.href = "./html/ftf.html";
  })
  $('.book-content .more').click(function () {
    window.location.href = "./html/books.html";
  })

});