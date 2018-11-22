
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
    大客户订单
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">大客户订单</li>
  </ol>
</section>

<section class="content">

	<div class="row">
	    <div class="col-xs-12">
	      <a href="/yyxa/item_add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新订单</span></a>
	    </div>
	</div>

	<div class="row">
		<div class="col-xs-12">
		<form method="post">
        <table class="table table-striped table-hover" style="background-color: #fff;">
                <thead>
                    <tr>
                        <th>订单名称</th>
                        <th>订单金额</th>
                        <th>订单地点</th>
                        <th>订单时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                 foreach ($result['item'] as $pri):
                ?>
                    <tr>
                        <td><?=$pri->title?></td>
                        <td><?=$pri->payment?></td>
                        <td><?=$pri->receiver_state.$pri->receiver_city.$pri->receiver_district.$pri->receiver_address?></td>
                        <td><?=date('Y-m-d H:i:s',$pri->update_time)?></td>
                        <td><a href="/yyxa/item_detele/<?=$pri->id?>"><span>删除</span><i class="fa fa-edit"></i></a></td>
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















