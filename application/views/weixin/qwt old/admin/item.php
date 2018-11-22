<style type="text/css">
    .left{
        float: left;
        padding-left: 10px;
    }
    .right{
        float: right;
        padding-right: 10px;
    }
    .img{
        display: inline-block;
        width: 100%;
        height: 200px;
    }
    .top{
        display: inline-block;
        width: 100%;
        height: 33px;
        color: #77D2B1;
        font-weight: bold;
        padding-left: 10px;
    }
</style>
<div class="page-heading">
            <h3>
                产品中心
            </h3>
        </div>
        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel">
                        <header class="panel-heading">
                           吸粉插件
                        </header>
                        <div class="panel-body">
                            <div id="gallery" class="media-gal">
                        <?php foreach ($result['system'] as $product):?>
                                <div class=" images documents item " >
                                <?php
                                $exist=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->id;
                                if($exist){
                                    $price=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }else{
                                    $price=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }
                                ?>
                                    <a style="display: inline-block;width: 100%;height: 245px;" href="/qwta/product/<?=$product->id?>">
                                <?php
                                $buy = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$product->id)->where('status','=',1)->find();
                                ?>
                                        <!-- <img src="<?=$product->picurl?>" alt="" /> -->
                                        <div class="img" style="background:url('<?=$product->picurl?>');background-size: cover;">

                                        </div>
                                    <p class="left"><?=$product->name?></p>
                                    <p class="right"><?=$price?></p>
                                    <?php if($buy->id):?>
                                        <span class='top'>已订购&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp有效期至：<?=date('Y-m-d',$buy->expiretime)?></span>
                                    <?php endif?>
                                    </a>
                                </div>
                        <?php endforeach?>
                            </div>
                        </div>
                    </section>
                    <!-- <section class="panel">
                        <header class="panel-heading">
                           插件类
                        </header>
                        <div class="panel-body">
                            <div id="gallery" class="media-gal">
                        <?php foreach ($result['plug'] as $product):?>
                                <div class=" images documents item " >
                                <?php
                                $exist=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$iid)->where('show1','=',1)->id;
                                if($exist){
                                    $price=ORM::factory('qwt_sku')->where('bid','=',$fubid)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }else{
                                    $price=ORM::factory('qwt_sku')->where('bid','=',0)->where('iid','=',$product->id)->where('show1','=',1)->find()->price;
                                }
                                ?>
                                    <a href="/qwta/product/<?=$product->id?>">
                                        <img src="<?=$product->picurl?>" alt="" />
                                    <p class="left"><?=$product->name?></p><p class="right"><?=$price?></p>
                                    </a>
                                </div>
                        <?php endforeach?>
                            </div>
                        </div>
                    </section> -->
                </div>
            </div>
        </div>
