<style>
.label {font-size: 14px}
.clone{
  color: #72afd2;
}
</style>

<section class="content-header">
  <h1>
    用户概况
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">用户概况</li>
  </ol>
</section>
<?php
if(isset($config['qr_count'])&&$qrcron==1){
  $text='公众号粉丝拉取完毕';
}elseif (!isset($config['qr_count'])) {
  $text='有赞参数未填写';
}else {
  $number=$config['qr_total']-$number2;
  $time=ceil($number/10000);
  $text='预计还需要'.$time.'分钟公众号粉丝拉取完毕';
}
?>
<!-- Main content -->
<section class="content">
<form method="get" name="loginsform">

  <div class="row">
    <div class="col-xs-12">
       <h3 class="box-title"><?=$text?></h3>
       <h4 class="box-title">特别说明：系统会首次自动拉取公众号的粉丝数据，拉取完成后需要点击刷新按钮才能刷新公众号的新增粉丝，取消关注的粉丝数据不会更新；</h4>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <a href="/yyba/qrcode?refresh=1" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-refresh"></i> &nbsp; <span>点击刷新公众号粉丝</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>预约活动用户数</th>
                  <th>公众号粉丝数</th>
                </tr>
                <tr>
                  <td><?=$number1?></td>
                  <td><?=$number2?></td>
                </tr>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>
    </div>
  </div>
</form>
</section><!-- /.content -->
