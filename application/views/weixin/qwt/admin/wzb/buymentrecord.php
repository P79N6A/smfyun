
<style type="text/css">
  label{
    text-align: left !important;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                购买记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">神码云直播</a></li>
    <li><a>会员中心</a></li>
    <li class="am-active">购买记录</li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 次购买
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>订单编号</th>
                  <th>类型</th>
                  <th>名称</th>
                  <th>购买时间</th>
                  <th>付款金额<th>
                  </tr>
                </thead>
                                    <tbody>
                  <?php
                  function type($type){
                    switch ($type) {
                      case 'month':
                        echo '续费包月';
                        break;
                      case 'year':
                        echo '续费包年';
                        break;
                      case 'stream':
                        echo '流量';
                        break;
                    }
                  }
                  ?>
                  <?php foreach ($result['orders'] as $k => $v):?>
                    <tr>
                      <td><?=$v->tid?></td>
                      <td><?=type($v->type)?></td>
                      <td><?=$v->title?></td>
                      <td><?=date('Y-m-d H:i:s',$v->time)?></td>
                      <td><?=$v->price?></td>
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
