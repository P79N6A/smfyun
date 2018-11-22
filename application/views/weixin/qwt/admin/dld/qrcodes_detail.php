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


  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="#">概况</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-lg-12">


      <div class="nav-tabs-custom">

          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <form method="get">
             <table class="table table-hover">
                <div class="input-group" style="width: 40%;display: flex;">
                <input type="text"  class="form-control pull-right formdatetime1" style="float:left;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
                <input type="text"  class="form-control pull-right formdatetime2" style="margin-left:10px;background-color: #fff;border-radius: 7px;" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
                <div id="add89" class="input-group-addon"><i class="fa fa-calendar"></i></div>
                 <div class="input-group-btn add88">
                    <button id="ssbtn" class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                 </div>
                </div>
                </div>
                <tbody>
                  <tr>
                    <th>时间段</th>
                    <th>新增粉丝量</th>
                    <th>新增销售量</th>
                 </tr>

                  <tr>
                    <td><?=$result['time']?></td>
                    <td><?=$result['addfans']?></td>
                    <td><?=$result['addbuy']?></td>
                  </tr>

                  <? if($status != 3):?>
                 <!-- <tr> <td colspan="7"><p style="color:red">注意:没有显示的日期表示所有数据都为0。</p> -->
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
    format: "yyyy-mm-dd hh:ii",
    language: "zh-CN",
    autoclose: true,
    minView:'hour',
    todayBtn:true,
    startDate: "<?=$result['begin']?>",
    endDate: "<?=$result['over']?>",

  });

  $(".formdatetime2").datetimepicker({
    format: "yyyy-mm-dd hh:ii",
    language: "zh-CN",
    autoclose: true,
    minView:'hour',
    todayBtn:true,
    startDate: "<?=$result['begin']?>",
    endDate: "<?=$result['over']?>",
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
