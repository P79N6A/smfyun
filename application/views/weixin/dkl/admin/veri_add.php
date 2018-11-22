<style type="text/css">
  .form-group {margin-left:10px;}
</style>
<section class="content-header">
  <h1>
    添加核销员
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/dkla/items">添加核销员</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<section class="content">
<?php if ($result['error']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
<?php endif?>
<div class="row">
<div class="col-lg-12">
<div class="box box-success">
  <?php if ($result['err']):?>
    <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err']?></div>
  <?php endif?>
  <form role="form" method="post" enctype="multipart/form-data" onsubmit="return check(this.form)">
      <div class="row">
        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">核销员账号(手机号)</label>
          <input type="text" class="form-control" id="stock" name="data[tel]" placeholder="输入核销员账号" >
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">核销员标签</label>
          <input type="text" class="form-control" id="stock" name="data[tag]" placeholder="输入核销员标签">
        </div>
        </div>
      </div>
       <div class="row">
        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">核销员名称</label>
          <input type="text" class="form-control" id="stock" name="data[name]" placeholder="输入核销员名称">
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">核销权限开关</label>
          <div class="radio">
            <label class="checkbox-inline">
              <input type="radio" name="data[switch]" id="rsync1" value="1" checked>
              <span class="label label-success"  style="font-size:14px">开启</span>
            </label>
            <label class="checkbox-inline">
              <input type="radio" name="data[switch]" id="rsync0" value="0">
              <span class="label label-danger"  style="font-size:14px">关闭</span>
            </label>
          </div>
        </div>
      </div>
    </div>
     <div class="box-footer">
      <button type="submit" class="btn btn-success">确定</button>
    </div>
  </form>
</div>
</div>
</div>
</section>


