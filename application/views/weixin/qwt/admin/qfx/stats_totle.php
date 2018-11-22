
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->

  <script type="text/javascript" src="http://jfb.dev.smfyun.com/wzb/js/mock.js"></script>

  <link rel="stylesheet" type="text/css" href="http://jfb.dev.smfyun.com/wzb/css/jquery.dropdown.css">

  <script src="http://jfb.dev.smfyun.com/wzb/js/jquery.dropdown.js"></script>
  <script type="text/javascript" src='/wzb/js/echart.min.js'></script>
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    color: black;
    }
</style>


<!-- Main content -->
  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                概况
            </div>
<?php
if ($status == 1) $title .= '按天统计';
if ($status == 2) $title .= '按月统计';
if ($status == 3) $title .= '按日期筛选';
?>

            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li>数据统计</li>
                <li><a href="/qwtrwba/stats_totle">概况</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
              <li id="orders<?=$status?>" class="<?=$status== 1 ? 'am-active' : ''?>"><a href="/qwtqfxa/stats_totle?qid=1">按天统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 2? 'am-active' : ''?>"><a href="/qwtqfxa/stats_totle/month?qid=2">按月统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 3? 'am-active' : ''?>"><a href="/qwtqfxa/stats_totle/shaixuan?qid=3">按日期筛选</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
              <? if($status == 3):?>
                <div class="tpl-block tpl-amazeui-form">
                    <div class="am-g">
                            <div class="am-form am-form-horizontal">
                    <div class="am-form-group">
                          <label for="fscore" class="am-u-sm-2 am-form-label">时间范围： </label>
                        <div class="am-u-sm-12 am-u-md-10">
  <input name="data[begin]" id="datetimepicker1" size="16" type="text" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
  <span>-</span>
  <input name="data[over]" id="datetimepicker2" size="16" type="text" value="<?=$_GET['data']['over']?>" class="am-form-field" readonly>
  </div>
</div>
                <div class="am-form-group">
                                    <label for="type" class="am-u-sm-2 am-form-label">分组：</label>
                        <div class="am-u-sm-12 am-u-md-3">

                                        <select name='data[group]' data-am-selected="{searchBox: 1}">
                      <option value="all" <?=$_GET['data']['group']=='all'?'selected':''?>>全部</option>
                    <?php foreach ($group as $k => $v):?>
                      <option value="<?=$v->id?>" <?=$_GET['data']['group']==$v->id?'selected':''?>><?=$v->name?></option>
                    <?php endforeach?>
                    </select>
                                    </div>
                                    <label for="group" class="am-u-sm-2 am-form-label">用户：</label>
                        <div class="am-u-sm-12 am-u-md-3" style="float:left;">

                                        <select name='data[user]' data-am-selected="{searchBox: 1}">
                          <option value="all" <?=$_GET['data']['user']=='all'?'selected':''?>>全部</option>
                          <?php foreach ($users as $k => $v):?>
                            <option value="<?=$v->id?>" <?=$_GET['data']['user']==$v->id?'selected':''?>><?=$v->nickname?></option>
                          <?php endforeach?>
                    </select>
                                    </div>
                        <div class="am-u-sm-12 am-u-md-2">
                                <span class="am-input-group-btn" style="display:inline-block">
            <button id="ssbtn" class="search-btn1 am-btn  am-btn-default am-btn-success am-icon-search" type="submit"></button>
          </span>
          </div>
            </div>
            </div>
            </div>
            </div>
                  <?php endif?>
                  </form>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                    <th>时间段</th>
                    <th>新增粉丝数量</th>
                    <th>生成海报数量</th>
                    <th>有赞订单数</th>
                    <th>有赞商品交易数量</th>
                    <th>有赞成交金额</th>
                    <th>产生的佣金</th>
<!--                     <th>待结算的佣金</th>
 -->                  </tr>
                                    </thead>
                                    <tbody>
                  <?php //foreach($newadd as $newadd):
                  for($i=0;$newadd[$i];$i++)
                  {
                  ?>
                  <tr>
                    <td><?=$newadd[$i]['time']?></td>
                    <td><?=$newadd[$i]['fansnum']?></td>
                    <td><?=$newadd[$i]['tickets']?></td>
                    <td id="" title="查看兑换明细"><?=$newadd[$i]['tradesnum']?></a></td>
                    <td><?=empty($newadd[$i]['goodsnum'])?0:$newadd[$i]['goodsnum']?></td>
                    <td><?=empty($newadd[$i]['payment'])?sprintf('%.2f',0):$newadd[$i]['payment']?></td>
                    <td><?=empty($newadd[$i]['commision'])?sprintf('%.2f',0):$newadd[$i]['commision']?></td>
                  </tr>

                  <?php //endforeach;
                  }?>
                                    </tbody>
                                </table>
                  <? if($status != 3):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p class="warning-text"> 注意:没有显示的日期表示所有数据都为0。</p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                        </div>

                    </div>
                </div>
                            </form>
            <?php if($status == 3):?>
            <div id="main" style="width: 100%;height:400px;"></div>
            <?php endif?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>










        </div>

<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    $('#datetimepicker2').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    </script>

<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
  .box-tools{
    display: inline-block;
    position: absolute;
    top: 4px;
    left: -116px
  }
  #inputNum1{
    display: inline-block;
    position: absolute;
    top: 0px;
    left: -333px;
    height: 33px;
    width: 150px;
    border-radius: 10px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  #inputNum2{
    display: inline-block;
    position: absolute;
    left: -154px;
    height: 33px;
    width: 150px;
    border-radius: 10px;
    top:0px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  #add89{
    display: inline-block;
    width: 50px;
    height: 33px;
    line-height: 19px;
    border-radius: 10px;
    margin-left: 10px;
  }
  .add88{
    display: inline-block;
    border-radius: 10px;
    background-color: #fff;
  }
  #ssbtn{
    display: inline-block;
    width: 40px;
    height: 33px;
    border-radius: 10px;
    margin-left: 3px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
  }
  .text88{
    position: absolute;
    top: 5px;
    left: -177px;
    font-size: 16px;
  }
</style>
<script>
$(function () {
  var timeline = new Array();
  var fansnum = new Array();
  var tickets = new Array();
  var tradesnum = new Array();
  var goodsnum = new Array();
  var payment = new Array();
  var commision = new Array();
  <?php for($i=0;$timeline[$i];$i++):?>
    timeline[<?=$i?>]='<?=$timeline[$i]['time']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    fansnum[<?=$i?>]='<?=$timeline[$i]['fansnum']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    tickets[<?=$i?>]='<?=$timeline[$i]['tickets']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    tradesnum[<?=$i?>]='<?=$timeline[$i]['tradesnum']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    goodsnum[<?=$i?>]='<?=$timeline[$i]['goodsnum']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    payment[<?=$i?>]='<?=$timeline[$i]['payment']?>';
  <?php endfor?>
  <?php for($i=0;$timeline[$i];$i++):?>
    commision[<?=$i?>]='<?=$timeline[$i]['commision']?>';
  <?php endfor?>
  var myChart = echarts.init(document.getElementById('main'));
  option = {
      color:["#d77cd8","#eaadda","#74c49d","#c0df62","#22afed","#c2e1ee"],
      grid: {
            bottom: 70
        },
        legend:{
          data:["新增粉丝数量","生成海报数量","有赞订单数","有赞商品交易数量","有赞成交金额","产生的佣金"],
          x:'center'
        },
        tooltip : {
            trigger: 'axis',
            z:-1,
            axisPointer:{
              type:'line',
              lineStyle:{
                type:'dashed',
                color:'#a4a4a4',
                opacity:0.7
              }
            },
            formatter: function(params) {
                return params[0].name + '<br/>'
                       + params[0].seriesName + ' : '  + params[0].value + '</br>'
                       + params[1].seriesName + ' : '  + params[1].value + '</br>'
                       + params[2].seriesName + ' : '  + params[2].value + '</br>'
                       + params[3].seriesName + ' : '  + params[3].value + '</br>'
                       + params[4].seriesName + ' : '  + params[4].value + '</br>'
                       + params[5].seriesName + ' : '  + params[5].value
            }
        },
        dataZoom:[
          {
            show:true,
            realtime:true,
            startValue:'<?=$begin?>',
            endValue:'<?=$over?>',
            handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
              handleSize: '30%',
              handleStyle: {
                  color: '#80cbc4'
              },
                fillerColor:'#d8faf4',
              borderColor:"#b1b1b1"
          }
        ],
        xAxis : [
            {
                type : 'category',
                axisTick: {
                  show: false
              },
              axisLine: {
                  lineStyle: {
                      color: '#90979c'
                  }
              },
                data : timeline.map(function (str) {
                    return str.replace(' ', '\n')
                })
            }
        ],
        yAxis: [
             {
                show:false
              }
        ],
        series: [
            {
                name:'新增粉丝数量',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#d77cd8',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:fansnum
            },
            {
                name:'生成海报数量',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#eaadda',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:tickets
            },
            {
                name:'有赞订单数',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#74c49d',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:tradesnum
            },
            {
                name:'有赞商品交易数量',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#c0df62',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:goodsnum
            },
            {
                name:'有赞成交金额',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#22afed',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:payment
            },
            {
                name:'产生的佣金',
                type:'line',
//                smooth:true,
                hoverAnimation:true,
                symbolSize:8,
                itemStyle:{
                  emphasis:{
                    color:'#c2e1ee',
                    borderColor:'#fff',
                    borderWidth:4,
                    borderType:'solid',
                    shadowBlur:5,
                    shadowColor:'#9c9a9b',
                  }
                },
                lineStyle: {
                    normal: {
                        width: 1,
                    }
                },
                data:commision
            }
        ]
    };
  // 使用刚指定的配置项和数据显示图表。
  myChart.setOption(option);
  $(".formdatetime1").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",

  });

  $(".formdatetime2").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",
  });


});
window.i=0;
$(document).on('click', '.shaixuan', function() {
if(i%2==0)
$(".pull-right").css("display",'block');
else
$(".pull-right").css("display",'none');
i++;
})
    $('.dropdown-sin-1').dropdown({
      readOnly: true,
      input: '<input type="text" maxLength="20" placeholder="请输入搜索">'
    });
</script>
