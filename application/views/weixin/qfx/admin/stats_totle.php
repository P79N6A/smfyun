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

  <link rel="stylesheet" type="text/css" href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css">

  <script type="text/javascript" src="http://jfb.dev.smfyun.com/wzb/js/mock.js"></script>

  <link rel="stylesheet" type="text/css" href="http://jfb.dev.smfyun.com/wzb/css/jquery.dropdown.css">

  <script src="http://jfb.dev.smfyun.com/wzb/js/jquery.dropdown.js"></script>
  <script type="text/javascript" src='/wzb/js/echart.min.js'></script>
<section class="content-header">
  <h1>
    概况
    <small><?=$desc?></small>
  </h1>

<?php
if ($status == 1) $title .= '按天统计';
if ($status == 2) $title .= '按月统计';
if ($status == 3) $title .= '按日期筛选';
?>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qfxa/stats_totle">概况</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-lg-12">


      <div class="nav-tabs-custom">

          <form method="get" name="ordersform">
            <ul class="nav nav-tabs">
              <li id="orders<?=$status?>" class="<?=$status== 1 ? 'active' : ''?>"><a href="/qfxa/stats_totle?qid=1">按天统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 2? 'active' : ''?>"><a href="/qfxa/stats_totle/month?qid=2">按月统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 3? 'active' : ''?>"><a href="/qfxa/stats_totle/shaixuan?qid=3">按日期筛选</a></li>
            </ul>
          </form>

          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <form method="get">
             <table class="table table-hover" style="margin-top:45px;">
              <? if($status == 3):?>
                <div class="input-group" style="padding:5px;display: flex;position:absolute;margin-top:-45px;">
                <span style="width:70px;line-height:35px;">时间范围：</span>
                <input type="text"  class="form-control pull-left formdatetime1" style="width:100px;float:left;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
                <input type="text"  class="form-control pull-left formdatetime2" style="width:100px;margin-left:10px;background-color: #fff;border-radius: 7px;" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
                <div id="add89" class="input-group-addon" style="width:40px;"><i class="fa fa-calendar"></i></div>
                <span style="margin-left:10px;line-height:35px;">分组：</span>
                <select name='data[group]' style="width:150px;background: white;border-color: #ccc;">
                      <option value="all" <?=$_GET['data']['group']=='all'?'selected':''?>>全部</option>
                    <?php foreach ($group as $k => $v):?>
                      <option value="<?=$v->id?>" <?=$_GET['data']['group']==$v->id?'selected':''?>><?=$v->name?></option>
                    <?php endforeach?>
                </select>
                <span style="margin-left:10px;line-height:35px;">用户：</span>
                <div class="row">
                  <div class="col-sm-4" style="width:200px;">
                    <div class="dropdown-sin-1">
                      <select name='data[user]' style="display:none;width:150px;">
                          <option value="all" <?=$_GET['data']['user']=='all'?'selected':''?>>全部</option>
                          <?php foreach ($users as $k => $v):?>
                            <option value="<?=$v->id?>" <?=$_GET['data']['user']==$v->id?'selected':''?>><?=$v->nickname?></option>
                          <?php endforeach?>
                      </select>
                    </div>
                  </div>
                </div>
                 <div class="input-group-btn add88">
                    <button id="ssbtn" class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                 </div>
                </div>
                </div>
              <?endif;?>
                <tbody>
                  <tr>
                    <th>时间段</th>
                    <th>新增粉丝数量</th>
                    <th>生成海报数量</th>
                    <th>有赞订单数</th>
                    <th>有赞商品交易数量</th>
                    <th>有赞成交金额</th>
                    <th>产生的佣金</th>
<!--                     <th>待结算的佣金</th>
 -->                  </tr>
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
                  <? if($status != 3):?>
                 <tr> <td colspan="7"><p style="color:red">注意:没有显示的日期表示所有数据都为0。</p>
                 <?endif;?>

               </tbody>
             </table>
            </form>
            <?php if($status == 3):?>
            <div id="main" style="width: 100%;height:400px;"></div>
            <?php endif?>
            </div><!-- table-resonsivpe -->

            <div class="box-footer clearfix">
              <?=$pages?>
            </div>

          </div><!-- tab-pane -->

      </div><!-- nav-tabs-custom -->
    </div>
  </div>

</section><!-- /.content -->
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
