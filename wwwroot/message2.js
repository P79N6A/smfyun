// var arguments = process.argv.splice(2);
// console.log('所传递的参数是：', arguments);
// console.log('所传递的参数是：', arguments[0]);
// console.log('所传递的参数是：', arguments[1]);
// console.log('所传递的参数是：', arguments[2]);
// return;
var appid = 'wxd3a678cfeb03e3a3';
var appsecret = '661fb2647a804e14ded1f65fad682695';

var WechatAPI = require('wechat-api');
var api = new WechatAPI(appid, appsecret);


var openid = 'oXYBfwJlL_l7tpFCyxOAteED8qDg';//1nnovator
var templateId = '6jBk_0RPs8aw6WvRoRGUcgnCCY7rmGC_VCjRTit7zjQ';
var url = 'http://nanrenwa.com/_info.php';

var data = {
   "first": {
     "value":"恭喜你购买成功！",
     "color":"#173177"
   },
   "keyword1":{
     "value":"巧克力",
     "color":"#173177"
   },
   "keyword2": {
     "value":"39.8元",
     "color":"#173177"
   },
   "keyword3": {
     "value":"2014年9月22日",
     "color":"#173177"
   },
   "remark":{
     "value":"欢迎再次购买！",
     "color":"#173177"
   }
};

api.sendTemplate(openid, templateId, url, data, function (err, data){
   console.log(data);
  console.log(err);
});
