<style>
.label {font-size: 14px}
th, td{
  text-align: center;
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
    提现记录
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><a href="/wzba/analyze">提现记录</a></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 条记录</h3>
            </div><!-- /.box-header -->
            <form method="get" name="qrcodesform">
              <div class="input-group" style="width: 250px;display: flex;">
                <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                 <div class="input-group-btn add88">
                    <button id="ssbtn" class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                 </div>
                </div>
                </form>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>微信昵称</th>
                  <th>时间</th>
                  <th>金额<th>
                  </tr>
                  <?php foreach ($result['scores'] as $k => $v):?>
                    <tr>
                      <td><a href="/bnka/qrcodes?qid=<?=$v->qrcode->id?>" title="查看红包发起人"><?=$v->qrcode->nickName?></a></td>
                      <td><?=date('Y-m-d H:i:s',$v->createdtime)?></td>
                      <td><?=-$v->score?></td>
                    </tr>
                  <?php endforeach?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</section><!-- /.content -->
</section><!-- /.content -->
<script type="text/javascript">

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
</script>
