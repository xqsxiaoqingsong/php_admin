$(function () {
  // 进入页面默认运行
  var urlId = getUrlParam('id')?getUrlParam('id'):0;
  getShowCnmedicineMajor();

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
        // var majorItem = majorBox.find("label");
        majorBox.empty();

        for (var i in dataMajorList) {
          var majorItem = $('<label class="btn btn-custom-nostyle" data-id="' + dataMajorList[i].id + '">' +
            '<input type="radio" name="options" id="option' + (i + 1) + '" autocomplete="off">' + dataMajorList[i].majorName +
            '</label>')
          majorBox.append(majorItem);
        }

        // 给元素绑定事件，根据内容id，获取列表数据
        $('.js-major-id .btn').click(function () {
          var majorId = $(this).attr('data-id');
          var majorType = $('.js-major-type').find('.active').attr('data-id');
          // console.log(majorId, majorType);
          getExaminationList(1, majorId, majorType);
        })
        // 给第一个按钮添加类名，设置默认
        $(".js-major-id").find('.btn-custom-nostyle[data-id='+urlId+']').addClass('active');
        // $(".js-major-id").find('.btn-custom-nostyle').eq(0).addClass('active');
      }
    });
  }

  getZiXunTypeList();
  // 获取所有类型
  function getZiXunTypeList(page) {
    $.ajax({
      url: urlZiXunTypeList,
      type: "POST",
      data: {
      },
      success: function (res) {
        console.log(res);
        var dataMajorList = res.Data;
        dataMajorList.splice(0, 0, {
          "typeName": "全部",
          "id": "0"
        })
        // console.log(dataMajorList)

        //获取模板
        var majorBox = $(".js-major-type");
        majorBox.empty();

        for (var i in dataMajorList) {
          var majorItem = $('<label class="btn btn-custom-nostyle" data-id="' + dataMajorList[i].id + '">' +
            '<input type="radio" name="options" id="option' + (i + 1) + '" autocomplete="off">' + dataMajorList[i].typeName +
            '</label>')
          majorBox.append(majorItem);
        }

        // 给元素绑定事件，根据内容类型，获取列表数据
        $('.js-major-type .btn').click(function () {
          var majorType = $(this).attr('data-id');
          var majorId = $('.js-major-id').find('.active').attr('data-id');
          // console.log(majorId, chargeType);
          getExaminationList(1, majorId, majorType);
        })
        // 给第一个按钮添加类名，设置默认
        $(".js-major-type").find('.btn-custom-nostyle').eq(0).addClass('active');
      }
    });
  }

  // 默认渲染1页，id为全部，类型为全部
  getExaminationList(1, urlId);
  
  // 获取列表数据
  function getExaminationList(pageNo, majorId, majorType) {
    var filterData = {
      page: pageNo || 1,
      majorId: majorId || 0,
      type: majorType || 0
    }
    $.ajax({
      url: urlExaminationList,
      type: "POST",
      data: filterData,
      success: function (res) {
        console.log(res)
        if (res.Data.ziXun) {
          // 判断是否有数据，切换列表和分页的显示和隐藏
          $('.left .blank').hide();
          $('.left .js-col-box').show();

          var dataZiXunList = res.Data.ziXun;
          var total = res.Data.total;

          $('.total-number').text('共找到 ' + total + ' 门资讯');

          //获取模板
          var ziXunBox = $(".left .js-col-box");
          var ziXunItem = ziXunBox.find(".js-col-item").eq(0);
          ziXunBox.empty();
          // 渲染列表
          for (var i in dataZiXunList) {
            var item = ziXunItem.clone(false);
            item.attr('data-id', dataZiXunList[i].id);
            item.find(".title").text(dataZiXunList[i].ziXunName).attr('data-id', dataZiXunList[i].id);
            item.find(".time").text(dataZiXunList[i].pushTime);
            item.find(".col-des").text(dataZiXunList[i].content);
            // 给元素绑定事件
            item.find(".title").click(function () {
              examDetailPath($(this).attr("data-id"));
            });
            ziXunBox.append(item);
          }

          // 计算分页总页数
          var pages = Math.ceil(total / 16);
          if (pages > 1) {
            $('.pagination').show();
            // 渲染分页
            createPaginate(pageNo, pages,total);
          } else {
            $('.pagination').hide();
          }

        } else {
          $('.left .blank').show();
          $('.left .js-col-box').hide();
          $('.pagination').hide();
          $('.total-number').text('没有找到！');
        }
      }
    });
  }

  // 分页
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
        var majorId = $('.js-major-id').find('.active').attr('data-id');
        var majorType = $('.js-major-type').find('.active').attr('data-id');
        // 获取数据
        getExaminationList(pageIndex, majorId, majorType);
        // 返回顶部
        // goTop($(this)[0], 300);
      }
    });
  }


})