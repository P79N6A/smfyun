<script src="//cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<section class="wrapper" style="width:85%;float:right;">
<section class="content-header">
  <h1>
    任务管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/rwba/items">任务管理</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<style type="text/css">
  .goala,.goalb{
    width: 400px;
    /*display: inline-block;*/
  }
  .goalc,.goald{
    /*display: inline-block;*/
  }
  .goalc{
    width: 400px;
  }
  .goald{
    width: 400px;
  }
  .goaltitle{
    margin-top: 8px;
  }
  label{
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: 700;
  }
  .form-group {
      margin-bottom: 15px;
  }
</style>

<!-- Main content -->
<section class="content" style="background:white; padding:20px">

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
  <form role="form" method="post" enctype="multipart/form-data" onsubmit="return toVaild()">
    <div class="box-body">

      <div class="row">
        <div class="col-lg-5 col-sm-6">

            <div class="form-group">
            <label for="name">任务名称</label>
            <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入任务名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
          </div>

            <div class="form-group">
              <label for="startdate">任务开始时间</label>
                <input class="form_datetime form-control" type="text" name="data[begintime]" value="<?=$_POST['data']['begintime']?date("Y-m-d H:i:s",$_POST['data']['begintime']):''?>" readonly="">
            </div>

            <div class="form-group">
              <label for="startdate">任务结束时间</label>
                <input type="text" class="form_datetime form-control" name="data[endtime]" value="<?=$_POST['data']['endtime']?date("Y-m-d H:i:s",$_POST['data']['endtime']):''?>" readonly="">
            </div>

            <span class="add btn btn-warning">添加目标级数</span>&nbsp&nbsp&nbsp<span class="cut btn btn-danger">减少目标级数</span>
            <?php if(!$skus):?>
              <div class="form-group">
                <h4 for="name">1级目标</h4>
              <label class="goaltitle">累计人气值:</label><input type="number" class="form-control goala" name="goal[0]" placeholder="累计人气值" value="">
              <label class="goaltitle">选择奖品:</label><select name="prize[0]" class="input-group goalb">
                <?php foreach ($items as $item): ?>
                  <option value="<?=$item->id?>"><?=$item->km_content?></option>
                <?php endforeach ?>
              </select>
              <label class="goaltitle">奖品库存:</label><input type="number" class="form-control goalc" name="stock[0]" placeholder="库存" value="">
              <label class="goaltitle">对应文案:</label><input type="text" class="form-control goald" name="text[0]" placeholder="发送奖励文案" value="">
              </div>
            <?php else:?>
            <?php foreach ($skus as $k => $v):?>
            <div class="form-group">
              <h4 for="name"><?=$k+1?>级目标</h4>
            <label class="goaltitle">累计人气值:</label><input type="number" class="form-control goala" name="goal[<?=$k?>]" placeholder="累计人气值" value="<?=$v->num?>">
            <label class="goaltitle">选择奖品:</label><select name="prize[<?=$k?>]" class="input-group goalb">
              <?php foreach ($items as $item): ?>
                <option value="<?=$item->id?>" <?=$v->iid==$item->id?'selected':''?>><?=$item->km_content?></option>
              <?php endforeach ?>
            </select>
            <label class="goaltitle">奖品库存:</label><input type="number" class="form-control goalc" name="stock[<?=$k?>]" placeholder="库存" value="<?=$v->stock?>">
            <label class="goaltitle">对应文案:</label><input type="text" class="form-control goald" name="text[<?=$k?>]" placeholder="发送奖励文案" value="<?=$v->text?>">
            </div>
            <?php endforeach?>
            <?php endif?>
        </div>
      </div>
        <script type="text/javascript">
        function toVaild(){
          var i=0;
          $(".goala").each(function (index,element) {
            console.log(index);
            console.log($(".goala").eq(index-1).val());
              if ($(this).val() == "") {
                  i++;
              }else{
                if(index>0){
                  if($(".goala").eq(index).val()<=$(".goala").eq(index-1).val()){
                    i++;
                  }
                }
              }
          })
          if(i>0){
            alert('请填写完整并保证每一级数量都大于上一级目标');
            return false;
          }else{
            return true;
          }
          return false;
        }
          $(function () {
            $(".formdatetime").datetimepicker({
              format: "yyyy-mm-dd hh:ii",
              language: "zh-CN",
              minView: "0",
              autoclose: true
            });
          });
          $('.add').click(function() {
            var goalnum = $(".goala").length;//
            if(goalnum==1){
              if($(".goala:last").val()!=''){

                }else{
                  alert('请先填写目标当前目标奖励');
                  return;
                }
              }else{
                if($(".goala:last").val()-$(".goala").eq(goalnum-2).val()>0){

                }else{
                  alert('目标要求人数需要大于上一级');
                  return;
                }
              }
              $(".col-sm-6").append("<div class=\"form-group\">"+
                  "<h4 for=\"name\">"+(goalnum+1)+"级目标</h4>"+
                  "<label class=\"goaltitle\">累计人气值:</label>"+
                "<input type=\"number\" class=\"form-control goala\" name=\"goal["+goalnum+"]\" placeholder=\"累计人气值\" value=\"\">"+
                "<label class=\"goaltitle\">选择奖品:</label>"+
                "<select name=\"prize["+goalnum+"]\" class=\"input-group goalb\">"+
                  <?php foreach ($items as $item): ?>
                    "<option value=\"<?=$item->id?>\">"+
                    "<?=$item->km_content?>"+
                    "</option>"+
                  <?php endforeach ?>
                "</select>"+
                "<label class=\"goaltitle\">奖品库存:</label>"+
                "<input type=\"number\" class=\"form-control goalc\" name=\"stock["+goalnum+"]\" placeholder=\"库存\" value=\"\">"+
                "<label class=\"goaltitle\">对应文案:</label>"+
                "<input type=\"text\" class=\"form-control goald\" name=\"text["+goalnum+"]\" placeholder=\"发送奖励文案\" value=\"\">"+
                "</div>");
          });
          $('.cut').click(function() {
            var goalnum = $(".goala").length;
            if(goalnum==1){
              alert('不能再减少，至少制定一个奖励级数');
            }else{
              $(".goala:last").parent().remove();
            }
          });
        </script>
    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
      <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>终止该任务</span></a>
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
        <h4 class="modal-title">确认要终止吗？该操作不可恢复！</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
        <a href="/rwba/tasks/edit/<?=$result['task']['id']?>?DELETE=1" class="btn btn-outline">确认终止</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</section>

