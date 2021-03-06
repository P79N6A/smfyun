<section class="wrapper" style="width:85%;float:right;background:#eff0f4">
<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
  <h3>
    奖品管理
    <small><?=$desc?></small>
  </h3>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qwtwdba/items">奖品管理</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>



<!-- Main content -->


<?php if ($result['error']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
<?php endif?>
<div class="wrapper" style="background:white">
<div class="row">
<div class="col-lg-12">

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$result['title']?></h3>
  </div><!-- /.box-header -->
  <!-- form start -->
  <form role="form" method="post" enctype="multipart/form-data">
    <div class="box-body">

      <div class="row">

        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
           <label for="startdate">兑换截止时间（为空则不限制）</label>
             <input name="data[endtime]" size="16" value="<?=$_POST['data']['endtime']?>" readonly="" class="form_datetime form-control" type="text">
        </div>
        </div>

      </div>

      <div class="form-group">
        <label for="show">是否在列表中显示？</label>
        <div style="margin-left:-20px">
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
      // if ($_POST['data']['url'] && substr($_POST['data']['url'], 0, 4) == 'http') $type = 2;
      // if ($type==2) $type = 1;
      // if ($type==0||!$type) $type = 2;
      ?>
      <!-- 修改 -->
      <div class="form-group">
        <label for="show">请选择奖品类型？</label>
        <div style="margin-left:-20px" id="myTab">
        <label class="checkbox-inline">
            <input id='true' type="radio" name="data[type]" value="2" onclick="change(3)"<?=$type==2 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">实物奖品</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="0" onclick="change(2)"<?=$type==0 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">虚拟奖品</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="3" onclick="change(4)"<?=$type==3 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">话费流量</span>
          </label>
          <?php if($config['hbmoney']==1):?>
            <label class="checkbox-inline">
            <input type="radio" name="data[type]" value="4" onclick="change(5)"<?=$type==4 ? ' checked="checked"' : ''?>>
            <span class="label label-success" style="font-size:14px">微信红包</span>
          </label>
          <?php endif?>
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

      <div class="form-group">

        <?php if ($result['action'] == 'edit' && $result['item']['pic']):?>
          <div class="form-group"><img class="img-thumbnail" src="/qwtwdba/images/item/<?=$result['item']['id']?>.v<?=$result['item']['lastupdate']?>.jpg" width="100"></div>
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

      <div id="tab-content" class="form-group" <?=($type==3||$type==4||$type==2) ? ' style="display:none;"' : ''?>>
        <div class="tab-content" >
          <label id='explain' for="url" style="display:block">
          <?=$type==2 ? ' 虚拟产品url' : ''?>
          </label>
         <input type="text" class="form-control zengpin" id="url" name="data[url]"  placeholder="http://" value="<?=htmlspecialchars($_POST['data']['url'])?>">
        </div>
      </div>
      <!-- <div id="hongbao-content" class="form-group" <?=($type==1||$type==2||$type==3||$type==4||$type==6) ? ' style="display:none;"' : ''?>>
        <div class="tab-content" >
        <p class="alert alert-danger alert-dismissable">添加微信红包奖品时，请务必填好首页微信参数中全部内容！</p>
        </div>
      </div>
 -->        <div class="form-group">
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
        <a href="/qwtwdba/items/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
function change(i){
      if(i=='5'){
        $('#hongbao-content').show();
        $('#tab-content').hide();
        //$('#yzcoupon-content').hide();

      }

      if(i=='2'){
        str='虚拟奖品领取链接';
        $('#tab-content').show();
        $('#hongbao-content').hide();
        $('.zengpin').attr('placeholder','http://');

      }
      if(i=='3'||i=='4'){
        $('#tab-content').hide();
        $('#hongbao-content').hide();
      }
      $('#explain').html(str);
    }
$(function () {
  var editor = new Simditor({
      textarea: $('.textarea')
    });
  // var editor = new Simditor({
  //   textarea: $('.textarea'),
  //   toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  // });

  // $(".formdatetime").datetimepicker({
  //   format: "yyyy-mm-dd hh:ii",
  //   language: "zh-CN",
  //   minView: "0",
  //   autoclose: true
  // });
});
</script>


