<section class="content-header">
  <h1>
    <?=$result['title']?>
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/yyba/url"><?=$result['title']?></a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<div class="row">
<div class="col-lg-12">

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$result['title']?></h3>
  </div><!-- /.box-header -->
  <!-- form start -->
  <form role="form" method="post" enctype="multipart/form-data">
    <div class="box-body">
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="address">预约链接（粉丝点击本链接进入，即默认订阅）：</label>
            <input type="text" maxlength="50" class="form-control" value="http://<?=$_SERVER['HTTP_HOST']?>/yyb/storefuop/<?=base64_encode($bid.'$yuyue')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="address">取消预约链接（粉丝点击本链接进入，即取消订阅）：：</label>
            <input type="text" maxlength="50" class="form-control" value="http://<?=$_SERVER['HTTP_HOST']?>/yyb/storefuop/<?=base64_encode($bid.'$cancel')?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="address">绑定预览（商户点击本链接进入即绑定，可接收预览的模板消息）：</label>
            <input type="text" maxlength="50" class="form-control" value="http://<?=$_SERVER['HTTP_HOST']?>/yyb/storefuop/<?=base64_encode($bid.'$yulan')?>">
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
</div>
</div>
</section>

