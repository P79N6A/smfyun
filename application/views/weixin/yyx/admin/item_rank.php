
<style>
.label {font-size: 14px}
</style>
<section class="content-header">
  <h1>
    热销商品
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/yyxa/qrcodes">热销商品排行</a></li>
    <li class="active">概览</li>
  </ol>
</section>



<section class="content">
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">概览：共 <?=$result['countall']?> 件商品</h3>
              <div class="box-tools">
                <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
            <form method="post">
              <table class="table table-hover">
                <tbody><tr>
                  <th>商品图片</th>
                  <th>商品名称</th>
                 <!--  <th>店铺名称</th> -->
                  <th><button class="btn btn-sm btn-warning" type="submit">点此修改下方产品优先级</button></th>
                  <th>商品价格</th>
                  <th>商品销量</th>
                </tr>

                <?php
                foreach ($result['items'] as $v):
                  $sname=ORM::factory('yyx_shop')->where('id','=',$v->sid)->find()->name;
                ?>

                <tr>
                  </td>
                  <td><img src="<?=$v->pic_url?>" width="32" height="32" title="<?=$v->num_iid?>"></td>
                  <td><?=$v->title?></td>
                  <!-- <td><?=$sname?></td> -->
                  <td><input type="number" name='rank[<?=$v->id?>]' value="<?=$v->lv?>" /></td>
                  <td><?=$v->price?></td>
                  <td><?=$v->sold_num?></td>
                </tr>
                <?php endforeach;?>
              </tbody>
              </table>
              </form>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
  </div>
</section><!-- /.content -->

