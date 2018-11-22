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
<section class="wrapper" style="width:85%;float:right;">
  <h3>
    概况
  </h3>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">概况</li>
  </ol>
  <form method="get">
    <div class="wrapper" style="background:white">
    <!-- 搜索框 -->
    <div class="panel-body" style="padding:1px">
                <div class="adv-table editable-table ">
                <div class="clearfix">
                <table class="table table-striped table-hover table-bordered" id="editable-sample">
                <thead>
                <tr>
                    <th>购买的口令配额</th>
            <th>生成的原始口令数</th>
            <th>裂变出来的口令数</th>
            <th>已生成的口令数</th>
            <th>原始口令已消耗</th>
            <th>裂变口令已消耗</th>
            <th>已消耗的口令数</th>
            <th>剩余的口令配额</th>
                </tr>
                </thead>
                <tbody>
                <tr style="text-align:center">
              <td><?=$result['buynum']?></td>
              <td><?=$result['creatnum']['normal']?></td>
              <td><?=$result['creatnum']['liebian']?></td>
              <td><?=$result['creatnum']['total']?></td>
              <td><?=$result['used']['normal']?></td>
              <td><?=$result['used']['liebian']?></td>
              <td><?=$result['used']['total']?></td>
              <th><?=$result['buynum']-$result['used']['total']?></th>
            </tr>
                </tbody>
                </table>
                </div>
                </div>
              </div>


      <p style="color:red">注意:裂变出来的口令不计入消耗，请根据剩余的口令配额判断是否需要续费。</p>
  </form>
</section>
