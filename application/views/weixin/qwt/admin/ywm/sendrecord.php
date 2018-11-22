
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送记录
            </div>
             <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">一物一码</a></li>
                <li id="name1" class="am-active">发送记录</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            发送记录
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
            <th>是否关注</th>
            <th>头像</th>
            <th>昵称</th>
            <th>扫码事件</th>
            <th>红包下发金额</th>
            <th>发送时间</th>
            <th>发送状态</th>
          </tr>
              </thead>
              <tbody>
              <?php
                function type($orders){
                  if($orders->ct==1){
                    return '发送成功';
                  }
                  if($orders->ct==2){
                    return $orders->error;
                  }
                  if($orders->ct==3){
                    return '关注后自动下发';
                  }
                }
                ?>
                  <?php
                foreach ($result['orders'] as $orders):
                ?>
                <tr>
                <td>
                  <?=$orders->qrcode->qrcodes->subscribe==1?"<span class='label label-success'>已关注</span>":"<span class='label label-danger'>已跑路</span>"?>
                </td>
              <td><img src="<?=$orders->qrcode->qrcodes->headimgurl?>" width="32" height="32" title="<?=$orders->openid?>"></td>
              <td><?=$orders->qrcode->qrcodes->nickname?></td>
              <td><?=$orders->kouling?></td>
              <th><?=number_format($orders->money/100, 2, '.', '')?>元</th>
              <td ><?=date('Y/m/d H:i:s ',$orders->lastupdate)?></td>
              <td ><?=type($orders)?></td>
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
