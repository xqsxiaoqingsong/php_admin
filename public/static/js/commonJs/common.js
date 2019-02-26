

$('.login').click(function () {
  // 自动输入默认信息cookie
  var userPhoneCookie = getCookie('userPhone');
  var userPassword = getCookie('password');
  $('input[name="phoneNumber"]').val(userPhoneCookie);
  $('input[name="password"]').val(userPassword);
})

// 手机号验证
$.validator.addMethod("isPhone", function (value, element) {
  return /^[1][3,4,5,7,8,6][0-9]{9}$/.test(value);
}, "手机号格式错误");
// 在键盘按下并释放及提交后验证提交表单
$("#js-loginForm").validate({
  rules: {
    phoneNumber: {
      required: true,
      isPhone: true
    },
    password: {
      required: true,
      minlength: 6,
      maxlength: 12
    },
  },
  messages: {
    phoneNumber: {
      required: "请输入手机号",
    },
    password: {
      required: "请输入密码",
      minlength: "密码长度不能小于 6 个字符",
      maxlength: "密码长度不能大于 12 个字符"
    }
  },
  errorElement: "span", //可以用其他标签，记住把样式也对应修改
  submitHandler: function (form) {
    // form.submit();
    doLogin();
  }
})

function doLogin(formData) {
  var phoneNum = $('input[name="phoneNumber"]').val();
  var passWord = $('input[name="password"]').val();
  $.ajax({
    url: "/Login/login",
    type: "POST",
    data: {
      phone: phoneNum,
      password: passWord
    },
    success: function (res) {
      console.log(res);
      if (res.Code == 0) {
        // 成功登录
        // 记住密码
          // 展示
          $('#login-modal').modal('hide');
          // var userPhone = maskCode(resInfo.Data.phone, 3, 4); //显示手机掩码
          // console.log(userPhone)
          if(res.userInfo.memberName){
              $('.user-name').text('欢迎：' + res.userInfo.memberName).show();
          }else{
              $('.user-name').text('欢迎：用户id-' + res.userInfo.id).show();
          }
          $('.login-bar').hide();
          $('.logout-bar').show();


      } else {
        // 登录失败
        $('#login-modal .alert-danger').show();
        var loginErrorTimer = setTimeout(function () {
          $('#login-modal .alert-danger').hide();
          clearTimeout(loginErrorTimer);
        }, 1000)
      }
    },
    error: function (jqXHR, textStatus, errorMsg) {
      console.log("请求失败：" + errorMsg);
      console.log(jqXHR.responseText);
      console.log(jqXHR.textStatus);
    }
  })
}

function doLogout() {
    $.ajax({
        url: "/logout",
        type: "POST",
        data: {'logout': 'logout'},
        success: function (res) {
          if(res.Code == 1){
              $('.login-bar').show();
              $('.logout-bar').hide();
              window.location.reload();
          }
        }
    })
}

// 退出登录
$('.logout-confirm-btn').click(function () {
  doLogout();
})


// jquery ajax 兼容IE8
jQuery.support.cors = true;
jQuery.ajaxSetup({
  xhr: function () {
    if (window.ActiveXObject) {
      return new window.ActiveXObject("Microsoft.XMLHTTP");
    } else {
      return new window.XMLHttpRequest();
    }
  }
});

// 避免重复提交JQuery ajax
var pendingRequests = {};
jQuery.ajaxPrefilter(function (options, originalOptions, jqXHR) {
  var key = options.url;
  // console.log(key);
  if (!pendingRequests[key]) {
    pendingRequests[key] = jqXHR;
  } else {
    //jqXHR.abort(); //放弃后触发的提交
    pendingRequests[key].abort(); // 放弃先触发的提交
  }
  var complete = options.complete;
  options.complete = function (jqXHR, textStatus) {
    pendingRequests[key] = null;
    if (jQuery.isFunction(complete)) {
      complete.apply(this, arguments);
    }
  };
});

// 后台代码转义
function escape2Html(str) {
  var arrEntities = {
    'lt': '<',
    'gt': '>',
    'nbsp': ' ',
    'amp': '&',
    'quot': '"'
  };
  return str.replace(/&(lt|gt|nbsp|amp|quot);/ig, function (all, t) {
    return arrEntities[t];
  });
}

// 递归操作产品目录数据
function recursiveData(data) {
  var classData = data;
  var arr1 = [];
  for (var i in classData) {
    var json1 = {};
    json1.label = classData[i].classjName;
    var arr2 = [];
    for (var j in classData[i].sub) {
      var json2 = {};
      json2.label = classData[i].sub[j].classjName;
      var arr3 = [];
      for (var k in classData[i].sub[j].sub) {
        var json3 = {};
        json3.label = classData[i].sub[j].sub[k].classjName;
        json3.url = classData[i].sub[j].sub[k].positiveUrl;
        arr3.push(json3);
      }
      json2.children = arr3;
      arr2.push(json2);
    }
    json1.children = arr2;
    arr1.push(json1);
  }
  return arr1;
}

// 回到顶部
function goTop(obj, height) {
  var timer = null;
  obj.onclick = function () {
    clearTimeout(timer);
    timer = setTimeout(function fn() {
      var oTop = document.body.scrollTop || document.documentElement.scrollTop;
      if (oTop > height) {
        document.body.scrollTop = document.documentElement.scrollTop = oTop - 100;
        timer = setTimeout(fn, 0);
      } else {
        clearTimeout(timer);
      }
    }, 50);
  }
}

// cookie
function setCookie(key, val, time) {
  var date = new Date();
  var expiresDays = time || 7; //设置过期时间，默认7天
  date.setTime(date.getTime() + expiresDays * 86400 * 1000);
  document.cookie = key + "=" + escape(val) + ";expires=" + date.toGMTString();
}

function getCookie(key) {
  var arr = document.cookie.replace(/\s/g, "").split(';');
  for (var i = 0; i < arr.length; i++) {
    var tempArr = arr[i].split('=');
    if (tempArr[0] == key) {
      return unescape(tempArr[1]);
    }
  }
  return
}

function removeCookie(key) {
  var date = new Date();
  date.setTime(date.getTime() - 10000); //将date设置为过去的时间,就会自动过期
  document.cookie = key + "=v; expires =" + date.toGMTString();
}

// 掩码显示字符串
function maskCode(str, frontLen, endLen) {
  var len = str.length - frontLen - endLen;
  var xing = '';
  for (var i = 0; i < len; i++) {
    xing += '*';
  }
  return str.substring(0, frontLen) + xing + str.substring(str.length - endLen);
}

// 获取路径中的参数
function getUrlParam(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
  var r = window.location.search.substr(1).match(reg);
  if (r != null) return unescape(r[2]);
  return null;
}

function goToUrl(url) {
    location.href=url;
}
