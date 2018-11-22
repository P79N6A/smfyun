<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
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
.box-header .buyflow{
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #dedede;
  width: 100%;
  font-size: 18px;
}
.label {font-size: 14px}
th, td{
  text-align: center;
}
input{
  padding: 10px 6px;
  line-height: 10px;
}
</style>
<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<section class="content-header">
  <h1>
    领取记录
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">领取记录</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">概览：共<?=$result['countall']?>条领取记录</h3>
              <form method="get" name="qrcodesform">
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              </form>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>红包ID</th>
                  <th>领取的用户</th>
                  <th>发送的用户</th>
                  <th>红包金额</th>
                  <th>上传的头像</th>
                  </tr>
                <?php foreach ($result['trade'] as $k => $v):?>
                  <tr>
                  <th><?=$v->order->id?></th>
                  <th><a href="/bnka/qrcodes?qid=<?=$v->qrcode->id?>" title="查看红包发起人"><?=$v->qrcode->nickName?></a></th>
                  <th><?=$v->order->qrcode->nickName?></th>
                  <th><?=$v->money?></th>
                  <td><img src="/bnka/images/trade/<?=$v->id?>.v<?=time()?>.jpg" width='40' height='40'></td>
                  </tr>
                <?php endforeach?>
                </tbody>
              </table>

            </div><!-- /.box-body -->
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
          </div>
          </div>

    </div>
    <!-- /.content -->
</section><!-- /.content -->
