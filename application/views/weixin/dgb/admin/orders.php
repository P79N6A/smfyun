<link rel="stylesheet" href="/dgb/styles/chosen.css">
<script src="/dgb/js/chosen.jquery.js"></script>
<style>
.label {font-size: 14px}
.nowrap th{
  white-space: nowrap;
  text-align: center;
}
.nowrap td{
  white-space: nowrap;
  text-align: center;
}
.button{
  border-radius: 4px;
  padding: 4px 8px;
  border-width: 1px;
  border-style: solid;
  margin-right: 4px;
  color: #fff;
}
.recover{
     background-color: #00d9ff;
    border-color: blue;
}
.edit{
      background-color: #00ce00;
    border-color: green;
}
.delete{
      border-color: red;
    background-color: #ff8686;
}
.send{
    background-color: #c7c700;
    border-color: #868600;
}
.cancel{
      background: #fff;
    color: #7b0000;
    border-color: #670000;
}
</style>
<section class="content-header">
  <h1>
    订单列表
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/rwba/qrcodes">订单列表</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<section class="content">


<form method="get" name="qrcodesform" id="searchtype">
<div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls&amp;s=<?=$result['s']?>&amp;type=<?=$type?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出订单列表</span></a>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$result['title']?>：共 <?=$result['countall']?> 个订单</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;float:right;padding-left:20px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按会员编号/微信昵称/姓名搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                  <select id="type" name="type" class="dept_select" value="<?=$type?>" style="width:200px;">
                    <option value="0" <?=$type==0?'selected':''?>>全部</option>
                    <option value="1" <?=$type==1?'selected':''?>>待发货订单</option>
                    <option value="2" <?=$type==2?'selected':''?>>已发货订单</option>
                    <option value="3" <?=$type==3?'selected':''?>>已取消订单</option>
                  </select>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover nowrap" style="text-align:center;">
                <tbody><tr>
                  <th><input type="checkbox" id='all'><label for='all'>全选</label></th>
                  <th>订单编号</th>
                  <th>添加时间</th>
                  <th>客户</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>常用收货地址</th>
                  <th>品牌</th>
                  <th>货号</th>
                  <th>单价/代购费（元/件）</th>
                  <th>件数</th>
                  <th>销售金额/代购费</th>
                  <th>状态</th>
                  <th>备注</th>
                  <th>快递公司</th>
                  <th>快递单号</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['orders'] as $v):
                ?>
                <tr class="line" data-id="<?=$v->id?>">
                  <td><?php if($v->state==0):?><input id="check<?=$v->id?>" class="checkbox" type="checkbox" value="<?=$v->id?>"><?php endif?></td>
                  <td><?=$v->tid?></td>
                  <td ><?=date('Y-m-d H:i:s',$v->createdtime)?></td>
                  <td ><?=$v->qrcode->No?>/<?=$v->qrcode->nickname?></td>
                  <td ><?=$v->qrcode->name?></td>
                  <td ><?=$v->qrcode->tel?></td>
                  <td ><?=$v->qrcode->city.$v->qrcode->address?></td>
                  <td><?=$v->item->name?></td>
                  <td><?=$v->style_id?></td>
                  <td><?=$v->price?>/<?=$v->fee?></td>
                  <td><?=$v->num?></td>
                  <td><?=($v->price-$v->fee)*$v->num?>/<?=$v->fee*$v->num?></td>
                   <td id="state<?=$v->id?>">
                  <?php
                  if ($v->state == 1)
                    echo '<span class="label label-success">已发货</span>';
                  if ($v->state == 2)
                    echo '<span class="label label-warning">已取消</span>';
                  if ($v->state == 0)
                    echo '<span class="label label-danger">待发货</span>';
                  ?>
                  </td>
                  <td><?=$v->remark?></td>
                  <td><?=$v->shiptype?></td>
                  <td><?=$v->shipcode?></td>
                  <td nowrap="true">
                  <?php if ($v->state==0):?>
                    <a class='send button' data-id="<?=$v->id?>">
                      <span>发货</span><i class='fa fa-send'></i>
                    </a>
                    <a class='cancel button' data-id="<?=$v->id?>">
                      <span>取消</span><i class='fa fa-times'></i>
                    </a>
                  <?php elseif ($v->state==2):?>
                    <a class='recover button' data-id="<?=$v->id?>">
                      <span>恢复</span><i class='fa fa-recycle'></i>
                    </a>
                  <?php endif?>
                    <a href="/dgba/order_edit/<?=$v->id?>" class='button edit'>
                      <span>修改</span><i class='fa fa-edit'></i>
                    </a>
                    <a class='delete button' data-id="<?=$v->id?>">
                      <span>删除</span><i class='fa fa-trash'></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
            </div>
            <button class="sendall hidebutton" style="display:none" type="button">发货选中订单</button><button class="cancelall hidebutton" style="display:none" type="button">取消选中订单</button>
          </div>
    </div>
  </div>
</form>
</section><!-- /.content -->
<script src="/qwt/assets/js/sweetalert.min.js"></script>
<link rel="stylesheet" href="/qwt/assets/css/sweetalert.css">
<script type="text/javascript">
$(document).ready(function(){
    $('.dept_select').chosen();
})
// $('.line').click(function(){
//   var id = $(this).data('id');
//   var status = $('#check'+id).is(':checked');
//   if (status == true) {
//     $('#check'+id).prop('checked',false);
//   }else{
//     $('#check'+id).prop('checked',true);
//   }
//   check();
// })
$('.checkbox').change(function(){
  check();
})
$('.delete').click(function(){
  var id = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(isConfirm){
      if (isConfirm) {
        window.location.href="/dgba/orders?delete="+id;
    }
  })
})
$('.cancel').click(function(){
  var id = $(this).data('id');
  swal({
    title: "确认要取消吗？",
    text: "取消后若需要重新处理可以恢复！",
    type: "info",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认取消',
    closeOnConfirm: false
    },
    function(isConfirm){
      if (isConfirm) {
        window.location.href="/dgba/orders?cancel="+id;
    }
  })
})
$('.send').click(function(){
  var id = $(this).data('id');
  swal({
    title: "请填写快递公司和快递单号",
    text: "快递公司<input type='text' name='posttype' id='posttype'>快递单号<input type='text' name='postcode' id='postcode'>",
    type: 'prompt',
    html: true,
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '提交',
    closeOnConfirm: false
    },
    function(inputValue){
    if (inputValue === false) return false;

    if (inputValue === "") {
      swal.showInputError("请填写完整！");
      return false
    }
    var posttype = $('#posttype').val();
    var postcode = $('#postcode').val();
    console.log(posttype);
    console.log(postcode);
    $.ajax({
        url: '/dgba/orders',
        type: 'post',
        dataType: 'json',
        data: {
                type: 'single',
                tid: id,
                posttype: posttype,
                postcode: postcode
              },
    })
    .done(function(res) {
        console.log("success");
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
        window.location.reload();
    });
  })
})
$('.recover').click(function(){
  var id = $(this).data('id');
  swal({
    title: "要恢复吗？",
    text: "",
    type: "info",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '恢复',
    closeOnConfirm: false
    },
    function(isConfirm){
      if (isConfirm) {
        window.location.href="/dgba/orders?recover="+id;
    }
  })
})
$('.cancelall').click(function(){
  var ids = group();
  swal({
    title: "确认要取消吗？",
    text: "取消后若需要重新处理可以恢复！",
    type: "info",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认取消',
    closeOnConfirm: false
    },
    function(isConfirm){
      $.ajax({
          url: '/dgba/orders',
          type: 'post',
          dataType: 'json',
          data: {
                  type: 'cancelall',
                  id: ids
                },
      })
      .done(function(res) {
          console.log("success");
      })
      .fail(function() {
          console.log("error");
      })
      .always(function() {
          console.log("complete");
          window.location.reload();
      });
    })
})
$('.sendall').click(function(){
  var ids = group();
  swal({
    title: "请填写快递公司和快递单号",
    text: "快递公司<input type='text' name='posttype' id='posttype'>快递单号<input type='text' name='postcode' id='postcode'>",
    type: 'prompt',
    html: true,
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '提交',
    closeOnConfirm: false
    },
    function(inputValue){
      if (inputValue === false) return false;

      if (inputValue === "") {
        swal.showInputError("请填写完整！");
        return false
      }
      var posttype = $('#posttype').val();
      var postcode = $('#postcode').val();
      console.log(posttype);
      console.log(postcode);
      $.ajax({
          url: '/dgba/orders',
          type: 'post',
          dataType: 'json',
          data: {
                  type: 'sendall',
                  id: ids,
                  posttype: posttype,
                  postcode: postcode
                },
      })
      .done(function(res) {
          console.log("success");
      })
      .fail(function() {
          console.log("error");
      })
      .always(function() {
          console.log("complete");
        window.location.reload();
      });
    })
})
    $('#all').change(function(){
      var a = $(this).is(':checked');
      console.log(a);
      if (a==true) {
        $('.checkbox').prop('checked',true);
      }else{
        $('.checkbox').prop('checked',false);
      }
      check();
    })
    function check(){
      var status = 0;
      $('.checkbox').each(function(){
      var a = $(this).is(':checked');
        if (a==true) {
          status = 1;
        };
      })
      if (status==1) {
        $('.hidebutton').show();
      }else{
        $('.hidebutton').hide();
      }
    }
    function group(){
      var arr = [];
      $('.checkbox').each(function(){
      var a = $(this).is(':checked');
      var b = $(this).val();
        if (a==true) {
          arr.push(b);
        };
      })
      return arr;
    }
</script>
<script type="text/javascript">
  $('#type').change(function(){
      var a = $(this).val();
      console.log(a);
      $('#searchtype').submit();
  })
  </script>

