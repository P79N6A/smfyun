<style type="text/css">
  th,td{
    white-space: nowrap;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                红包记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">用户扫码明细</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            共<?=$result['countall']?>条记录
                        </div>

                        <div class="am-u-sm-12 am-u-md-3" style="float:right">
                            <div class="am-input-group am-input-group-sm">
                          <input type="text" name="s" class="am-form-field form-control input-sm pull-left" placeholder="用户昵称搜索" value="">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll;">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main">
                                    <thead>
               <tr>
            <th>红包码串码</th>
            <th>红包码生成时间</th>
            <th>来源门店</th>
            <th>营销规则</th>
            <th>头像</th>
            <th>昵称</th>
            <th>是否关注</th>
            <th>奖励内容</th>
            <th>奖励发送时间</th>
            <th>发送状态</th>
            <th>分享链接UV</th>
            <th>分享链接PV</th>
          </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    function type($orders){
                                      if($orders->ct==0){
                                        return '用户未分享朋友圈';
                                      }
                                      if($orders->ct==1){
                                        switch($orders->status)
                                          {
                                              case 'COUPON SENDING':
                                                  $statu="卡券待领取";
                                                  break;
                                              case 'COUPON GET':
                                                  $statu="卡券已领取";
                                                  break;
                                              case 'SENDING':
                                                  $statu="红包发放中";
                                                  break;
                                              case 'SENT':
                                                  $statu="红包已发放待领取";
                                                  break;
                                              case 'FAILED':
                                                  $statu="红包发放失败";
                                                  break;
                                              case 'RECEIVED':
                                                  $statu="红包已领取";
                                                  break;
                                              case 'REFUND':
                                                  $statu="长时间未领取已退款";
                                                  break;
                                              default:
                                                  $statu=$orders->error;
                                          }
                                        return $statu;
                                      }
                                      if($orders->ct==2){
                                        return $orders->error;
                                      }
                                      if($orders->ct==3){
                                        return '关注后自动下发';
                                      }
                                    }
                                    ?>
                <?php foreach ($result['orders'] as $key => $orders):?>
                <tr>
              <td><?=$orders->kouling?></td>
              <?php
              $createtime = ORM::factory('qwt_hbykl')->where('bid','=',$orders->bid)->where('code','=',$orders->kouling)->find()->createtime;
              ?>
              <td ><?=date('Y/m/d H:i:s ',$createtime)?></td>
              <td><a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/qrcodes?from_lid=<?=$orders->from_lid?>"><?=$orders->logins->name?></a></td>
              <td><?=$orders->rule_name?></td>
              <td><img src="<?=$orders->qrcode->qrcodes->headimgurl?>" width="32" height="32" title="<?=$orders->openid?>"></td>
              <td><?=$orders->qrcode->qrcodes->nickname?></td>
              <td>
                  <?=$orders->qrcode->qrcodes->subscribe==1?"<span class='label label-success'>已关注</span>":"<span class='label label-danger'>已跑路</span>"?>
              </td>
              <!-- <th><?=$orders->money>0?number_format($orders->money/100, 2, '.', '').'元':''?></th> -->
              <!-- <th><?=$orders->couponname?></th> -->
              <?php
              if($orders->money>0||$orders->couponname){
                if($orders->money>0){
                  $money = '微信红包：'.number_format($orders->money/100, 2, '.', '').'元';
                }
                if($orders->couponname){
                  $money = '微信卡券：'.$orders->couponname;
                }
              }else{

              }
              ?>
              <th>
                <?=$money?>
              </th>
              <td ><?=$orders->sendtime?date('Y/m/d H:i:s ',$orders->sendtime):'无'?></td>
              <td ><?=type($orders)?></td>
              <td ><?=$orders->uv?></td>
              <td ><?=$orders->pv?></td>
            </tr>
                <?php endforeach;?>
                                    </tbody>
                                </table>
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
