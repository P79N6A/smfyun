<?php
  function convert($key){
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
      case 'yhm':
        echo '卡密';
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

<section class="content-header">
  <h1>
    奖品管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">奖品管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/rwba/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['items'])?> 件产品</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>奖品名称</th>
                  <th>奖品类型</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['items'] as $key=>$item):?>

                <tr>
                  <td><?=$item->km_content?></td>
                  <td><?=convert($item->key)?></td>
                  <td><?=date('Y-m-d H:i:s',$item->lastupdate)?></td>
                  <td><a href="/rwba/items_edit/<?=$item->id?>"><span>修改</span><i class="fa fa-edit"></i></a></td>
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

</section><!-- /.content -->
