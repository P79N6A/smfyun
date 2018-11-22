
<style>
.label {font-size: 14px}
</style>

<?php
 $title = '概览';
?>

<section class="content-header">
  <h1>
    数据
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/snsa/qrcodes">数据统计</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
             <!--  <h3 class="box-title"><?//=$title?>：共 <?//=$result['countall']?> 种奖品</h3> -->
              <!-- <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按奖品名搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div> -->
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <td>总共参与人次</td>
                 <th>总共参与人数</th>
                 <th>分享次数(朋友圈／朋友)</th>
                  <th>成团人数</th>
                  <th>开团数（团员已满+团员未满）</th>
                  <th>uv</th>
                </tr>

                <tr>
                  <td><?=$result['injoin']?></td>
                  <td><?=$result['ininjoin']?></td>
                  <td><?=$result['shareline']?>/<?=$result['shareapp']?>(共<?=($result['shareline']+$result['shareapp'])?>次)</td>
                  <td><?=$result['groupnum']?></td>
                  <td><?=$result['kaituan']?></td>
                  <td><?=$result['uv']?></td>
                </tr>
              </tbody></table>
            </div><!-- /.box-body -->

            </div>

          </div>

    </div>
</section><!-- /.content -->
