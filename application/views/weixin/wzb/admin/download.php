<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
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
  .qrcode{
    height: 150px;
    width: 150px;
  }
  .android{

    margin-top: 5px;
    width: 150px;
    text-align: center;
    font-size: 16px;
  }
  .subtext{
    font-size: 1px;
  }
</style>

<section class="content-header">
  <h1>
    直播应用下载
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">直播应用下载</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';
          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if (isset($_POST['password'])) $active = 'account';
          ?>

          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">

            <div class="tab-pane active" id="cfg_text">
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                   <img class="qrcode" src="http://<?=$_SERVER["HTTP_HOST"]?>/wzb/img/qrcode.jpg">
                   <div class="android">安卓版<span class="subtext">(for android)</span></div>
                  </div><!-- /.box-body -->
                </form>
            </div>
            <div class="box-footer">由于微信浏览器禁止下载文件，建议使用QQ浏览器扫码下载</div>
          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->
<script type="text/javascript">
function showfunc(){
  $('.coupon').fadeIn(500);
}
function hidefunc(){
  $('.coupon').fadeOut(500);
}
</script>
