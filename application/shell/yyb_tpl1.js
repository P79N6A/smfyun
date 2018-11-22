var arguments = process.argv.splice(2);
var log4js = require('log4js');
log4js.configure({
  appenders: { cheese: { type: 'file', filename: '/var/www/html/jfb.smfyun.com/application/shell/node.log' } },
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
console.log('所传递的参数是：', arguments[0]);
var bid = arguments[0];
function getcfg(bid,client,callback){
  console.log(bid);
  client.query(
  "SELECT * FROM yyb_cfgs where `bid` ="+bid,
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
function getorder(bid,client,callback){
  var timestamp=new Date().getTime().toString();
  timestamp = timestamp.substr(0,10);
  var timestamp = parseInt( timestamp);
  client.query(
  "SELECT * FROM yyb_orders where `bid` = "+bid+" and `state` =0 and (`way` = 1 or `time` < "+timestamp+" ) order by id ",
  function selectCb(err, orders, fields) {
      if (err) {
        console.log(err);
        logger.debug(err);
        throw err;
      }
      if(orders)
      {
        return callback(orders[0]);
      }
    }
  );
}

getcfg(bid,client,function(data){
  // console.log('end'+data.name);
  // return data.name;
  all = 0;
  fail = 0;
  var cfg=data;
  var WechatAPI = require('wechat-api');
  var memcached = require('memcached');
  var memcache = new memcached('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com:11211');
  var memcache_key = 'wechat_access_token'+cfg['appid'];
  console.log(memcache_key);
  var api = new WechatAPI(cfg['appid'], cfg['appsecret'],function (callback) {
      memcache.get(memcache_key, function (err, token) {
          console.log('get:' + JSON.stringify(token));
          var token2 = {};
          token2.accessToken = token;
          token2.expireTime = new Date().getTime()+100000;
          callback(null, token2);
      });
  },function (token, callback) {
      console.log('set:' + JSON.stringify(token));
      memcache.set(memcache_key , token.accessToken, 100, callback);
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
  getorder(bid,client,function(data){
      var order=data;
      console.log('1'+data);
      console.log('2'+order);
      if(typeof(order)=="undefined") {
        logger.debug("没有满足要求的oid");
        throw "没有满足要求的oid"
      };
      console.log('3'+order.id);
      if(order.id){
        var oid=order.id;
        var type=order.type;
        var flag=order.flag;
        console.log('bid:'+bid);
        var sql = "SELECT  `id`, `bid`,  `nickname`,`openid`  FROM `yyb_qrcodes` where `bid`="+bid+" and `flag`=1"
        console.log(sql);
         if(flag==1){
            client.query(
            sql,
            function selectCb(err, qrcodes, fields) {
                if (err) {
                  console.log(err);
                  logger.debug(err);
                  throw err;
                }
                if(qrcodes){
                    console.log('qrcodes'+qrcodes);
                    if(qrcodes=='') throw "没有满足要求用户";//为空就返回
                    for(var i = 0; i < qrcodes.length; i++){
                        var openid=qrcodes[i]['openid'];
                        var nickname=qrcodes[i]['nickname'];
                        var qid=qrcodes[i]['id'];
                        if(type==1){
                            url=order.url;
                        }else if(type==2){
                            url='http://yyb.smfyun.com/yyb/yzcode?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
                        }else if($type==3){
                            url='http://yyb.smfyun.com/yyb/yzgift?url='+$order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
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
                        console.log(data);
                        console.log(openid);
                        console.log(templateId);
                        console.log( url);
                        logger.debug(data);
                        logger.debug(openid);
                        logger.debug(templateId);
                        logger.debug(url);
                        all++;
                        (function(i,qid){
                          api.sendTemplate(openid, templateId, url, data, function (err, data){
                            if(err!=null){
                              var timestamp = Date.parse( new Date())/1000;
                              var sql = "INSERT INTO `smfyun`.`yyb_items` (`id`, `bid`, `oid`, `qid`, `cron`, `state`, `status`, `flag`, `reason`, `lastupdate`) VALUES (NULL, '"+bid+"', '"+oid+"', '"+qid+"', '1', '0', '0', '1', '"+err+"', '"+timestamp+"')";
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
                              var sql = "UPDATE `yyb_orders` SET `all_user`= "+all+", `fail_user`= "+fail+" WHERE `id` = "+oid;
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
         }else{
          //发送全部
            client.query(
            "SELECT  `id`,`nickname`,`openid`, `bid` FROM `yyb_qrcodes` where `bid`="+bid+" ORDER BY id ASC LIMIT 150000,150000",
            function selectCb(err, qrcodes, fields) {
                if (err) {
                  console.log(err);
                  logger.debug(err);
                  throw err;
                }
                if(qrcodes){
                    if(qrcodes=='') throw "没有满足要求用户";//为空就返回
                    for(var i = 0; i < qrcodes.length; i++){
                         var openid=qrcodes[i]['openid'];
                         var nickname=qrcodes[i]['nickname'];
                         var qid=qrcodes[i]['id'];
                         if(type==1){
                            url=order.url;
                          }else if(type==2){
                            url='http://yyb.smfyun.com/yyb/yzcode?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
                          }else if($type==3){
                              url='http://yyb.smfyun.com/yyb/yzgift?url='+$order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
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
                          all++;
                        (function(i,qid){
                            api.sendTemplate(openid, templateId, url, data, function (err, data){
                               if(err!=null){
                                  var timestamp = Date.parse( new Date())/1000;
                                  var sql = "INSERT INTO `smfyun`.`yyb_items` (`id`, `bid`, `oid`, `qid`, `cron`, `state`, `status`, `flag`, `reason`, `lastupdate`) VALUES (NULL, '"+bid+"', '"+oid+"', '"+qid+"', '1', '0', '0', '1', '"+err+"', '"+timestamp+"')";
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
                                    var sql = "UPDATE `yyb_orders` SET `all_user`= "+all+", `fail_user`= "+fail+" WHERE `id` = "+oid;
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
         }
         console.log('oid:::::::'+oid);
         console.log("UPDATE `yyb_orders` SET `state`= 1 WHERE `id` = "+oid);
         client.query(
          "UPDATE `yyb_orders` SET `state`= 1 WHERE `id` = "+oid,
          function selectCb(err, order, fields) {
              if (err) {
                console.log(err);
                logger.debug(err);
                throw err;
              }
              console.log('order'+order);
            }
          );
        // client.end();
        return;
      }else{
        client.end();
        return;
      }
  });
});
