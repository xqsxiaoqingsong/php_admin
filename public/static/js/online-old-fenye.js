$(function () {
  // 进入页面默认运行
  getShowCnmedicineMajor();

  // 默认渲染1页，id为全部，类型为全部
  getOnline(1, 0, '全部');

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
        dataMajorList.splice(0, 0, {
          "majorName": "全部",
          "id": "0"
        })
        // console.log(dataMajorList)

        //获取模板
        var majorBox = $(".js-major-id");
        var majorItem = majorBox.find("label");
        majorBox.empty();

        for (var i in dataMajorList) {
          var majorItem = $('<label class="btn btn-custom-nostyle" data-id="' + dataMajorList[i].id + '">' +
            '<input type="radio" name="options" id="option' + (i + 1) + '" autocomplete="off">' + dataMajorList[i].majorName +
            '</label>')
          majorBox.append(majorItem);
        }

        // 给元素绑定事件，根据图书内容id，获取列表数据
        $('.js-major-id .btn').click(function () {
          var majorId = $(this).attr('data-id');
          var chargeType = $('.js-charge-type').find('.active').attr('data-id');
          // console.log(majorId, chargeType);
          getOnline(1, majorId, chargeType);
        })
        // 给第一个按钮添加类名，设置默认
        $(".js-major-id").find('.btn-custom-nostyle').eq(0).addClass('active');
      }
    });
  }

  // 根据图书类型，获取列表数据
  $('.js-charge-type .btn').click(function () {
    var chargeType = $(this).attr('data-id');
    var majorId = $('.js-major-id').find('.active').attr('data-id');
    // console.log(majorId, chargeType);
    getOnline(1, majorId, chargeType);
  })

  // 获取列表数据
  function getOnline(pageNo, majorId, chargeType) {
    var filterData = {
      page: pageNo,
      majorId: majorId,
      charge: chargeType
    }
    $.ajax({
      url: urlOnlineList,
      type: "POST",
      data: filterData,
      success: function (res) {
        // console.log(res)
        if (res.Data) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.blank').hide();
          $('.js-col-box').show();

          var dataOnlineList = res.Data.courseManage;
          var total = res.Data.total;

          $('.total-number').text('共找到 ' + total + ' 门课程');

          //获取模板
          var onlineBox = $(".js-col-box");
          var onlineItem = onlineBox.find(".js-col-item").eq(0);
          onlineBox.empty();
          // 渲染列表
          for (var i in dataOnlineList) {
            var item = onlineItem.clone(false);
            item.find(".imgbox img").attr('src', dataOnlineList[i].imgUrl);
            item.attr('data-id', dataOnlineList[i].id);
            item.find(".title").text(dataOnlineList[i].title);
            item.find(".real-price-number").text('￥' + dataOnlineList[i].activityPrice);
            if (dataOnlineList[i].originalPrice != 0) {
              item.find(".original-price").text('￥' + dataOnlineList[i].originalPrice);
            }
            item.find(".class-number").text('课时:' + dataOnlineList[i].classNumber);
            item.find(".speaker").text('主讲人：' + dataOnlineList[i].speaker);
            item.find(".learn-members").text(dataOnlineList[i].learnMembers + '人在学');
            // 给元素绑定事件
            item.click(function () {
              onlineDetailPath($(this).attr("data-id"));
            });
            onlineBox.append(item);
          }

          // 计算分页总页数
          var pages = Math.ceil(total / 16);
          if (pages > 1) {
            $('.pagination').show();
            // 渲染分页
            createPaginate(pageNo, pages);
          } else {
            $('.pagination').hide();
          }

        } else {
          $('.blank').show();
          $('.js-col-box').hide();
          $('.pagination').hide();
          $('.total-number').text('没有找到！');
        }
      }
    });
  }

  // 分页
  function createPaginate(pageNo, pages) {
    var pageOptions = {
      bootstrapMajorVersion: 3,
      alignment: "center", //居中显示
      currentPage: pageNo, //当前页数
      numberOfPages: 13,
      pageSize: 16,
      totalPages: pages, //总页数 注意不是总条数, 需要自己计算
      itemTexts: function (type, page, current) { //如下的代码是将页眉显示的中文显示我们自定义的中文。
        switch (type) {
          case "first":
            return "首页";
          case "prev":
            return "上一页";
          case "next":
            return "下一页";
          case "last":
            return "尾页";
          case "page":
            return page;
        }
      },
      onPageClicked: function (event, originalEvent, type, page) {
        var majorId = $('.js-major-id').find('.active').attr('data-id');
        var chargeType = $('.js-charge-type').find('.active').attr('data-id');
        // 获取数据
        getOnline(page, majorId, chargeType);
        // 返回顶部
        // goTop($(this)[0], 300);
      }
    }
    $(".pagination").bootstrapPaginator(pageOptions);
  }

  // 去图书详情页
  function onlineDetailPath(id) {
    window.location.href = "./onlineDetail.html&id=" + id;
  }

})