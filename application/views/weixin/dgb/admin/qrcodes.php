<style>
.label {font-size: 14px}
.nowrap th{
  white-space: nowrap;
}
.nowrap td{
  white-space: nowrap;
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
    客户列表
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/rwba/qrcodes">客户列表</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<section class="content">
<div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls&amp;s=<?=$result['s']?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出客户列表</span></a>
    </div>
  </div>
<form method="get" name="qrcodesform">
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$result['title']?>：共 <?=$result['countall']?> 个客户</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称，微信号，姓名，手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover nowrap">
                <tbody><tr>
                  <th>会员编号</th>
                  <th>微信昵称</th>
                  <th>微信号</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>常用收货地址</th>
                  <th><a href="/dgba/qrcodes?sort=all_money&amp;s=<?=$result['s']?>" title="按累计消费金额">累计消费金额（点击可排序）</a></th>
                  <th><a href="/dgba/qrcodes?sort=month_money&amp;s=<?=$result['s']?>" title="按当月消费金额">当月消费金额（点击可排序）</a></th>
                  <th><a href="/dgba/qrcodes?sort=most_money&amp;s=<?=$result['s']?>" title="按单笔最高消费">单笔最高消费（点击可排序）</a></th>
                  <th>已发货订单</th>
                  <th>待发货订单</th>
                  <th>已取消订单</th>
                  <th>备注</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['qrcodes'] as $v):
                  $has_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',1)->count_all();
                  $wait_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',0)->count_all();
                  $cancel_send=ORM::factory('dgb_order')->where('qid','=',$v->id)->where('state','=',2)->count_all();
                ?>
                <tr>
                  <td><?=$v->No?></td>
                  <td ><?=$v->nickname?></td>
                  <td ><?=$v->weixin_id?></td>
                  <td ><?=$v->name?></td>
                  <td ><?=$v->tel?></td>
                  <td ><?=$v->city.$v->address?></td>
                  <td><a href="/dgba/orders?qid=<?=$v->id?>" title="查看订单"><?=$v->all_money?></a></td>
                  <td><?=$v->month_money?></td>
                  <td><?=$v->most_money?></td>
                  <td><?=$has_send?></td>
                  <td><?=$wait_send?></td>
                  <td><?=$cancel_send?></td>
                  <td><?=$v->remark?></td>
                  <td nowrap="true">
                    <a href="/dgba/qrcode_edit/<?=$v->id?>" class='button edit'>
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

          </div>
    </div>
  </div>
</form>
</section><!-- /.content -->
<script src="/qwt/assets/js/sweetalert.min.js"></script>
<link rel="stylesheet" href="/qwt/assets/css/sweetalert.css">
<script type="text/javascript">
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
    function(){
      window.location.href = "/dgba/qrcodes?delete="+id;
    })
  })
</script>
