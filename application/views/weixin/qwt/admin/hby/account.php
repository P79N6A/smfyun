
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                红包充值
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>红包充值及收支明细</li>
                <li class="am-active">红包充值</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li id="tab1-bar" class="am-active"><a href="#tab1">红包充值</a></li>
                                <li id="tab2-bar"><a href="payment">收支明细</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                <div class="am-u-sm-12 am-u-md-12">
                    <label for="menu" class="am-u-sm-12 am-form-label">您的账户余额为：<?=number_format($result['all'],2)?>元</label>
                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-6">
                        <input id="money" type="number" step='1' class="form-control" placeholder='请输入您要充值的金额，单位：元'>
                        </div>
                        </div>

                <div class="am-form-group">
                        <div class="am-u-sm-6">
                  <a class="am-btn am-btn-danger" id="delete" data-toggle="modal" data-target="#deleteModel">充值余额</a>
                        </div>
                </div>
                <div class="am-form-group">
                    <span class='sprice'></span>
                </div>
                <small style="color:red">说明：<br>
1、新建的营销规则，营销类型选择的是“分享到朋友圈后发送微信红包”，需操作红包充值；<br>
2、为方便操作，发送微信红包我方采用的是代充值、代发送的模式，代充值微信支付会产生千分之六的手续费，此手续费是微信支付收取；<br>
3、在充值的时候，支付的金额是红包金额+千分之六的手续费；比如充值红包金额1000元，需支付的金额是1000+1000*0.6%共计1006，产生的手续费不足1分钱的按1分钱计算；<br>
4、账户余额目前不支持提现，请提前预估好后再进行红包充值；</small>
                </div>
                </div>
                </div>
                </div>
                                <div class="am-tab-panel am-fade" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                          <th class="table-type">订单编号</th>
                                          <th class="table-title">充值金额</th>
                                          <th class="table-id">充值时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($result['orders'] as $k => $v):?>
                                    <tr>
                                      <td><?=$v->tid?></td>
                                      <td><?=number_format($v->money,2)?></td>
                                      <td><?=date('Y-m-d H:i:s',$v->time)?></td>
                                    </tr>
                                  <?php endforeach?>
                                    </tbody>
                                </table>
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                        </div>

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
<script type="text/javascript">
$("#money").keydown(function (e) {
    var code = parseInt(e.keyCode);
    if (code >= 96 && code <= 105 || code >= 48 && code <= 57 || code == 8) {
        return true;
    } else {
        return false;
    }
})
$('#money').bind('input propertychange',function(){
    if($(this).val()>0){
        var a = parseInt($(this).val())*6/1000?parseInt($(this).val())*6/1000:0;
        window.sprice = a.toFixed(2)?a.toFixed(2):0.00;
        $('.sprice').text('收取'+sprice+'元手续费');
    }else{
        $('.sprice').text('');
    }
});
document.querySelector('#delete').onclick = function(){
    var a = $('#money').val();
    if (a<5) {
        alert('最少充值金额为5元');
    }else{
        $.ajax({
            // 地址类型datatype
            url: "/qwthbya/buy/"+a,
            type: 'post',
            datatype: 'json',
            success:function(res){
            window.imgsrc = res.imgurl;
            console.log(window.imgsrc);
            window.oid = res.oid;
            var all = parseInt(a)+parseFloat(window.sprice);
            swal({
                title: "微信扫码付款",
                text: "充值金额："+a+"元<br>收取手续费："+window.sprice+"元<br> 实付金额："+all+'元',
                imageUrl: '/qwthbya/show_qr?img='+encodeURI(window.imgsrc),
                imageSize: "200x200",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "我已付款",
                cancelButtonText: "取消付款",
                closeOnConfirm: false,
                closeOnCancel: true,
                html: true,
                  },
                function(isConfirm){
                  if (isConfirm) {
                        $.ajax({
                          url: '/qwthbya/notify_qr/'+window.oid,
                          type: 'post',
                          dataType: 'text',
                          success: function (res){
                    swal("等待", "查询中，请稍等", "info");
                            if(res=='支付成功'){
                    swal("成功", "支付成功", "success");
                            }else{
                    swal("失败", res, "error");
                            }
                          }
                        });
                  }
              })

            }
        })
    }
};
</script>
