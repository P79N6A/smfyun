//发送模板消息

const fs = require('fs');
const http = require('http');
const readline = require('readline');

var openIdFile = 'openid.test.csv';
var Memcached = require('memcached');
var host = '127.0.0.1';
var memcached = new Memcached(host);

var appid = 'wxf3e29c3a2838100e';
var appsecret = 'd60b755f9362a16120d203c7cba34c26';

let key = 'wechat_access_tokenwxd3a678cfeb03e3a3';
const access_token = 'ZPSOos_z8EZmsDfYCw_WclR-e21v1LGmV3eX5OL7EyG5M3YGhXGlT28RtVYrCGCmt_w9gSaLNw-Y2zn_CQQ3X3rWpJX5yJ5aSC9IZnpiamgshOBk5QSGoTWNXUvstF4OJTQhCCANXU';

var WechatAPI = require('wechat-api');
var api = new WechatAPI(appid, appsecret,

    function (callback) {
        callback(null, {"accessToken":access_token, 'expireTime':new Date().getTime()+3600*1000});

        // memcached.get(key, function (err, token) {
        //     // console.log('get:' + JSON.stringify(token));
        //     token = access_token;
        //     callback(null, {"accessToken":token, 'expireTime':new Date().getTime()+3600*1000});
        // });
    },

    function (token, callback) {
        // console.log('set:' + JSON.stringify(token));
        token.accessToken = access_token;
        memcached.set(key, token.accessToken, 3600, callback);
    }
);

api.setEndpoint('sh.api.weixin.qq.com');
var maxSockets = 100;
api.setOpts({
    timeout: 150000,
    httpAgent: new http.Agent({
        keepAlive: true,
        maxSockets
    })
});

var openid = 'oDB2TjvzpkBAoc2wE2dWOKk1DrE4';
var templateId = '0tHnn-cQTIFzukhqF7v-lR-lzOntzbyFIXIe01XXOp0';
var url = 'http://nanrenwa.youzan.com';

var data = {
   "first": {
     "value":"恭喜您成功参与买袜子送无人机活动", "color":"#EE761C"
   },
   "keyword1":{
     "value":"大疆无人机「晓 Spark」一台", "color":"#1D2A73"
   },
   "keyword2": {
     "value":"8月7日（周一）晚20点", "color":"#1D2A73"
   },
   "remark":{
     "value":"\n潜在获奖用户为\n1、李x，付款金额：488元，手机尾号 2212\n2、柏x宇，付款金额：399元，手机尾号 1068\n3、何x军，付款金额：374元，手机尾号 1075",
     "color":"#666666"
   }
};


var rd = readline.createInterface({input: fs.createReadStream(openIdFile)});
rd.on('line', function(str) {
    // console.log(str);
    api.sendTemplate(str, templateId, url, data, function (err, data) {
        console.log(data);
        // if (data.errcode > 0) console.log(api);
    });    
});

return;

// 获取全部粉丝 OpenID
function getF(nextOpenid) {
    api.getFollowers(nextOpenid ? nextOpenid : '', function(err, result){

        if (result.errcode) {
            console.log(result);
            return;
        }

        if (!result.data) return;

        result.data.openid.forEach(function(key) {
           console.log(key);
        });

        if (result.next_openid) return getF(result.next_openid);
    });
}
// getF();
