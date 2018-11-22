
<style>
.label {font-size: 14px}
label{
  margin-bottom: 0;
}
.searchbox{
  border-bottom: 1px solid #ddd;
  overflow: scroll;
}
.sourceselector{
  margin-left: 10px;
  margin-top:5px;
  margin-bottom: 5px;
}
.typeselector{
  margin-left: 10px;
  margin-top:5px;
  margin-bottom: 5px;
}
.box-tools{
  display: inline-block;
  position: relative;
  top: 5px;
  margin-left: 10px;
}
.add88{
  display: inline-block;
  bottom: 1px;
}
.selected1 .radio-box1{

    color: #608908;
    background: #ffffff;
    border: 2px solid #a5c85b;
    padding: 7px 9px;
    outline: 0;
}
.radio-box1{
display: block;
    color: #555555;
    background: #f5f5f5;
    border: 1px solid #cfcfcf;
    padding: 8px 10px;
    text-decoration: none;
}
.selected2 .radio-box2{

    color: #608908;
    background: #ffffff;
    border: 2px solid #a5c85b;
    padding: 7px 9px;
    outline: 0;
}
.radio-box2{
display: block;
    color: #555555;
    background: #f5f5f5;
    border: 1px solid #cfcfcf;
    padding: 8px 10px;
    text-decoration: none;
}
#ssbtn{
  display: inline-block;
    width: 40px;
    height: 33px;
    border-radius: 10px;
    margin-left: 3px;
    background-color: #fff;
    border: 1px solid #A2CD5A;
}
#add89{
  display: inline-block;
    width: 50px;
    height: 33px;
    line-height: 19px;
    border-radius: 10px;
    margin-left: 10px;
    border-left: 1px solid #d2d6de;
}
.btn{
  margin: 5px 0 5px 10px;
}

</style>
<section class="content-header">
  <h1>
    销售管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wzba/qrcodes">销售管理</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <form method="post" name="ordersform">
            <div class="searchbox">
            <!-- <nobr> -->
              <div class="sourceselector">
              <label><input id="sourceall" name="zaid" type="radio" value="" <?=$_POST['zaid']==''?'checked':''?> />全部</label>
              <label><input id="sourcedirect" name="zaid" type="radio" value="<?=$biz->id?>" <?=$_POST['zaid']==$biz->id?'checked':''?>/>直销</label>
              <label><input id="sourceagent" name="zaid" type="radio" value="select" <?=$_POST['zaid']=='select'?'checked':''?>/>代理销售</label>
                <div class="agentpicker" style="display:none">
                <select name='aid' class="agentcselector">
                  <?php foreach ($chadmins as $chadmin):
                  ?>
                  <option value="<?=$chadmin->id?>"><?=$chadmin->name?></option>
                <?php endforeach ?>
                </select>
                </div>
                <input id="sourcetype" name="" value="alltype" type="hidden">
              </div>
              <div class="typeselector">
              <label><input id="typeall" name="type" type="radio" value=""  <?=$_POST['type']==''?'checked':''?>/>全部</label>
              <label><input id="typeyear" name="type" type="radio" value="year" <?=$_POST['type']=='year'?'checked':''?>/>包年</label>
   <!--            <label><input id="typemonth" name="type" type="radio" value="month" <?=$_POST['type']=='month'?'checked':''?>/>包月</label> -->
              <label><input id="typeflow" name="type" type="radio" value="stream" <?=$_POST['type']=='stream'?'checked':''?>/>流量包</label>
              </div>
                  <div class="input-group" style="width: 250px;display: flex;vertical-align:middle;margin-left:10px">
                  <input type="text"  class="form-control pull-right formdatetime1" style="float:left;background-color: #fff;border-radius: 7px;" name="begin" value="<?=$_POST['begin']?>" readonly="">
                  <input type="text"  class="form-control pull-right formdatetime2" style="margin-left:10px;background-color: #fff;border-radius: 7px;" name="over" value="<?=$_POST['over']?>" readonly="">
                  <div id="add89" class="input-group-addon"><i class="fa fa-calendar"></i></div>
              </div>
           <!-- <div class="input-group-btn add88">
                      <button id="ssbtn" class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
               </div>
              </nobr>-->
              <button class="btn btn-success" type="submit">提交</button>
            </div>
             </form>
            <div class="box-header">
              <h3 class="box-title">共 <?=$count?> 个订单</h3>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>商户名称</th>
                  <th>商户账号</th>
                  <th>订单名称</th>
                  <th>订单金额/总计<?=$sum?>元</th>
                  <th>订单类型</th>
                  <th>时间</th>
                  <th>销售来源</th>
                </tr>
                <?php
                foreach ($orders as $o):
                 $biz_name = ORM::factory('wzb_cfg')->where('bid', '=', $o->bid)->where('key', '=', 'name')->find()->value;
                  $suser=ORM::factory('wzb_login')->where('id','=',$o->bid)->find()->user;
                  $faid=ORM::factory('wzb_login')->where('id','=',$o->bid)->find()->faid;
                  $fadmin=ORM::factory('wzb_login')->where('id','=',$o->bid)->find()->fadmin;
                  $ssname=ORM::factory('wzb_admin')->where('id','=',$faid)->find()->name;
                  $aname=ORM::factory('wzb_admin')->where('id','=',$fadmin)->find()->name;
                  $typename='';
                  $beginname='';
                  if($faid==$fadmin){
                    $typename='直销';
                  }else{
                    $typename='销售';
                    $beginname=$aname.'所属';
                  }

                ?>
                <tr>
                  <td><?=$biz_name ?></td>
                  <td><?=$suser?></td>
                  <td><?=$o->title?></td>
                  <td><?=$o->price?>元</td>
                  <td>
                  <?php
                  if ($o->type=='month')
                    echo '<span class="label label-danger">包月</span>';
                  elseif($o->type=='year')
                    echo '<span class="label label-success">包年</span>';
                  elseif($o->type=='stream')
                    echo '<span class="label label-success">流量包</span>';
                  ?>
                  </td>
                  <td><?=date('Y-m-d H:i:s',$o->time)?></td>
                  <td><?=$beginname.$ssname.$typename?></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</section><!-- /.content -->

<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script>

$(function () {
  $(".formdatetime1").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",

  });

  $(".formdatetime2").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",
  });


});

$(document).on('click','.sourceselector label',function(){
    if ($('.sourceselector label input[name="zaid"]:checked ').val()=="select") {
      $('.agentpicker').css('display','inline-flex');
    } else{
      $('.agentpicker').css('display','none');
    };
});
$(document).on('click','.radio-box2',function(){
    $(this).parent().parent().children().removeClass('selected2');
    $(this).parent().addClass('selected2');
});
</script>


