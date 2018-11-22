
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                订单明细
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
    <li class="am-active"><a href="/qwtxdba/qrcodes">订单明细</a></li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=count($yzorder)?> 个订单
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll;">
                            <form class="am-form">
                                <table class="am-table am-text-nowrap am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                                    <th>用户昵称</th>
                                    <th>用户头像</th>
                                    <th>订单编号</th>
                                    <th>商品名称</th>
                                    <th>商品图片</th>
                                    <th>订单总价</th>
                                    <th>下单时间</th>
                                    <th>最后更新</th>
                                    <th>订单状态</th>
                </tr>
                </thead>
                                    <tbody>
                                    <?php foreach ($yzorder as $k => $v):?>
                                      <tr>
                                    <td><?=$v->qrcode->nickname?></td>
                                    <td><img src='<?=$v->qrcode->headimgurl?>' style='height:30px'></td>
                                    <td><?=$v->tid?></td>
                                    <td><?=$v->title?></td>
                                    <td><img src='<?=$v->pic_thumb_path?>' style='height:30px'></td>
                                    <td><?=$v->total_fee?></td>
                                    <td><?=$v->pay_time?></td>
                                    <td><?=$v->update_time?></td>
                                    <td><?=$v->status=='TRADE_SUCCESS'?'已完成':$v->status?></td>
                                    </tr>
                                  <?php endforeach?>
                                    </tbody>
                                </table>
                            </form>
                            <div class="am-u-lg-12">
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
                </div>
                <div class="tpl-alert"></div>
            </div>
</form>









        </div>
        </div>
<script type="text/javascript">
</script>
