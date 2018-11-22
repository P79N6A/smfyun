<!-- 详情页开始-->
        <div class="page-heading">
            <h3>
                产品中心
            </h3>

<!-- ▲ Main container -->

        <!-- page heading end-->
        <!--body wrapper start-->
<link href="/qwt/css/bootstrap.min.css" rel="stylesheet">
<link href="/qwt/css/index_1.css" rel="stylesheet">
<link href="/qwt/css/product_1.css" rel="stylesheet">
<div class="wrapper">
     <div class="row">
        <div class="col-sm-12">
            <section class="panel">
            <header class="panel-heading" style="padding-bottom:25px">
            <img style='width:222px;' src="<?=$result['product']->picurl?>">
             <div class="app-board-intro" style="width:78%;float:right;padding-left:20px">
                   <h2 class="title" style="margin-left:0px;"><?=$result['product']->name?></h2>
                   <p class="intro"><?=$result['product']->abstract?></p>
            <div class="original-price">
                订购价:<span class="normal-price"><?=$shprice?></span>
                <!-- 价格区间 -->
            </div>
        <div class="activity-date clearfix js-activity-date" style="margin-bottom:10px">
            <span class="pull-left">有效期：</span>
            <?php foreach ($result['sku'] as $key => $v):?>
                <a class="pull-left js-select-click <?=$key==0?'selected':'';?>" data-sku="<?=$v->id?>" data-price="<?=$v->price?>">
                    <span><?=$v->name?></span>
                </a>
            <?php endforeach?>
           <!-- <a href="#myModal" data-toggle='modal'><img src="http://<?=$_SERVER["HTTP_HOST"]?>/qwta/buy/1" /></a> -->
        </div>
        <a href="#myModal" data-toggle='modal' class="buy btn btn-success" type="button">立即订购</a>

</div>
            </header>
<style type="text/css">
    .buy{
        margin-left: 50px;
    }
    .product_intro{
        margin-top: 12px;
    }
</style>
<section class="product_intro">
    <div class="tab-content">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#intro" aria-controls="intro" role="tab" data-toggle="tab">商品介绍</a></li>
            <li role="presentation"><a href="#comment" aria-controls="comment" role="tab" data-toggle="tab">案例用户</a></li>
        </ul>
        <div role="tabpanel" class="tab-pane active" id="intro">
                <?=$result['product']->interduce?>
        </div>
        <div role="tabpanel" class="tab-pane" id="comment">
            <?=$result['product']->example?>
        </div>
    </div>
</section>

    </section>
        </div>
    </div>
</div>
        <!--详情页结束 -->
       <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:400px;height:100%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">购买方式</h4>
            </div>

        <div class="modal-body row">
           <div style="margin:auto;width:236px;height:236px">
            <img class='buyimg' src="">
            <div class="col-md-12" style="text-align:center">
            <span class='res'></span>
            </div>
            <style>
            .buyimg
            {
                width: 256px;
                height: 256px;
            }
            </style>
            <div class="col-md-12">
                <div  style="margin-top:20px;float:right;">
                    <button onclick="" class="btn btn-success btn-sm notify" type="button">已付款</button>
                    <button class="btn btn-danger btn-sm cancle" type="button" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
                            <!-- modal -->
<script src="//cdn.bootcss.com/jquery/2.1.0/jquery.js"></script>
<script type="text/javascript">
    $('.buy').click(function() {
        window.sku_id = $('.selected').data('sku');
        var timestamp = Date.parse(new Date());
        timestamp = timestamp / 1000;
        window.orderid = 'E'+sku_id+timestamp;
        $('.buyimg').attr("src","http://<?=$_SERVER['HTTP_HOST']?>/qwta/buy/"+sku_id+"/"+orderid);
        $.ajax({
          url: '/qwta/createorder/'+orderid+'/'+sku_id,
          type: 'post',
          dataType: 'text',
          success: function (res){
            console.log(res);
          }
        });
    });
    $('.notify').click(function() {
        $.ajax({
          url: '/qwta/notify/'+orderid+'/'+sku_id,
          type: 'post',
          dataType: 'text',
          success: function (res){
            $('.res').html(res);
            if(res=='支付成功,请前往【插件中心】查看'){
                window.location.href='http://<?=$_SERVER["HTTP_HOST"]?>/qwta/order/';
            }else{
                setTimeout(function(){
                    $('.res').html('');
                },2000);
            }
          }
        });
    });
    $('.js-select-click').click(function() {
        $('.js-select-click').removeClass('selected');
        $(this).addClass('selected');
        $('.normal-price').html($('.selected').data('price'));
    });
</script>
