<style type="text/css">
    .green{
        color: green!important;
    }
    .red{
        color: red!important;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                红包充值
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>红包充值及收支明细</li>
                <li class="am-active">收支明细</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li><a href="/qwthbya/account">红包充值</a></li>
                                <li class="am-active"><a>收支明细</a></li>
                            </ul>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                          <th class="table-type">订单编号</th>
                                          <th class="table-type">类型</th>
                                          <th class="table-title">金额（单位：元）</th>
                                          <th class="table-title">当前余额</th>
                                          <th class="table-id">时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                     function convert_type($v){
                                        if($v->tid){
                                            return '充值';
                                        }else{
                                            if($v->wxid&&$v->money<0){
                                                return '红包发送';
                                            }
                                            if($v->wxid&&$v->money>0){
                                                return '红包24h未领取，退回账户';
                                            }
                                        }
                                     }
                                    ?>
                                    <?php foreach ($result['orders'] as $k => $v):?>
                                    <tr>
                                      <td><?=$v->tid?$v->tid:'WXID_'.$v->wxid?></td>
                                      <td><?=convert_type($v)?></td>
                                      <td class="<?=number_format($v->money,2)>0?'green':'red'?>"><?=number_format($v->money,2)?></td>
                                      <td><?=number_format($v->left,2)?></td>
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
                <div class="tpl-alert"></div>
            </div>
