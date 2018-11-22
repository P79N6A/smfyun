<style>
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>

<section class="content-header">
  <h1>
    基础设置
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

        <ul class="nav nav-tabs">
          <li id="cfg_area_li"><a href="#cfg_notice" data-toggle="tab">公告设置</a></li>
          <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
        </ul>

        <?php
        if (!$_POST||$_POST['notice']) $active = 'notice';
        if (isset($_POST['password'])) $active = 'account';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">
              <!-- 2015.12.21增加指定地区用户参与部分 -->

              <div class="tab-pane" id="cfg_notice">
                <?php if ($result['ok5'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功!</div>
                <?php endif?>
                <form role="form" method="post" onsubmit="return toVaild()">
    <div class="form-group">
      <label for="notice">公告设置</label>
      <input type="text" class="form-control" id="notice" placeholder="请输入公告文字" name="notice" value="<?=$notice?>">
    </div>



            <div class="form-group">
              <label for="show">
                <div class="box-footer">
                  <button id="sub" type="submit" class="btn btn-success">保存</button>
                </div>
              </label>

            </div>


          </form>
        </div>

        <!-- 2015.12.21增加指定地区用户参与部分 完-->
<script language=javascript>
  function onlyNum()
      {
      if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
      if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
      event.returnValue=false;
  }
  $(function () {
  var editor = new Simditor({
    textarea: $('.textarea'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });
})
</script>

<!-- 批量打标签 结束 -->


<div class="tab-pane" id="cfg_account">

  <?php if ($result['ok4'] > 0):
  $_SESSION['sqba'] = null;
  ?>
  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>新密码已生效，请重新登录</div>
<?php endif?>

<?php if ($result['err4']):?>
  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err4']?></div>
<?php endif?>


<form role="form" method="post">
  <div class="box-body">

    <div class="form-group">
      <label for="password">旧密码</label>
      <input type="password" class="form-control" id="password" placeholder="请输入旧密码" maxlength="16" name="password">
    </div>

    <div class="form-group">
      <label for="newpassword">新密码</label>
      <input type="password" class="form-control" id="newpassword" placeholder="请输入新密码" maxlength="16" name="newpassword">
    </div>

    <div class="form-group">
      <label for="newpassword2">重复新密码</label>
      <input type="password" class="form-control" id="newpassword2" placeholder="请再次输入新密码" maxlength="16" name="newpassword2">
    </div>

    <div class="box-footer">
      <input type="hidden" name="yz" value="1">
      <button type="submit" class="btn btn-success">修改登录密码</button>
    </div>
  </form>
</div>

</div>
</div>

</div><!--/.col (left) -->

</section><!-- /.content -->

