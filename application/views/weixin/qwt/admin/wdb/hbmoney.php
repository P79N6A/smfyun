
<style>
.label {font-size: 14px}
</style>

<section class="wrapper" style="width:85%;float:right;">

  <h1>
    红包统计
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">红包统计</li>
  </ol>


<!-- Main content -->



  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>已发放红包数</th>
                  <th>已领取红包数</th>
                  <th>已发放红包金额</th>
                  <th>已领取红包金额</th>
                </tr>

                <tr>
                  <td><?=$hbmoney3s?></td>
                  <td><?=$hbmoney4s?></td>
                  <td><?=$hbmoney1s?></td>
                  <td><?=$hbmoney2s?></td>
                </tr>

              </tbody></table>
            </div><!-- /.box-body -->

          </div>
    </div>
  </div>

</section><!-- /.content -->
