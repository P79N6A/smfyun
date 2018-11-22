<!-- 卡密管理 -->
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
        echo '卡密';
        break;
      case 'yzcoupon':
        echo '有赞优惠券';
        break;
      case 'freedom':
        echo '自定义文本消息';
        break;
      default:
        break;
    }
  }
?>
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
    添加发送的奖品
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">添加发送的奖品</li>
  </ol>
</section>

<section class="content">

	<div class="row">
	    <div class="col-xs-12">
	      <a href="/kmia/kmi" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
	    </div>
	</div>

	<div class="row">
		<div class="col-xs-12">
		<form method="post">
        <table class="table table-striped table-hover" style="background-color: #fff;">
                <thead>
                    <tr>
                        <th>奖品名称</th>
                        <th>奖品类型</th>
                        <th>已发送数量</th>
                        <th>剩余数量</th>
                        <th>上架时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                 foreach ($result['prizes'] as $pri):
                ?>
                    <tr>
                        <td><?=$pri->km_content?></td>
                        <td><?=convert($pri->key)?></td>
                        <?if($pri->key=='kmi'):?>
                        <td><?=ORM::factory('kmi_km')->where('bid','=',$pri->bid)->where('startdate','=',$pri->value)->where('live','!=',1)->count_all()?></td>
                        <?else:?>
                        <td><?=$pri->km_num1-$pri->km_num?></td>
                        <?endif?>
                        <?if($pri->key=='kmi'):?>
                        <td><?=ORM::factory('kmi_km')->where('bid','=',$pri->bid)->where('startdate','=',$pri->value)->where('live','=',1)->count_all()?></td>
                        <?else:?>
                        <td><?=$pri->km_num?></td>
                        <?endif?>
                        <td><?=date('Y-m-d H:i:s',$pri->startdate)?></td>
                        <td><a href="/kmia/prizes_edit/<?=$pri->id?>"><span>修改</span><i class="fa fa-edit"></i></a></td>
                    </tr>
                <?php endforeach;?>
                </tbody>
        </table>
    </form>
		</div>
	</div>
  <div class="box-footer clearfix">
            <?=$pages?>
  </div>
</section>















