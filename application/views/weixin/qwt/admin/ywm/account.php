
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                红包充值
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">一物一码</a></li>
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
                    <label for="menu" class="am-u-sm-12 am-form-label">您的账户余额为：<?=number_format($result['all']-$result['used'],2)?>元</label>
                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-6">
                        <input id="money" type="number" step='1' class="form-control" placeholder='请输入您要充值的金额，单位：元'>
                        </div>
                        </div>

                <div class="am-form-group">
                        <div class="am-u-sm-3">
                  <a class="am-btn am-btn-danger" id="delete" data-toggle="modal" data-target="#deleteModel">充值余额</a>
                        </div>
                </div>
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
document.querySelector('#delete').onclick = function(){
    var a = $('#money').val();
    if (a=='') {
        alert('请先填写充值金额');
    }else{
        $.ajax({
            // 地址类型datatype
            url: "/qwtywma/buy/"+a,
            type: 'post',
            datatype: 'json',
            success:function(res){
            window.imgsrc = res.imgurl;
            window.qrid = res.qrid;
            swal({
                title: "付款方式",
                text: "微信扫码付款",
                imageUrl: window.imgsrc,
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
                          url: '/qwtywma/notify_qr/'+window.qrid,
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
