<!DOCTYPE html>
<html id="full">
   <head>
       <meta charset="utf-8">
       <title>神码浮云数据中心</title>
   </head>
   <!-- <link rel="stylesheet" href="../yyx/odometer/themes/odometer-theme-car.css" /> -->
   <link rel="stylesheet" href="../yyx/odometer/themes/odometer-theme-digital.css" />
   <link rel="stylesheet" href="../yyx/odometer/themes/odometer-theme-train-station.css" />
   <link rel="stylesheet" href="../yyx/dist/css/semantic.min.css" />
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css"> -->
   <link href="//cdn.bootcss.com/animate.css/3.5.2/animate.min.css" rel="stylesheet">
   <style type="text/css">
/*   body{
    font-family: Microsoft YaHei;
   }*/
   /** {
    background: #000 !important;
    color: #0f0 !important;
    outline: solid #f00 1px !important;
}*/
   *{
    padding: 0;
    margin: 0;
   }
   body{
    margin: 0px;
    font-family: 微软雅黑, MicrosoftYahei, sans-serif;
    /*overflow: hidden;*/
    border-top: 1px solid #fff;
    box-sizing:border-box;
   }
   p{
    font-size: 1.2em;
    /*color: white;*/
   }
   .odometer {
    font-size: 40px;
   }
   div{
    display: inline-block;
   }
   .line0{
    display: inline-block;
    width: 100%;
    height: 10%;
    color: #6DE0FF;
    font-size: 47px;
    font-weight: bold;
    line-height: 10%;
    padding-top: 10px;
    padding-bottom: 10px;
    text-align: center;
   }
   .line1, .line2{
    display: block;
   }
   .line1{
    height: 30%;
    padding-top: 1%;
   }
   .line2{
    height: 60%;
   }
   .line3{
    /*height: 100%;*/
    height: 660px;
    width: 24%;
    vertical-align: top;
    margin-top: 310px;
   }
   .title{
    color: yellow;
    font-size:22px;
    margin-bottom: 20px;
    margin-top: 20px;
   }
   .number{
    background-color: rgba(182, 247, 161, 0.22);
    margin-left: 10px;
    color: white;
    width: 22px;
    display: inline-block;
    font-size: 40px;
    text-align: center;
    padding: 15px;
    border-radius: 10px;
   }
   .money{
    margin-left: 10px;
    color: white;
    display: inline-block;
    font-size: 40px;
    text-align: center;
    border-radius: 10px;
    line-height: 50px;
    vertical-align: middle;
   }
   table{
    width: 100%;
    text-align: center;
    color: white;
    font-size: 1.2em;
    padding: 0px;
    line-height: 27px;
   }
   .tidhead{
    width: 33%;
    text-align: center;
    display: inline-block;
   }
   .tidhead1{
    width: 46%;
    text-align: center;
    display: inline-block;
   }
   .tidhead2{
    width: 20%;
    text-align: center;
    display: inline-block;
   }
   .thead,#ticker{
    width: 100%;
    color: #0ECFED;
   }
   #ticker{
    height: 366px;
    margin-top: 20px;
    font-size: 1em;
   }
   .tidcontent{
    display:inline-block;
    width: 33%;
    /*font-size: 1em;*/
    text-align: center;
   }
   .tidcontent1{
    display:inline-block;
    width: 46%;
    /*font-size: 1em;*/
    text-align: center;
   }
   .tidcontent2{
    display:inline-block;
    width: 20%;
    /*font-size: 1em;*/
    text-align: center;
   }
   .border{
        /*background:url('../yyx/dist/img/border.png');*/
    background-size: cover;
    border-top: 1px solid #28769A;
    border-right: 1px solid #28769A;
    border-left: 1px solid #28769A;
    padding-left: 10px;
    padding-right: 10px;
    vertical-align: top;
   }
   #order{
    width:100%;
    height:427px;
    margin-top:160px;
    padding-left:15px;
    vertical-align: top;
   }
   .item{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
   }
   .today_money{
    font-size: 56px;
   }
   .hot_item,.thead,.hot_item_num{
    font-size: 1em;
   }
   .label{
    color: white;
   }
   #choose_time{
    position: absolute;
    top: 1190px;
    left: 1450px;
   }
   table{
    color:rgb(15, 210, 240);
    border-spacing:3px;
   }
   .item_second{
    background-color:rgba(22, 79, 132, 0.560784);
    color:rgb(15, 210, 240);
   }
   .column0{
    margin-left: 8px;
    width: 470px;
    /*float: left;*/
    position: absolute;
   }
   #xinkehu{
    margin-left: 51px;
    position: relative;
    width: 170px;
   }
   #laokehu{
    margin-left: 23px;
    position: relative;
    width: 170px;
   }
   .text1{
    font-size: 16px;
    line-height: 21px;
    color: #fff;
    padding: 4px 14px;
  }
  .rate1{
    font-size: 25px;
    color: #fff;
    position: absolute;
    top: 16px;
    right: 10px
  }
  .column1{
    margin-left: 44px;
    width: 870px;
    /*float: left;*/
    position: absolute;
    left: 482px;
  }
  .column2{
    margin-left: 37px;
    width: 474px;
    position: absolute;
    left: 1398px;
  }
  #today{
    margin-left: 36px;
    margin-top: 192px;
    width: 800px;
    padding: 24px 19px 21px 19px;
    height: 128px;
    /* width: 50%; */
    /* height: 100%; */
    vertical-align: middle;
    /* padding: 0 10px 0 10px; */
    text-align: center;
    /* padding-top: 5%; */
    position: relative;
  }
  #date1{
    position: absolute;
    color: #fff;
    top: -38px;
    right: 7px;
    font-size: 24px;
  }
  #all{
    margin-left: 128px;
    margin-top: 28px;
    width: 612px;
    height: 97px;
    padding: 34px 0 33px 67px;
    font-size: 30px;
    color: #fff;
    line-height: 30px;
  }
  .all{
    color: #E60012;
    font-weight: bold;
  }
  .city_top{
    display: inline-block;
    margin-top: 107px;
    height: 309px;
    width: 100%;
  }
  #choose_time2{
    display: inline-block;
    margin-top: 70px;
    margin-left: 70px;
  }
  .button1{
    background-color: #06528d;
    width: 50px;
    height: 28px;
    margin-right: 2px;
    color: #01fffc;
    font-size: 17px;
    text-align: center;
    line-height: 28px;
    border: 0;
    padding: 0;
  }
  #area{
    width: 430px;
    /*margin-top: 24px;*/
    height: 180px;
    margin-left: 20px;
  }
  .goods_top{
    margin-top: 11px;
    width: 100%;
    height: 310px;
  }
  .goods_top_money{
    margin-top: 9px;
    width: 100%;
    height: 310px;
  }
  #topmoney{
    margin-top: 50px;
    margin-left: 0;
    width: 472px;
    height: 250px;
  }
  #topmount{
    margin-top: 82px;
    margin-left: 24px;
    width: 420px;
    height: 220px;
  }
  .maintitle{
    position: absolute;
    color: #00ffff;
    font-size: 58px;
    font-weight: bold;
    width: 100%;
    top: 4.5%;
    text-align: center;
  }

   </style>
   <body style="width: 1920px; height: 1080px;  transform-origin: left top 0px;background:url('../yyx/dist/img/bgnewnewnew.png') ;background-repeat:no-repeat;background-size:cover;visibility:hidden">
    <div class="maintitle">神码浮云实时交易数据中心</div><div class="column0">
       <div id="order">
        <div class="thead" style="margin-top:20px;"><span class='tidhead'>时间</span><span class='tidhead1'>地点</span><span class='tidhead2'>订单金额</span></div>
          <div id="ticker">
        <?php if($order_details):?>
          <?php foreach ($order_details as $v): ?>
            <p><span class='tidcontent'><?=$v[0]?></span><span class='tidcontent1'><?=$v[1]?></span><span class='tidcontent2'><?=$v[2]?></span></p>
          <?php endforeach ?>
        <?php endif?>
          </div>
       </div>
       <?php
       $rate['sum']=$result['old']+$result['new'];
       if ($rate['sum']==0) {
         $rate['old']=0;
         $rate['new']=0;
       }else{
         $rate['old']=$result['old']*100/$rate['sum'];
         $rate['new']=$result['new']*100/$rate['sum'];
       }
       ?>
           <div id='vip' style="width:100%;height:308px;margin-top:63px;display:inline-block"></div>
           <div id="xinkehu">
             <div class="text1">新客户<br><?=$result['new']?>人</div>
             <div class="rate1"><?=intval($rate['new'])?>%</div>
           </div>
           <div id="laokehu">
             <div class="text1">老客户<br><?=$result['old']?>人</div>
             <div class="rate1"><?=intval($rate['old'])?>%</div>
           </div>

    </div><div class="column1">
       <div id="today">
            <div class="today_money odometer" style="color:rgb(255, 204, 0);line-height:1.4em;">
                0
            </div>
            <?php $time=time();?>
            <div id="date1">
              <?=date("Y-m-d H:i",$time)?>
            </div>
       </div>
       <div id="all">
            累计交易金额：
              <span class='all'><?=$result['all_done']?>元</p>
       </div>
        <div id="map" style="width:100%;height:512px;margin-top:97px;display:inline-block"></div>
    </div>
    <div class="column2">
      <div class="city_top">
        <div id='choose_time2' class="blue ui orange buttons">
          <button class="button1 ri2">今日</button>
          <button class="button1 yue2 ">本月</button>
          <button class="button1 nian2">本年</button>
        </div>
       <div id="area">
       </div>
       </div>
       <div class="goods_top">
       <div id="topmount">
       </div>

       </div>
       <div class="goods_top_money">
       <div id="topmoney">
       </div>
       </div>

    </div>

       <!-- <div id='choose_time' class="blue ui orange buttons">
          <button class="ui orange button active ri">今日</button>
          <button class="ui orange button yue ">本月</button>
          <button class="ui orange button nian">本年</button>
        </div> -->
    </div>
       <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/echarts-all-3.js"></script>
       <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/dataTool.min.js"></script>
       <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/china.js"></script>
       <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/map/js/world.js"></script>
       <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ZUONbpqGBsYGXNIYHicvbAbM"></script>
       <script type="text/javascript" src="http://echarts.baidu.com/gallery/vendors/echarts/extension/bmap.min.js"></script>
       <script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
       <script src="../yyx/dist/js/semantic.min.js"></script>
      <script type="text/javascript">
$('#today').click(function() {
 var el = document.getElementById("full");
 el.webkitRequestFullscreen();
});
// $('#all').click(function() {
//  document.webkitCancelFullScreen();
// });
        window.global = window;
        (function() {

            $(window, document).resize(function() {
                // resizeWidth();
            }).load(function() {
                resizeWidth();
                $('body').css('visibility', 'visible');
            });

            function resizeWidth() {
                var ratio = $(window).width() / 1920;
                $('body').css({
                    transform: "scale(" + ratio + ")",
                    transformOrigin: "left top",
                    backgroundSize: "100%"
                });
            }
        })();
      </script>
       <script>
        window.odometerOptions = {
            format: '(ddd).dd'
        };
        setTimeout(function() {
            $('.odometer').html(<?=$result['today_done']?>);
        }, 1000);
        $('#example2').progress({
          percent:<?=$result['done_goal']/$result['sx_goal']?>*100
        });
       </script>
       <script src="../yyx/odometer/odometer.min.js"></script>
       <script type="text/javascript">
       //topmount
          window.topmount_area = document.getElementById("topmount");
          window.topmount_option_area = echarts.init(window.topmount_area);

          // window.area_dataAxis = ['武汉', '上海', '北京', '深圳', '广州', '杭州','武汉', '上海', '北京', '深圳', '广州', '杭州'];
          window.topmount_dataAxis = [
                  "",
                  <?php foreach ($result['hot_items'] as $k => $v): ?>
                  "<?=$v['title']?>",
                  // "<?=mb_substr($v['title'],0,13,'utf-8')?>…",
                  <?php endforeach ?>
                  ""
            ]
          // window.area_data = [220, 182, 191, 234, 290, 1000,220, 182, 191, 234, 290, 330];
          window.topmount_dataold = [
                  0,
                  <?php foreach ($result['hot_items'] as $k => $v): ?>
                  <?=intval($v['oldCTN'])?>,
                  <?php endforeach ?>
                  0
            ]
          window.topmount_datanew = [
                  0,
                  <?php foreach ($result['hot_items'] as $k => $v): ?>
                  <?=intval($v['newCTN'])?>,
                  <?php endforeach ?>
                  0
            ]
          window.yMax1 = <?=intval($result['hot_items'][0]['zongfen'])?>;
window.dataShadowmount = [];
for (var i = 0; i < window.topmount_dataold.length; i++) {
    window.dataShadowmount.push(window.yMax1);
}
          window.topmount_option = {
              tooltip:{
                show: true,
                      formatter:function(params){
                          console.log(params);
                        console.log(params.name);
                          var name = params.name.split("$").join("");
                          var val = params.value;
                          return name+'<br>'+'销量：'+val;
                      }
              },
              grid:{
                  left:0,
                  right:0,
                  top:10,
                  bottom:0,
                  containLabel:true
              },
              xAxis: {
                  data: window.topmount_dataAxis,
                  axisLabel: {
    // lineHeight: 6,
    // interval: 0,
    //                   fontSize: 6,
    //                   textStyle: {
    //                       color: '#fff'
    //                   },
                      show: false,
                      // formatter:function(val){
                      //     return val.split("$").join("\n");
                      // }
                  },
                  axisTick: {
                      show: false
                  },
                  axisLine: {
                      show: false
                  },
                  width: '16px',
                  z: 10,
              },
              yAxis: {
                  axisLine: {
                      show: false
                  },
                  axisTick: {
                      show: false
                  },
                  axisLabel: {
                      fontSize: 12,
                      show: true,
                      textStyle: {
                          color: '#ffffff'
                      }
                  },
                  splitLine: {
                        show: true,
                        lineStyle: {
                          color: ['#1c293d']
                        }
                  }
              },
    series: [
        // { // For shadow
        //     type: 'bar',
        //               barWidth:'12px',
        //     itemStyle: {
        //         normal: {color: '#132741'}
        //     },
        //     barGap:'-100%',
        //     barCategoryGap:'40%',
        //     data: window.dataShadowmount,
        //     animation: false
        // },
        {
            name: '新客户购买量',
            type: 'bar',
            stack: '总购买量',
                      barWidth:'12px',
            itemStyle: {
                normal: {
                    color: new echarts.graphic.LinearGradient(
                        0, 0, 0, 1,
                        [
                            {offset: 0, color: '#e60012'},
                            {offset: 0.5, color: '#e60012'},
                            {offset: 1, color: '#e60012'}
                        ]
                    ),
                              label: {
                                  show: false,
                              },
                },
            },
            data: window.topmount_datanew,
        },
        {
            name: '老客户购买量',
            type: 'bar',
            stack: '总购买量',
                      barWidth:'12px',
            itemStyle: {
                normal: {
                    color: new echarts.graphic.LinearGradient(
                        0, 0, 0, 1,
                        [
                            {offset: 0, color: '#58feff'},
                            {offset: 0.5, color: '#46d3f4'},
                            {offset: 1, color: '#35a9e8'}
                        ]
                    ),
                              label: {
                                  show: false,
                              },
                },
            },
            data: window.topmount_dataold,
        }
    ]
          };
           window.topmount_option_area.setOption(window.topmount_option);


       //topmountend
function reverse(a){
var len = a.length;//获取数组的长度
var mid = parseInt(len/2);//获取数组长度的中间值，由于有可能是不是整数，将得到的数字转为整数
//利用交换的思想，将数组内的元素放在相应的地方，循环次数为mid+1次
for(i = 0;i <= mid;i++){
  var temp = a[i];
  a[i] = a[len-1-i];
  a[len-1-i] = temp;
}
return a;
}

       //topmoney

          window.topmoney_area = document.getElementById("topmoney");
          window.topmoney_option_area = echarts.init(window.topmoney_area);

          // window.area_dataAxis = ['武汉', '上海', '北京', '深圳', '广州', '杭州','武汉', '上海', '北京', '深圳', '广州', '杭州'];
          window.topmoney_dataAxisfan = [
                  "",
                  <?php foreach ($result['hot_items_num'] as $k => $v): ?>
                  <?php if($k<5):?>
                  "<?=$v['title']?>",
                  // "<?=mb_substr($v['title'],0,5,'utf-8')?>",
                  <?php endif?>
                  <?php endforeach ?>
                  ""
            ];
          window.topmoney_dataAxis = reverse(window.topmoney_dataAxisfan);
          window.topmoney_datafan = [
                  0,
                  <?php foreach ($result['hot_items_num'] as $k => $v): ?>
                  <?php if($k<5):?>
                  <?=intval($v['zongfen'])?>,
                  <?php endif?>
                  <?php endforeach ?>
                  0
            ];
          // window.area_data = [220, 182, 191, 234, 290, 1000,220, 182, 191, 234, 290, 330];
          window.topmoney_data = reverse(window.topmoney_datafan);
          window.xMax = <?=$result['hot_items_num'][0]['zongfen']?>;
window.dataShadowmoney = [];
for (var i = 0; i < window.topmoney_data.length; i++) {
    window.dataShadowmoney.push(window.xMax);
}
          window.topmoney_option = {
              tooltip:{
                show: true,
                      formatter:function(params){
                          console.log(params);
                        console.log(params.name);
                          var name = params.name.split("$").join("");
                          var val = params.value;
                          return name+'<br>'+'销售额：'+val;
                      }
              },
              grid:{
                  left:0,
                  top:10,
                  bottom:0,
                  containLabel:true
              },
              xAxis: {
                  axisLabel: {
                      fontSize: 10,
                      textStyle: {
                          color: '#fff'
                      },
                      show: true
                  },
                  axisTick: {
                      show: false
                  },
                  axisLine: {
                      show: false
                  },
                  z: 10,
                  splitLine: {
                        show: true,
                        lineStyle: {
                          color: ['#012f65']
                        }
                  }
              },
              yAxis: {
                  data: window.topmoney_dataAxis,
                  axisLine: {
                      show: false
                  },
                  axisTick: {
                      show: false
                  },
                  axisLabel: {
                      fontSize: 8,
                      show: true,
                      interval: 0,
                      textStyle: {
                          color: '#ffffff'
                      },
                      formatter:function(val){
                          return val.split("$").join("\n");
                      }
                  },
              },
    series: [
        // { // For shadow
        //     type: 'bar',
        //               barWidth:'12px',
        //     itemStyle: {
        //         normal: {color: '#003574'}
        //     },
        //     barGap:'-100%',
        //     barCategoryGap:'40%',
        //     data: window.dataShadowmoney,
        //     animation: false
        // },
        {
            type: 'bar',
                      barWidth:'12px',
            itemStyle: {
                normal: {
                    barBorderRadius: 6,
                    color: new echarts.graphic.LinearGradient(
                        0, 0, 0, 1,
                        [
                            {offset: 0, color: '#58feff'},
                            {offset: 0.5, color: '#46d3f4'},
                            {offset: 1, color: '#35a9e8'}
                        ]
                    ),
                              label: {
                                  show: false,
                              },
                },
            },
            data: window.topmoney_data
        }
    ]
          };
           window.topmoney_option_area.setOption(window.topmoney_option);
           //topmoneyend

          window.area_area = document.getElementById("area");
          window.area_option_area = echarts.init(window.area_area);

          // window.area_dataAxis = ['武汉', '上海', '北京', '深圳', '广州', '杭州','武汉', '上海', '北京', '深圳', '广州', '杭州'];
          window.area_dataAxis = [
            <?php for ($i=0; $result['hot_area'][$i] ; $i++):?>
              <?php if($result['hot_area'][$i+1]):?>
                  "<?=mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8')?>",
              <?else:?>
                  "<?=mb_substr($result['hot_area'][$i]['receiver_city'],0,3,'utf-8')?>"
              <?endif?>
            <?php endfor?>
            ]
          // window.area_data = [220, 182, 191, 234, 290, 1000,220, 182, 191, 234, 290, 330];
          window.area_data = [
            <?php for ($i=0; $result['hot_area'][$i] ; $i++):?>
              <?php if($result['hot_area'][$i+1]):?>
                <?=$result['hot_area'][$i]['zongfen']?>,
              <?else:?>
                <?=$result['hot_area'][$i]['zongfen']?>
              <?endif?>
            <?php endfor?>
            ]
          window.yMax2 = <?=$result['hot_area'][0]['zongfen']?>;
window.dataShadow2 = [];
for (var i = 0; i < window.area_data.length; i++) {
    window.dataShadow2.push(window.yMax2);
}
          window.area_option = {
              tooltip:{
                show: true,
                      formatter:function(params){
                        var val = params.value;
                        return '销量:'+ val;
                      }
              },
              grid:{
                  left:0,
                  top:10,
                  bottom:0,
                  containLabel:true
              },
              xAxis: {
                  data: window.area_dataAxis,
                  axisLabel: {
                      inside: false,
                      fontSize: 10,
                      textStyle: {
                          color: '#fff'
                      },
                      show: true,
                      formatter:function(val){
                          return val.split("").join("\n");
                      }
                  },
                  axisTick: {
                      show: false
                  },
                  axisLine: {
                      show: false
                  },
                  z: 10
              },
              yAxis: {
                  axisLine: {
                      show: false
                  },
                  axisTick: {
                      show: false
                  },
                  axisLabel: {
                      show: true,
                      textStyle: {
                          color: '#ffffff'
                      }
                  },
                  splitLine: {
                        show: true,
                        lineStyle: {
                          color: ['#253347']
                        }
                  }
              },
    series: [
        // { // For shadow
        //     type: 'bar',
        //               barWidth:'12px',
        //     itemStyle: {
        //         normal: {color: '#1b2d46'}
        //     },
        //     barGap:'-100%',
        //     barCategoryGap:'40%',
        //     data: window.dataShadow2,
        //     animation: false
        // },
        {
            type: 'bar',
                      barWidth:'12px',
            itemStyle: {
                normal: {
                    color: new echarts.graphic.LinearGradient(
                        0, 0, 0, 1,
                        [
                            {offset: 0, color: '#58feff'},
                            {offset: 0.5, color: '#46d3f4'},
                            {offset: 1, color: '#35a9e8'}
                        ]
                    ),
                              label: {
                                  show: false,
                                  position: 'top',
                                  formatter: '{b}\n{c}',
                              },
                },
            },
            data: window.area_data
        }
    ]
          };
           window.area_option_area.setOption(window.area_option);
       </script>
       <script type="text/javascript">
            // window.trade_fold_line = document.getElementById("fold_line");
            // window.trade_option_fold_line = echarts.init(window.trade_fold_line);
            // window.trade_data_val = [
            // <?php for ($i=0; $result['line'][$i] ; $i++):?>
            //   <?php if($result['line'][$i+1]):?>
            //     <?=$result['line'][$i]['commision']?>,
            //   <?else:?>
            //     <?=$result['line'][$i]['commision']?>
            //   <?endif?>
            // <?php endfor?>
            // ]
            // // window.trade_data_val = [2220, 1682, 2791, 3000, 4090, 3230, 2910];
            // // window.trade_xAxis_val = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            // window.trade_xAxis_val = [
            // <?php for ($i=0; $result['line'][$i] ; $i++):?>
            //   <?php if($result['line'][$i+1]):?>
            //     "<?=$result['line'][$i]['time']?>",
            //   <?else:?>
            //     "<?=$result['line'][$i]['time']?>"
            //   <?endif?>
            // <?php endfor?>
            // ]
            // window.trade_option = {

            //     grid: {
            //         left: 10,
            //         top: '10%',
            //         bottom: 20,
            //         right: 40,
            //         containLabel: true
            //     },
            //     tooltip: {
            //         show: true,
            //         backgroundColor: '#384157',
            //         borderColor: '#384157',
            //         borderWidth: 1,
            //         formatter: '{b}:{c}',
            //         extraCssText: 'box-shadow: 0 0 5px rgba(0, 0, 0, 1)'
            //     },
            //     legend: {
            //         right: 0,
            //         top: 0,
            //         data: ['距离'],
            //         textStyle: {
            //             color: '#5c6076'
            //         }
            //     },
            //     title: {
            //         text: '成交额',
            //         x: '4.5%',
            //         top: '1%',
            //         textStyle: {
            //             color: '#5c6076'
            //         }
            //     },
            //     xAxis: {
            //         data: window.trade_xAxis_val,
            //         boundaryGap: false,
            //         axisLine: {
            //             show: false
            //         },
            //         axisLabel: {
            //             textStyle: {
            //                 color: '#f17a52'
            //             }
            //         },
            //         axisTick: {
            //             show: false
            //         }
            //     },
            //     yAxis: {
            //         ayisLine: {
            //             show: false
            //         },
            //         axisLabel: {
            //             textStyle: {
            //                 color: '#f17a52'
            //             }
            //         },
            //         splitLine: {
            //             show: true,
            //             lineStyle: {
            //                 color: '#2e3547'
            //             }
            //         },
            //         axisLine: {
            //             lineStyle: {
            //                 color: '#384157'
            //             }
            //         }
            //     },

            //     series: [{
            //         type: 'bar',
            //         name: 'linedemo',


            //         tooltip: {
            //             show: false
            //         },
            //         animation: false,
            //         barWidth: 1.4,
            //         hoverAnimation: false,
            //         data: window.trade_data_val,
            //         itemStyle: {
            //             normal: {
            //                 color: '#f17a52',
            //                 opacity: 0.6,
            //                 label: {
            //                     show: false
            //                 }
            //             }
            //         }
            //     }, {
            //         type: 'line',
            //         // name: '距离',

            //         animation: false,
            //         symbol: 'circle',

            //         hoverAnimation: false,
            //         data: window.trade_data_val,
            //         itemStyle: {
            //             normal: {
            //                 color: '#f17a52',
            //                 opacity: 0,
            //             }
            //         },
            //         lineStyle: {
            //             normal: {
            //                 width: 1,
            //                 color: '#384157',
            //                 opacity: 1
            //             }
            //         }
            //     }, {
            //         type: 'line',
            //         name: 'linedemo',
            //         smooth: true,
            //         symbolSize: 10,
            //         animation: false,
            //         lineWidth: 1.2,
            //         hoverAnimation: false,
            //         data: window.trade_data_val,
            //         symbol: 'circle',
            //         itemStyle: {
            //             normal: {
            //                 color: '#f17a52',
            //                 shadowBlur: 40,
            //                 label: {
            //                     show: true,
            //                     position: 'top',
            //                     textStyle: {
            //                         color: '#f17a52',

            //                     }
            //                 }
            //             }
            //         },
            //         areaStyle: {
            //             normal: {
            //                 color: '#f17a52',
            //                 opacity: 0.08
            //             }
            //         }

            //     }]
            // };
            //  window.trade_option_fold_line.setOption(window.trade_option);
        </script>
       <script type="text/javascript">
        var dom = document.getElementById("map");
        var myChart = echarts.init(dom);
        var app = {};
        option = null;

        var data = [
        <?php for ($i=0; $arr_place[$i] ; $i++):?>
          <?php if($arr_place[$i+1]):?>
            {name: '<?=$arr_place[$i]?>', value: 100},
          <?else:?>
            {name: '<?=$arr_place[$i]?>', value: 100}
          <?endif?>
        <?php endfor?>
        ]
        // var data1 = [{name:'元阳县',value:'元阳县'}];
        // console.log(data);
        window.geoCoordMap = <?=json_encode($arr_location)?>;
        // window.geoCoordMap1 = {'元阳县':[102.81,23.17]};

        window.ordervalue = <?=json_encode($arr_order)?>;
        // console.log(window.ordervalue);
        var convertData = function (data) {
            var res = [];
            for (var i = 0; i < data.length; i++) {
                var geoCoord = window.geoCoordMap[data[i].name];
                if (geoCoord) {
                    res.push({
                        name: data[i].name,
                        value: geoCoord.concat(window.ordervalue[data[i].name])
                    });
                }
            }
            return res;
        };
        // var convertData1 = function (data) {
        //     var res = [];
        //     for (var i = 0; i < data.length; i++) {
        //         var geoCoord = window.geoCoordMap1[data[i].name];
        //         if (geoCoord) {
        //             res.push({
        //                 name: data[i].name,
        //                 value: geoCoord.concat(data[i].value)
        //             });
        //         }
        //     }
        //     return res;
        // };
        option0 = {
            //backgroundColor: '#404a59',
            title: {
                left: 'center',
                textStyle: {
                    color: '#fff'
                },
                top:20,
            },
            tooltip : {
                show: true,
                showContent: true,
                trigger: 'item',
                triggerOn: 'click',
                alwaysShowContent: false,
                showDelay: 0,
                hideDelay: 1000,
                enterable: false,
                formatter: function (params) {
                    return params.value[2];
                }
            },
            legend: {
                orient: 'vertical',
                y: 'bottom',
                x:'right',
                data:['pm2.5'],
                textStyle: {
                    color: '#fff'
                }
            },
    visualMap: {
        type: 'continuous',
        dimension: 1,
        text: ['High', 'Low'],
            textStyle: {
                fontSize: 12,
                color: '#177394'
            },
        inverse: false,
        itemHeight: 140,
        calculable: true,
        min: 0,
        max: 500,
        bottom: 0,
        right: 36,
        inRange: {
            colorLightness: [0.6, 0.24]
        },
        outOfRange: {
            color: '#bbb'
        },
        controller: {
            inRange: {
                color: '#1378A2'
            }
        }
    },
            geo: {
                map: 'china',
                label: {
                    emphasis: {
                        show: false
                    }
                },
                left:100,
                top:50,
                silent:true,
                zoom:1.2,
                roam: false,
                itemStyle: {
                    normal: {
                        areaColor: '#0D405C',
                        opacity:0.4,
                        borderColor: '#03D1F8',
                        borderWidth: '1',
                    },
                    emphasis: {
                        // areaColor: '#2a333d'
                    }
                }
            },
            series : [
                // {
                //     type: 'effectScatter',
                //     coordinateSystem: 'geo',
                //     data: convertData1(data1),
                //     symbolSize: 10,
                //     showEffectOn: 'render',
                //     rippleEffect: {
                //         brushType: 'stroke'
                //     },
                //     hoverAnimation: true,
                //     label: {
                //         normal: {
                //             formatter: '{b}',
                //             position: 'right',
                //             show: true
                //         }
                //     },
                //     itemStyle: {
                //         normal: {
                //             color: 'red',
                //             shadowBlur: 10,
                //             shadowColor: 'red'
                //         }
                //     },
                //     zlevel:2
                // },
                {
                    name: 'Top 10',
                    type: 'effectScatter',
                    coordinateSystem: 'geo',
                    data: convertData(data),
                    symbolSize: 2,
                    showEffectOn: 'render',
                    rippleEffect: {
                        brushType: 'fill',
                        scale: 50,
                    },
                    hoverAnimation: false,
                    label: {
                        normal: {
                            color: '#fff100',
                            formatter: '{b}',
                            position: 'right',
                            show: true
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: 'rgba(230,0,18,0.5)',
                            shadowBlur: 10,
                            shadowColor: '#333'
                        }
                    },
                    zlevel: 1
                },
            ]
        };
        app.currentIndex = -1;
        window.loop0 = setInterval(function(){
        var dataLen = option0.series[0].data.length;
        app.currentIndex = (app.currentIndex + 1) % dataLen;
        myChart.dispatchAction({
            type: 'showTip',
            seriesIndex: 0,
            dataIndex: app.currentIndex
        });
        },1500);
        if (option0 && typeof option0 === "object") {
        myChart.setOption(option0, true);
        }
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=1',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    clearInterval(window.loop0);
                    clearInterval(window.loop);
                    // console.log(JSON.stringify(res['地理位置']));
                    // console.log(JSON.stringify(res['订单详情']));
                    window.ordervalue = res['订单详情'];
                    window.geoCoordMap = res['地理位置'];
                    var data = [
                       {name: res['位置预设'][0], value: 100},
                       {name: res['位置预设'][1], value: 100},
                       {name: res['位置预设'][2], value: 100},
                       {name: res['位置预设'][3], value: 100},
                       {name: res['位置预设'][4], value: 100},
                       {name: res['位置预设'][5], value: 100},
                       {name: res['位置预设'][6], value: 100},
                       {name: res['位置预设'][7], value: 100},
                       {name: res['位置预设'][8], value: 100},
                       {name: res['位置预设'][9], value: 100},
                       {name: res['位置预设'][10], value: 100},
                       {name: res['位置预设'][11], value: 100},
                       {name: res['位置预设'][12], value: 100},
                       {name: res['位置预设'][13], value: 100},
                       {name: res['位置预设'][14], value: 100},
                       {name: res['位置预设'][15], value: 100},
                       {name: res['位置预设'][16], value: 100},
                       {name: res['位置预设'][17], value: 100},
                       {name: res['位置预设'][18], value: 100},
                       {name: res['位置预设'][19], value: 100},
                       {name: res['位置预设'][20], value: 100},
                       {name: res['位置预设'][21], value: 100},
                       {name: res['位置预设'][22], value: 100},
                       {name: res['位置预设'][23], value: 100},
                       {name: res['位置预设'][24], value: 100},
                       {name: res['位置预设'][25], value: 100},
                       {name: res['位置预设'][26], value: 100},
                       {name: res['位置预设'][27], value: 100},
                       {name: res['位置预设'][28], value: 100},
                       {name: res['位置预设'][29], value: 100},
                       {name: res['位置预设'][30], value: 100},
                       {name: res['位置预设'][31], value: 100},
                       {name: res['位置预设'][32], value: 100},
                       {name: res['位置预设'][33], value: 100},
                       {name: res['位置预设'][34], value: 100},
                       {name: res['位置预设'][35], value: 100},
                       {name: res['位置预设'][36], value: 100},
                       {name: res['位置预设'][37], value: 100},
                       {name: res['位置预设'][38], value: 100},
                       {name: res['位置预设'][39], value: 100}
                    ]
                    // var data1 = [{name:'元阳县',value:'元阳县'}];
                    option = {
                            //backgroundColor: '#404a59',
                            title: {
                                left: 'center',
                                textStyle: {
                                    color: '#fff'
                                },
                            top:20
                            },
                            tooltip : {
                                show: true,
                                showContent: true,
                                trigger: 'item',
                                triggerOn: 'click',
                                alwaysShowContent: false,
                                showDelay: 0,
                                hideDelay: 1000,
                                enterable: false,
                                formatter: function (params) {
                                    return params.value[2];
                                }
                            },
                            legend: {
                                orient: 'vertical',
                                y: 'bottom',
                                x:'right',
                                data:['pm2.5'],
                                textStyle: {
                                    color: '#fff'
                                }
                            },
    visualMap: {
        type: 'continuous',
        dimension: 1,
        text: ['High', 'Low'],
            textStyle: {
                fontSize: 12,
                color: '#177394'
            },
        inverse: false,
        itemHeight: 140,
        calculable: true,
        min: 0,
        max: 500,
        bottom: 0,
        right: 36,
        inRange: {
            colorLightness: [0.6, 0.24]
        },
        outOfRange: {
            color: '#bbb'
        },
        controller: {
            inRange: {
                color: '#1378A2'
            }
        }
    },
                            geo: {
                                map: 'china',
                                label: {
                                    emphasis: {
                                        show: false
                                    }
                                },
                                left:100,
                                top:50,
                                silent:true,
                                zoom:1.2,
                                roam: false,
                                itemStyle: {
                    normal: {
                        areaColor: '#0D405C',
                        opacity:0.4,
                        borderColor: '#03D1F8',
                        borderWidth: '1',
                    },
                                    emphasis: {
                                        // areaColor: '#2a333d'
                                    }
                                }
                            },
                            series : [
                                // {
                                //     name: 'self',
                                //     type: 'effectScatter',
                                //     coordinateSystem: 'geo',
                                //     data: convertData1(data1),
                                //     symbolSize: 10,
                                //     label: {
                                //         normal: {
                                //             formatter: '{b}',
                                //             position: 'right',
                                //             show: true
                                //         }
                                //     },
                                //     itemStyle: {
                                //         normal: {
                                //             color: 'red',
                                //             shadowBlur: 10,
                                //             shadowColor: 'red'
                                //         }
                                //     },
                                //     zlevel:2
                                // },
                {
                    name: 'Top 10',
                    type: 'effectScatter',
                    coordinateSystem: 'geo',
                    data: convertData(data),
                    symbolSize: 2,
                    showEffectOn: 'render',
                    rippleEffect: {
                        brushType: 'fill',
                        scale: 50,
                    },
                    hoverAnimation: false,
                    label: {
                        normal: {
                            color: '#fff100',
                            formatter: '{b}',
                            position: 'right',
                            show: true
                        }
                    },
                    itemStyle: {
                        normal: {
                            color: 'rgba(230,0,18,0.5)',
                            shadowBlur: 10,
                            shadowColor: '#333'
                        }
                    },
                    zlevel: 1
                },
                            ]
                        };;
                    app.currentIndex = -1;
                    window.loop = setInterval(function(){
                        var dataLen = option.series[0].data.length;
                        app.currentIndex = (app.currentIndex + 1) % dataLen;
                        myChart.dispatchAction({
                            type: 'showTip',
                            seriesIndex: 0,
                            dataIndex: app.currentIndex
                        });
                    },1500);
                    if (option && typeof option === "object") {
                        myChart.setOption(option, true);
                    }
                }
            })
        },72000);
       </script>
       <script type="text/javascript">
        // var dom_source = document.getElementById("source");
        // var source = echarts.init(dom_source);
        // option_source = null;
        // option_source = {
        //     tooltip: {
        //         trigger: 'item',
        //         formatter: "{a} <br/>{b}: {c} ({d}%)",
        //         position: ['50%', '50%'],
        //         alwaysShowContent:true
        //     },
        //     series : [
        //         {
        //             name: '订单来源',
        //             type: 'pie',
        //             radius: '60%',
        //             center: ['50%', '35%'],
        //             data:[{
        //                 value:<?=$result['yz_orders']?>,
        //                 name:'神码浮云商城交易',
        //                 itemStyle: {
        //                     normal: {
        //                         color: '#5200FF'
        //                     }
        //                 }},
        //                 {value:<?=$result['sd_orders']?>,
        //                 name:'其它渠道',
        //                 itemStyle: {
        //                     normal: {
        //                         color: '#BAF327'
        //                     }
        //                 }}
        //             ],
        //             label: {
        //                 normal: {
        //                     position: 'inside',
        //                 }
        //             },
        //         }
        //     ]
        // };
        // currentIndex = -1;
        // var loop_source = setInterval(function () {
        //     var dataLen = option_source.series[0].data.length;
        //     // alert(option_goal.series[0].data[0].value);
        //     // 取消之前高亮的图形
        //     source.dispatchAction({
        //         type: 'downplay',
        //         seriesIndex: 0,
        //         dataIndex: currentIndex
        //     });
        //     currentIndex = (currentIndex + 1) % dataLen;
        //     // 高亮当前图形
        //     source.dispatchAction({
        //         type: 'highlight',
        //         seriesIndex: 0,
        //         dataIndex: currentIndex
        //     });
        //     // 显示 tooltip
        //     source.dispatchAction({
        //         type: 'showTip',
        //         seriesIndex: 0,
        //         dataIndex: currentIndex
        //     });
        // }, 1000);
        // if (option_source && typeof option_source === "object") {
        //     source.setOption(option_source, true);
        // }
       </script>

       <script type="text/javascript">
        var dom_vip = document.getElementById("vip");
        var vip = echarts.init(dom_vip);
        option_vip = null;
        option_vip = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)",
                alwaysShowContent:true
            },
            series: [
                {
                    name:'成交会员',
                    type:'pie',
                    radius: ['60%', '75%'],
                    center: ['50%', '50%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: true
                        },
                    },
                    opacity:1,
                    labelLine: {
                        normal: {
                            lineStyle: {
                                color: 'rgba(255, 255, 255, .7)'
                            },
                            smooth: 0.2,
                            length: 15,
                            length2: 20
                        }
                    },
                    data:[
                        {
                        value:<?=$result['old']?>,
                        name:'老客户',
                        itemStyle: {
                            normal: {
                                color: '#0EB5EC'
                            }
                        }},
                        {
                        value:<?=$result['new']?>,
                        name:'新客户',
                        itemStyle: {
                            normal: {
                                color: '#E60012'
                            }
                        }}
                    ]
                }
            ]
        };
        ;
        currentIndex = -1;
        var loop_vip = setInterval(function () {
            var dataLen = option_vip.series[0].data.length;
            // alert(option_goal.series[0].data[0].value);
            // 取消之前高亮的图形
            vip.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: currentIndex
            });
            currentIndex = (currentIndex + 1) % dataLen;
            // 高亮当前图形
            vip.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: currentIndex
            });
            // 显示 tooltip
            vip.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: currentIndex
            });
        }, 2000);
        if (option_vip && typeof option_vip === "object") {
            vip.setOption(option_vip, true);
        }
       </script>
       <script type="text/javascript">
        setInterval(function(){
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + " " + date.getHours() + seperator2 + date.getMinutes();
            $('#date1').text(currentdate);
        },30000);
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=2',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    // console.log(res['订单来源']);
                    // console.log(res['本月目标']);
                    // console.log(res['成交会员']);
                    // option_source.series[0].data[0].value = res['订单来源']['有赞平台'];
                    // option_source.series[0].data[1].value = res['订单来源']['自己平台'];
                    // option_goal.series[0].data[0].value = res['本月目标']['已完成'];
                    // option_goal.series[0].data[1].value = res['本月目标']['总目标'];
                    $('.goal_label').text(res['本月目标']['已完成']+'（已完成）/'+res['本月目标']['总目标']+'（总目标）');
                    $('#example2').progress({
                      percent:res['本月目标']['已完成']/res['本月目标']['总目标']*100
                    });
                    option_vip.series[0].data[0].value = res['成交会员']['新会员'];
                    option_vip.series[0].data[1].value = res['成交会员']['老会员'];
                    // if (option_source && typeof option_source === "object") {
                    //     source.setOption(option_source, true);
                    // }
                    // if (option_goal && typeof option_goal === "object") {
                    //     goal.setOption(option_goal, true);
                    // }
                    if (option_vip && typeof option_vip === "object") {
                        vip.setOption(option_vip, true);
                    }
                }
            })
        },60000);
       </script>
       <script type="text/javascript">
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=3',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    // console.log(res['今日成交额']);
                    // console.log(res['昨日成交额']);

                    $('.yestoday').text(res['昨日成交额']+'元');
                    $('.all').text(res['累计成交额']+'元');

                    setTimeout(function() {
                        $('.odometer').html(res['今日成交额']);
                    }, 1000);
                }
            })
        },60000);
       </script>
       <script type="text/javascript">
        var loop = function () {

        //cache the ticker
        var ticker = $("#ticker");

        //wrap dt:dd pairs in divs
        ticker.children().filter("dt").each(function() {

          var dt = $(this),
            container = $("<div>");

          dt.next().appendTo(container);
          dt.prependTo(container);

          container.appendTo(ticker);
        });

        //hide the scrollbar
        ticker.css("overflow", "hidden");

        //animator function
        function animator(currentItem) {

          //work out new anim duration
          var distance = currentItem.height();
            duration = (distance + parseInt(currentItem.css("marginTop"))) / 0.05;

          //animate the first child of the ticker
          currentItem.animate({ marginTop: -distance }, duration, "linear", function() {

            //move current item to the bottom
            currentItem.appendTo(currentItem.parent()).css("marginTop", 0);

            //recurse
            animator(currentItem.parent().children(":first"));
          });
        };

        //start the ticker
        animator(ticker.children(":first"));
      };
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=4',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    var str = '';
                    for (var i = 0; res['交易订单'][i]; i++) {
                        str = str+"<p><span class='tidcontent'>"+res['交易订单'][i][0]+"</span><span class='tidcontent1'>"+res['交易订单'][i][1]+"</span><span class='tidcontent2'>"+res['交易订单'][i][2]+"</span></p>"
                    };
                    $('#ticker').html(str);
                    // cache the ticker
                    var ticker = $("#ticker");

                    //wrap dt:dd pairs in divs
                    ticker.children().filter("dt").each(function() {

                      var dt = $(this),
                        container = $("<div>");

                      dt.next().appendTo(container);
                      dt.prependTo(container);

                      container.appendTo(ticker);
                    });

                    //hide the scrollbar
                    ticker.css("overflow", "hidden");

                    //animator function
                    function animator(currentItem) {

                      //work out new anim duration
                      var distance = currentItem.height();
                        duration = (distance + parseInt(currentItem.css("marginTop"))) / 0.05;

                      //animate the first child of the ticker
                      currentItem.animate({ marginTop: -distance }, duration, "linear", function() {

                        //move current item to the bottom
                        currentItem.appendTo(currentItem.parent()).css("marginTop", 0);

                        //recurse
                        animator(currentItem.parent().children(":first"));
                      });
                    };

                    //start the ticker
                    animator(ticker.children(":first"));
                }
            })
        },60000);
        loop();
       </script>
       <script type="text/javascript">//热销商品
        $.ajax({
            url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=5',
            type: 'GET',
            dataType: 'json',
            success:function(res) {
                window.res5 = res;
            }
        })
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=5',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    window.res5 = res;
                }
            })
        },6000);
        setInterval(function(){
                  window.topmount_dataAxis = [];
                  window.topmount_dataold = [];
                  window.topmount_datanew = [];
          window.yMax1 = Math.ceil(window.res5['热销商品'][0][2]);
                  window.topmount_dataAxis.push("");
                  window.topmount_dataold.push(0);
                  window.topmount_datanew.push(0);
            for (var i = 0; i<=9; i++) {
              if (typeof(window.res5['热销商品'][i])!='undefined') {
                  window.topmount_dataAxis.push(window.res5['热销商品'][i][1]);
                  window.topmount_dataold.push(window.res5['热销商品'][i][3]);
                  window.topmount_datanew.push(window.res5['热销商品'][i][4]);
              };
            };
                  window.topmount_dataAxis.push("");
                  window.topmount_dataold.push(0);
                  window.topmount_datanew.push(0);
window.dataShadowmount = [];
for (var i = 0; i < window.topmount_dataold.length; i++) {
    window.dataShadowmount.push(window.yMax1);
  };
           window.topmount_option_area.setOption(window.topmount_option);
        },5000);
       </script>
       <script type="text/javascript">//热销商品
        $.ajax({
            url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=6',
            type: 'GET',
            dataType: 'json',
            success:function(res) {
                window.res6 = res;
            }
        })
        setInterval(function(){
            $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=6',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                    window.res6 = res;
                }
            })
        },60000);
        setInterval(function(){
                  window.topmoney_dataAxisfan = [];
                  window.topmoney_datafan = [];
                  window.topmoney_dataAxisfan.push("");
                  window.topmoney_datafan.push(0);
            for (var i = 0; i<=4; i++) {
              if (typeof(window.res6['热销商品金额'][i])!='undefined') {
                  window.topmoney_dataAxisfan.push(window.res6['热销商品金额'][i][1]);
                  window.topmoney_datafan.push(window.res6['热销商品金额'][i][2]);
              };
            };
                  window.topmoney_dataAxisfan.push("");
                  window.topmoney_datafan.push(0);
          window.xMax = Math.ceil(window.res6['热销商品金额'][0][2]);
window.dataShadowmoney = [];
for (var i = 0; i < window.topmoney_data.length; i++) {
    window.dataShadowmoney.push(window.xMax);
  };
          window.topmoney_dataAxis = reverse(window.topmoney_dataAxisfan);
          window.topmoney_data = reverse(window.topmoney_datafan);
           window.topmoney_option_area.setOption(window.topmoney_option);
        },5000);
       </script>
       <script type="text/javascript">
         // setInterval(function(){
         //      $.ajax({
         //          url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=7',
         //          type: 'GET',
         //          dataType: 'json',
         //          success:function(res) {
         //            window.area_dataAxis = [];
         //            window.area_data = [];
         //            for (var i = 0; res[i]; i++) {
         //                window.area_dataAxis.push(res[i]['receiver_city']);
         //                window.area_data.push(res[i]['zongfen']);
         //            };
         //            console.log(window.area_dataAxis);
         //            console.log(window.area_data);
         //            window.area_option.xAxis.data = window.area_dataAxis;
         //            window.area_option.series.data = window.area_data;

         //            window.area_option_area.setOption(window.area_option);
         //          }
         //      })
         //  },60000);
       </script>
       <script type="text/javascript">
          $(document).on('click', '.ri', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=d',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.trade_data_val = [];
                  window.trade_xAxis_val = [];
                  for (var i = 0; res[i]; i++) {
                      window.trade_data_val.push(res[i]['commision']);
                      window.trade_xAxis_val.push(res[i]['time']);
                  };
                  console.log(window.trade_xAxis_val);
                  console.log(window.trade_data_val);
                  window.trade_option.xAxis.data = window.trade_xAxis_val;
                  window.trade_option.series[0].data = window.trade_data_val;
                  window.trade_option.series[1].data = window.trade_data_val;
                  window.trade_option.series[2].data = window.trade_data_val;

                  window.trade_option_fold_line.setOption(window.trade_option);
                }
            })
          });
          $(document).on('click', '.yue', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=m',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.trade_data_val = [];
                  window.trade_xAxis_val = [];
                  for (var i = 0; res[i]; i++) {
                      window.trade_data_val.push(res[i]['commision']);
                      window.trade_xAxis_val.push(res[i]['time']);
                  };
                  console.log(window.trade_xAxis_val);
                  console.log(window.trade_data_val);
                  window.trade_option.xAxis.data = window.trade_xAxis_val;
                  window.trade_option.series[0].data = window.trade_data_val;
                  window.trade_option.series[1].data = window.trade_data_val;
                  window.trade_option.series[2].data = window.trade_data_val;

                  window.trade_option_fold_line.setOption(window.trade_option);
                }
            })
          });
          $(document).on('click', '.nian', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=y',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.trade_data_val = [];
                  window.trade_xAxis_val = [];
                  for (var i = 0; res[i]; i++) {
                      window.trade_data_val.push(res[i]['commision']);
                      window.trade_xAxis_val.push(res[i]['time']);
                  };
                  console.log(window.trade_xAxis_val);
                  console.log(window.trade_data_val);
                  window.trade_option.xAxis.data = window.trade_xAxis_val;
                  window.trade_option.series[0].data = window.trade_data_val;
                  window.trade_option.series[1].data = window.trade_data_val;
                  window.trade_option.series[2].data = window.trade_data_val;

                  window.trade_option_fold_line.setOption(window.trade_option);
                }
            })
          });
       </script>
       <script type="text/javascript">
          $(document).on('click', '.ri2', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=d2',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.area_dataAxis = [];
                  window.area_data = [];
                  for (var i = 0; res['hot_area'][i]; i++) {
                      window.area_dataAxis.push(res['hot_area'][i]['receiver_city']);
                      window.area_data.push(res['hot_area'][i]['zongfen']);
                  };
          window.yMax2 = Math.ceil(res['hot_area'][0]['zongfen']);
window.dataShadow2 = [];
for (var i = 0; i < window.area_data.length; i++) {
    window.dataShadow2.push(window.yMax2);
}
                  console.log(window.area_dataAxis);
                  console.log(window.area_data);

                  window.area_option.xAxis.data = window.area_dataAxis;
                  window.area_option.series[0].data = window.area_data;

                  window.area_option_area.setOption(window.area_option);

                  window.res5 = res['hot_item'];
                  window.res6 = res['hot_num'];
                }
            })
          });
          $(document).on('click', '.yue2', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=m2',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.area_dataAxis = [];
                  window.area_data = [];
                  for (var i = 0; res['hot_area'][i]; i++) {
                      window.area_dataAxis.push(res['hot_area'][i]['receiver_city']);
                      window.area_data.push(res['hot_area'][i]['zongfen']);
                  };
          window.yMax2 = Math.ceil(res['hot_area'][0]['zongfen']);
window.dataShadow2 = [];
for (var i = 0; i < window.area_data.length; i++) {
    window.dataShadow2.push(window.yMax2);
}
                  console.log(window.area_dataAxis);
                  console.log(window.area_data);
                  window.area_option.xAxis.data = window.area_dataAxis;
                  window.area_option.series[0].data = window.area_data;

                  window.area_option_area.setOption(window.area_option);

                  window.res5 = res['hot_item'];
                  window.res6 = res['hot_num'];
                }
            })
          });
          $(document).on('click', '.nian2', function() {
                $.ajax({
                url: 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtdpm/map?t=y2',
                type: 'GET',
                dataType: 'json',
                success:function(res) {
                  window.area_dataAxis = [];
                  window.area_data = [];
                  for (var i = 0; res['hot_area'][i]; i++) {
                      window.area_dataAxis.push(res['hot_area'][i]['receiver_city']);
                      window.area_data.push(res['hot_area'][i]['zongfen']);
                  };
          window.yMax2 = Math.ceil(res['hot_area'][0]['zongfen']);
window.dataShadow2 = [];
for (var i = 0; i < window.area_data.length; i++) {
    window.dataShadow2.push(window.yMax2);
}
                  console.log(window.area_dataAxis);
                  console.log(window.area_data);
                  window.area_option.xAxis.data = window.area_dataAxis;
                  window.area_option.series[0].data = window.area_data;

                  window.area_option_area.setOption(window.area_option);

                  window.res5 = res['hot_item'];
                  window.res6 = res['hot_num'];
                }
            })
          });
       </script>
   </body>
</html>
