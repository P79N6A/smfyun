<section class="content-header">
  <h1>
    <?=$title?>
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/yyba/orders_add"><?=$title?></a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

<?php if ($result['error']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
<?php endif?>
<?php if ($ok > 0):?>
  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
<?php endif?>
<div class="row">
<div class="col-lg-12">

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$title?></h3>
  </div><!-- /.box-header -->
  <!-- form start -->
  <form role="form" method="post" enctype="multipart/form-data">
    <div class="box-body">
      
      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
          <label for="title">模板消息标题</label>
          <input type="text" maxlength="50" class="form-control" id="title" name="data[title]" value="<?=$order->title?>" placeholder="请输入模板消息标题" value="">
          </div>
        </div>
      </div>

<!--       <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="items">模板消息摘要</label>
            <input type="text" maxlength="50" class="form-control" id="items" name="data[item]" value="<?=$order->item?>" placeholder="请输入预约提醒的项目">
          </div>
        </div>
      </div> -->

      <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="content">模板消息详细内容</label>
            <input type="text" maxlength="100" class="form-control" id="content" name="data[content]" placeholder="请输入模板消息详细内容" value="<?=$order->content?>">
          </div>
        </div>
      </div>
      <div class="form-group">
        <label for="show">点击模板消息跳转到</label>
        <div class="radio" id="myTab">
           <label class="checkbox-inline">
            <input type="radio" name="ordertype" value="1" onclick="change(1)"<?=$order->type==1||!$order->type ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">跳转到指定的活动链接地址</span>
          </label>
          <label class="checkbox-inline">
            <input id='xn' type="radio" name="ordertype" value="2" onclick="change(2)"<?=$order->type==2 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">有赞优惠券/优惠码</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="ordertype" value="3" onclick="change(3)"<?=$order->type==3 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">有赞赠品</span>
          </label>
        </div>
      </div>
      <div class="row" id="tab-content">
        <div class="col-lg-12">
          <div class="tab-content">
             <label id='explain' for="url" style="display:block">
              <?=$type==1||!$type ? '跳转到指定的活动链接地址' : ''?>
              <?=$type==2 ? ' 有赞优惠码/优惠码' : ''?>
              <?=$type==3 ? ' 有赞赠品' : ''?>
            </label>
            <input type="text" maxlength="50" class="form-control url" id="url" name="data[url]" placeholder="请输入活动链接地址(可不填)" <?=$order->type==1||$action1==1?'value='.$order->url:'style="display:none"'?>>
          <select name="yzcode" id='yzcode' <?=($order->type==2)?'':'style="display:none"'?> class="form-control yzcode" >
         <?php if($yzcoupons):?>
          <?php foreach ($yzcoupons as $yzcoupon):?>
          <option <?=$order->url==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
          <?php endforeach; ?>
           <?php endif;?>
          </select>
           <select name="yzgift" id='yzgift' <?=($order->type==3)?'':'style="display:none"'?> class="form-control yzgift" >
         <?php if($yzgifts):?>
          <?php foreach ($yzgifts as $yzgift):?>
          <option <?=$order->url==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
         <?php endforeach; ?>
           <?php endif;?>
          </select>
          </div>
        </div>
      </div>
<!--       <div class="row">
        <div class="col-lg-12">
          <div class="form-group">
            <label for="address">生成的预约链接：</label>
            <input type="text" maxlength="50" class="form-control" disabled="true" value="http://<?=$_SERVER['HTTP_HOST'].'/yyb/storefuop/'.base64_encode($bid.'$'.$oid)?>">
          </div>
        </div>
      </div> -->
      <div class="form-group">
        <label for="show" style="font-size:16px;">发送用户</label>
          <div class="radio">
            <label class="checkbox-inline">
              <input type="radio" name="orderflag" id="rsync1" value="1" <?=$order->flag == 1 ? ' checked=""' : ''?>>
              <span class="label label-success"  style="font-size:14px">已订阅</span>
            </label>
            <label class="checkbox-inline">
              <input type="radio" name="orderflag" id="rsync0" value="0" <?=$order->flag==0? ' checked=""' : ''?>>
              <span class="label label-danger"  style="font-size:14px">全部</span>
            </label>
          </div>
        </div>



        <div class="form-group">
        <label for="show" style="font-size:16px;">发送方式</label>
          <div class="radio">
            <label class="checkbox-inline">
              <input type="radio" name="orderway" id="rsync1" value="1" <?=$order->way == 1 ? ' checked=""' : ''?>>
              <span class="label label-success"  style="font-size:14px">立即发送</span>
            </label>
            <label class="checkbox-inline">
              <input type="radio" name="orderway" id="rsync0" value="0" <?=$order->way==0? ' checked=""' : ''?>>
              <span class="label label-danger"  style="font-size:14px">指定时间发送</span>
            </label>
          </div>
        </div>
      <div class="row">
        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="sendTime">模板消息发送时间(立即发送可不设置)</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[expiretime]" value="<?=date("Y-m-d H:i:s",$order->time?$order->time:time())?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>
      </div>
 

    </div><!-- /.box-body -->
    <div class="box-footer">
     <a href="javascript:" class="btn btn-danger" id="searchStart"><i class="fa fa-edit"></i>预览</a>
    <?php if($order->title):?>
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i>保存</button>
    <?php else:?>
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i>提交</button>
    <?php endif?>
    <?php if($order->state==0&&$order->way==0&$action1==2):?>
    <a href="/yyba/orders?flag=delete&oid=<?=$order->id?>" class="btn btn-danger" id="searchStart"><i class="fa fa-edit"></i>删除</a>
    <?php endif;?>
    </div>
  </form>
</div>

</div>
</div>

</section><!-- /.content -->
<script type="text/javascript">
  $(document).ready(function(){
    $(".formdatetime").datetimepicker({
      format: "yyyy-mm-dd hh:ii",
      language: "zh-CN",
      minView: "0",
      autoclose: true,
      pickerPosition:'top-right'
    });
  })
  $(function(){
    $('#searchStart').click(function(){
        var ordertype= $('input:radio[name=ordertype]:checked').val();
        var way=$('input:radio[name=orderway]:checked').val();
        var flag=$('input:radio[name=orderflag]:checked').val();
        var yzcode=$("#yzcode").find("option:selected").val();
        var yzgift=$("#yzgift").find("option:selected").val();
         $.ajax({
             type: "GET",
             url: "/yyba/orders_add",
             data: {title:$("#title").val(), item:$("#items").val(),typev:ordertype,content:$("#content").val(),url:$("#url").val(),yzcodev:yzcode,yzgiftv:yzgift,flagv:flag,wayv:way},
             dataType: "text",
             success: function(data){
                        alert(data);
                         //       html += '<div class="comment"><h6>' + comment['username']
                         //                 + ':</h6><p class="para"' + comment['content']
                         //                 + '</p></div>';
                         // });
                         // $('#resText').html(html);
                       }
                      
                     });
                });
            });
  function change(i){
  window.a=i;
      if(i=='2'){
        str='有赞优惠券/优惠码';
        $('.tab-content').children(".form-control").hide();
        $('.yzcode').show();
      }
      if(i=='1'){
        str='跳转到指定的活动链接地址';
        $('.tab-content').children(".form-control").hide();
        $('.url').show();
      }

      if(i=='3'){
        str='有赞赠品';
        $('.tab-content').children(".form-control").hide();
        $('.yzgift').show();
      }
      $('#tab-content').show();
      $('#explain').html(str);
    }
</script>

