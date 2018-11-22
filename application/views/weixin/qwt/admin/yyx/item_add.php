

<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .switch-content{
        overflow: hidden !important;
    }
    .hide{height: 0;}
    label{
        text-align: left !important;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                添加大客户订单
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li>大客户订单</li>
                <li class="am-active">添加大客户订单</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            添加大客户订单
                        </div>
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                        <div class="am-form am-form-horizontal">

                <?php if ($result['ok3'] > 0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>订单录入成功!</p>
                            </div>
                        </div>
                </div>
                <?php endif?>
                <form role="form" method="post" onsubmit="return toVaild()">
                                <div class="am-form-group">
                                    <label for="datetimepicker1" class="am-u-sm-12 am-form-label">订单时间</label>
                                    <div class="am-u-sm-12">
  <input name="order[time]" id="datetimepicker1" size="16" type="text" value="" class="am-form-field" readonly>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal3" class="am-u-sm-12 am-form-label">订单名称</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal3" name="order[name]"  value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal4" class="am-u-sm-12 am-form-label">订单金额</label>
                                    <div class="am-u-sm-12">
                    <input type="number" step="0.01" class="tpl-form-input" id="goal4" name="order[money]"  value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="prov" class="am-u-sm-12 am-form-label">地址</label>
                        <div class="am-u-sm-12 loc" id="city1">
                <div class="am-form-group">
                          <select id="prov" class="prov" name="area[pro1]" style="width:25%;min-width:200px;"></select>
                          <select class="city" name="area[city1]" style="width:25%;min-width:200px;" disabled="disabled"></select>
                          <select class="dist" name="area[dis1]" style="width:25%;min-width:200px;" disabled="disabled"></select>
                        </div>
                        </div>
                                    </div>
                                </div>
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">录入订单信息</button>
                        </div>
                </div>
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
                <script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/wdy/plugins/citySelect/city.min1.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">

<?php if($result['err3']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
                  $("#city1").citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    </script>

