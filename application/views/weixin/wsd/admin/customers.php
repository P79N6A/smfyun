<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    客户管理
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wsda/qrcodes">客户管理</a></li>
    <li class="active"><?//=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部客户信息</span></a>
    </div>
  </div>
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共<?=$result['countall']?>个客户</h3>
         <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称,手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>手机号</th>
                  <th>累计订单数</th>
                  <th>累计订单金额</th>
                  <th>所属代理</th>
                </tr>
                <?php foreach ($result['customers'] as $customer):
                $num=ORM::factory('wsd_trade')->where('bid','=',$customer->bid)->where('deletedd','=',0)->where('openid','=',$customer->openid)->count_all();
                $allmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as allmoney from wsd_trades where bid=$customer->bid and deletedd = 0 and `openid` = '$customer->openid' ")->execute()->as_array();
                    $allmoney=$allmoney[0]['allmoney'];
                  $fname=ORM::factory('wsd_qrcode')->where('bid','=',$customer->bid)->where('openid','=',$customer->fopenid)->where('lv','=',1)->find()->nickname;
                ?>
                <tr>
                  <td><img src="<?=$customer->headimgurl?>" width="32" height="32" title="<?=$customer->openid?>"></td>
                  <td><?=$customer->nickname?></td>
                  <td><?=$customer->receiver_mobile==0?'无':$customer->receiver_mobile?></td>
                  <td><a href="/wsda/history_trades?flag=cnum&qid=<?=$customer->id?>"><?=$num?></td>
                  <td><?=$allmoney?$allmoney:0?></td>
                  <td><?=$fname?></td>
                </tr>
              <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>

</form>
</section><!-- /.content -->

<script>
</script>
