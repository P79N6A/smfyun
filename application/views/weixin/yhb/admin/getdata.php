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
    概况
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">概况</li>
  </ol>
</section>

<section class="content">
  <form method="get">
    <!-- 搜索框 -->
      <table class="table table-striped table-hover" style="background-color: #fff;">
       <tbody>
          <tr >
            <th>购买的口令配额</th>
            <th>生成的口令数</th>
            <th>已消耗的口令数</th>
            <th>剩余的口令配额</th>
          </tr>
          <tr >
              <td><?=$result['buynum']?></td>
              <td><?=$result['total']?></td>
              <td><?=$result['normal']?></td>
              <td><?=$result['buynum']-$result['normal']?></td>
            </tr>
        </tbody>
      </table>
  </form>
</section>
