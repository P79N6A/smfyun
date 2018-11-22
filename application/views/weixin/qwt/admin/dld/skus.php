<style type="text/css">
  .tpl-form-line-form .am-form-label{
    text-align: left !important;
  }
  label{
    text-align: left !important;

  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                代理设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li class="am-active">代理设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>代理设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body tpl-form-line">
                            <form name="qrcodesform" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 代理申请链接</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">申请成为代理商的链接（可投放）： </label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" value="http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_base/<?=$bid?>/dld/form" readonly="readonly">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 消息设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">自己成为代理时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[self]" value="<?=htmlspecialchars($config['text_self'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">获得直属新代理时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[direct]" value="<?=htmlspecialchars($config['text_direct'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">团队获得新代理时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[group]" value="<?=htmlspecialchars($config['text_group'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">获得直属新客户时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[dirctcus]" value="<?=htmlspecialchars($config['text_dirctcus'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">获得直属新客户时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[dirctcus]" value="<?=htmlspecialchars($config['text_dirctcus'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">团队获得新客户时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[customer]" value="<?=htmlspecialchars($config['text_customer'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">获得直属新订单时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[dirctorder]" value="<?=htmlspecialchars($config['text_dirctorder'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">代理商自购时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[selforder]" value="<?=htmlspecialchars($config['text_selforder'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">团队获得新订单时接收消息：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[order]" value="<?=htmlspecialchars($config['text_order'])?>">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 分享文案设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">邀请新代理时候分享朋友圈标题文案</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="timeline" name="share[timeline]" value="<?=$config['timeline']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">邀请新代理时候分享给朋友描述文案</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="appmessage" name="share[appmessage]" value="<?=$config['appmessage']?>">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 代理相关设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">代理申请提示购买文案：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="buytip" name="quest[buytip]" value="<?=htmlspecialchars($config['buytip'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">购物满多少元获得代理资格（单位：元）：</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" step="0.01" class="tpl-form-input" id="price" name="quest[buy]" value="<?=$config['buy_money']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">购买后获得代理资格的商品链接：</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="link" name="quest[buy_url]" value="<?=$config['buy_url']?>" placeholder='http://'>
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>团队销售奖励设置 </p>
                </div>
            </div>
            <div class="row">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="ql" class="am-form-label">销售额区间起点（元）：</label>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="qt" class="am-form-label">销售额区间终点（元）：</label>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="qw" class="am-form-label">分成率（%）：</label>
                                    </div>
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
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                          <input type="number" step="0.01" class="tpl-form-input" id="start<?=$v->lv?>" maxlength="10" name="menu[key_c<?=$name1?>_dld]" value="<?=$v->money1?>" readonly="readonly">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                          <input type="number" step="0.01" class="tpl-form-input" id="end<?=$v->lv?>" maxlength="10" name="menu[key_c<?=$name2?>_dld]" value="<?=$v->money2?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                          <input type="number" step="0.01" class="tpl-form-input" id="rate<?=$v->lv?>" maxlength="200" name="menu[value_c<?=$v->lv?>_dld]" value="<?=$v->scale?>">
                                    </div>
                                </div>
                </div>
            </div>
                  <?php endforeach;?>
                    </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button id="add" type="button" class="am-btn am-btn-primary tpl-btn-bg-color-success ">添加一行</button>
                                        <button id="delete" type="button" class="am-btn am-btn-primary tpl-btn-bg-color-danger ">删除一行</button>
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p>顶级代理商邀请码设置 </p>
                </div>
            </div>

                                <div class="am-form-group">
                                    <label for="qw" class="am-u-sm-12 am-form-label">成为一级代理商所需输入的邀请码：</label>
                                    <div class="am-u-sm-12">
                        <div class="input-group">
                          <input type="text" readonly="" class="tpl-form-input" id='code' name="ivcode" value="<?=$config['code']?>" style="display:inline-block;width:50%;"><span class="input-group-btn">
                            <button class="am-btn am-btn-default click_change" type="button" style="border: 1px solid #ccc;">
                              点击生成随机邀请码
                            </button>
                          </span>
                        </div>
                        <small>（输入后可以直接成为一级代理，请谨慎设置）</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">输入邀请码成为一级代理商的链接（请谨慎投放）： </label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" value="http://<?=$_SERVER["HTTP_HOST"]?>/smfyun/user_snsapi_base/<?=$bid?>/dld/code" readonly="readonly">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button id="save-btn" type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存</button>
                                    </div>
                                </div>
                                <small id="formerrormsg" style="margin-left:20px;color:red;display:none">您输入的信息有误或不完整，请仔细检查</small>
                            </form>

                        </div>
                    </div>
                </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>



        </div>


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
    $('.input-box').append(
            "<div class=\"row\">"+
                "<div class=\"am-u-sm-4\">"+
                                "<div class=\"am-form-group\">"+
                                    "<div>"+
                          "<input type=\"number\" step=\"0.01\" class=\"tpl-form-input\" id=\"start"+b+"\" maxlength=\"10\" name=\"menu[key_c"+c+"_dld]\" value=\"\" readonly=\"readonly\">"+
                                    "</div>"+
                                "</div>"+
                "</div>"+
                "<div class=\"am-u-sm-4\">"+
                                "<div class=\"am-form-group\">"+
                                    "<div>"+
                          "<input type=\"number\" step=\"0.01\" class=\"tpl-form-input\" id=\"end"+b+"\" maxlength=\"10\" name=\"menu[key_c"+d+"_dld]\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                "</div>"+
                "<div class=\"am-u-sm-4\">"+
                                "<div class=\"am-form-group\">"+
                                    "<div>"+
                          "<input type=\"number\" step=\"0.01\" class=\"tpl-form-input\" id=\"rate"+b+"\" maxlength=\"200\" name=\"menu[value_c"+b+"_dld]\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                "</div>"+
            "</div>"
                    );
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
