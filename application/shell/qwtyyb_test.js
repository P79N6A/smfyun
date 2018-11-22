var arguments = process.argv.splice(2);
var log4js = require('log4js');
var request = require('request');
appId = 'wx4d981fffa8e917e7';
log4js.configure({
  appenders: { cheese: { type: 'file', filename: '/var/www/html/jfb.smfyun.com/application/shell/qwtnode.log' } },
  categories: { default: { appenders: ['cheese'], level: 'debug' } }
});
var logger = log4js.getLogger();
logger.level = 'debug';
logger.debug("Some debug messages about bid============="+ arguments[0]);

const http = require('http')
var mysql = require('mysql');
var TEST_DATABASE = 'smfyun';
// var TEST_TABLE = 'user';
var client = mysql.createConnection({
  host: 'rds47z172hu2m8vci749private.mysql.rds.aliyuncs.com',
  user: 'smfyun',
  password: 'emg4h2q',
});
client.connect();
client.query("use " + TEST_DATABASE);

// var bid=1;
//console.log('所传递的参数是：', arguments[0]);
function countall(bid,flag,aid,callback){
    console.log(bid);
    console.log(aid);
    if(flag==1){//发订阅
      var sql = 'SELECT COUNT(*) as num FROM `qwt_yybqrcodes` WHERE  `bid`='+bid+' and id IN (SELECT qid FROM qwt_yybrecords WHERE `aid` ='+aid+')';
     }else{//发全部
      var sql = 'SELECT COUNT(*) as num FROM `qwt_yybqrcodes` WHERE bid='+bid;
    }
    console.log(sql);
    client.query(
      sql,
      function selectCb(err, count, fields) {
          if (err) {
            console.log(err);
            logger.debug(err);
            throw err;
          }
          if(count){
            // console.log(count[0]['num']);
            return callback(bid,count[0]['num']);
          }
        }
      );
  }
function getcfg(bid,client,callback){
  console.log(bid);
  client.query(
  "SELECT * FROM qwt_yybcfgs where `bid` ="+bid,
  function selectCb(err, cfgs, fields) {
    if (err) {
      console.log(err);
      logger.debug(err);
      throw err;
    }
    if(cfgs)
    {
      var rcfgs=new Array();
      for(var i = 0; i < cfgs.length; i++)
        {
          rcfgs[cfgs[i].key]=cfgs[i].value;
        }
      // console.log(rcfgs);
      return callback(rcfgs);
    }
  }
);
}
function refresh_token(bid,client,memcache,callback){
  var component_access_token_key = 'component_access_token'+appId;
  memcache.get(component_access_token_key, function (err, component_access_token) {
    var url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='+component_access_token;
    client.query(
    "SELECT * FROM qwt_logins where `id` = "+bid,
    function selectCb(err, user, fields) {
        if (err) {
          console.log(err);
          logger.debug(err);
          throw err;
        }
        if(user){
          // console.log('component_access_token::'+component_access_token);
          // console.log('user'+user);
          // console.log('appid::'+user[0]['appid']);
          // console.log('refresh_token::'+user[0]['refresh_token']);
          var requestData={component_appid:'wx4d981fffa8e917e7',authorizer_appid:'wxd3a678cfeb03e3a3',authorizer_refresh_token:'KHkUBBAOmI0mKiUHsqCZmeF317EQrLT063612vh_mso'};
          request({
              url: url,
              method: "POST",
              json: true,
              headers: {
                  "content-type": "application/json",
              },
              body: JSON.stringify(requestData)
          }, function(error, response, body) {
              // console.log('wx_api_error:'+error);
              // console.log('wx_api_response:'+JSON.stringify(response));
              // console.log('wx_api_body:'+JSON.stringify(body));
              if (!error && response.statusCode == 200) {
                  console.log(body); // 请求成功的处理逻辑
                  if(body.authorizer_refresh_token){
                    console.log('aaaaaa'+body.authorizer_refresh_token.substring(15));
                    client.query(
                      "UPDATE qwt_logins set `refresh_token` = '"+body.authorizer_refresh_token.substring(15)+"' where id ="+bid,
                      function selectCb(err, user, fields){
                      if (err) {
                        console.log(err);
                        logger.debug(err);
                        throw err;
                      }
                    })
                  }
                  if(body.authorizer_access_token){
                    console.log('bbbbbb'+body.authorizer_access_token);
                    return callback(body.authorizer_access_token);
                  }else{
                    logger.debug("authorizer_access_token_error:"+bid+JSON.stringify(body));
                  }
              }
          });
        }
      })
  })
}
function getorder(client,callback){
  var timestamp=new Date().getTime().toString();
  timestamp = timestamp.substr(0,10);
  var timestamp = parseInt( timestamp);
  client.query(
  "SELECT * FROM qwt_yyborders where `ifsend`=1 and `state` =0 and (`way` = 1 or `time` < "+timestamp+" ) order by id ",
  function selectCb(err, orders, fields) {
      if (err) {
        console.log(err);
        logger.debug(err);
        throw err;
      }
      // console.log(orders);
      // throw orders;
      // return;
      if(orders[0])
      {
        // console.log(orders);
        // throw orders;
        // return;
        client.query(
          "UPDATE `qwt_yyborders` SET `start`= 1 WHERE `id` = "+orders[0].id,
          function selectCb(err, orders, fields) {
              if (err) {
                console.log(err);
                logger.debug(err);
                throw err;
              }
            }
          );
        return callback(orders[0]);
      }else{
        throw "no oid";
      }
    }
  );
}
getorder(client,function(data){
      var order=data;
      console.log('1'+data);
      console.log('2'+order);
      if(typeof(order)=="undefined") {
        logger.debug("没有满足要求的oid");
        throw "没有满足要求的oid"
      };
      console.log('3'+order.id);
      if(order.id){
        var bid=order.bid;
        var oid=order.id;
        var type=order.type;
        var flag=order.flag;
        var aid=order.aid;
        var start = order.start_qid;
        getcfg(bid,client,function(data){
          // console.log('end'+data.name);
          // return data.name;
          all = 0;
          fail = 0;
          var cfg=data;
          var WechatAPI = require('wechat-api');
          var memcached = require('memcached');
          var memcache = new memcached('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com:11211');
          var memcache_key = 'qwt.access_token'+bid;
          console.log(memcache_key);
          var api = new WechatAPI(cfg['appid'], cfg['appsecret'],function (callback) {
              memcache.get(memcache_key, function (err, token) {
                  console.log('get:' + JSON.stringify(token));
                  // token = null;
                  if(!token){
                      refresh_token(bid,client,memcache,function(token){
                          console.log('wx_get_token'+token);
                          var token2 = {};
                          token2.accessToken = token;
                          token2.expireTime = new Date().getTime()+5400000;//5400s
                          callback(null, token2);
                      });
                  }else{
                    var token2 = {};
                    token2.accessToken = token;
                    token2.expireTime = new Date().getTime()+5400000;//5400s
                    callback(null, token2);
                  }
              });
          },function (token, callback) {
              console.log('set:' + JSON.stringify(token));
              memcache.set(memcache_key , token.accessToken, 5400, callback);
          });
          api.setEndpoint('sh.api.weixin.qq.com');
          var maxSockets = 100;
          api.setOpts({
            timeout: 150000,
            httpAgent: new http.Agent({
              keepAlive: true,
              maxSockets
            })
          })
          console.log('bid:'+bid);
        //var sql = "SELECT  `id`, `bid`,  `nickname`,`openid`  FROM `qwt_yybqrcodes` where `bid`="+bid+" and `flag`=1"
        console.log(sql);
         if(flag==1){
          var sql = "SELECT  `id`,`nickname`,`openid`, `bid` FROM `qwt_yybqrcodes` where  `bid`="+bid+" and id IN (SELECT qid FROM qwt_yybrecords WHERE  `qid`> "+start+" and `aid` ="+aid+") order by `id` asc limit 0,12500";
          }else{
            var sql = "SELECT  `id`,`nickname`,`openid`, `bid` FROM `qwt_yybqrcodes` where `id`> "+start+" and  `bid`="+bid+" order by `id` asc limit 0,12500";
          }
          console.log(sql);
         //发送全部
        client.query(
        sql,
        function selectCb(err, qrcodes, fields) {
            if (err) {
              console.log(err);
              logger.debug(err);
              throw err;
            }
            if(qrcodes){
                // console.log(qrcodes);
                if(qrcodes=='') throw "没有满足要求用户";//为空就返回
                for(var i = 0; i < qrcodes.length; i++){
                     var openid=qrcodes[i]['openid'];
                     // console.log(qrcodes[i]['openid']);
                     // console.log(qrcodes[i].openid);
                     var nickname=qrcodes[i]['nickname'];
                     var qid=qrcodes[i]['id'];
                     if(type==1){
                       var url=order.url;
                      }else if(type==2){
                       var url='http://yyb.smfyun.com/yyb/yzcode?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
                      }else if(type==3){
                        var  url='http://yyb.smfyun.com/yyb/yzgift?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
                      }
                      //发模板消息
                      var templateId = cfg['mbtpl'];
                      var myDate = new Date();
                      var date=myDate.getFullYear()+'-'+(myDate.getMonth()+1)+'-'+myDate.getDate()+' '+myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds();
                      var data = {
                         "first": {
                           "value":order.title,
                           "color":"#173177"
                         },
                         "keyword1":{
                           "value":nickname,
                           "color":"#173177"
                         },
                         "keyword2": {
                           "value":date,
                           "color":"#173177"
                         },
                         "keyword3": {
                           "value":"预约通知",
                           "color":"#173177"
                         },
                         "remark":{
                           "value":order.content,
                           "color":"#173177"
                         }
                      };
                      if(type==4){
                        var miniprogram={
                          "appid":order.appid,
                          "pagepath":order.url
                        };
                      }else{
                        var miniprogram='';
                      }
                      all++;
                    (function(i,qid){
                        api.sendTemplate(openid, templateId, url, data,miniprogram, function (err, data){
                           if(err!=null){
                              var timestamp = Date.parse( new Date())/1000;
                              var sql = "INSERT IGNORE INTO `smfyun`.`qwt_yybitems` (`id`, `bid`, `oid`, `qid`, `cron`, `state`, `status`, `flag`, `reason`, `lastupdate`) VALUES (NULL, '"+bid+"', '"+oid+"', '"+qid+"', '1', '0', '0', '1', '"+err+"', '"+timestamp+"')";
                              console.log(sql);
                              client.query(
                                sql,
                                function selectCb(err, order, fields) {
                                    if (err) {
                                      console.log(err);
                                      logger.debug(err);
                                      throw err;
                                    }
                                  }
                                );
                              fail++;
                            }
                             console.log('success===================qid'+qid+data);
                             console.log('openid===================qid'+qid);
                             console.log('err===================qid'+qid+err);
                             console.log('all::'+all);
                             console.log('fail::'+fail);
                             console.log('i::::::::'+i);

                             logger.debug('success===================qid'+qid+data);
                             logger.debug('openid===================qid'+qid);
                             logger.debug('err===================qid'+qid+err);
                             logger.debug('all::'+all);
                             logger.debug('fail::'+fail);
                             logger.debug('i::::::::'+i);
                             if(i==all-1){
                                var fail_user = order.fail_user+fail;
                                all_user = order.all_user+all;
                                countall(bid,order.flag,order.aid,function(bid,num){
                                  console.log('总数：'+num);
                                  if(all<12500){//脚本全部轮询完毕
                                      console.log("UPDATE `qwt_yyborders` SET `state`= 1 WHERE `id` = "+oid);
                                       client.query(
                                        "UPDATE `qwt_yyborders` SET `state`= 1 WHERE `id` = "+oid,
                                        function selectCb(err, order, fields) {
                                            if (err) {
                                              console.log(err);
                                              logger.debug(err);
                                              throw err;
                                            }
                                            console.log('order'+order);
                                          }
                                        );
                                  }
                                })
                                start_qid = qid;
                                var sql = "UPDATE `qwt_yyborders` SET `start_qid`="+start_qid+" ,`fail_user`= "+fail_user+" ,`all_user`="+all_user+" ,`has_send`="+(i+1)+" WHERE `id` = "+oid;
                                console.log(sql);
                                client.query(
                                  sql,
                                  function selectCb(err, order, fields) {
                                      if (err) {
                                        console.log(err);
                                        logger.debug(err);
                                        throw err;
                                      }
                                    }
                                  );
                                // client.end();
                              }
                        });
                    })(i,qid);
                  }
            }
          }
        );
         console.log('oid:::::::'+oid);
        return;
        });
      }else{
        client.end();
        return;
      }
  });
