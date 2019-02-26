$(function () {
  var baseUrl = 'https://www.xfxerj.com';

  // 登录
  var urlLogin = '/login';
  // 获取个人信息
  var urlUserInfo = baseUrl + '/wrdp-web/face/memberPC/showInfo';
  // 发送注册验证码
  var urlVerificateCode = baseUrl + '/wrdp-web/face/memberPC/askRegister';
  // 注册
  var urlRegister = baseUrl + '/wrdp-web/face/memberPC/startRegister';

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


  // 设置背景图高度
  $('.bg').css({
    'height': $(window).height()
  })

  // 切换显示登录和注册
  $('.login-btn').click(function () {
    $(this).toggle();
    $('.register-btn').toggle();
    $('.login-wrap').toggle();
    $('.register-wrap').toggle();

    // 自动输入默认信息cookie
    var userPhoneCookie = getCookie('userPhone');
    var userPassword = getCookie('password');
    $('.login-wrap input[name="phoneNumber"]').val(userPhoneCookie);
    $('.login-wrap input[name="password"]').val(userPassword);
  })
  $('.register-btn').click(function () {
    $(this).toggle();
    $('.login-btn').toggle();
    $('.login-wrap').toggle();
    $('.register-wrap').toggle();
  })

  // 手机号验证
  $.validator.addMethod("isPhone", function (value, element) {
    return /^[1][3,4,5,7,8][0-9]{9}$/.test(value);
  }, "手机号格式错误");

  // 登录验证提交表单
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

  function doLogin() {
    var phoneNum = $('.login-wrap input[name="phoneNumber"]').val();
    var passWord = $('.login-wrap input[name="password"]').val();
    $.ajax({
      url: urlLogin,
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
          if ($('.login-wrap input[name=rem-psd]').prop('checked')) {
            setCookie('userPhone', phoneNum)
            setCookie('password', passWord)
          } else {
            // removeCookie('userPhone');
            // removeCookie('password');
          }
        } else {
          // 登录失败
          $('.login-wrap .alert-danger').text(res.Msg).show();
          var loginErrorTimer = setTimeout(function () {
            $('.login-wrap .alert-danger').hide();
            clearTimeout(loginErrorTimer);
          }, 2000)
        }
      },
      error: function (jqXHR, textStatus, errorMsg) {
        console.log("请求失败：" + errorMsg);
        console.log(jqXHR.responseText);
        console.log(jqXHR.textStatus);
      }
    })
  }

  // 注册验证提交表单
  $("#js-registerForm").validate({
    rules: {
      phoneNumber: {
        required: true,
        isPhone: true
      },
      verificate: {
        required: true,
        minlength: 6,
        maxlength: 6
      },
      password: {
        required: true,
        minlength: 6,
        maxlength: 12
      },
      confirm_password: {
        required: true,
        minlength: 6,
        maxlength: 12,
        equalTo: "#register-psd"
      },
      agreement:{
        required: true,
      }
    },
    messages: {
      phoneNumber: {
        required: "请输入手机号",
      },
      verificate: {
        required: "请输入验证码",
        minlength: "请输入正确验证码",
        maxlength: "请输入正确验证码"
      },
      password: {
        required: "请输入密码",
        minlength: "密码长度不能小于 6 个字符",
        maxlength: "密码长度不能大于 12 个字符"
      },
      confirm_password: {
        required: "请再次输入密码",
        minlength: "密码长度不能小于 6 个字符",
        maxlength: "密码长度不能大于 12 个字符",
        equalTo: "两次密码输入不一致"
      },
      agreement: {
        required: "请接受我们的声明"
      }
    },
    errorElement: "span", //可以用其他标签，记住把样式也对应修改
    submitHandler: function (form) {
      // form.submit();
      doRegister();
    }
  })

  // 发送验证码
  $('.send-verificate-btn').click(function () {
    var phoneNumCheck = $("#js-registerForm").validate().element($(".register-phone"))
    if (phoneNumCheck) {
      sendVerificate()
    }
  })
  // 发送验证码
  function sendVerificate() {
    var phoneNum = $('.register-wrap input[name="phoneNumber"]').val();
    $.ajax({
      url: urlVerificateCode,
      type: 'POST',
      data: {
        phone: phoneNum,
        type: '1'
      },
      success: function (res) {
        console.log(res)
        if (res.Code == 0) {
          // 验证成功，等待验证码
          var sixtyCount = 10;
          var reSendVerifiTimer = setInterval(function () {
            sixtyCount--;
            $('.send-verificate-btn').attr('disabled', 'true').text(sixtyCount + ' 秒')
            if (sixtyCount < 0) {
              clearInterval(reSendVerifiTimer);
              sixtyCount = 10;
              $('.send-verificate-btn').removeAttr('disabled').text('重新发送验证码')
            }
          }, 1000)
        } else {
          // 手机号验证失败
          $('.register-wrap .alert-danger').text(res.Msg).show();
          var VerifiErrorTimer = setTimeout(function () {
            $('.register-wrap .alert-danger').hide();
            clearTimeout(VerifiErrorTimer);
          }, 2000)
        }
      }
    })
  }

  function doRegister() {
    var phoneNum = $('.register-wrap input[name="phoneNumber"]').val();
    var passWord = $('.register-wrap input[name="password"]').val();
    var verificate = $('.register-wrap input[name="verificate"]').val();
    $.ajax({
      url: urlRegister,
      type: "POST",
      data: {
        phone: phoneNum,
        verificationCode: verificate,
        password: passWord,
        type: 1
      },
      success: function (res) {
        console.log(res);
        if (res.Code == 0) {
          console.log('注册成功')
          // 获取个人信息
          // 此步骤个人认为是多余的，数据应该在之前就传过来了
          $.ajax({
            url: urlUserInfo,
            type: 'POST',
            data: {
              id: res.Data.name.id
            },
            success: function (infor) {
              console.log(infor)
              setCookie('userInfo', JSON.stringify(infor.Data), 1)
              // 去主页
              window.location.href = "books.html";
              // window.location.href = "../index.html";
            }
          })
        } else {
          // 登录失败
          $('.register-wrap .alert-danger').text(res.Msg).show();
          var registerErrorTimer = setTimeout(function () {
            $('.register-wrap .alert-danger').hide();
            clearTimeout(registerErrorTimer);
          }, 2000)
        }
      },
      error: function (jqXHR, textStatus, errorMsg) {
        console.log("请求失败：" + errorMsg);
        console.log(jqXHR.responseText);
        console.log(jqXHR.textStatus);
      }
    })
  }

})