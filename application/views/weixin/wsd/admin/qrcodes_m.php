<link rel="stylesheet" type="text/css" href="http://jfb.dev.smfyun.com/wzb/css/jquery.dropdown.css">

<script src="http://jfb.dev.smfyun.com/wzb/js/jquery.dropdown.js"></script>
<style>
.label {font-size: 14px}
.search-bar{
  padding-left: 10px;font-size: 14px;color: #666666;padding-bottom: 10px;
}
.mask{
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
  color: rgba(255,255,255,.7);
}
.take{
  position: fixed;
  background-color: #fff;
  width: 500px;
  height: 150px;
  top: 30%;
  left: 50%;
  margin-left: -250px;
  color: #000;
  border-radius: 5px;
}
.take-hd,.take-ft{
  width: 100%;
  text-align: center;
  margin-top: 16px;
}
.take-hd{
  display: inline-block;
  text-align: left;
  margin-left: 40px;
}
.close{
  margin-top: 5px;
  margin-right: 10px;
  position: absolute;
  top: 0;
  right: 0;
}
.confirm-btn{
  background-color: #00a65a;
  width: 100px;
  color: #fff;
  border-radius: 3px;
  border: 1px solid #008d4c;
  padding: 5px
}
.take-tt{
    margin-top: 10px;
    margin-left: 10px;
    font-size: 16px;
    font-weight: bold;
}
.nowrap th{
  white-space: nowrap;
}
.nowrap td{
  white-space: nowrap;
}

</style>

<?php

$title = '代理列表';
?>

<section class="content-header">
  <h1>
    代理列表
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wsda/qrcodes">代理商设置</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部代理信息</span></a>
    </div>
  </div>


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个代理</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称,手机号搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover nowrap">
                <tbody>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>所在分组</th>
                  <th>微信昵称</th>
                  <th>状态</th>
                  <th>所辖团队成员</th>
                  <th>客户数</th>
                  <th>当日个人销量</th>
                  <th>当月个人销量</th>
                  <th>累计销量</th>
                  <th>当日团队销量</th>
                  <th>当月团队销量</th>
                  <th>累计团队销量</th>
                  <th>当月团队奖励</th>
                  <th>当月个人团队奖励</th>
                  <th>当日销售利润</th>
                  <th>当月销售利润</th>
                  <th>累计销售利润</th>
                  <th>上级代理</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['qrcodes'] as $v):
                    $group1=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
                  // if($group1->bottom){
                  //   $bottom='('.$group1->bottom.')';
                  //   //echo $bottom.'<br>';
                  //   $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from wsd_groups where bid=$v->bid and id in $bottom ")->execute()->as_array();
                  //   $group_num=$group_ay[0]['group_num'];
                  // }else{
                  //   $group_num=0;
                  // }
                  $group_num = ORM::factory('wsd_qrcode')->where('lv','=',1)->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->count_all();
                  //echo $group_num.'<br>';所辖团队成员
                  $qr_num=ORM::factory('wsd_qrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->where('fopenid','!=','')->count_all();
                   $groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                   $month=date('Y-m',time());
                       //echo $month.'<br>';
                    $daytype='%Y-%m-%d';
                    $monthtype='%Y-%m';
                    $day=date('Y-m-d',time());
                    $month_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as month_pnum from wsd_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_pnum=$month_pnum[0]['month_pnum'];
                    //echo $month_pnum.'<br>';当月个人销量
                    $day_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as day_pnum from wsd_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                    $day_pnum=$day_pnum[0]['day_pnum'];
                     //echo $day_pnum.'<br>';当天个人销量
                    $all_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as all_pnum from wsd_trades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' ")->execute()->as_array();
                    $all_pnum=$all_pnum[0]['all_pnum'];
                    $day_tnum=0;
                    $month_tnum=0;
                    $all_tnum=0;
                    $month_tmoney=0;
                    $month_pmoney=0;
                    foreach ($groups as $group) {
                      if($group->bottom){
                          $bottom1='('.$group->id.','.$group->bottom.')';
                      }else{
                          $bottom1='('.$group->id.')';
                      }
                      // echo $bottom1.'<br>';
                      $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                      $day_tnum+=$day_tnum1[0]['day_tnum'];
                      //echo  $day_tnum.'<br>';当天团队销量
                      // echo $bottom1."<br>";
                      // echo $monthtype."<br>";
                      // echo $month."<br>";
                      // exit;
                      $month_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tnum from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                      $month_tnum+=$month_tnum1[0]['month_tnum'];
                      //echo  $month_tnum.'<br>';当月团队销量
                      $all_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as all_tnum from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 ")->execute()->as_array();
                      $all_tnum+=$all_tnum1[0]['all_tnum'];
                      //累计团队销量
                      $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                      $month_tmoney1=$month_tmoney1[0]['month_tmoney'];
                      //echo  $month_tmoney.'<br>';
                      $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                        if(!$sku->id){
                            $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                            if(!$fsku->id){
                               $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scale=0;
                            }
                        }else{
                            $scale=$sku->scale;
                        }
                        $month_tmoney+=$month_tmoney1*$scale/100;
                        // echo $scale.'<br>';
                        // echo $month_tmoney1.'<br>';
                        // echo $month_tmoney.'<br>';
                        // echo $group->id."<br>";
                      $child_groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                      $child_moneys=0;
                      // echo 'child:<br>';
                      foreach ($child_groups as $child_group) {
                            if($child_group->bottom){
                                 $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                              }else{
                                    $bottom2='('.$child_group->id.')';
                              }

                            //echo $bottom2."<br>";
                            $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from wsd_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                            $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                            //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                            $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>',$month_ltmoney)->find();
                            if(!$sku->id){
                                $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                                if(!$fsku->id){
                                   $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                                }else{
                                    $scale=0;
                                }
                            }else{
                                $scale=$sku->scale;
                            }
                            // echo 'month_ltmoney::::'.$month_ltmoney.'<br>';
                            // echo 'scale::::'.$scale.'<br>';
                            $child_money= $month_ltmoney*$scale/100;
                            $child_moneys+=$child_money;
                      }
                      //echo  $child_moneys.'<br>';
                      // echo $month_tmoney.'-'.$child_moneys.'<br>';
                      $month_pmoney+=$month_tmoney-$child_moneys;
                    }

                    //echo  $month_pmoney.'<br>';当月个人团队奖励
                    $day_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as day_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$day' ")->execute()->as_array();
                    $day_pxmoney=$day_pxmoney[0]['day_pxmoney'];
                    //当天销售利润
                    $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                    //当月销售利润
                    //echo  $month_pxmoney.'<br>';
                    $all_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as all_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                    $all_pxmoney=$all_pxmoney[0]['all_pxmoney'];
                    //累计销售利润
                    $fname = '';
                    if($v->tid){
                      $fname = '自主购买';
                    }
                    if($v->code){
                      $fname = '来自邀请码：'.$v->code;
                    }
                    $nickname=ORM::factory('wsd_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                    if($nickname){
                      $fname = $nickname;
                    }
                    // echo $all_pxmoney.'<br>';
                    // exit();
                ?>
                <tr>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->suites->name?$v->suites->name:'无'?></td>
                  <td><a href="/wsd/memberpage/<?=$v->openid?>"><?=$v->nickname?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  $fuser = ORM::factory('wsd_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->find();
                  if ($v->lv == 1)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lv == 2)
                    echo '<span class="label label-warning">待审核</span>';
                  if ($v->lv == 3)
                    echo '<span class="label label-danger">已取消</span>';
                  ?>
                  </td>
                  <td><a href="/wsda/qrcodes_m?qid=<?=$v->id?>"><?=$group_num?></td>
                  <td><a href="/wsda/customers?qid=<?=$v->id?>"><?=$qr_num?></td>
                  <td><a href="/wsda/history_trades?flag=dayp&qid=<?=$v->id?>"><?=$day_pnum?$day_pnum:0?></td>
                  <td><a href="/wsda/history_trades?flag=monthp&qid=<?=$v->id?>"><?=$month_pnum?$month_pnum:0?></td>
                  <td><a href="/wsda/history_trades?flag=allp&qid=<?=$v->id?>"><?=$all_pnum?$all_pnum:0?></td>
                  <td><a href="/wsda/history_trades?flag=dayt&qid=<?=$v->id?>"><?=$day_tnum?></td>
                  <td><a href="/wsda/history_trades?flag=montht&qid=<?=$v->id?>"><?=$month_tnum?></td>
                  <td><a href="/wsda/history_trades?flag=allt&qid=<?=$v->id?>"><?=$all_tnum?></td>
                  <td><?=$month_tmoney?></td>
                  <td><?=$month_pmoney?></td>
                  <td><?=$day_pxmoney?$day_pxmoney:0?></td>
                  <td><?=$month_pxmoney?$month_pxmoney:0?></td>
                  <td><?=$all_pxmoney?$all_pxmoney:0?></td>
                  <td><?=$fname?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-bz='<?=$v->bz?>' data-suite='<?=$v->group_id?>' data-fid='<?=$fuser->id?>' data-fname='<?=$fuser->nickname?>'>
                      <span>修改用户</span> <i class="fa fa-edit"></i>
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
</section><!-- /.content -->
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改用户</h4>
      </div>
      <div class="modal-body">
        <div class="form-body"></div>
        <!-- <div>
          <label>修改当前用户分组：</label>
          <select name='form[suite]' class='select_choose' style="width:150px;">
              <option value="0">不加入分组</option>
              <?php foreach ($suite as $k => $v):?>
                <option value="<?=$v->id?>"><?=$v->name?></option>
              <?php endforeach?>
          </select>
        </div> -->
        <div class="dropdown-sin-1" style="width:250px">
          <label>修改当前用户的上级代理为：</label>
          <select name='form[fuser]' class='select_choose' style="width:150px;">
              <option value="nochange">不修改</option>
              <option value="zerofopenid">设为无上级(只能修改真正没有上级的用户)</option>
              <?php foreach ($alls as $k => $v):?>
                <option value="<?=$v->id?>"><?=$v->nickname?></option>
              <?php endforeach?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
opt = '<option value=\"0\">不加入分组</option>';
<?php foreach ($suite as $k => $v):?>
  opt = opt + '<option value=\"<?=$v->id?>\"><?=$v->name?></option>'
<?php endforeach?>
$('.dropdown-sin-1').dropdown({
      readOnly: true,
      input: '<input type="text" maxLength="20" placeholder="请输入搜索">'
});
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var uname = button.data('uname')
  var tel = button.data('tel')
  var bz = button.data('bz')
  var lv = button.data('lv')
  var fid = button.data('fid');
  var fname = button.data('fname');
  var suite = button.data('suite');
  form = '';
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock0" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="3"'+ (lv==3 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">取消代理商资格</label></div><label for="flock">修改当前用户注册手机号：</label><input type="text" name="form[tel]" class="form-control" value="'+tel+'" style="width:50%;"><label for="flock">修改当前用户分组：</label><div class="radio"><select id=\'suite\' style="width:250px;" name="form[suite]">'+opt+'</select></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  // $('.select_choose').val(fid);
  modal.find('.modal-title').text(name)
  modal.find('.form-body').html(form)
  $('#suite').val(suite);
  // $('.dropdown-selected').text(fname);
  console.log($(".select_choose").val());
})

$(document).on('click','#take_user',function(){
    $(".mask_user").fadeIn(500);
});
$(document).on('click','#take_group',function(){
    $(".mask_group").fadeIn(500);
});
$(document).on('click','.close',function(){
    $(".mask_user").fadeOut(500);
    $(".mask_group").fadeOut(500);
});
// $(function () {
//   $(".formdatetime1").datetimepicker({
//     format: "yyyy-mm-dd",
//     language: "zh-CN",
//     autoclose: true,
//     minView:'month',
//     todayBtn:true,
//     startDate: "<?=$duringtime['begin']?>",
//     endDate: "<?=$duringtime['over']?>",

//   });

//   $(".formdatetime2").datetimepicker({
//     format: "yyyy-mm-dd",
//     language: "zh-CN",
//     autoclose: true,
//     minView:'month',
//     todayBtn:true,
//     startDate: "<?=$duringtime['begin']?>",
//     endDate: "<?=$duringtime['over']?>",
//   });
// })
</script>
