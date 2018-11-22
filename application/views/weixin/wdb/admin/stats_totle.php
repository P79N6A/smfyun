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
<section class="content-header">
  <h1>
    数据统计
    <small><?=$desc?></small>
  </h1>

<?php
if ($status == 1) $title .= '按天统计';
if ($status == 2) $title .= '按月统计';
if ($status == 3) $title .= '按日期筛选';
?>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wdba/stats_totle">概况</a></li>
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
              <li id="orders<?=$status?>" class="<?=$status== 1 ? 'active' : ''?>"><a href="/wdba/stats_totle?qid=1">按天统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 2? 'active' : ''?>"><a href="/wdba/stats_totle/month?qid=2">按月统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 3? 'active' : ''?>"><a href="/wdba/stats_totle/shaixuan?qid=3">按日期筛选</a></li>
              <!-- <div> -->
             <!--  <li style="display:none" class="pull-right">
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                <input type="text" id="inputNum1" class="form-control pull-right formdatetime1" style="float:left" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
                <span class="text88">至</span>
                <input type="text" id="inputNum2" class="form-control pull-right formdatetime2" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
                <div id="add89" class="input-group-addon"><i class="fa fa-calendar"></i></div>
                 <div class="input-group-btn add88">
                    <button id="ssbtn" class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                 </div>
                </div>
              </div>
              </li> -->


            </ul>
          </form>

          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <form method="get">
             <table class="table table-hover">
              <? if($status == 3):?>
                <div class="input-group" style="width: 250px;display: flex;">
                <input type="text"  class="form-control pull-right formdatetime1" style="float:left;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
                <input type="text"  class="form-control pull-right formdatetime2" style="margin-left:10px;background-color: #fff;border-radius: 7px;" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
                <div id="add89" class="input-group-addon"><i class="fa fa-calendar"></i></div>
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
                    <th>活动参与人数</th>
                    <th>奖品兑换数量</th>
                 </tr>
                  <?php //foreach($newadd as $newadd):
                  for($i=0;$newadd[$i];$i++)
                  {
                  ?>
                  <tr>
                    <td><?=$newadd[$i]['time']?></td>
                    <td><?=$newadd[$i]['fansnum']?></td>
                    <td><?=$newadd[$i]['tickets']?></td>
                    <td><?=$newadd[$i]['actnums']?></td>
                    <td><?=$newadd[$i]['ordernums']?></td>
                  </tr>

                  <?php //endforeach;
                  }?>
                  <? if($status != 3):?>
                 <tr> <td colspan="7"><p style="color:red">注意:没有显示的日期表示所有数据都为0。</p>
                 <?endif;?>

               </tbody>
             </table>
            </form>
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
</script>
