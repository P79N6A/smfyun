<!DOCTYPE html>
<html style="height: 100%">
   <head>
       <meta charset="utf-8">
       <title>阿芙AR应用</title>
        <script src="//g.alicdn.com/tmapp/tida/3.2.38/tida.js?appkey=<?=$config['webappkey']?>"></script>
   </head>
   <body>

   </body>
   <script type="text/javascript">
    Tida.ready({
        module: ["device", "media", "server", "social", "widget", "sensor", "share", "buy", "draw", "im", "calendar", "prize"],
        console:1,  //  默认关闭, 值为1时打开浮层console.
    }, function(){
        Tida.doAuth(true, function(data){
          if(data.finish){
              // 授权成功 可以顺利调用需要授权的接口了
              alert(data);
          }else {
              // 未能成功授权
              alert(data);
          }
      });
    })
    Tida.doAuth(true, function(data){
        if(data.finish){
            // 授权成功 可以顺利调用需要授权的接口了
            alert(data);
        }else {
            // 未能成功授权
            alert(data);
        }
    });
</script>
</html>
