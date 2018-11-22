<!-- 卡密记录 -->
<?php
    function convert($a){
        switch ($a) {
            case 'gift':
            echo '赠品';
                break;
            case 'coupon':
            echo '卡券';
                break;
            case 'kmi':
            echo '卡密';
                break;
            case 'hongbao':
            echo '红包';
                break;
            case 'yzcoupon':
            echo '有赞优惠券';
                break;
            case 'freedom':
            echo '自定义文本消息';
                break;
            case 0:
            echo '未处理';
                break;
            case 1:
            echo '已处理';
                break;
            case 2:
            echo '已处理';
                break;
            case 3:
            echo '已处理';
                break;
            case 4:
            echo '已处理';
                break;
            case 5:
            echo '已处理';
                break;
            default:
            echo '';
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
		发送记录
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
		<li class="active">发送记录</li>
	</ol>
</section>

<section class="content">
	<form method="get">
		<!-- 搜索框 -->
		<table class="table table-striped">
			<thead>
				<tr>
					<th>
						<li class="pull-left" style="list-style:none;">
				                <div class="input-group" style="width: 250px;">
				                  <input type="text" name="s" class="form-control input-sm pull-left" placeholder="模糊搜(收货人，商品名称，奖品类型)" value="">
				                  <div class="input-group-btn">
				                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
				                  </div>
				                </div>
				        </li>
					</th>
					<th>
						<!-- <a href="<?=$_SERVER['PATH_INFO']?>?bid=<?=$result['bid']?>&amp;export=csv" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部未处理订单</span>
						</a> -->
						<a href="<?=$_SERVER['PATH_INFO']?>?bid=<?=$result['bid']?>&amp;export=csv" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部发送记录</span></a>
					</th>
				</tr>
			</thead>
		</table>

			<table class="table table-striped table-hover" style="background-color: #fff;">
				<thead>
					<tr>
						<th>头像</th>
						<th>昵称</th>
						<th>订单</th>
						<th>商品名称</th>
						<th>收货人</th>
						<th>时间</th>
						<th>奖品类型</th>
						<th>奖品内容</th>
						<th>状态</th>
                        <th>原因</th>
					</tr>
				</thead>
				<tbody>
                <?php
                foreach ($result['orders'] as $orders):
                ?>
					<tr>
						<td><img src="<?=$orders->heardimageurl?>" style="width:25px;height"></td>
						<td><?=$orders->nikename?></td>
						<td><?=$orders->tid?></td>
						<td><?=$orders->tradename?></td>
						<td><?=$orders->name?></td>
						<td><?=$orders->time?></td>
						<td><?=convert($orders->km_type)?></td>
						<td><?=$orders->km_comtent?></td>
						<td><?=convert($orders->state)?></td>
                        <td><?=$orders->log?></td>
					</tr>
                <?php endforeach;?>
				</tbody>
			</table>
	</form>
    <div class="box-footer clearfix">
        <?=$pages?>
    </div>
</section>
