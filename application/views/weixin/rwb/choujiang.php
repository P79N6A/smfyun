<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1.0 user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="full-screen" content="yes">
    <meta name="x5-full-screen" content="true">
    <meta name="browsermode" content="application">
    <meta name="x5-page-mode" content="app">
    <title>商品详情</title>

    <link rel="stylesheet" type="text/css" href="../dist/css/detial.css">
    <script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>

    <style>
    .mtk{
      position:fixed;
      left:0px;
      top:0px;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,.5);
      -webkit-animation: fadeIn 0.6s ease;
      z-index: 100000;
    }
    .mtkcont{
      width:300px;
      height:195px;
      position: absolute;
      top:40%;
      left:50%;
      margin-top:-90px;
      margin-left: -150px;
      background-color: #fff;
      text-align: center;
      border-radius: 15px;
    }
    .mtkpic{
      width:50px;
      margin-top:3%;
    }
    .textmtk{
      margin-top: 6%;
      color:#888;
      font-size: 13px;
      width: 92%;
        margin-left: 4%;
    }
    .queding{
      width: 60%;
      height: 30px;
      line-height: 30px;
      border-radius: 15px;
      border:1px solid transparent;
      background-color: #58c401;
      color:#fff;
      margin-top:10%;
      font-size: 13px;
    }

    /*生成海报*/
    .imgload{
      position: absolute;
      top:24%;
      left:50%;
      margin-left: -50px;
    }
    .imgload~span{
      color:#fff;
      font-size: 18px;
      position: absolute;
      top:40%;
      left:13%;
    }
    /*a链接样式*/
    a, a:link, a:visited, a:hover, a:active{
      text-decoration:none;
      color:#fff;
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
            z-index: 10;
            background-color: rgba(0, 0, 0, .4);
            -webkit-animation: fadeIn .6s ease;
        }
    .imgbg00{
      width:300px;
      height:180px;
      position: absolute;
      top:40%;
      left:50%;
      margin-left:-150px;
      margin-top: -90px;
    }
    .imgbg00>img{
      width:300px;
      height:180px;
    }
    .btn00{
      position: absolute;
      top:67%;
      left:15%;
      width:80%;
    }
    input{
      outline: none;
      -webkit-appearance: none;
    }
    textarea {  -webkit-appearance: none;}
    .ok2, .notok2{
      width:40%;
      background-color: #58c401;
      border:1px solid transparent;
      color: #fff;
      height:30px;
      line-height: 24px;
      border-radius: 15px;
      font-size: 13px;
    }
    .notok2{
      background-color:#c8c8c8;
      margin-left: 10%;
    }
    </style>

</head>


    <body class="page-detail" data-page="detail" ontouchstart="">
    <div class="view js-page-view" id="page-view-detail">
        <div class="detail-header" id="detail-header">

                <div style="width: 320px;">
                 <img src="http://cdn.jfb.smfyun.com/rwb/images/item/<?=$thitem->id?>.v<?=$thitem->lastupdate?>0.jpg" alt="<?=$thitem->name?>">

         <div class="swiper-pagination">

         </div>
     </div>
      <div class="goods-info">
        <span class="type">手气大比拼</span>
        <span class="name"><?=$thitem->name?></span>
    </div>
</div>
<div id="chose-price" class="chose-price" style="display:none">
 <div class="price-row js-price-select state-selected" data-type="0">  <span> <b><?=$thitem->price?></b>滴币+<b>0</b>元 </span>
    <b class="coins"><?=$thitem->score?></b><?=$scorename?>
     <span class="check mall-radio checked"> </span>
 </div>
</div>
<div id="pay-bar" class="pay-bar"> 单价：
    <span class="pay-total js-total-price">
     <b class="coins"><?=$thitem->score?></b><?=$scorename?> </span>
      <?php if ($dlimit==1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已经达到兑换上限</a>
                <?php elseif($thitem->stock <= 0):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已换完</a>
                <?php elseif ($user2['score'] < $thitem->score):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">您的<?=$config['score']?>不够</a>
                <?php elseif ($thitem->endtime && strtotime($thitem->endtime) < time()):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">已截止</a>
                <?php elseif ($climit==1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">此奖品抽奖次数达到上限</a>
                <?php elseif ($user2['lock'] == 1):?>
                    <a href="javascript:;" class="pay-btn js-pay disabled">您的账号已被锁定</a>
                <?php else:?>
                <a  class="pay-btn">立即抽奖</a>
                <?php endif?>
      <script>

   $('.pay-btn').click(function() {
            $.ajax({
              url: '/rwb/choujiang?choujiang=true&iid=<?=$thitem->id?>',
              type: 'get',
              dataType: 'json',
              timeout:15000,
        beforeSend:function(XMLHttpRequest){
              //alert('远程调用开始...');
              // $("#loading").html("<img src='dist/img/loading.gif' />");
              $("#imgload").css({'z-index':'2','display':'block','height':$("html").height(),'width': $("html").width()});
              },
              success: function (res){
                      // alert(res);
                      setTimeout("",5000);
                      if (res.fl==0) {
                      $("body").append(
              "<div class=\"mtk\">"+
                "<div class=\"mtkcont\">"+
                  "<img class=\"mtkpic\" src=\"../dist/img/daddy.gif\">"+
                  "<div class=\"textmtk\">"+res.con+"</div>"+
                  "<a href=\"/rwb/prize/<?=$thitem->id?>/1\"><input type=\"button\" class=\"queding ok2\" value=\"立即领奖\" id=\"one\" style=\"cursor:pointer\"></a>"+
                "</div>"+
              "</div>"
                      );
                    }else
                    {
                      $("body").append(
              "<div class=\"mtk\">"+
                "<div class=\"mtkcont\">"+
                  "<img class=\"mtkpic\" src=\"../dist/img/daddy.gif\">"+
                  "<div class=\"textmtk\">"+res.con+"</div>"+
                  "<input type=\"button\" class=\"queding ok2\" value=\"本宫知道了\"  style=\"cursor:pointer\" id=\"one\">"+
                "</div>"+
              "</div>"
            );};
              },
          });
          $(document).on('click','#one',function(){

            $('.mtk').remove();
          });
        });

   </script>
  </div>
   <div class="desc-main" id="desc-main">
    <div class="rich-txt">
     <div><b><u>商品简介：</u></b>
     </div>
     <div><?=$thitem->desc?>
        <br><br>
        </div>

        <div>
            <br>
        </div>
        <div>
            <b>
                <u>使用范围：</u>
            </b>
        </div>
        <!-- <div>全国<br><br>
        </div> -->




        <div><br>
        </div>
        <div><br>
        </div>
    </div>
     </div>

  <!-- <div class="important-tip">
    <span class="hd">重要说明</span>
     <div class="bd"> 商品兑换流程请仔细参照商品详情页的“兑换流程”、“注意事项”与“使用时间”，除商品本身不能正常兑换外，商品一经兑换，一律不退还。（如商品过期、兑换流程操作失误、仅限新用户兑换）
      </div>
       </div> -->
   </div>
</body>



