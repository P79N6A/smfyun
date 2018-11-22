
<style type="text/css">
    label{
        text-align: left !important;
    }
    .loc .am-form-group select{
        width: 30% !important;
        display: inline-block !important;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                有赞微商城下单后下发
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">公众号自动下发小程序卡片工具</a></li>
                <li>下发规则</li>
                <li class="am-active">有赞微商城下单后下发</li>
            </ol>
            <div class="tpl-portlet-components" style="overflow: -webkit-paged-x;">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            有赞微商城下单后下发
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
<?php if ($result['content']=='ok'):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 保存成功! </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-3 am-form-label">是否开启用户在绑定的有赞微商城支付后下发小程序卡片</label>
                        <div class="am-u-sm-9 am-u-md-9" style="float:left">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$rule->switch == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$rule->switch === "0" || !$rule->switch ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="rule[switch]" id="show0" value="<?=$rule->switch?>">
                                </ul>
                            </div>
                </div>
                                </div>
                                <div class="am-form-group box" <?=$rule->switch == 1 ? '' : 'style="display:none;"'?>>
                                    <div class="am-form-group">
                                        <label for="user-phone" class="am-u-sm-3 am-form-label">发送的小程序卡片</label>
                                        <div class="am-u-sm-9">
                                            <select data-am-selected="{searchBox: 1}" name="rule[content]" value="<?=$rule->mid?>">
                                            <?php foreach ($msg as $k => $v):?>
      <option <?=$v->id==$rule->mid?'selected':''?> value="<?=$v->id?>"><?=$v->name?></option>
    <?php endforeach?>
    </select>
                                        </div>
                                    </div>
                                    <div class="am-form-group">
                                        <label for="goal2" class="am-u-sm-3 am-form-label">发送文案（在小程序卡片下发后发送，不填即不发送）</label>
                                        <div class="am-u-sm-9">
                        <input type="text" class="tpl-form-input" id="goal2" name="rule[text]" value="<?=$rule->text?>">
                                        </div>
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

<script type="text/javascript">
<?php if($youzan==null):?>
$(document).ready(function(){
  swal({
    title: "请先绑定有赞",
    text: "请先前往【绑定我们】-【有赞一键授权】绑定有赞店铺，才能使用此功能",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '立即前往',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/yzoauth";
    })
})
<?php else:?>
$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.box').show();
    $('#show0').val(1);
})
<?php endif?>
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.box').hide();
    $('#show0').val(0);
})

</script>
