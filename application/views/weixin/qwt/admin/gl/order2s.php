

<?php
  function convert1($a){
  switch ($a) {
    case 5:
      echo '优惠券';
      break;
    case 4:
      echo '微信红包';
      break;
    case 6:
      echo '有赞赠品';
      break;
    default:
      # code...
      break;
  }
}
  function cstatus($status){
    if($status=='1'){
      echo '发送成功';
    }else if($status=='0'){
      echo '发送失败';
    }else {
      echo $status;
    }
  }
?>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                中奖记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">微信盖楼</a></li>
                <li class="am-active">中奖记录</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        中奖记录
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                        <tr>
                          <th>中奖楼层</th>
                          <th>昵称</th>
                          <th>奖品类型</th>
                          <th>奖品名字</th>
                          <th>奖品内容</th>
                          <th>发送状态</th>
                          <th>发送时间</th>
                        </tr>
                                    </thead>
                                    <tbody>
                      <?php if($item):?>
                     <?php foreach ($item as $item): ?>
                        <tr>
                          <td ><?=$item->floor?></td>
                          <td><?=$item->nickname?></td>
                          <td><?=convert1($item->type)?></td>
                          <th><?=$item->name?></th>
                          <td><?=$item->type==4?number_format((($item->code)/100),2).'元':$item->code?></td>
                          <td><?=cstatus($item->status)?></td>
                          <td ><?=date('Y/m/d H:i:s ',$item->lastupdate)?></td>
                        </tr>
                     <?php endforeach ?>
                 <?php else:?>
                      <tr>
                          <td>暂时还没有哟</td>
                        </tr>
                 <?php endif;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                    <?=$pages?>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>

        </div>

    </div><!-- 发送管理 -->
