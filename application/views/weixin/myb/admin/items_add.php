<section class="content-header">
  <h1>
    奖品管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/myba/items">奖品管理</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">

<?php if ($result['error']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
<?php endif?>

<div class="row">
<div class="col-lg-12">

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$result['title']?></h3>
  </div><!-- /.box-header -->
  <!-- form start -->
  <form role="form" method="post" enctype="multipart/form-data" onsubmit="return check(this.form)">
    <div class="box-body">

      <div class="row">

        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="startdate">兑换截止时间（为空则不限制）</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[endtime]" value="<?=$_POST['data']['endtime']?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>




      </div>
<!-- <div class="form-group">
        <label for="show">奖品分类</label>
        <div class="radio">
          <label class="checkbox-inline" checked="">
            <input type="radio" name="data[rtype]" id="top1" value="0" onclick="ratehide()" <?=$_POST['data']['rtype'] == 0 || !$_POST['data']['rtype'] ? ' checked=""' : ''?>>
            <span class="label label-success" style="font-size:14px" >兑换奖品</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[rtype]" id="top2" value="1" onclick="rateshow()" <?=$_POST['data']['rtype'] == 1 ? ' checked=""' : ''?>>
            <span class="label label-danger" style="font-size:14px" >抽奖奖品
          </label>
        </div>
      </div> -->
<script>
<?php
if (!$_POST['data']['rtype']) {
  $rtype=0;
}else{
  $rtype=1;
};
?>
var rtype=<?=$rtype?>;
function ratehide()
{
  $('#rate').fadeOut(500);
}
function rateshow()
{
  $('#rate').fadeIn(500);
}
$(document).ready(function(){
  if (rtype==1) {
    $('#rate').show();
  };
});
</script>


      <div class="form-group">
        <label for="show">是否在列表中显示？</label>
        <div class="radio">
          <label class="checkbox-inline" checked="">
            <input type="radio" name="data[show]" id="show1" value="1"<?=$_POST['data']['show'] == 1 || !$_POST['data']['show'] ? ' checked=""' : ''?>>
            <span class="label label-success" style="font-size:14px">显示</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[show]" id="show0" value="0"<?=$_POST['data']['show'] === "0" ? ' checked=""' : ''?>>
            <span class="label label-danger" style="font-size:14px">隐藏
          </label>
        </div>
      </div>

      <?php
      //$type:1,2,3

      if ($_POST['data']['url'] && substr($_POST['data']['url'], 0, 4) != 'http') $type = 1;
      if ($_POST['data']['url'] && substr($_POST['data']['url'], 0, 4) == 'http') $type = 2;
      if (!$_POST['data']['url']) $type = 3;
      if ($_POST['data']['type']==3) $type = 4;
      if ($_POST['data']['type']==4) $type = 5;
      if ($_POST['data']['type']==5) $type = 6;
      ?>


      <!-- 修改 -->
      <div class="form-group">
        <label for="show">请选择奖品类型？</label>
        <div class="radio" id="myTab">
        <label class="checkbox-inline">
            <input id='true' type="radio" name="data[type]" value="2" onclick="change(3)"<?=$type==3 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">实物奖品</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="1" onclick="change(1)"<?=$type==1 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">微信卡券</span>
          </label>
          <label class="checkbox-inline">
            <input id='xn' type="radio" name="data[type]" value="0" onclick="change(2)"<?=$type==2 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">虚拟奖品</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="3" onclick="change(4)"<?=$type==4 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">话费流量</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="4" onclick="change(5)"<?=$type==5 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">微信红包</span>
          </label>
<!--           <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="5" onclick="change(6)"<?=$type==6 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">有赞赠品</span>
          </label> -->
        </div>
      </div>
      <!-- 修改完 -->


      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">优先级（数字越大越靠前）</label>
          <input type="number" class="form-control" id="pri" name="data[pri]" placeholder="展示优先级" value="<?=intval($_POST['data']['pri'])?>">
        </div>
        </div>
      </div>

      <div class="form-group">
        <label for="name">产品名称</label>
        <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入产品名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
      </div>
      <div class="row" style="display:none" id="rate">
        <div class="col-lg-6 col-sm-6">
        <div class="form-group">
          <label for="name">中奖产品概率设置（非中奖产品此处不设置）</label>
          <input type="number" class="form-control"  name="data[rate]" placeholder="概率设置" value="<?=$_POST['data']['rate']?>">
        </div>
        </div>
      </div>

      <div class="form-group">

        <?php if ($result['action'] == 'edit' && $result['item']['pic']):?>
          <div class="form-group"><img class="img-thumbnail" src="/myba/images/item/<?=$result['item']['id']?>.v<?=$result['item']['lastupdate']?>.jpg" width="100"></div>
          <label for="pic">产品图片（重新上传会覆盖原照片）</label>
        <?php else:?>
          <label for="pic">产品图片</label>
        <?php endif?>

          <input type="file" id="pic" name="pic" accept="image/jpeg" class="form-control">
          <input type="hidden" name="MAX_FILE_SIZE" value="102400" />
          <p class="help-block">JPEG 格式，规格为正方形，推荐 600*600px，最大不超过 200K</p>
          <label style="color:red;max-width:1000px;width:1000px;padding-left:10px">注意事项：<br>
1、请根据积分奖励的规则，核算单个奖品兑换所需要的积分数量，单个奖品兑换的门槛不能过低，兑换门槛过低会导致奖品被刷，特别是单个奖品兑换所需要的积分要高于用户首次关注奖励的积分；<br>
2、剩余数量即库存，请控制每天的数量，按照情况少量添加；<br>
3、每人限购请设置为1；<br>
4、请商户按照上述要求操作，因为运营操作不当、兑换门槛过低导致的损失，与我方无关；</label>
      </div>

      <div class="row">

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">剩余数量</label>
          <input type="number" class="form-control" id="stock" name="data[stock]" placeholder="输入库存数量" value="<?=intval($_POST['data']['stock'])?>">
        </div>
        </div>

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="price">产品价格</label>
          <input type="number" class="form-control" id="price" name="data[price]" placeholder="市场价" value="<?=floatval($_POST['data']['price'])?>">
        </div>
        </div>

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="score">消耗积分</label>
          <input type="number" class="form-control" id="score" name="data[score]" placeholder="消耗积分数量" value="<?=intval($_POST['data']['score'])?>">
        </div>
        </div>

      </div>

      <div class="row">

        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="limit">每人限购几件？（0 为不限购）</label>
          <input type="number" class="form-control" id="limit" name="data[limit]" placeholder="限购数量" value="<?=intval($_POST['data']['limit'])?>">
        </div>
        </div>

      </div>

      <div id="tab-content" class="form-group" <?=($type==3||$type==4||$type==5) ? ' style="display:none;"' : ''?>>
        <div class="tab-content" >
          <label id='explain' for="url" style="display:block">
          <?=$type==1 ? ' 微信卡券' : ''?>
          <?=$type==2 ? ' 虚拟产品url' : ''?>
          <?=$type==6 ? ' 有赞赠品' : ''?>
          </label>
          <input type="text" <?=($type==2)?'':'style="display:none"'?> class="form-control zengpin" id="url" name="data[url]"  placeholder="http://" value="<?=htmlspecialchars($_POST['data']['url'])?>">
      <select name="wecoupons" <?=($type==1)?'':'style="display:none"'?> class="form-control wecoupons">
          <?php if($wxcards):?>
          <?php foreach ($wxcards as $wxcard):?>
          <option <?=$_POST['data']['url']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
          <?php endforeach; ?>
           <?php endif;?>
          </select>
     <select name="yzgift"  <?=($type==6)?'':'style="display:none"'?> class="form-control yzgift" >
        <?php if($yzgifts):?>
        <?php foreach ($yzgifts as $yzgift):?>
        <option <?=$_POST['data']['url']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
        <?php endforeach; ?>
         <?php endif;?>
        </select>
        </div>
      </div>
      <!--有赞赠品-->
      <!--<div id="yzcoupon-content" class="form-group" <?=($type==1||$type==2||$type==3||$type==4||$type==5) ? ' style="display:none;"' : ''?>>
        <div class="tab-content" >
        <label id='explain' for="url" style="display:block"> 有赞赠品 </label>
        <button type="button" id='fresh' class="btn btn-success">刷新</button>
        <select style="display:none" name="data[url]" id='sel'></select>
        </div>
      </div>-->
      <!--微信红包-->
      <div id="hongbao-content" class="form-group" <?=($type==1||$type==2||$type==3||$type==4||$type==6) ? ' style="display:none;"' : ''?>>
        <div class="tab-content" >
        <p class="alert alert-danger alert-dismissable">添加微信红包奖品时，请务必填好首页微信参数中全部内容！</p>
        </div>
      </div>
        <div class="form-group">
        <label for="desc">详细介绍</label>
        <textarea class="textarea" id="desc" name="data[desc]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=htmlspecialchars($_POST['data']['desc'])?></textarea>
        </div>


    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
      <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该奖品</span></a>
      <?php endif?>
    </div>
  </form>
</div>

</div>
</div>

</section><!-- /.content1 -->

<div class="modal modal-danger" id="deleteModel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">确认要删除吗？该操作不可恢复！</h4>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
        <a href="/myba/items/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
function check(form){
  if(a=='2'&&$('.zengpin').val()==''){
      alert('请填写相应虚拟奖品url');
      return false;
  }else{
    return true;
  }
}
function change(i){
  window.a=i;
      if(i=='1'){
        str='微信卡券';
        $('#tab-content').show();
        $('#hongbao-content').hide();
        //$('#yzcoupon-content').hide();
        $('.tab-content').children(".form-control").hide();
        $('.wecoupons').show();
      }
      if(i=='2'){
        str='虚拟奖品领取链接';
        $('#tab-content').show();
        $('#hongbao-content').hide();
        //$('#yzcoupon-content').hide();
        $('.zengpin').attr('placeholder','http://');
        $('.tab-content').children(".form-control").hide();
        $('.zengpin').show();
      }
      if(i=='5'){
        $('#hongbao-content').show();
        $('#tab-content').hide();
        //$('#yzcoupon-content').hide();

      }
      if(i=='6'){
        str='有赞赠品';
        //$('#yzcoupon-content').show();
        $('#tab-content').show();
        $('#hongbao-content').hide();
        $('.tab-content').children(".form-control").hide();
        $('.yzgift').show();
      }
      if(i=='3'||i=='4'){
        //$('#yzcoupon-content').hide();
        $('#tab-content').hide();
        $('#hongbao-content').hide();
      }
      if(i=='5'){
        $("#money").text("红包金额");
      }else{
        $("#money").text("产品价格");
      }
      $('#explain').html(str);
    }
$(function () {
  var editor = new Simditor({
    textarea: $('.textarea'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });

  $(".formdatetime").datetimepicker({
    format: "yyyy-mm-dd hh:ii",
    language: "zh-CN",
    minView: "0",
    autoclose: true
  });
});
</script>
<script>
// $('#fresh').click(function() {
//     $.ajax({
//       url: '/myba/items',
//       type: 'post',
//       dataType: 'json',
//       data: {'yz':'2'},
//       success: function (res){
//         if(res[0]=='fail'){
//           alert('请填写相应的有赞appid和APPsecret');
//         }else{
//           $('#sel').html('');
//           for(i=0;res['response']['presents'][i];i++){
//           $('#sel').append('<option value='+res['response']['presents'][i]['present_id']+'>'+res['response']['presents'][i]['title']+'</option>');
//           }
//           $('#sel').css('display','block');
//         }
//       },
//     });
// });
  </script>

