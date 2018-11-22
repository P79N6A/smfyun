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
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0,0,0,.5);
    z-index: 1030;
    display: none;
  }
  .model{
    position: absolute;
    width: 400px;
    top: 200px;
    left: calc(50% - 200px);
    background-color: #fff;
    border-radius: 5px;
    padding: 10px 0;
  }
  .title{
    padding-left: 20px;
    font-size: 18px;
    font-weight: bold;
    color: #5985f7;
    padding-bottom: 5px;
    border-bottom: 2px solid #efefef;
  }
  .body{
    margin-top: 20px;
    font-size: 13px;
    font-weight: bold;
    color: #999;
    line-height: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #efefef;
  }
  .confirm{
    margin-left: 40px;
    background-color: #71b7ff;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
    padding: 5px 10px;
    border: 1px solid #3f9dff;
  }
  .cancel{
    margin-left: 10px;
    background-color: #ff5c5c;
    color: #fff;
    font-size: 14px;
    font-weight: bold;
    border-radius: 5px;
    padding: 5px 10px;
    border: 1px solid #b50000;
  }
</style>
<section class="content-header">
  <h1>
    提现申请
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><a href="/wzba/analyze">提现申请</a></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">

<?php if ($result['ok']):?>
                  <div class="alert alert-success alert-dismissable"><?=$result['ok']?></div>
                <?php endif?>


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
                  <th>金额</th>
                  <th>操作</th>
                  </tr>
                  <?php foreach ($result['scores'] as $k => $v):?>
                    <tr>
                      <td><a href="/bnka/qrcodes?qid=<?=$v->qrcode->id?>" title="查看红包发起人"><?=$v->qrcode->nickName?></a></td>
                      <td><?=date('Y-m-d H:i:s',$v->createdtime)?></td>
                      <td><?=-$v->score?></td>
                  <td nowrap="">
                    <a class="edit" data-sid="<?=$v->id?>" data-qid="<?=$v->qrcode->id?>" data-name="<?=$v->qrcode->nickName?>" data-time="<?=date('Y-m-d H:i:s',$v->createdtime)?>" data-score="<?=-$v->score?>">
                      <span>处理</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
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
    <div class="shadow">
      <div class="model">
        <div class="title">
          提现申请
        </div>
        <ul class="body">
          <li>姓名：<span class="name"></span></li>
          <li>时间：<span class="time"></span></li>
          <li>金额：<span class="score"></span></li>
        </ul>
        <form method="post">
        <div class="bottom">
          <input id="qid" type="hidden" name="qid" value="">
          <input id="sid" type="hidden" name="sid" value="">
          <button type="submit" class="confirm">确认放款</button>
          <button type="button" class="cancel">取消</button>
        </div>
        </form>
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
$('.edit').click(function(){
  var name = $(this).data('name');
  var time = $(this).data('time');
  var score = $(this).data('score');
  var qid = $(this).data('qid');
  var sid = $(this).data('sid');
  $('.name').text(name);
  $('.time').text(time);
  $('.score').text(score);
  $('#qid').val(qid);
  $('#sid').val(sid);
  $('.shadow').show();
})
$('.cancel').click(function(){
  $('.shadow').hide();
})
</script>
