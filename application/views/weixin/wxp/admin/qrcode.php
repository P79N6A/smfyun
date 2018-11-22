<style>
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
  .inputtxt{
    width:5%;
  }
</style>
<section class="content-header">
  <h1>
    红包记录
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">红包记录</li>
  </ol>
</section>

<section class="content">
  <form method="get">
    <!-- 搜索框 -->
    <table class="table table-striped">
      <thead>
        <tr>
          <th>
            <li class="pull-left" style="list-style:none;">
                        <div class="input-group" style="width: 250px;">
                          <input type="text" name="s" class="form-control input-sm pull-left" placeholder="昵称或口令模糊搜索" value="">
                          <div class="input-group-btn">
                            <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                          </div>
                        </div>
                </li>
          </th>
        </tr>
      </thead>
    </table>

      <table class="table table-striped table-hover" style="background-color: #fff;">
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
                  $information=ORM::factory('wxp_weixinsatu')->where('mch_billno','=',$orders->mch_billno)->find();
                  $koulintype=ORM::factory('wxp_kl')->where('code','=',$orders->kouling)->where('bid','=',$orders->bid)->find()->split;
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
  </form>
    <div class="box-footer clearfix">
        <?=$pages?>
    </div>
</section>
