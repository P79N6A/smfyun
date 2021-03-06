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
.label {font-size: 14px}

.backgroundcolor-red{
  background-color: #dd4b39;
}
.backgroundcolor-yellow{
  background-color: #f39c12;
}
.backgroundcolor-green{
  background-color: #008d4c;
}
.mask{
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    display: -webkit-box;
    -webkit-box-pack: center;
    -webkit-box-align: center;
    z-index: 10000;
    background-color: rgba(0, 0, 0, .7);
    -webkit-animation: fadeIn .6s ease;
    color: rgba(255,255,255,.7);
}
.payment{
  position: fixed;
  background-color: #fff;
  width: 400px;
  height: 400px;
  top: 20%;
  left: 50%;
  margin-left: -200px;
  color: #000;
  border-radius: 5px;
}
.payment-hd,.payment-bd,.payment-lg,.payment-ft{
  width: 100%;
  text-align: center;
  margin-top: 10px;
}
.close{
  margin-top: 5px;
  margin-right: 10px;
}
.payment-hd{
  display: inline-block;
  text-align: left;
  margin-left: 80px;
}
.payment-lg img{
  padding: 5px;
  border: 1px solid #d5d5d5;
  width: 150;
  height: 150px;
}
.payment-bd{
  height: 44px;
  line-height: 44px;
}
.price-left{
  float: left;
  text-align: right;
  width: 150px;
}
.price-num{
  float: left;
  text-align: left;
  padding-left: 5px;
  font-size: 28px;
  color: #ff6600;
}
.price-right{
  float: left;
  text-align: right;
  width: 25px;
}
.subtips{
  color: #777777;
}
input{
  padding: 10px 6px;
}
.explain{
  background-color: #445e84;
  padding-top: 2px;
  width: 150px;
  display: inline-block;
}
.controls{
  display: inline-block;
}
.selected .radio-box{

    color: #608908;
    background: #ffffff;
    border: 2px solid #a5c85b;
    padding: 7px 9px;
    outline: 0;
}
.radio-box{
display: block;
    color: #555555;
    background: #f5f5f5;
    border: 1px solid #cfcfcf;
    padding: 8px 10px;
    text-decoration: none;
  }
.selectedf .radio-boxf{

    color: #608908;
    background: #ffffff;
    border: 2px solid #a5c85b;
    padding: 7px 9px;
    outline: 0;
}
.radio-boxf{
display: block;
    color: #555555;
    background: #f5f5f5;
    border: 1px solid #cfcfcf;
    padding: 8px 10px;
    text-decoration: none;
  }
.timelength{
  display: block;
  margin-top: 20px;
  width: 600px;
  }
.box-limit{
  display: inline-block;
  padding: 10px;
  margin: 0;
  color: #fff;
  font-size: 14px;
  border-radius: 3px;
  margin-left: 20px;
}
.box-left{
  display: inline-block;
  padding:10px;
  margin: 0;
  color: #fff;
  font-size: 14px;
  border-radius: 3px;
  margin-left: 20px;
}
.flowsum{
  display: block;
  margin-top: 20px;
  width: 600px;
}
/*上买时间，下买流量*/
.paymentf{
  position: fixed;
  background-color: #fff;
  width: 500px;
  height: 400px;
  top: 20%;
  left: 50%;
  margin-left: -250px;
  color: #000;
  border-radius: 5px;
}
.paymentf-hd,.paymentf-bd,.paymentf-lg,.paymentf-ft{
  width: 100%;
  text-align: center;
  margin-top: 10px;
}
.close{
  margin-top: 5px;
  margin-right: 10px;
}
.paymentf-hd{
  display: flex;
  text-align: left;
  margin-left: 80px;
}
.paymentf-lg img{
  padding: 5px;
  border: 1px solid #d5d5d5;
  width: 150;
  height: 150px;
}
.paymentf-bd{
  height: 44px;
  line-height: 44px;
}
.pricef-left,.flow-left{
  float: left;
  text-align: right;
  width: 150px;
}
.pricef-num,.flow-num{
  float: left;
  text-align: left;
  padding-left: 5px;
  font-size: 28px;
  color: #ff6600;
}
.pricef-right,.flow-right{
  float: left;
  text-align: right;
  width: 25px;
}
.subtips{
  color: #777777;
}
input{
  padding: 10px 6px;
  line-height: 10px;
}
.explain{
  background-color: #445e84;
  padding-top: 2px;
  width: 150px;
  display: inline-block;
}
.controls{
  display: inline-block;
}
.selected .radio-box{

    color: #608908;
    background: #ffffff;
    border: 2px solid #a5c85b;
    padding: 7px 9px;
    outline: 0;
}
.radio-box{
display: block;
    color: #555555;
    background: #f5f5f5;
    border: 1px solid #cfcfcf;
    padding: 8px 10px;
    text-decoration: none;
}
.enter{
  display: inline-block;
  float: right;
  margin-top: 5px;
  margin-right: 80px;
}
#timeperchase{
  width: 110px;
  text-align: center;
}
</style>
<?php
if (!$_POST) $active = 'yz';
if (isset($_POST['password'])) $active = 'account';
?>
<section class="content-header">
  <h1>
    基本信息
    <small>有效期与直播流量</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基本信息</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_yz_li"><a href="#cfg_yz" data-toggle="tab">有效期和直播流量</a></li>
            <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">账号密码</a></li>
          </ul>
          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">

            <div class="tab-pane" id="cfg_yz" style="overflow:scroll">
              <div class="timelength">
            <?php if($shop->expiretime):?>
              <?php if((strtotime($shop->expiretime)+24*3600)<time()):?>
                <h3 class="box-limit backgroundcolor-red">您的账户已过期！</h3>
      <a id="timeperchase" class="btn btn-success pull-right"> <span>购买有效期</span></a>
              <?php else:?>
                <h3 class="box-limit backgroundcolor-red">您的使用权限将于<?=$shop->expiretime?>到期</h3>
                <h3 class="box-left backgroundcolor-yellow">剩余<?=floor((strtotime($shop->expiretime)-time())/(24*3600))?>天</h3>
      <a id="timeperchase" class="btn btn-success pull-right"> <span>购买有效期</span></a>
              <?php endif?>
            <?php else:?>
              <h3 class="box-limit backgroundcolor-green">您的账户永久使用！</h3>
            <?php endif?>
</div>
<div class="flowsum">
  <h3 class="box-limit backgroundcolor-red">您已经使用了<?=number_format($use/(1024*1024*1024),2)?>G/<?=$all?>G直播流量</h3>
  <h3 class="box-left backgroundcolor-yellow">剩余<?=number_format($all-number_format($use/(1024*1024*1024),2),2)?>G</h3>
  <a id="flowperchase" class="btn btn-success pull-right"> <span>购买直播流量</span></a>
</div>
<div id="mask-time" class="mask" style="display:none">
  <div class="payment">
    <div class="close">X</div>
    <div class="payment-hd">
      <span>续费有效期：</span>
      <div class="controls">
        <label data-action="updateAmount" data-value="720" class="selected" data-type='year'><a class="radio-box" href="javascript:void(0);">包年</a></label>
      </div>
    </div>
    <div class="payment-bd">
      <span class="price-left">应付金额：</span>
      <span class="price-num">720</span>
      <span class="price-right">元</span>
    </div>
    <div class="payment-lg">
    </div>
    <div class="payment-ft">
      <p class="explain">
        <img src="http://imgcache.qq.com/bossweb/ipay/images/pay/tips-rwm.png">
      </p>
    </div>
  </div>
</div>
<div id="mask-flow" class="mask" style="display:none">
  <div class="paymentf">
    <div class="close">X</div>
    <div class="paymentf-hd">
      <span>充值数量：</span>
      <div class="controls">
        <label data-action="updateAmount" data-value="60" class="selectedf" data-type='100GB'><a class="radio-boxf" href="javascript:void(0);">100GB</a></label>
        <label data-action="updateAmount" data-value="275" data-type='500GB'><a class="radio-boxf" href="javascript:void(0);">500GB</a></label>
        <label data-action="updateAmount" data-value="542.72" data-type='1TB'><a class="radio-boxf" href="javascript:void(0);">1TB</a></label>
        <label data-action="updateAmount" data-value="2611.2" data-type='5TB'><a class="radio-boxf" href="javascript:void(0);">5TB</a></label><br>
        <label data-action="updateAmount" data-value="5017.6" data-type='10TB'><a class="radio-boxf" href="javascript:void(0);">10TB</a></label>
        <label data-action="updateAmount" data-value="24064" data-type='50TB'><a class="radio-boxf" href="javascript:void(0);">50TB</a></label>
        <label data-action="updateAmount" data-value="46080" data-type='100TB'><a class="radio-boxf" href="javascript:void(0);">100TB</a></label>
      </div>
      <!-- <input id='stream_data' class="flownum" type="number" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" ></input>
      <span>GB</span> -->
    </div>
      <span class="subtips" style="margin-left:80px">(1TB=1024GB 1GB=1024MB 1MB=1024KB)</span>
    <div class="paymentf-bd">
      <span class="pricef-left">应付金额：</span>
      <span class="pricef-num" id='pricef-num'>60</span>
      <span class="pricef-right">元</span>
    </div>
    <div class="paymentf-lg" id='paymentf-lg'>
    </div>
    <div class="paymentf-ft" id="paymentf-ft" style="display:none">
      <p class="explain">
        <img src="http://imgcache.qq.com/bossweb/ipay/images/pay/tips-rwm.png">
      </p>
    </div>
  </div>
</div>
            </div>
            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['wzba'] = null;
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
                      <input type="text" class="form-control" id="password" value="<?=$shop->pass?>" maxlength="16" name="password"  >
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
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
function get_qrcode(){
  if (!typeof(check)=="undefined") {
    clearInterval(check);
  };
  $.ajax({
      type: "post",
      url: "/wzba/get_qrcode",
      data: { data: $('.price-num').text(),type: $(".selected").data('type') },
      dataType: "json",
      success: function(res) {
        type = $(".selected").data('type');
        $('.payment-lg').html(res.imgurl);
        flag = 1;
        check = setInterval(function() {
        if (!flag) {
            clearInterval(check);
        } else {
            $.ajax({
                method: "post",
                url: "/wzba/notify",
                data: { "qrid": res.imgid,type: type},
                dataType: "html",
                success: function(data) {
                     console.log(res.imgid);
                    if (data) {
                        $(".mask").fadeOut(500);
                        alert(data);
                        flag = false;
                    } else {
                        console.log(2);
                    }
                }
            });
        }
      }, 1000);
      }
  });
}
function get_qrcode_flow(){
  $("#paymentf-ft").show();
  if (!typeof(check)=="undefined") {
      clearInterval(check);
  };

  $.ajax({
      type: "post",
      url: "/wzba/get_qrcode",
      data: { data: $('#pricef-num').text() ,stream:$(".selectedf").data('type'),type:'stream'},
      dataType: "json",
      success: function(res) {
        stream_data = $(".selectedf").data('type');
        $('#paymentf-lg').html(res.imgurl);
        flag = 1;
        check = setInterval(function() {
        if (!flag) {
            clearInterval(check);
        } else {
            $.ajax({
                method: "post",
                url: "/wzba/notify",
                data: { "qrid": res.imgid,stream_data:stream_data,type:'stream'},
                dataType: "html",
                success: function(data) {
                     console.log(res.imgid);
                    if (data) {
                        $(".mask").fadeOut(500);
                        alert(data);
                        flag = false;
                    } else {
                        console.log(2);
                    }
                }
            });
        }
      }, 1000);
      }
  });
}
$(document).on('click','#timeperchase',function(){
    $("#mask-time").fadeIn(500);
    get_qrcode();
});
$(document).on('click','#flowperchase',function(){
  $("#mask-flow").fadeIn(500);
  get_qrcode_flow();
});
$(document).on('click','.radio-box',function(){
    clearInterval(check);
    $(this).parent().parent().children().removeClass('selected');
    $(this).parent().addClass('selected');
    $('.price-num').text($(this).parent().data('value'));
    get_qrcode();
});
$(document).on('click','.radio-boxf',function(){
    clearInterval(check);
    $(this).parent().parent().children().removeClass('selectedf');
    $(this).parent().addClass('selectedf');
    $('#pricef-num').text($('.selectedf').data('value'));
    get_qrcode_flow();
});
$(document).on('click','.close',function(){
    $(".mask").fadeOut(500);
    clearInterval(check);
});

</script>
<script type="text/javascript">
function showfunc(){
  $('.coupon').fadeIn(500);
}
function hidefunc(){
  $('.coupon').fadeOut(500);
}
</script>
