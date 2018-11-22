<style>
.label {font-size: 14px}
.warning-text{

    height: 50px;
    padding: 10px;
    background-color: #ff1c00;
    color: white;
    font-size: 18px;
    font-weight: bold;
    line-height: 30px;
    margin-bottom: 20px;
    border-radius: 7px;
    border: 1px solid #e5e5e5;
}
</style>

<section class="content-header">
  <h1>
    代理设置
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">代理设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">


<form method="post" name="qrcodesform">
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">代理设置</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                     <div class="alert alert-danger">消息设置</div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>自己成为代理时接收消息：</label>
                          <input type="text" class="form-control" name="text[self]" value="<?=$config['text_self']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>获得直属新代理时接收消息：</label>
                          <input type="text" class="form-control" name="text[direct]" value="<?=$config['text_direct']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>团队获得新代理时接收消息：</label>
                          <input type="text" class="form-control" name="text[group]" value="<?=$config['text_group']?>" >
                        </div>
                      </div>
                    </div>
                    <!-- <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>获得直属新客户时接收消息：</label>
                          <input type="text" class="form-control" name="text[dirctcus]" value="<?=$config['text_dirctcus']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>团队获得新客户时接收消息：</label>
                          <input type="text" class="form-control" name="text[customer]" value="<?=$config['text_customer']?>" >
                        </div>
                      </div>
                    </div> -->
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>获得直属新订单时接收消息：</label>
                          <input type="text" class="form-control" name="text[dirctorder]" value="<?=$config['text_dirctorder']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>代理商自购时接收消息：</label>
                          <input type="text" class="form-control" name="text[selforder]" value="<?=$config['text_selforder']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>团队获得新订单时接收消息：</label>
                          <input type="text" class="form-control" name="text[order]" value="<?=$config['text_order']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="alert alert-danger">代理相关设置</div>
                    <div class="row">
                      <div class="col-lg-6 col-sm-6">
                        <div class="form-group">
                          <label>购物满多少元获得代理资格（单位：元）：</label>
                          <input type="number" step="0.01" class="form-control" id="price" name="quest[buy]" value="<?=$config['buy_money']?>" >
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class="form-group">
                          <label>购买后获得代理资格的商品链接：</label>
                          <input type="text" class="form-control" id="link" name="quest[buy_url]" value="<?=$config['buy_url']?>" placeholder='http://'>
                        </div>
                      </div>
                    </div>
                    <div class="alert alert-danger">团队销售奖励设置</div>
                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">销售额区间起点（元）：</label>
                        </div>
                      </div>
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">销售额区间终点（元）：</label>
                        </div>
                      </div>
                     <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="v10">分成率（%）：</label>
                        </div>
                      </div>
                      <input type="text" id="count" name="menu[count]" style="display:none" value="">
                    </div>

                    <div class="input-box">
                  <?php foreach ($result['skus'] as $v):
                  $name1 = (($v->lv)*2)-1;
                  $name2 = ($v->lv)*2;
                  ?>
                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="number" step="0.01" class="form-control" id="start<?=$v->lv?>" maxlength="10" name="menu[key_c<?=$name1?>_wsd]" value="<?=$v->money1?>" readonly="readonly">
                        </div>
                      </div>
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="number" step="0.01" class="form-control" id="end<?=$v->lv?>" maxlength="10" name="menu[key_c<?=$name2?>_wsd]" value="<?=$v->money2?>">
                        </div>
                      </div>
                     <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="number" step="0.01" class="form-control" id="rate<?=$v->lv?>" maxlength="200" name="menu[value_c<?=$v->lv?>_wsd]" value="<?=$v->scale?>">
                        </div>
                      </div>
                    </div>
                  <?php endforeach;?>
                    </div>
                    <div class="row">
                     <div class="col-lg-12 col-sm-12">
                    <button id="add" type="button" class="btn btn-success">添加一行</button>
                    <button id="delete" type="button" class="btn btn-danger">删除一行</button>
                    </div>
                    </div>
                    <br>
                    <div class="alert alert-danger">顶级代理商邀请码设置</div>
                    <div class="row">
                      <div class="col-lg-12 col-sm-12">
                        <div class='form-group'>
                          <label>成为一级代理商所需输入的邀请码（输入后可以直接成为一级代理，请谨慎设置）：</label>
                        </div>
                      </div>
                      <div class="col-lg-6 col-sm-6" style="margin-bottom:20px;">
                        <div class="input-group">
                          <input id='code' type="text" readonly="" class="form-control" name="ivcode" value="<?=$config['code']?>" >
                          <span class="input-group-btn">
                            <button class="btn btn-default click_change" type="button">
                              点击生成随机邀请码
                            </button>
                          </span>
                        </div>
                      </div>
                      <!-- <div class="col-lg-6">
                          <div class="input-group">
                              <input type="text" class="form-control">
                              <span class="input-group-btn">
                                  <button class="btn btn-default" type="button">Go!</button>
                              </span>
                          </div>
                      </div> -->
                      <!-- <div class="col-lg-6 col-sm-6"><div class="form-group"><button type="button" class="click_change btn btn-success">点击生成随机邀请码</button></div></div> -->
                    </div>
                    <div class="row">
                      <div class="col-lg-6 col-sm-6">
                        <div class="form-group">
                          <label>输入邀请码成为一级代理商的链接（请谨慎投放）：</label>
                          <input type="text" class="form-control" value="http://<?=$_SERVER["HTTP_HOST"]?>/wsd/index_oauth/<?=$bid?>/code" readonly="readonly">
                        </div>
                      </div>
                    </div>
                <!--     <div class="row" style="margin-top:15px;">
                      <div class="col-lg-6 col-sm-6">
                        <div class="form-group">
                          <label>每月结算日期：</label>
                          <input type="number" class="form-control" name="date" value="<?=$config['date']?>" >
                        </div>
                      </div>
                    </div> -->

                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button id="save-btn" type="submit" class="btn btn-success">保存代理设置</button>
                    <span id="formerrormsg" style="margin-left:20px;color:red;display:none">您输入的信息有误或不完整，请仔细检查</span>
                  </div>
            </div><!-- /.box-body -->
          </div>

    </div>
    </form>

</section><!-- /.content -->

<script type="text/javascript">
    $('.click_change').click(function() {
        var str = '';
        var chars = ['0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        for (var i = 0; i<=5; i++) {
            var id = Math.ceil(Math.random()*35);
            str += chars[id];
        };
        $('#code').val(str);
    });
                $(document).ready(function(){
    var a = $('.input-box').children().length;
    if (a==0) {add()};
                  count();
                  $('#start1').removeAttr('readonly');
                });
  $(document).on('click','#add',function(){
    add();
  })
  $(document).on('click','#delete',function(){
    var a = $('.input-box').children().length;
    if (a>1) {
    $('.input-box').children('div:last').remove();
    };
    count();
    check();
  })
  function add(){
    var a = $('.input-box').children().length;
    console.log(a);
    var b = a+1;
    var c = a*2+1;
    var d = c+1;
    $('.input-box').append("<div class=\"row\">"+
                      "<div class=\"col-lg-3 col-sm-3\">"+
                        "<div class=\"form-group\">"+
                          "<input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"start"+b+"\" maxlength=\"10\" name=\"menu[key_c"+c+"_wsd]\" value=\"\" readonly=\"readonly\">"+
                        "</div>"+
                      "</div>"+
                      "<div class=\"col-lg-3 col-sm-3\">"+
                        "<div class=\"form-group\">"+
                          "<input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"end"+b+"\" maxlength=\"10\" name=\"menu[key_c"+d+"_wsd]\" value=\"\">"+
                        "</div>"+
                      "</div>"+
                     "<div class=\"col-lg-3 col-sm-3\">"+
                        "<div class=\"form-group\">"+
                          "<input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"rate"+b+"\" maxlength=\"200\" name=\"menu[value_c"+b+"_wsd]\" value=\"\">"+
                        "</div>"+
                      "</div>"+
                    "</div>");
    count();
    change();
  }
  function count(){
    var a = $('.input-box').children().length;
    $('#count').val(a);
  }
  $(document).on('change','input',function(){
    change();
  })
  function change(){
    var a = $('.input-box').children().length;
    for (var b = a; b >= 2; b--) {
      d = b-1;
      e = $('#end'+d).val();
      $('#start'+b).val(e);
  }
  check();
}
  function check(){
    var a = $('.input-box').children().length;
      $('#save-btn').removeAttr('disabled');
      $('#formerrormsg').hide();
    for (var b = a; b >= 1; b--) {
      c = $('#start'+b).val();
      f = $('#end'+b).val();
      d = $('#rate'+b).val();
      e = d.length;
      if (parseInt(d)>100) {$('#save-btn').attr('disabled','disabled');$('#formerrormsg').show();};
      if (e==0) {$('#save-btn').attr('disabled','disabled');$('#formerrormsg').show();};
      if (parseInt(f)>parseInt(c)) {}else{$('#save-btn').attr('disabled','disabled');$('#formerrormsg').show();};
    };
  }

</script>
