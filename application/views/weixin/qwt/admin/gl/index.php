<?php foreach($scripts as $file) echo HTML::script($file)."\n"?>
<!-- <script src="/Resource/js/rebuy.js"></script> -->
<section class="content-header">
    <h1>基础设置<small>盖楼参数设置</small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
        <li class="active">基础设置</li>
    </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
             <!--    <ul class="nav nav-tabs">
                    <li class='jump active' id="cfg_yz_li" data-url='index'><a href="#cfg_yz" data-toggle="tab" aria-expanded="true">绑定有赞</a></li>
                    <li class='jump' id="cfg_wx_li" data-url='wx'><a href="#cfg_wx" data-toggle="tab" aria-expanded="true">微信参数</a></li>
                    <li class='jump' id="cfg_text_li" data-url='text'><a href="#cfg_text" data-toggle="tab" aria-expanded="true">盖楼设置</a></li>
                    <li class='jump' id="cfg_item_li" data-url='item'><a href="#cfg_item" data-toggle="tab" aria-expanded="true">奖品设置</a></li>
                    <li class='jump' id="cfg_floor_li" data-url='floor'><a href="#cfg_floor" data-toggle="tab" aria-expanded="true">中奖楼层设置</a></li>
                    <li class='jump' id="cfg_orderli" data-url='order'><a href="#cfg_order" data-toggle="tab" aria-expanded="true">中奖记录</a></li>
                    <li class='jump' id="cfg_delete_li" data-url='delete'><a href="#cfg_delete" data-toggle="tab" aria-expanded="true">清空楼层</a></li>
                </ul> -->
                <div class="tab-content">
                    <div class="tab-pane active" id="cfg_yz">
                        <?php if ($success =='yz' ):?>
                          <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>配置保存成功!</div>
                        <?php endif?>
                        <div class="callout callout-danger">
                            <p>首次使用，请 <a href="https://wap.koudaitong.com/v2/showcase/feature?alias=14zors0de" target="_blank">点击这里</a> 查看插件配置使用说明书</p>
                        </div>
                        <div class="callout callout-success">
                            <h4>有赞插件配置信息</h4>
                            <?php if($config==null):?>
                                <p><b>您还没有进行配置请先按照要求进行配置！</b></p>
                            <?php else:?>
                                <p><b>到期时间：</b> <u><?=$overtime?></u></p>
                                <?$remainderdays=round((strtotime($overtime)-strtotime(date("Y-m-d"))+86400)/3600/24);
                                 if($remainderdays<0):?>
                                  <p>
                                   <input type="hidden" name="rebuy_id" value="<?=$config['buy_id']?>">
                                   <span class="xufei btn btn-danger btnn">已过期请续费</span>
                                  </p>
                                 <!-- <p><button id="butovertime" style="color:#000000;" onclick="renew()">已到期,点击续费</button></b></p> -->
                                 <?endif;?>
                                <?if($remainderdays<=10&&$remainderdays>0):?>
                                <!-- <p id="butovertime"><b><a href="javascript:void(0)" onclick="renew()">还剩<?=$remainderdays?>天到期,点击续费</a></b></p> -->
                                <p>
                                   <input type="hidden" name="rebuy_id" value="<?=$config['buy_id']?>">
                                   <span class="xufei btn btn-danger btnn">还剩<?=$remainderdays?>天到期,点击续费</span>
                                </p>
                                <?endif;?>
                            <?php endif;?>
                            <p><b>插件 token：</b> <u>smfyun1234</u></p>
                            <p><b>插件 url：</b> <u><?=$url?></u></p>
                            <p><a href="http://koudaitong.com/apps/third" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                        </div>
                        <form role="form" method="post" >
                        <?php if($oauth==1):?>
                          <div class="lab4">
                            <a href='/user/config/gl/oauth/<?=$config['buy_id']?>'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                          </div>
                          <br>
                        <?else:?>
                          <div class="lab4">
                            <a href='/user/config/gl/oauth/<?=$config['buy_id']?>'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                          </div>
                        <?endif?>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<style type="text/css">
.product_name,.product_price,.product_time,.product_count,.product_sku{
        font-size: 16px;
        height:30px;
        line-height: 30px;
        width:100%;
        text-align: left;
    }
    .product_price span{
        font-size: 20px;
        color:#f00000;
        font-weight: bold;
    }
    .product_sku{
        height:70px;
        margin-top: 10px;
    }
    .product_sku span{
        border: 2px solid #c7c4c4;
        text-align: center;
        display: block;
        position:relative;
        float:left;
        margin:0 4px 4px 0;
        padding:3px;
        /*更改1*/
        padding-left: 12px;
        padding-right: 12px;
        border-radius: 5px;
    }
    .product_sku span:hover{
        border:2px solid #f00000;
        cursor: pointer;
    }
    .product_active{
        border:2px solid #f00000 !important;
    }
    .product_name{
        font-size: 18px;
    }
    .product_name,.product_count{
        border-bottom: 1px dotted #000;
    }
    .product_sku{
        margin-bottom: 20px;
    }
    .product_intro{
        margin-top: 100px;
        background-color: #f6f6f6;
        width:90%;
        height:auto;
        margin:0 auto;
        /* border:1px solid #fff; */
    }
    .product_confirm span{
        margin-left: 10px;
        font-size: 20px;
        color:#f00000;
        font-weight: normal;

    }
    .allc{
      position:absolute;
            top:45%;
            left:50%;
            margin:-200px 0 0 -200px;
            width:350px;
            height:333px;
            background-color:#fff;
            color:#000;
            border-radius: 5px;
    }
    .img1{
      width:200px;
      height:200px;
      float:left;
    }
    .all{
      position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: -webkit-box;
            -webkit-box-pack: center;
            -webkit-box-align: center;
            z-index: 10000;
            background-color: rgba(0, 0, 0, .7);
            -webkit-animation: fadeIn .6s ease;
            color:rgba(255,255,255,.7);
    }
    .product_content{
        float:left;
        width:300px;
        height:100%;
        padding-top: 30px;
        margin-left:25px;
    }
    .queren{
      margin-top:10px;
    }
    .number1{
      cursor: pointer;
      width: 320px;
      font-size: 15px;
    }
    /*微信购买取消按钮*/
    .remove{
      margin-left: 134px;
      color: #fff;
      font-weight: 600;
      font-size: 18px;
      cursor:pointer;
    }
    /*关闭按钮*/
    .close {
      float: right;
      font-size: 22px;
      font-weight: 700;
      line-height: 1;
      color: #000;
      text-shadow: 0 1px 0 #fff;
      filter: alpha(opacity=20);
      opacity: .5;
      margin-top:-2px;
      margin-right:10px;
      outline: none;
      position:absolute;
      top:7px;
      right:5px;
    }

      #keyword{
        margin-top:5px;
      }

 .btnn{
      background-color:#00a65a;
      color:#ffffff;
      border-color: transparent;
      height:27px;
      margin-left:16px;
      border-radius:;
      padding-top:2px;
  }

</style>
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var gl = '<?=URL::site("user/config/gl")?>';
    //alert(gl+'/'+url+'/<?=$config['buy_id']?>');
    location.href = gl+'/'+url+'/<?=$config['buy_id']?>';
  })
</script>

