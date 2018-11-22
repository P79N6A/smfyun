<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
</style>

<?php

 if ($result['s']) $title = '搜索结果';

?>
<section class="content-header">
  <h1>
    商品统计
    <small><?//=$desc?></small>
  </h1>


  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qfxa/stats_goods">商品统计</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-lg-12">
           <form method="get" name="ordersform">
           <div class="box-header">
           <?php if ($result['s']):?>
              <h3 class="box-title">搜索出<?=count($goods)?>种商品</h3>
            <?else:?>
            <h3 class="box-title"><?=count($goods)?>种有销售记录的商品</h3>
            <?endif;?>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="商品名称模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->
            </form>

      <div class="nav-tabs-custom">
          <div class="tab-pane active" id="orders<?=$result['status']?>">
            <div class="table-responsive">
            <form method="post" method="post">
             <table class="table table-hover">
                <tbody>
                  <tr>
                    <th>商品名</th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=toprice" title="按交额">有赞成交金额</a></th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=totle" title="按订单数">有赞订单数</th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=tonum" title="按销售数量">销售数量</th>

                 </tr>
                  <?php foreach($goods as $good):?>
                  <tr>
                    <td><?=$good['title']?></td>
                    <td><?=$good['toprice']?></td>
                    <td><?=$good['totle']?></td>
                    <td><?=$good['tonum']?></td>

                  </tr>
                  <?php endforeach;?>

               </tbody>
             </table>
            </form>
            </div><!-- table-resonsivpe -->

            <div class="box-footer clearfix">
              <?=$pages?>
            </div>

          </div><!-- tab-pane -->

      </div><!-- nav-tabs-custom -->
    </div>
  </div>

</section><!-- /.content -->

 <script>
// $(function () {
//   $(".formdatetime1").datetimepicker({
//     format: "yyyy-mm-dd",
//     language: "zh-CN",
//     autoclose: true,
//     minView:'month',
//     todayBtn:true,
//     startDate: "<?=$duringtime['begin']?>",
//     endDate: "<?=$duringtime['over']?>",

//   });

//   $(".formdatetime2").datetimepicker({
//     format: "yyyy-mm-dd",
//     language: "zh-CN",
//     autoclose: true,
//     minView:'month',
//     todayBtn:true,
//     startDate: "<?=$duringtime['begin']?>",
//     endDate: "<?=$duringtime['over']?>",
//   });


// });
 </script>
