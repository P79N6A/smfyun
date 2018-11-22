<!doctype html>
<head>
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="renderer" content="webkit">

   <meta charset="utf-8">
   <meta name="baidu-site-verification" content="ZRexZ7XL4P" />
   <title>神码浮云营销应用平台</title>
   <meta name="keywords" content="微信公众平台,微信公众平台官方网站,公众号注册,微信公众号注册,微信订阅号注册,微信公众号申请,微信服务号申请,微信订阅号申请,微信企业号申请,微信服务号注册,微信企业号注册,订阅号注册,公众号申请,服务号申请,订阅号申请,企业号申请,服务号注册,企业号注册,微信">
   <meta name="description" content="微信公众平台，给个人、企业和组织提供业务服务与用户管理能力的全新服务平台。">
   <link href="../qwt/images/logo.ico" rel="Shortcut Icon">
   <link  rel="stylesheet" href="../qwt/css/page_login26f6ea.css">
   </head>
   <body class="zh_CN">
        <span class="bottomqr"><img class="kf" src="/qwt/images/kf.png"></span>
    <div class="head" id="header">
     <div class="head_box">
      <div class="inner wrp">
       <h1 class="logo" style="padding-top:0px;">
        <a href="../qwta/login" title="神码浮云营销插件平台" style="width:auto;height:60px;display:block;position:relative;overflow:visible;"><img style="height:100%;" src="../qwt/images/logo.png"><!-- <img style="
    position: absolute;
    top: 30px;
    left: 180px;" src="../qwt/images/image.png"> --></a>
       </h1>
          <div class="account">
           <div class="account_meta account_faq">
         <?php if($_SESSION['qwta']['user']):?>
           <div style="display:inline-block;color:#999;margin-right:10px;"><?=$_SESSION['qwta']['user']?></div>
           <a style="display:inline-block;margin-right:10px;" href="./userinfo"><div>管理中心</div></a>
           <a style="display:inline-block;margin-right:10px;" href="./layout"><div>退出登录</div></a>
         <?php endif?>
           <a style="display:inline-block;" href="./help" target="_blank"><div>帮助中心</div></a>
           </div>
          </div>
      </div>
     </div>
     <div class="banner">
      <div class="inner wrp" style="height:350px;">
      <?php if(!$_SESSION['qwta']['user']):?>
       <div class="login_frame">
        <h3>登录</h3>
        <!-- <div class="login_err_panel" style="display:none;" id="err">
        </div> -->
        <?php if($error):?>
          <div class="login_err_panel"><?=$error?></div>
        <?php endif?>
        <form class="login_form" id="loginForm" method="post">
        <div class="login_input_panel" id="js_mainContent">
         <div class="login_input">
          <i class="icon_login un"> </i>
          <input placeholder="手机号" id="account" name="username" type="text">
         </div>
         <div class="login_input">
          <i class="icon_login pwd"> </i>
          <input placeholder="密码" id="pwd" name="password" type="password">
         </div>
        </div>
        <!-- <div class="verifycode" style="display:none;" id="verifyDiv">
         <span class="frm_input_box">
         <input class="frm_input" id="verify" name="verify" type="text">
         </span>
         <img id="verifyImg" src="">
         <a href="javascript:;" id="verifyChange">换一张</a>
        </div> -->
        <!-- <div class="login_help_panel">
         <label class="frm_checkbox_label" for="rememberCheck">
         <i class="icon_checkbox"></i>
         <input class="frm_checkbox" id="rememberCheck" type="checkbox">记住帐号                       </label>
         <a class="login_forget_pwd" href="/acct/resetpwd?action=send_email_page">无法登录？
         </a>
        </div> -->
        <div id="embed-captcha"></div>
        <p id="wait" class="show">正在加载验证码......</p>
        <p id="notice" class="hide">请先完成验证</p>
        <div class="login_btn_panel">
          <button class="btn_login" type="submit">登录</button>
       <a style="margin-left:20px;" href="/qwta/register">立即注册</a>
       <a style="margin-left:20px;" href="/qwta/forget">忘记密码</a>
        </div>
        </form>
        </div>
      <?php endif?>
        <!-- <dl class="qrcode_panel">
         <dt>
         <img src="https://res.wx.qq.com/mpres/htmledition/images/mp_qrcode218877.gif">
         </dt>
         <dd>
         扫描并关注
         <br>
         微信公众平台
         </dd>
         </dl> -->
        </div>
       </div>
      </div>
      <div id="body" class="body page_login">
       <style type="text/css">
       .embed-captcha{
          margin-top: 20px;
       }
       .one{
          display: inline-block;
          width: 250px;
          height: 330px;
          margin: 0px 30px 0px 40px;
       }
       .himg img{
        width: 100%;
        height: 250px;
       }
       .himg{
        height: auto;
       }
       .name{
        text-align: center;
       }
       .content{
        color: #8d8d8d;
       }
            .kf{
                position:fixed;
                top:90px;
                right:38px;
                z-index: 1;
            }
            @media only screen and (max-width: 500px){
                .kf{
                    right: 0px;
                }
                .htext{
                    font-size: 22px;
                }
            }
            .mcontent{
                padding:10px;
                text-align: center;
            }
            .message_box{
                position: fixed;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                display: -webkit-box;
                -webkit-box-pack: center;
                -webkit-box-align: center;
                z-index: 1002;}
            .message_box.mask{
                background-color: rgba(0, 0, 0, .7);
                -webkit-animation: fadeIn .6s ease;
            }
            .message_box > div{
                padding-top: 25px;
                height:auto;


                top:150px;
                width: 500px;

                background-color: #ffffff;
                border-radius: 7px;
                font-size: 16px;
                -webkit-animation: fadeIn1 .6s ease;
            }
            .message_box .btns{
              border-radius: 0 0 7px 7px;
                display: -webkit-box;
                line-height: 40px;
                border-top:1px solid rgb(203, 203, 203);
                background-color:#00a65a;
                text-align:center;
                padding-top:10px;
                padding-bottom:10px;
            }
            .message_box .ok{
                color:white;
                background-color:#008749;
                width:100px;
                margin-left:200px;
                border-radius:10px;
                display:inline-block;
            }
            .barrage{
                position: absolute;
                top:10px;
                left:17%;
                width:73%;
                color:#EE7600;
                display: inline-block;
            }
       </style>
       <!-- 节假日告示 -->
        <?php if($barrage):?>
          <div class="barrage">
              <marquee style="font-size:18px;" behavior="alternate" bgcolor="" direction="right" scrolldelay="200"><?=$barrage?></marquee>
          </div>
        <?php endif?>

        <dl class="notices_box" style="margin-top:15px;margin-bottom:15px;">
         <dt>
          <i class="icon_login speaker"></i>系统公告
         </dt>
         <dd style="margin-left:20px;">
          <i style="color:#7fff7f">●</i>
          <a class="notices_title" href="/qwta/anounce_testsku" target="_blank">神码浮云上线试用功能</a>
          <span class="label_new"></span>
          </dd>
         <dd style="margin-left:20px;">
          <i style="color:#7fff7f">●</i>
          <a class="notices_title" href="/qwta/product_preview/27" target="_blank">新应用推荐有礼已上线</a>
          <span class="label_new"></span>
          </dd>
         <dd style="margin-left:20px;">
          <i style="color:#7fff7f">●</i>
          <a class="notices_title" href="/qwta/product_preview/25" target="_blank">任务宝订阅号版已上线</a>
          <span class="label_new"></span>
          </dd>
         <dd style="margin-left:20px;">
          <i style="color:#7fff7f">●</i>
          <a class="notices_title" href="/qwta/product_preview/24" target="_blank">新应用砍价宝已上线</a>
          <span class="label_new"></span>
          </dd>
         <!-- <dd>
          <i style="color:#7fff7f">●</i>
          <a class="notices_title" href="/qwta/product_preview/17" target="_blank">新应用语音红包已上线</a>
          <span class="label_new"></span>
          </dd> -->
          <!-- <dd>
            <i>●</i>
            <a class="notices_title" href="#" target="_blank">关于风险规避公告</a>
            <span class="label_new"></span>
          </dd> -->
          <!-- <dd class="extra">
            <a href="#" target="_blank">查看更多
            <i class="icon_arrow"></i>
            </a>
          </dd> -->
        </dl>
        <style type="text/css">
#mybtns .apptype{
    float: left;
    color: #fff;
    font-size: 20px;
    line-height: 42px;
    padding-left: 10px;
    width: 90px;
    font-weight: bold;
    text-decoration: none;
}
#mybtns .app-active{
  background-color: #347fdc;
}
#mybtns #weixinhudong{
  width: 150px;
}
#mybtns #smzhibo{
  width: 110px;
}
#mybtns #xifen{
  width: 150px;
}
#mybtns #fenxiao{
  width: 150px;
}
#mybtns #cuxiao{
  width: 150px;
}
#mybtns #smallapp{
  width: 110px;
}
*{margin:0;padding:0;}
body {
  margin:0px;
  padding:0px;
  font-size:14px;
  font-family:"微软雅黑";
}
.scrollpic {
  width: 1100px;
  margin:0 auto;
}
#myscroll {
  display: block;
  width: 100%;
  position: relative;
  height: 300px;
  overflow: hidden;
}
#myscroll #myscrollbox {
  display: block;
  float: left;
  position: absolute;
  left: 0;
  top: 0;
  width: 1000000px;
}
#myscroll #myscrollbox2 {
  display: block;
  float: left;
  position: absolute;
  left: 0;
  top: 300px;
  width: 1000000px;
}
#myscroll ul {
  display: block;
  float: left;
  list-style-type: none;
  padding: 0;
  margin: 0;
}
#myscroll ul li {
  display: block;
  float: left;
  padding: 0;
  width:280px;
}
#myscroll ul li a {
  display: block;
  float: left;
  width: 250px;
  padding: 0;
  position: relative;
  height: 300px;
  color: #333;
  text-decoration: none;
}
#myscroll a .intro {
  position: absolute;
  left: 0;
  top: 0;
  height: 250px;
  z-index: 10;
  width: 250px;
  color: #000;
  opacity: 0;
  -moz-opacity: 1;
  -khtml-opacity: 1;
  text-align: center;
  background-color: rgba(255,255,255,0);
}
#myscroll a h5 {
  padding: 0;
  margin: 0;
  font-size: 14px;
  text-decoration: none;
  height: 40px;
  width: 250px;
  line-height: 30px;
  font-weight:bold;
  color: #666;
}
#myscroll a .intro p {
  font-size: 13px;
  line-height: 20px;
  margin: 20px 20px;
  height: 210px;
  overflow: hidden;
}
#myscroll a:hover .intro {
  background-color: rgba(255,255,255,.7);
  opacity: 1;
}
#mybtns {
  margin: 10px 0;
  width: 1100px;
  display: block;
  height: 42px;
  margin-left: -10px;
}
#mybtns a {
  width: 42px;
  height: 42px;
  display: block;
  float: right;
  margin-right: 1px;
  background-color: #c1c1c1;
  margin-left:10px;
}
#mybtns a:hover {
  background-color: #347fdc;
}
#mybtns a:hover, #myscroll, #mybtns a, #myscroll a:hover .intro, #myscroll a .intro, #myscroll #myscrollbox, #myscroll #myscrollbox2 {
  -webkit-transition: all 0.5s ease;
  -moz-transition: all 0.5s ease;
  -ms-transition: all 0.5s ease;
  -o-transition: all 0.5s ease;
  transition: all 0.5s ease;
}
#mybtns #left {
  background-image: url(../qwt/images/jt_l.png);
}
#mybtns #right {
  background-image: url(../qwt/images/jt_r.png);
}

#kinMaxShow {
  visibility: hidden;
  width: 100%;
  height: 500px;
  overflow: hidden;
}
#tabs li{
  display: inline-block;
    float: left;
    margin-bottom: 1px;
    border-bottom: 1px solid #ddd;
}
#tabs li.acitve{
    border-bottom-color: transparent;
}
#tabs a{
  color: black;
  text-decoration: none;
  cursor: pointer;
    position: relative;
    display: block;
    padding: 10px 15px;
    margin-right: 2px;
    line-height: 1.42857143;
    border-radius: 4px 4px 0 0;
  }
#tabs li.active a{

    color: #555;
    cursor: default;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
    padding: 10px 14px 8px 14px;
}
#tabs li:hover a{

    color: #fff;
    cursor: default;
    background-color: red;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
    padding: 10px 14px 8px 14px;
}
        </style>
           <div class="mp_kind_mod">
              <div class="mp_kind_mod_hd" style="padding-top:0;padding-bottom:10px;">
               <h3>购买应用</h3>
              </div>
<div class="container">
  <div id="tabs" class="tabs">
    <nav>
      <ul>
        <li class="active"><a class="apptype" id="all"><span>全部应用</span></a></li>
        <li><a class="apptype" id="smzhibo"><span>神码云直播</span></a></li>
        <li><a class="apptype" id="offline"><span>门店引流</span></a></li>
        <li><a class="apptype" id="xifen"><span>公众号吸粉应用</span></a></li>
        <li><a class="apptype" id="fenxiao"><span>公众号分销应用</span></a></li>
        <li><a class="apptype" id="weixinhudong"><span>公众号互动应用</span></a></li>
        <li><a class="apptype" id="cuxiao"><span>公众号促销应用</span></a></li>
        <li><a class="apptype" id="zidongfaka"><span>自动发卡工具</span></a></li>
        <li><a class="apptype" id="smallappyinliu"><span>小程序引流工具</span></a></li>
        <li><a class="apptype" id="sjdpm"><span>数据大屏幕</span></a></li>
        <li><a class="apptype" id="H5game"><span>H5小游戏</span></a></li>
        <li><a class="apptype" id="smallapp"><span>小程序应用</span></a></li>
        <li><a class="apptype" id="dingzhikaifa"><span>定制开发服务</span></a></li>
      </ul>
    </nav>
  </div>
</div>
<div class="scrollpic">
  <div id="mybtns"><a href="javascript:;" id="right"></a><a href="javascript:;" id="left"></a>  </div>
  <div id="myscroll">
    <div id="myscrollbox">
      <ul>
      </ul>
    </div>
    <div id="myscrollbox2">
      <ul>
      </ul>
    </div>
  </div>
</div>
              <!-- <div class="mp_kind_mod_bd group">
                <div class="one">
                  <div class="himg"><img src="../qwt/images/hb.jpg"></div>
                  <div class="name">带参数二维码微信红包</div>
                  <div class="content">通过包裹或者线下投放卡片,用户扫码关注公众号,输入卡片上的口令领取微信红包,将已购买和潜在客户导入微信做CRM和复购.</div>
                </div>
                <div class="one">
                  <div class="himg"><img src="../qwt/images/jfb.jpg"></div>
                  <div class="name">“吸粉神器”积分宝服务号版</div>
                  <div class="content">无缝对接微信服务号的营销插件,用户通过分享专属二维码海报,好友扫码关注公众号的形式获得积分,用积分兑换实物和虚拟奖品,短时间内迅速增粉.</div>
                </div>
                <div class="one">
                  <div class="himg"><img src="../qwt/images/dyb.jpg"></div>
                  <div class="name">“吸粉神器”积分宝订阅号版</div>
                  <div class="content">无缝对接微信订阅号的营销插件,用户通过分享专属二维码海报,好友扫码关注公众号的形式获得积分,用积分兑换实物和虚拟奖品,短时间内迅速增粉.</div>
                </div>
                <div class="one">
                  <div class="himg"><img src="../qwt/images/dkb.png"></div>
                  <div class="name">打卡宝</div>
                  <div class="content">以打卡签到为核心，用户通过早起打卡、摇一摇、砸金蛋获得积分奖励，通过邀请卡，好友、好友的好友打卡，该用户也能获得积分，积分可以兑换多种类型的奖品。</div>
                </div>
              </div> -->
           </div>
            <!-- <div class="mp_kind_mod">
            <div class="mp_kind_mod_hd">
             <h3>微信互动插件</h3>
            </div>
            <div class="mp_kind_mod_bd group">
                <div class="one" style="position:relative;top:-21px;">
                  <div class="himg"><img src="../qwt/images/gl.png"></div>
                  <div class="name">微信盖楼插件</div>
                  <div class="content">用户发送关键词参与盖楼，并获得对应的奖励，唤醒沉睡的粉丝.</div>
                </div>
                <div class="one">
                  <div class="himg"><img src="../qwt/images/yy.jpg"></div>
                  <div class="name">微信智能语音识别插件</div>
                  <div class="content">用户发送语音与公众号互动，并获得对应的奖励，快速消除陌生感，与粉丝亲近起来.</div>
                </div>
              </div>
             </div>
             <div class="mp_kind_mod">
            <div class="mp_kind_mod_hd">
             <h3>促销插件</h3>
            </div>
            <div class="mp_kind_mod_bd group">
                <div class="one" style="position:relative;top:-21px;">
                  <div class="himg"><img src="../qwt/images/cxb.png"></div>
                  <div class="name">促销宝</div>
                  <div class="content">粉丝购买指定的商品后，给指定粉丝发奖品。奖品包括微信红包、有赞赠品、有赞优惠券、微信卡券、卡密等奖品以及个性化文本消息，增强粉丝粘性，促进复购、促销和传播，解决商户的营销盲点。</div>
                </div>
              </div>
             </div> -->
          <style type="text/css">
          .example_logo>div{
            display: inline-block;
            /*width: 96px;*/
            height: 98px;
            margin-right: 50px;
            text-align: center;
          }
          .example_logo>div>img{
            width: 72px;
            height: auto;
            border: 1px solid rgba(41, 9, 9, 0);
            border-radius: 36px;
          }
          .example_logo>div>span{
            display: block;
            padding-top: 4px;
            font-size: 14px;
            color: #222;
            font-weight: 400;
            font-style: normal;
          }
          .example_logo>div>img:hover{
            border: 1px solid rgba(41, 9, 9, 0.33);
          }
          .example{
            border: 1px solid #e7e7eb;
            margin-top: 15px;
            padding: 15px;
          }
          .example>img{
            display: inline-block;
            width: 30%;
            height: auto;
            margin-right: 10px;
            vertical-align: top;
          }
          .example>span{
            display: inline-block;
            width: 30%;
            vertical-align: top;
            line-height: 2;
          }
          .type{
            margin-left: .5em;
            color: #44b549;
          }
          .ex_content{
            color: #8d8d8d;
          }
          .hide{
            display: none;
          }
          .price{
            /*float: right;*/
            position: absolute;
            right: 0;
            color: #f40;
          }
          </style>
          <div class="mp_kind_mod examplebox">
            <div class="mp_kind_mod_hd">
               <h3>成功案例</h3>
            </div>
            <div class="example_logo">
              <div class="examplelogo examplehb examplexifen"><img src="../qwt/images/nrw.jpg"><span>绿盒子</span></div>
              <div class="examplelogo examplewfb examplexifen"><img src="../qwt/images/tlz.jpg"><span>探路者</span></div>
              <div class="examplelogo examplegl exampleweixinhudong"><img src="../qwt/images/zl.jpg"><span>中粮健康生活</span></div>
              <div class="examplelogo examplewdb examplexifen"><img src="../qwt/images/cg.jpg"><span>晨光文具</span></div>
              <div class="examplelogo examplerwb examplexifen"><img src="../qwt/images/yjht.jpg"><span>有间海淘</span></div>
              <div class="examplelogo examplewzb examplesmzhibo"><img src="../qwt/images/ypl.jpg"><span>一片林</span></div>
              <div class="examplelogo examplefxb examplefenxiao"><img src="../qwt/images/lr.jpg"><span>龙润微商城</span></div>
              <div class="examplelogo exampleyyb examplecuxiao"><img src="../qwt/images/zmqn.png"><span>周末去哪儿</span></div>
              <div class="examplelogo examplexxb examplecuxiao"><img src="../qwt/images/zl.jpg"><span>中粮健康生活</span></div>
              <div class="examplelogo exampledld examplefenxiao"><img src="../qwt/images/mxs.jpg"><span>尛鲜生</span></div>
              <div class="examplelogo exampleqfx examplefenxiao"><img src="../qwt/images/mzyj.jpg"><span>米占优家</span></div>
              <div class="examplelogo exampledpm examplesjdpm"><img src="../qwt/images/yysc.jpg"><span>元阳商城</span></div>
            </div>
            <div class="example">
              <img src="../qwt/images/nrw1.jpg">
              <img src="../qwt/images/nrw2.jpg">
              <span>
                <div class="title">口令红包<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：绿盒子童装使用口令红包应用，1个月，5万张卡片，新增粉丝40667，留存粉丝31179，转化率81.33%，留存率76.67%，转化订单500+，截止到2015年9月6日，新增粉丝86347，粉丝留存67502
                <br>
                <br>
                服务的客户：品胜、好色派沙拉、静佳JPlus、晨光文具、男人袜、绿盒子、品胜、连星咖啡、绽放、久品酒、简品100、MC100、每天葡萄酒、抹茶生活、萝卜运动、加一尚品、我是大美人、美丽俏佳人、波司登童装等。
              </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/tlz1.jpg">
              <img src="../qwt/images/tlz2.jpg">
              <span>
                <div class="title">积分宝服务号版<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：探路者双十一前使用积分宝，活动时间2天半，新增粉丝369039，参与人数354899，生成海报人数36478，活动结束后头条图文推送双十一优惠信息，2个小时阅读5万+；
                <br>
                <br>
                服务的客户：中粮、龙润茶、男人袜、绽放、你好植物、乡土乡亲、十色、晨光文具、 连星咖啡、小狗电器、麦包包、探路者、威露士、品胜、新农哥、盘龙云海、艺福堂、幸福西饼、宏巍软件、好想你云商、美丽俏佳人、方太厨具、极果、歌伦贝尔、有间海淘、琥珀亲子、周末去哪儿等。</span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/zl1.jpg">
              <img src="../qwt/images/zl2.jpg">
              <span>
                <div class="title">微信盖楼<span class='type'>成功案例</span></div>
                <span class='ex_content'>
                典型案例：典型案例：中粮健康生活6月13日以微信盖楼的形式发起父亲节活动，活动上线10分钟，参与人数破万；15分钟，参与人数破3万；45分钟，参与人数破10万；一个半小时，参与人数破20万，活动期间，接收消息数711937，互动人数223545，用户互动次数1067583。
                <br>
                服务的客户：中粮健康生活、男人袜、有间海淘等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/cg1.jpg">
              <img src="../qwt/images/cg2.jpg">
              <span>
                <div class="title">积分宝订阅号版<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：晨光文具控使用积分宝订阅号版，3天新增粉丝12000+，生成海报数4000+；
                <br>
                <br>
                服务的客户：方太厨具、闲画部落、童心元、苏州万科、拾鲤、成都周边游、武汉女性、沈一点、晨光文具控等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/rwb3.jpeg">
              <img src="../qwt/images/rwb5.png">
              <span>
                <div class="title">任务宝服务号版<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：知名跨境电商有间海淘使用任务宝服务号版，一周新增粉丝30000+，生成海报人数3000+；
                <br>
                <br>
                服务的客户：有间海淘、歌伦贝尔、船歌鱼水饺、艺福堂、琥珀亲子、俏十岁、贡天下等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/wzb6.png">
              <img src="../qwt/images/kongbai.png">
              <span>
                <div class="title">神码云直播<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：一片林商城使用神码云直播，同时在线观看人数2000+，直播销售转化11841元；
                <br>
                <br>
                服务的客户：有赞茶会、极客农场、那片山、老爹果园、有间海淘、成都商报、阿芙精油、一片林、洽洽食品、央广购物、米马杂货、元阳商城等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/lr.png">
              <img src="../qwt/images/kongbai.png">
              <span>
                <div class="title">订单宝<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：龙润微商城配合有赞双11促销活动进行，11月10号上线，3天时间通过订单宝成交金额为：￥315280；
                <br>
                <br>
                典型案例：男人袜双11前配合招募一万名火种用户的活动，通过订单宝成交金额为：￥41238
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/zmqne1.jpeg">
              <img src="../qwt/images/zmqne2.jpeg">
              <span>
                <div class="title">预约宝<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：周末去哪儿使用预约宝，配合门票促销活动，下发1小时销量3400+并售罄；
                <br>
                <br>
                服务的客户：周末去哪儿、船歌鱼水饺、男人袜、阿芙精油等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/xxb2.jpeg">
              <img src="../qwt/images/kongbai.png">
              <span>
                <div class="title">消息宝<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：中粮健康生活上线消息宝一周，下发消息6万+，实现额外销售收入2万+；
                <br>
                <br>
                服务的客户：中粮健康生活、有间海淘等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/dld7.png">
              <img src="../qwt/images/kongbai.png">
              <span>
                <div class="title">代理哆<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：食品生鲜卖家尛鲜生使用代理哆，17年10月底上线半个月代理出货20万+，目前累计出货60万+；
                <br>
                <br>
                服务的客户：尛鲜生、蓉城镖局等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/qfxal.png">
              <img src="../qwt/images/kongbai.png">
              <span>
                <div class="title">全员分销<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：米占悠使用全员分销系统，累计发展77个分销商，新导入粉丝17000+，新增销售收入33000+；
                <br>
                <br>
                服务的客户：服务的客户：米占悠、艺福堂等。
                </span>
              </span>
            </div>
            <div class="example hide">
              <img src="../qwt/images/system/dpm1.png" style="width:60%;">
              <span>
                <div class="title">数据大屏幕<span class='type'>成功案例</span></div>
                <span class='ex_content'>典型案例：元阳商城使用数据大屏幕，实时监测交易数据调整运营策略，同时方便对外展示元阳县电商扶贫成果；
                <br>
                <br>
                服务的客户：元阳商城、一片林、有间海淘、中粮健康生活等。
                </span>
              </span>
            </div>
          </div>
          <script src="//cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
          <script type="text/javascript">
            $(document).on('click','.kf',function(){
                console.log(2);
                $('body').append('<div class=\'message_box mask\'>'+
                                          '<div>'+
                                          '<div class=\'mcontent\'>'+
                                           '<div style=\'margin-top:5px;text-align:left;\'>1.工作时间：周一到周五9:30-18:30；<br>2.客服电话：15926434187；<br>3.客服微信：扫码添加微信，方便快速沟通问题（优先回复）。</div>'+
                                           '<img style=\'width:200px\' src=\'/qwt/images/qr.jpg\'>'+
                                          '</div>'+
                                          '<div class=\'btns\'><span  class=\'ok\'>确定</span></div>'+
                                          '</div>'+
                                          '</div>');
            })

                $(document).on('click', '.ok', function() {
                               $(".message_box").fadeOut();
                          });
          $(".example_logo>div").hover(function() {
            var no = $(".example_logo>div").index(this);
            $('.example').hide();
            $('.example').eq(no).show();
          });
          </script>
          <script type="text/javascript">
          // var i=0;
          // setInterval(function(){
          //   $(".example_logo>div").eq(i).trigger('mouseenter');
          //   $('.example_logo>div').find('img').css('border','1px solid rgba(41, 9, 9, 0)');
          //   $('.example_logo>div').eq(i).find('img').css('border','1px solid rgba(41, 9, 9, 0.33)');
          //   if(i==6){
          //     i=0;
          //   }else{
          //     i++;
          //   }
          // },2000);
          </script>
    </div>
     <div class="foot" id="footer">
      <ul class="links ft">

       <li class="links_item">
        <p class="copyright">鄂ICP备15002606号 武汉神码浮云科技有限公司
        </p>
       </li>
       <br>
       <li class="links_item">
        <p class="copyright">Copyright©2015 Smfyun All Rights Reserved.
        </p>
       </li>
       </ul>
      </div>
  </body>
  <script src="/qwt/js/gt.js"></script>
  <script>
  var none=
        '<div class="zanwuyingyong">'+
          '暂无该类应用'+
        '</div>';
  var weixinhongbao=
        '<li>'+
          '<a href="/qwta/product_preview/1" target="_blank">'+
            '<img src="../qwt/images/hb.jpg" width="250" height="250">'+
              '<h5>'+'口令红包'+'<span class="price">'+'￥200.00'+'</span></h5>'+
            // '<span class="intro">'+
            //   '<p>'+'通过包裹或者线下投放卡片,用户扫码关注公众号,输入卡片上的口令领取微信红包,将已购买和潜在客户导入微信做CRM和复购.'+'</p>'+
            // '</span>'+
          '</a>'+
        '</li>';
  var wfb=
        '<li>'+
          '<a href="/qwta/product_preview/3" target="_blank">'+
            '<img src="../qwt/images/jfb.jpg" width="250" height="250">'+
              '<h5>'+'“吸粉神器”积分宝服务号版'+'<span class="price">'+'￥350.00'+'</span></h5>'+
          '</a>'+
        '</li>'
  var wdb=
        '<li>'+
          '<a href="/qwta/product_preview/2" target="_blank">'+
            '<img src="../qwt/images/dyb.jpg" width="250" height="250">'+
              '<h5>'+'“吸粉神器”积分宝订阅号版'+'<span class="price">'+'￥1500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var dka=
        '<li>'+
          '<a href="/qwta/product_preview/13">'+
            '<img src="../qwt/images/dkb.png" width="250" height="250">'+
              '<h5>'+'打卡宝'+'<span class="price">'+'￥500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var gl=
        '<li>'+
          '<a href="/qwta/product_preview/6" target="_blank">'+
            '<img src="../qwt/images/gl.png" width="250" height="250">'+
              '<h5>'+'微信盖楼'+'<span class="price">'+'￥199.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var znyy=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/yy.jpg" width="250" height="250">'+
              '<h5>'+'微信智能语音识别插件'+'<span class="price">'+'暂不销售'+'</span></h5>'+
          '</a>'+
        '</li>';
  var cxb=
        '<li>'+
          '<a href="/qwta/product_preview/5" target="_blank">'+
            '<img src="../qwt/images/zdfk.png" width="250" height="250">'+
              '<h5>'+'自动发卡工具'+'<span class="price">'+'￥199.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var rwb=
        '<li>'+
          '<a href="/qwta/product_preview/4" target="_blank">'+
            '<img src="../qwt/images/rwb.jpg" width="250" height="250">'+
              '<h5>'+'任务宝服务号版'+'<span class="price">'+'￥500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var qfx=
        '<li>'+
          '<a href="/qwta/product_preview/7" target="_blank">'+
            '<img src="../qwt/images/qfx.png" width="250" height="250">'+
              '<h5>'+'全员分销'+'<span class="price">'+'￥600.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var dld=
        '<li>'+
          '<a href="/qwta/product_preview/9" target="_blank">'+
            '<img src="../qwt/images/dld.png" width="250" height="250">'+
              '<h5>'+'代理哆'+'<span class="price">'+'￥3600.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var yyb=
        '<li>'+
          '<a href="/qwta/product_preview/10" target="_blank">'+
            '<img src="../qwt/images/yyb.png" width="250" height="250">'+
              '<h5>'+'预约宝'+'<span class="price">'+'￥300.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var wzb=
        '<li>'+
          '<a href="/qwta/product_preview/11" target="_blank">'+
            '<img src="../qwt/images/wzb.png" width="250" height="250">'+
              '<h5>'+'神码云直播'+'<span class="price">'+'￥720.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var xxb=
        '<li>'+
          '<a href="/qwta/product_preview/12" target="_blank">'+
            '<img src="../qwt/images/xxb.png" width="250" height="250">'+
              '<h5>'+'消息宝'+'<span class="price">'+'￥249.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var fxb=
        '<li>'+
          '<a href="/qwta/product_preview/8" target="_blank">'+
            '<img src="../qwt/images/ddb.jpg" width="250" height="250">'+
              '<h5>'+'订单宝'+'<span class="price">'+'￥3000.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var mmh=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/mmh.jpg" width="250" height="250">'+
              '<h5>'+'晨光卖萌货"国民萌值大比拼"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/mmh.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var chihuo=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/chihuo.jpg" width="250" height="250">'+
              '<h5>'+'良品铺子"吃货众生相"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/chihuo.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var dzp=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/dzp.jpg" width="250" height="250">'+
              '<h5>'+'大转盘'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/dzp.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var yaoyy=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/yaoyy.jpg" width="250" height="250">'+
              '<h5>'+'晨光文具“摇一摇开运灵签”'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/yaoyy.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var hsy=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/hsy.jpg" width="250" height="250">'+
              '<h5>'+'良品铺子"有料好声音"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/hsy.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var cyc=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/cyc.jpg" width="250" height="250">'+
              '<h5>'+'纯亭"测一测你离王子有多远"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/cyc.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var youzan=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/youzan.jpg" width="250" height="250">'+
              '<h5>'+'有赞微商城"吃货大作战"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/youzan.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var nrw=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/nrw.jpg" width="250" height="250">'+
              '<h5>'+'男人袜"找妹子"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/nrw.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var lhj=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/lhj.jpg" width="250" height="250">'+
              '<h5>'+'宁波万象城"老虎机小游戏"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/lhj.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var hxb=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/hxb.jpg" width="250" height="250">'+
              '<h5>'+'弘信宝"端午节语音贺卡"'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/xhb.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var roseonly=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/roseonly.jpg" width="250" height="250">'+
              '<h5>'+'中华小曲库调用中...'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/roseonly.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var zzy=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/zzy.jpg" width="250" height="250">'+
              '<h5>'+'三里屯要出大事了...'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/zzy.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var qf=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/qf.jpg" width="250" height="250">'+
              '<h5>'+'来，我给大家唱了一首求佛...'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/qf.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var zx=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/zx.jpg" width="250" height="250">'+
              '<h5>'+'最炫的民族风...'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/zx.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var wj=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/wj.jpg" width="250" height="250">'+
              '<h5>'+'吴酒'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/wj.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var xiq=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/xiq.jpg" width="250" height="250">'+
              '<h5>'+'戏球名茶'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/xiq.png" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var chijian=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/chijian.jpeg" width="250" height="250">'+
              '<h5>'+'尺简（微信小程序定制）'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/chijian.jpg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var phone=
        '<li>'+
          '<a>'+
            '<img src="../qwt/images/H5/phone.jpg" width="250" height="250">'+
              '<h5>'+'范冰冰的未接来电'+'</h5>'+
            '<span class="intro">'+
              '<img src="../qwt/images/erweima/phone.jpeg" width="250" height="250">'+
            '</span>'+
          '</a>'+
        '</li>';
  var dpm=
        '<li>'+
          '<a href="/qwta/product_preview/16" target="_blank">'+
            '<img src="../qwt/images/dpm.png" width="250" height="250">'+
              '<h5>'+'数据大屏幕'+'<span class="price">'+'￥1580.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var hby=
        '<li>'+
          '<a href="/qwta/product_preview/14" target="_blank">'+
            '<img src="../qwt/images/hby.png" width="250" height="250">'+
              '<h5>'+'红包雨'+'<span class="price">'+'￥200.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var zdf=
        '<li>'+
          '<a href="/qwta/product_preview/20" target="_blank">'+
            '<img src="../qwt/images/zdf.png" width="250" height="250">'+
              '<h5>'+'公众号自动下发小程序卡片工具'+'<span class="price">'+'￥300.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var dzkf=
        '<li>'+
          '<a href="/qwta/product_preview/21" target="_blank">'+
            '<img src="../qwt/images/dzkf.png" width="250" height="250">'+
              '<h5>'+'定制开发服务'+'<span class="price">'+'价格需评估'+'</span></h5>'+
          '</a>'+
        '</li>';
  var yyhb=
        '<li>'+
          '<a href="/qwta/product_preview/17" target="_blank">'+
            '<img src="../qwt/images/yyhb.png" width="250" height="250">'+
              '<h5>'+'语音红包'+'<span class="price">'+'￥249.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var kjb=
        '<li>'+
          '<a href="/qwta/product_preview/24" target="_blank">'+
            '<img src="../qwt/images/kjb.png" width="250" height="250">'+
              '<h5>'+'砍价宝'+'<span class="price">'+'￥500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var rwd=
        '<li>'+
          '<a href="/qwta/product_preview/25" target="_blank">'+
            '<img src="../qwt/images/rwd.jpg" width="250" height="250">'+
              '<h5>'+'任务宝订阅号版'+'<span class="price">'+'￥1500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
  var xdb=
        '<li>'+
          '<a href="/qwta/product_preview/27" target="_blank">'+
            '<img src="../qwt/images/xdb.png" width="250" height="250">'+
              '<h5>'+'推荐有礼'+'<span class="price">'+'￥500.00'+'</span></h5>'+
          '</a>'+
        '</li>';
$(document).ready(function() {
  $('#myscrollbox ul').html(weixinhongbao+wfb+gl+cxb+wzb+fxb+xxb+hby+dzkf+kjb+xdb);
  $('#myscrollbox2 ul').html(rwb+dld+qfx+yyb+dka+wdb+dpm+zdf+yyhb+rwd);
  $('#myscrollbox2').show();
  $('#myscroll').css('height','600px');
  newtype();
});
function newtype(){

    var blw=$("#myscrollbox li").width();
    //获取单个子元素所需宽度
    var liArr = $("#myscrollbox ul").children("li");
    //获取子元素数量
    var mysw = $("#myscroll").width();
    //获取子元素所在区域宽度
    var mus = parseInt(mysw/blw);
    //计算出需要显示的子元素的数量
    var length = Math.ceil(liArr.length/4);
    //计算子元素可移动次数（被隐藏的子元素数量）
    var i=0
    if (liArr.length<5) {
      $('#right').hide();
      $('#left').hide();
    }else{
      $('#right').show();
      $('#left').hide();
    };
    $("#right").click(function(){
      i++
      //点击i加1
      if(i<length){
          $("#myscrollbox").css("left",-(blw*i*4));
          $("#myscrollbox2").css("left",-(blw*i*4));
        //子元素集合向左移动，距离为子元素的宽度乘以i。
      }else{
        i--;
          $("#myscrollbox").css("left",-(blw*i*4));
          $("#myscrollbox2").css("left",-(blw*i*4));
        //超出可移动范围后点击不再移动。最后几个隐藏的元素显示时i数值固定位已经移走的子元素数量。
        }
      if (i+1<length) {
      $('#right').show();
      }else{
      $('#right').hide();
      };
      $('#left').show();
      });
    $("#left").click(function(){
      i--
      //点击i减1
      if(i>=0){
         $("#myscrollbox").css("left",-(blw*i*4));
         $("#myscrollbox2").css("left",-(blw*i)*4);
       //子元素集合向右移动，距离为子元素的宽度乘以i。
      }else{
       i=0;
       $("#myscrollbox").css("left",0);
       $("#myscrollbox2").css("left",0);
       //超出可移动范围后点击不再移动。最前几个子元素被显示时i为0。
        }
      if (i>0) {
      $('#left').show();
      }else{
      $('#left').hide();
      };
      $('#right').show();
      });
}
$('#all').click(function(){
  $('#myscrollbox ul').html(weixinhongbao+wfb+gl+cxb+wzb+fxb+xxb+hby+dzkf+kjb+xdb);
  $('#myscrollbox2 ul').html(rwb+dld+qfx+yyb+dka+wdb+dpm+zdf+yyhb+rwd);
  $('#myscrollbox2').show();
  $('#myscroll').css('height','600px');
  newtype();
  $('.examplelogo').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#xifen').click(function(){
  $('#myscrollbox ul').html(weixinhongbao+wfb+rwb);
  $('#myscrollbox2 ul').html(wdb+dka+rwd);
  $('#myscrollbox2').show();
  $('#myscroll').css('height','600px');
  newtype();
  $('.examplelogo').hide();
  $('.examplexifen').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#fenxiao').click(function(){
  $('#myscrollbox ul').html(dld+qfx+fxb);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplefenxiao').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#cuxiao').click(function(){
  $('#myscrollbox ul').html(yyb+xxb+kjb+xdb);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplecuxiao').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#zidongfaka').click(function(){
  $('#myscrollbox ul').html(cxb);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplezidonfaka').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#smallappyinliu').click(function(){
  $('#myscrollbox ul').html(zdf);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplesmallappyinliu').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#weixinhudong').click(function(){
  $('#myscrollbox ul').html(gl+yyhb);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.exampleweixinhudong').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#smzhibo').click(function(){
  $('#myscrollbox ul').html(wzb);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplesmzhibo').show();
  $('.example').hide();
  $('.example').hide();
  $('.examplebox').show();
});
$('#sjdpm').click(function(){
  $('#myscrollbox ul').html(dpm);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplesjdpm').show();
  $('.example').hide();
  $('.examplebox').show();
});
$('#offline').click(function(){
  $('#myscrollbox ul').html(hby);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.example').hide();
});
$('#H5game').click(function(){
  $('#myscrollbox ul').html(xiq+mmh+dzp+cyc+youzan+lhj+roseonly+qf+wj);
  $('#myscrollbox2 ul').html(chihuo+yaoyy+hsy+nrw+hxb+zzy+zx+phone);
  $('#myscrollbox2').show();
  $('#myscroll').css('height','600px');
  newtype();
  $('.examplelogo').hide();
  $('.exampleH5game').show();
  $('.examplebox').hide();
});
$('#smallapp').click(function(){
  $('#myscrollbox ul').html(chijian);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplesmallapp').show();
  $('.examplebox').hide();
});
$('#dingzhikaifa').click(function(){
  $('#myscrollbox ul').html(dzkf);
  $('#myscrollbox2').hide();
  $('#myscroll').css('height','300px');
  newtype();
  $('.examplelogo').hide();
  $('.examplebox').hide();
});
$('.apptype').click(function(){
  $('#tabs li').removeClass('active');
  $(this).parent().addClass('active');
  $("#myscrollbox").css("left",0);
  $("#myscrollbox2").css("left",0);
})

    var handlerEmbed = function (captchaObj) {
        $("#embed-submit").click(function (e) {
            var validate = captchaObj.getValidate();
            if (!validate) {
                $("#notice")[0].className = "show";
                setTimeout(function () {
                    $("#notice")[0].className = "hide";
                }, 2000);
                e.preventDefault();
            }
        });
        // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
        captchaObj.appendTo("#embed-captcha");
        captchaObj.onReady(function () {
            $("#wait")[0].className = "hide";
        });
        // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
    };
    $.ajax({
        // 获取id，challenge，success（是否启用failback）
        url: "/qwta/login?StartCaptchaServlet=1&t=" + (new Date()).getTime(), // 加随机数防止缓存
        type: "get",
        dataType: "json",
        success: function (data) {
            console.log(data);
            // 使用initGeetest接口
            // 参数1：配置参数
            // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                new_captcha: data.new_captcha,
                product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
            }, handlerEmbed);
            $('#embed-captcha').css({
              'margin-top': '20px'
            });
            setTimeout(function(){
              $('.geetest_detect').css({
                'width': '100%'
              });
              $('.geetest_wind').css({
                'width': '100%'
              });
              $('.geetest_holder').css({
                'width': '100%'
              });
            },500);
        }
    });

</script>
</html>
