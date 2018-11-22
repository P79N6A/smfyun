<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    红包规则管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">红包规则管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wxpa/downcsv" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>下载全部发送规则的口令</span></a>
      <a href="/wxpa/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新的红包发送规则</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['items'])?> 条规则</h3>
              <div class="box-tools">
                已生成口令数量：<?=$all?$all:0?>个；已购买口令数量：<?=$buynum?>个；
                <?php if($all>=$buynum):?>
                  已到达购买口令数量上限！
                <?php endif?>
                <!-- <div class="input-group" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div> -->
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>id</th>
                  <th>名字</th>
                  <th>金额</th>
                  <th>领取概率</th>
                  <th>已使用/总数量</th>
                  <th>设置时间</th>
                  <th>操作</th>
                  <th>下载口令</th>
                </tr>

                <?php foreach ($result['items'] as $item):?>
                <?php
                $count = ORM::factory('wxp_kl')->where('iid','=',$item->id)->where('used','>',0)->count_all();
                ?>
                <tr>
                  <td><?=$item->id?></td>
                  <td><?=$item->name?></td>
                  <td><?=$item->money/100?>元</td>
                  <td><?=$item->rate?>%</td>
                  <td><?=$count?>/<?=$item->num?>个</td>
                  <td><?=date('Y-m-d H:i', $item->lastupdate)?></td>
                  <td><a href="/wxpa/items_edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                  <td>
                  <?php if($item->hasdown==1):?>
                      <span>已下载</span> <i class="fa fa-edit"></i>
                  <?php else:?>
                      <a href="/wxpa/downcsv/<?=$item->id?>"><span>下载</span> <i class="fa fa-edit"></i></a>
                  <?php endif?>
                  </td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

            <!-- <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div> -->

          </div>

    </div>
  </div>

</section><!-- /.content -->