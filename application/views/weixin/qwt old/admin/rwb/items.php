<?php
  function converta($key){
    switch ($key) {
      case 'hongbao':
        echo '微信红包';
        break;
      case 'coupon':
        echo '微信卡券';
        break;
      case 'gift':
        echo '赠品';
        break;
      case 'kmi':
        echo '特权商品';
        break;
      case 'yzcoupon':
        echo '有赞优惠券';
        break;
      default:
        break;
    }
  }
?>
<style>
.label {font-size: 14px}
</style>




<section class="wrapper" style="width:85%;float:right;">
 <div wclass="wrapper">
      <div class="row">
          <div class="page-heading">
            <h3>
                奖品管理
                <small><?=$desc?></small>
              </h3>
         </div>
<ul class="breadcrumb" style="margin-left:15px">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">奖品管理</li>
</ul>


                <div class="col-sm-12">
                <section class="panel">
                <header class="panel-heading">
                    共 <?=count($result['items'])?> 件产品
                    <span class="tools pull-right">
                        <a href="/qwtrwba/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px;height:40px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
                     </span>
                </header>
                 <div class="input-group" style="width: 150px;margin-left:15px">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="panel-body">
                <div class="adv-table editable-table ">
                <div class="clearfix">
                <table class="table table-striped table-hover table-bordered">

                <thead>
                <tr>
                  <th>奖品名称</th>
                  <th>奖品类型</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($result['items'] as $key=>$item):?>
                <tbody>
                <tr>
                  <td><?=$item->km_content?></td>
                  <td><?=converta($item->key)?></td>
                  <td><?=date('Y-m-d H:i:s',$item->lastupdate)?></td>
                  <td><a href="/qwtrwba/items_edit/<?=$item->id?>"><span>修改</span><i class="fa fa-edit"></i></a></td>
                </tr>
                <?php endforeach;?>
              </tbody></table>
                </div>
            <div class="box-footer clearfix">
                <?=$pages?>
            </div>
                </div>
                </section>
                </div>
                </div>
        </div>
        <!--body wrapper end-->

</section><!-- /.content -->
