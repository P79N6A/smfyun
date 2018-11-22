<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';
$order['WAIT_SELLER_SEND_GOODS'] = '买家已付款';
$order['WAIT_BUYER_CONFIRM_GOODS'] = '卖家已发货';
$order['TRADE_BUYER_SIGNED'] = '买家已签收';
$order['TRADE_CLOSED'] = '交易关闭';
$order['TRADE_CLOSED_BY_USER'] = '交易关闭';
$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>


<div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                订单记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">直播分析</a></li>
                <li class="am-active">订单记录</li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>: 共 <?=$result['countall']?> 条记录
                    </div>
                    </div>
                  <!-- <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>"> -->

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th>性别</th>
                  <th>商品名称</a></th>
                  <th>购买数量</a></th>
                  <th>订单金额</a></th>
                  <th>付款金额</a></th>
                  <th>下单时间</th>
                  <th>订单状态</th>
                </tr>
                                    </thead>
                                    <tbody>
 <?php
                foreach ($result['trades'] as $v):
                $information=ORM::factory('qwt_wzbqrcode')->where('id','=',$v->qid)->find();
                ?>

                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><?=$information->nickname?></td>
                  <td><?=$sex[$information->sex]?></td>
                  <td><?=$v->title?></td>
                  <td ><?=$v->num?></a></td>
                  <td><?=$v->total_fee?></td>
                  <td><?=$v->payment?></td>
                  <td><?=$v->pay_time?></td>
                  <td><?=$order[$v->status]?></td>
                </tr>

                <?php endforeach;?>
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
                <div class="tpl-alert"></div>
            </div>










        </div>
