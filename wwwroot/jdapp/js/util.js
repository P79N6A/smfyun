/**
 * 工具类
 */


var util = {
  /**
   * 跳登录
   * @param  {[type]} jumbBackUrl [description]
   * @return {[type]}             [description]
   */
  goLogin: function(jumbBackUrl) {
      window.location.href = "https://passport.m.jd.com/user/login.action?returnurl=" + encodeURIComponent(jumbBackUrl ? jumbBackUrl : window.location);
  },

  /**
   * 延迟函数
   * @param  {Function} fn    [description]
   * @param  {[type]}   delay [description]
   * @return {[type]}         [description]
   */
  delay: function(fn, delay) {
    return setTimeout(function() {
      fn();
    }, (typeof delay === 'number' && delay) || 80);
  },

  /**
   * 埋点上报
   * @param  {[type]} event_id    [description]
   * @param  {[type]} event_param [description]
   * @param  {[type]} level       [description]
   * @return {[type]}             [description]
   */
  reportData: function(event_id, event_param, level) {
      try {
          var id = event_id || '';
          var click = new MPing.inputs.Click(id);
          if (event_param) {
              click.event_param = event_param;
          }
          if (level) {
              click.event_level = level;
          }
          click.updateEventSeries();
          var mping = new MPing();
          mping.send(click);
      } catch (e) {
      }
  },

  /**
   * 地址添加参数
   * @param {[type]} name  [description]
   * @param {[type]} value [description]
   */
  addParamToUrl: function(url, name, value) {
        var currentUrl = url.split('#')[0];//window.location.href.split('#')[0];
        if (/\?/g.test(currentUrl)) {
            if (/name=[-\w]{4,25}/g.test(currentUrl)) {
                currentUrl = currentUrl.replace(/name=[-\w]{4,25}/g, name + "=" + value);
            } else {
                currentUrl += "&" + name + "=" + value;
            }
        } else {
            currentUrl += "?" + name + "=" + value;
        }
        if (window.location.href.split('#')[1]) {
            return currentUrl + '#' + window.location.href.split('#')[1];
        } else {
            return currentUrl;
        }
    },

    /**
   * 获取地址参数
   * @param  {[type]} url [description]
   * @param  {[type]} key [description]
   * @return {[type]}     [description]
   */
  queryString: function(key) {
    var result = new RegExp('[\\?&]' + key + '=([^&#]*)', 'i').exec(window.location.href);
    return result && decodeURIComponent(result[1]) || '';
  },

  /**
   * 关闭XView
   * @param  {[type]} os [description]
   * @return {[type]}    [description]
   */
    closeXView: function(os) {
        if (os == 'android') {
            setTimeout(function() {
                window.XView && window.XView.close();
            }, 200);
        } else if (os == 'iphone') {
            setTimeout(function() {
              location.href = 'openapp.jdmobile://communication?params={"action":"sh_xview_close"}';
          }, 200);
        } else {
            return;
        }
    },

  /**
   * 唤醒app
   * @param appurl
   * @param murl
   */
  openJdApp: function(appurl, murl) {
      var g_sSchema = appurl;
      var g_sDownload = murl;
      var g_sUA = navigator.userAgent.toLowerCase();
      var jdApp = g_sUA.indexOf('jdapp');
      if (jdApp != -1) {
          location.href = appurl;
      } else {
          //创建iframe，呼起app schema
          var div = document.createElement('div');
          div.style.visibility = 'hidden';
          div.innerHTML = "<iframe src=" + g_sSchema + " scrolling=\"no\" width=\"1\" height=\"1\"></iframe>";
          document.body.appendChild(div);

          setTimeout(function(){
              location = g_sDownload;   //否则跳下载
          },1200);
          //注意：ios在safari进程挂起之后，js代码还会继续运行至少500ms，这里写1200来保证起效。魔法数字，有待优化。
      }
  },

    /**
     * 上报PV埋点
     * @return {[type]} [description]
     */
    reportPV: function() {
        try{
            new MPing().send(new MPing.inputs.PV());
        }catch(e){}
    }

}
