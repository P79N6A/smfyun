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
    贺卡记录
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">贺卡记录</li>
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
                          <input type="text" name="s" class="form-control input-sm pull-left" placeholder="模糊搜索" value="">
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
            <th>贺卡类型</th>
            <th>时间</th>
          </tr>
        </thead>
        <tbody>
                <?php
                foreach ($result['orders'] as $orders):
                ?>
               <?php
                  switch($orders->type)
                  {
                      case 1:
                          $statu="文字贺卡";
                          break;
                      case 2:
                          $statu="音频贺卡";
                          break;
                      default:
                          $statu='不详';
                  }
                  ?>
            <tr>
              <td><img src="<?=$orders->headimgurl?>" width="32" height="32" title="<?=$orders->openid?>"></td>
              <td><?=$orders->nickname?></td>
              <td><?=$orders->code?></td>
              <td ><?=$statu?></td>
              <td ><?=date('Y/m/d H:i:s ',$orders->lastupdate)?></td>
            </tr>
                <?php endforeach;?>
        </tbody>
      </table>
  </form>
    <div class="box-footer clearfix">
        <?=$pages?>
    </div>
</section>
