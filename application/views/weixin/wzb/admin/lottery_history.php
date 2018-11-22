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
    中奖记录
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">中奖记录</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_yz_li"><a data-toggle="tab">幸运大转盘</a></li>
            <li id="cfg_wx_li" class="active"><a data-toggle="tab">获奖记录</a></li>
          </ul>

          <script>
    $(document).on('click','#cfg_yz_li',function(){
      window.location.href = '/wzba/lottery';
    });
          </script>

          <div class="tab-content">

            <div class="tab-pane active" id="cfg_wx">


  <div class="row">
    <div class="col-xs-12">
            <div class="box-header">
              <h3 class="box-title">概览：共<?=$result['countall']?>条获奖记录</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>序号</th>
                  <th>中奖用户昵称</th>
                  <th>抽奖所在直播ID</th>
                  <th>中奖时间</th>
                  <th>中奖等级</th>
                  <th>中奖内容</th>
                  </tr>
                <?php foreach ($result['sweep'] as $k => $v):?>
                  <tr>
                  <th><?=$k+1?></th>
                  <th><?=$v->qrcode->nickname?></th>
                  <th><?=$v->lid?></th>
                  <th><?=date('Y-m-d H:i:s',$v->lastupdate)?></th>
                  <th><?=$v->item->item?>等奖</th>
                  <th><?=$v->content?></th>
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
    <!-- /.content -->
            </div>


          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->
