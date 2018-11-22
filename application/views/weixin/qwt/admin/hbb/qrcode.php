
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                红包记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">口令红包</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">用户领取明细</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            基础设置
                        </div>

                        <div class="am-u-sm-12 am-u-md-3" style="float:right">
                            <div class="am-input-group am-input-group-sm">
                          <input type="text" name="s" class="am-form-field form-control input-sm pull-left" placeholder="模糊搜索" value="">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main">
                                    <thead>
               <tr>
            <th>头像</th>
            <th>昵称</th>
            <th>领取口令</th>
            <th>口令类型</th>
            <th>红包金额</th>
            <th>发送时间</th>
            <th>领取状态</th>
          </tr>
                                    </thead>
                                    <tbody>
                  <?php
                foreach ($result['orders'] as $orders):
                ?>
               <?php
                  $information=ORM::factory('qwt_hbbweixinsatu')->where('mch_billno','=',$orders->mch_billno)->find();
                  $koulintype=ORM::factory('qwt_hbbkl')->where('code','=',$orders->kouling)->where('bid','=',$orders->bid)->find()->split;
                  switch($information->status)
                  {
                      case 'SENDING':
                          $statu="发放中";
                          break;
                      case 'SENT':
                          $statu="已发放待领取";
                          break;
                      case 'FAILED':
                          $statu="发放失败";
                          break;
                      case 'RECEIVED':
                          $statu="已领取";
                          break;
                      case 'REFUND':
                          $statu="长时间未领取已退款";
                          break;
                      default:
                          $statu=$orders->error;
                  }
                  ?>
                <tr>
              <td><img src="<?=$orders->headimgurl?>" width="32" height="32" title="<?=$orders->openid?>"></td>
              <td><?=$orders->nickname?></td>
              <td><?=$orders->kouling?></td>
              <td><?=$koulintype>0?'裂变':'普通'?></td>
              <th><?=number_format($orders->money/100, 2, '.', '')?>元</th>

              <td ><?=date('Y/m/d H:i:s ',$orders->lastupdate)?></td>
              <td ><?=$statu?></td>
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
