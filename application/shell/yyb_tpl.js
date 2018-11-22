var arguments = process.argv.splice(2);
console.log(11);

const http = require('http')
var mysql = require('mysql');
var TEST_DATABASE = 'smfyun';
// var TEST_TABLE = 'user';
var client = mysql.createConnection({
  host: 'rds47z172hu2m8vci749private.mysql.rds.aliyuncs.com',
  user: 'smfyun',
  password: 'emg4h2q',
});
console.log(22);
client.connect();
client.query("use " + TEST_DATABASE);
console.log(33);
// var bid=1;
// console.log('所传递的参数是：', arguments[0]);
function getcfg(bid,client,callback){
  console.log(bid);
  console.log(bid);
  client.query(
  "SELECT * FROM yyb_cfgs where `bid` ="+bid,
  function selectCb(err, cfgs, fields) {
    if (err) {
      console.log(err);
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
function getbid(callback){
  // var timestamp=new Date().getTime().toString();
  // timestamp = timestamp.substr(0,10);
  // var timestamp = parseInt( timestamp);
  //  client.query(
  // "SELECT * FROM yyb_orders where `state` =0 and (`way` = 1 or `time` < "+timestamp+" ) order by id asc ",
  // function selectCb(err, orders, fields) {
  //     if (err) {
  //       console.log(err);
  //       logger.debug(err);
  //       throw err;
  //     }
  //     if(orders[0]['bid'])
  //     {
  //       return callback(orders[0]['bid']);
  //     }else{
  //       throw "no bid";
  //     }
  //   }
  // );
  console.log('order11110000011111');
  return 15;
}
function getorder(bid,client,callback){
  console.log('order111100000');
  var timestamp=new Date().getTime().toString();
  timestamp = timestamp.substr(0,10);
  var timestamp = parseInt( timestamp);
  console.log('order1111');
  //"SELECT * FROM yyb_orders where `bid` = "+bid+" and `state` =0 order by id asc",
  client.query(
  "SELECT * FROM yyb_orders where `bid` = "+bid+" and `state` =0 order by id ",
  function selectCb(err, orders, fields) {
      if (err) {
        console.log(err);
        throw err;
      }
      if(orders)
      {
        client.query(
          "UPDATE `yyb_orders` SET `start`= 1 WHERE `id` = "+orders[0].id,
          function selectCb(err, orders, fields) {
              if (err) {
                console.log(err);

                throw err;
              }
            }
          );
        return callback(orders[0]);
      }else{
        throw 'no oid';
      }
    }
  );
}




  getcfg(bid=15,client,function(data){
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
            token2.expireTime = new Date().getTime()+5400000;
            callback(null, token2);
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
    function countall(bid,flag,callback){
      if(flag==1){//发订阅
        var sql = 'SELECT COUNT(*) as num FROM `yyb_qrcodes` WHERE bid='+bid+' and `flag`=1';
      }else{//发全部
        var sql = 'SELECT COUNT(*) as num FROM `yyb_qrcodes` WHERE bid='+bid;
      }

      console.log(sql);
      client.query(
        sql,
        function selectCb(err, count, fields) {
            if (err) {
              console.log(err);

              throw err;
            }
            if(count){
              // console.log(count[0]['num']);
              return callback(bid,count[0]['num']);
            }
          }
        );
    }
    console.log('order....');
    getorder(bid,client,function(data){
        var order=data;
        console.log('1'+data);
        console.log('2'+order);
        if(typeof(order)=="undefined") {

          throw "没有满足要求的oid"
        };
        console.log('3'+order.id);
        if(order.id){
          var oid=order.id;
          var type=order.type;
          var flag=order.flag;
          console.log('bid:'+bid);
            //发送全部
          var start = order.start_qid;
          if(flag==1){
            var sql = "SELECT  `id`,`nickname`,`openid`, `bid` FROM `yyb_qrcodes` where `id`> "+start+" and `bid`="+bid+" and `flag`=1 order by `id` asc limit 0,50000";
          }else{
            var sql = "SELECT  `id`,`nickname`,`openid`, `bid` FROM `yyb_qrcodes` where `id`> "+start+" and  `bid`="+bid+" order by `id` asc limit 750000,50000";
          }
          countall(bid,order.flag,function(bid,num){
                var num = num;
                console.log(sql);
                  client.query(
                  sql,
                  function selectCb(err, qrcodes, fields) {
                      if (err) {
                        console.log(err);

                        throw err;
                      }
                      if(qrcodes){
                          if(qrcodes=='') throw "没有满足要求用户";//为空就返回
                          for(var i = 0; i < qrcodes.length; i++){
                               var openid=qrcodes[i]['openid'];
                               var nickname=qrcodes[i]['nickname'];
                               var qid=qrcodes[i]['id'];
                               if(type==1){
                                  var url=order.url;
                                }else if(type==2){
                                  var url='http://yyb.smfyun.com/yyb/yzcode?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
                                }else if(type==3){
                                  var url='http://yyb.smfyun.com/yyb/yzgift?url='+order.url+'&openid='+openid+'&bid='+bid+'&oid='+oid;
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
                                  api.sendTemplate(openid, templateId, url, data,'', function (err, data){
                                     if(err!=null){
                                        var timestamp = Date.parse( new Date())/1000;
                                        var sql = "INSERT IGNORE INTO `smfyun`.`yyb_items` (`id`, `bid`, `oid`, `qid`, `cron`, `state`, `status`, `flag`, `reason`, `lastupdate`) VALUES (NULL, '"+bid+"', '"+oid+"', '"+qid+"', '1', '0', '0', '1', '"+err+"', '"+timestamp+"')";
                                        console.log(sql);
                                        client.query(
                                          sql,
                                          function selectCb(err, order, fields) {
                                              if (err) {
                                                console.log(err);

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


                                       console.log('总数：'+num);
                                       if(i==all-1){
                                          var fail_user = order.fail_user+fail;
                                          all_user = order.all_user+all;
                                          if(all<50000){//脚本全部轮询完毕
                                             console.log('记录state时总数：'+num);
                                             console.log('记录state时此时all_user：'+all_user);
                                             console.log('记录state的all'+all);
                                             console.log("UPDATE `yyb_orders` SET `state`= 1 WHERE `id` = "+oid);
                                             client.query(
                                              "UPDATE `yyb_orders` SET `state`= 1 WHERE `id` = "+oid,
                                              function selectCb(err, order, fields) {
                                                  if (err) {
                                                    console.log(err);

                                                    throw err;
                                                  }
                                                  console.log('order'+order);
                                                }
                                              );
                                         }
                                          // start_qid = qid;
                                          // var sql = "UPDATE `yyb_orders` SET `start_qid`="+start_qid+" ,`fail_user`= "+fail_user+" ,`all_user`="+all_user+" ,`has_send`="+(i+1)+" WHERE `id` = "+oid;
                                          // console.log(sql);
                                          // client.query(
                                          //   sql,
                                          //   function selectCb(err, order, fields) {
                                          //       if (err) {
                                          //         console.log(err);

                                          //         throw err;
                                          //       }
                                          //     }
                                          //   );
                                          return;
                                          // client.end();
                                        }
                                  });
                              })(i,qid);
                          }
                      }
                  });
           })
           console.log('oid:::::::'+oid);
           // client.query(
           //  "UPDATE `yyb_orders` SET `state`= 1 WHERE `id` = "+oid,
           //  function selectCb(err, order, fields) {
           //      if (err) {
           //        console.log(err);
           //        logger.debug(err);
           //        throw err;
           //      }
           //      console.log('order'+order);
           //    }
           //  );
          // client.end();
          return;
        }else{
          client.end();
          return;
        }
    });
  })

