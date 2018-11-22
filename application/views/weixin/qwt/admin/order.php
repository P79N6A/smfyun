<section>
<style type="text/css">
    th{
        width: 25%;
    }
</style>





  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                订购记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">会员中心</a></li>
                <li class="am-active">订购记录</li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>共有<?=$result['countall']?>条记录</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="inline-block am-scrollable-horizontal am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-id">订单编号</th>
                                            <th class="table-id">产品名称</th>
                                            <th class="table-title">产品规格</th>
                                            <th class="table-type">价格（元）</th>
                                            <th class="table-author am-hide-sm-only">购买时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        <?php foreach ($result['orders'] as $order ): ?>

        <?php
            $iid=ORM::factory('qwt_buy')->where('id','=',$order->buy_id)->find()->iid;
            $name =ORM::factory('qwt_item')->where('id','=',$iid)->find()->name;
            $sh_name=ORM::factory('qwt_sku')->where('id','=',$order->sku_id)->find()->name;
        ?>
        <tr class="gradeX">
            <td><?=$order->tid?></td>
            <td><?=$name?></td>
            <td><?=$sh_name?></td>
            <td><?=$order->rebuy_price?></td>
            <td><?=date('Y-m-d H:i:s',$order->rebuy_time)?></td>
        </tr>
        <?php endforeach ?>

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
                <div class="tpl-alert"></div>
            </div>
        </div>
</body>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>

</html>
