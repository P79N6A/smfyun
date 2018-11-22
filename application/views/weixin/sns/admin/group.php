<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/snsa/qrcodes">成团详情</a></li>
    <li class="active"><?//=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
            <?php  if($flag==0){?>
              <h3 class="box-title">共<? if($total) echo $total;else echo 0;?> 个团</h3>
            <?php } else{
              $qqrcode=orm::factory("sns_qrcode")->where('id','=',$qid)->find();
              ?>
             <h3 class="box-title"><?php echo $qqrcode->nickname."的团 "?>共<? if($total) echo $total+1;else echo 1;?>成员</h3>

            <?php }?>
         <!--      <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div> -->
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
              <?php if($flag==0){ ?>
                <tr>
                  <th>团id</th>
                  <th>团长头像</th>
                  <th>团长昵称</th>
                  <th>团员数量</th>
                  <th>创建时间</th>
                  <th>是否抽奖</th>
                 <!--  <th>操作</th> -->
                </tr>
                <?php
                foreach ($result as $v)
                {
                  $myqrcode=orm::factory("sns_qrcode")->where('id','=',$v['qid'])->find();
                  $membernum=orm::factory('sns_qrcode')->where('gid','=',$v['id'])->count_all();
                ?>

                <tr>
                  <td><?=$v['id']?></td>
                  <td><img src="<?=$myqrcode->headimgurl?>" width="32" height="32"></td>
                 <td><?=$myqrcode->nickname?></td>
                  <td><a href=<?="/snsa/group/".$v['id']."?qid=".$v['qid']?> ><?=$membernum+1?></a></td>
                  <td><?=date("Y-m-d H:i:s",$v['starttime'])?></td>
                   <td id="lock">
                  <?php
                  if ($myqrcode->oid1!= 0){
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                </tr>

                <?php }  }
                else {?>

                 <tr>
                  <th>成员id</th>
                  <th>成员头像</th>
                  <th>成员昵称</th>
                  <th>创建时间</th>
                  <th>是否抽奖</th>
                 <!--  <th>操作</th> -->
                </tr>

                <tr>
                  <td><?=$qqrcode->id?></td>
                  <td><img src="<?=$qqrcode->headimgurl?>" width="32" height="32"></td>
                  <td><?=$qqrcode->nickname?>(团长)</td>
                  <!-- <td><a href=<?//="/snsa/group/".$v['id']."?qid=".$v['qid']?> ><?=$v['count']?></a></td> -->
                  <td><?=date("Y-m-d H:i:s",$qqrcode->jointime)?></td>
                   <td id="lock">
                  <?php
                  if ($qqrcode->oid1!= 0){
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <!-- <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?//=$v[num_iid]?>" data-price="<?//=$v['price']?>" data-name="<?//=$v['title']?>" data-money1="<?//=$money1?>" >
                      <span></span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?//=$goodid?>'> -->
                </tr>


                <?php
                foreach ($result as $v)
                {
                  
                ?>

                <tr>
                  <td><?=$v['id']?></td>
                  <td><img src="<?=$v['headimgurl']?>" width="32" height="32"></td>
                 <td><?=$v['nickname']?></td>
                  <!-- <td><a href=<?//="/snsa/group/".$v['id']."?qid=".$v['qid']?> ><?=$v['count']?></a></td> -->
                  <td><?=date("Y-m-d H:i:s",$v['starttime'])?></td>
                   <td id="lock">
                  <?php
                  if ($v['oid2']!= 0){
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <!-- <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?//=$v[num_iid]?>" data-price="<?//=$v['price']?>" data-name="<?//=$v['title']?>" data-money1="<?//=$money1?>" >
                      <span></span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?//=$goodid?>'> -->
                </tr>



                <?php  } }?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>

</form>
</section><!-- /.content -->

<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改佣金比例</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改佣金比例</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('num_id');
  var name = button.data('name');
  var price=button.data('price');
  var money1=button.data('money1');
  var information=name+"  (价格:"+price+"元)";


  var form = '';
  form+='<div class="form-group"><label for="fscore">佣金比例（相对价格的百分比）</label><input class="form-control" id="fscore" name="form[money1]" max="999" type="number" style="width:150px" value="'+money1+'"></div>';
 // form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>';
  form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
  form += '<input type="hidden" name="form[title]" value="'+ name +'">';

  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
