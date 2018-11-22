
<style type="text/css">
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  th,td{
    white-space: nowrap;
  }
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
if ($result['ffuser']) $title=$result['ffuser']->nickname.'的二级';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
?>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                代理列表
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li><a href="#">代理设置</a></li>
                <li class="am-active">代理列表</li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个代理
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称,手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success am-btn-secondary" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出全部代理信息</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll;">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
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
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                    $group1=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
                  // if($group1->bottom){
                  //   $bottom='('.$group1->bottom.')';
                  //   //echo $bottom.'<br>';
                  //   $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from qwt_dldgroups where bid=$v->bid and id in $bottom ")->execute()->as_array();
                  //   $group_num=$group_ay[0]['group_num'];
                  // }else{
                  //   $group_num=0;
                  // }
                  $group_num = ORM::factory('qwt_dldqrcode')->where('lv','=',1)->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->count_all();
                  //echo $group_num.'<br>';所辖团队成员
                  $qr_num=ORM::factory('qwt_dldqrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->where('fopenid','!=','')->count_all();
                   $groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                   $month=date('Y-m',time());
                       //echo $month.'<br>';
                    $daytype='%Y-%m-%d';
                    $monthtype='%Y-%m';
                    $day=date('Y-m-d',time());
                    $month_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as month_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_pnum=$month_pnum[0]['month_pnum'];
                    //echo $month_pnum.'<br>';当月个人销量
                    $day_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as day_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                    $day_pnum=$day_pnum[0]['day_pnum'];
                     //echo $day_pnum.'<br>';当天个人销量
                    $all_pnum=DB::query(Database::SELECT,"SELECT SUM(payment) as all_pnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `fopenid` = '$v->openid' ")->execute()->as_array();
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
                      //echo $bottom1.'<br>';
                      $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                      $day_tnum+=$day_tnum1[0]['day_tnum'];
                      //echo  $day_tnum.'<br>';当天团队销量
                      $month_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                      $month_tnum+=$month_tnum1[0]['month_tnum'];
                      //echo  $month_tnum.'<br>';当月团队销量
                      $all_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as all_tnum from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 ")->execute()->as_array();
                      $all_tnum+=$all_tnum1[0]['all_tnum'];
                      //累计团队销量
                      $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                      $month_tmoney1=$month_tmoney1[0]['month_tmoney'];
                      //echo  $month_tmoney.'<br>';
                      $sku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                        if(!$sku->id){
                            $fsku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                            if(!$fsku->id){
                               $scale=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scale=0;
                            }
                        }else{
                            $scale=$sku->scale;
                        }
                        $month_tmoney+=$month_tmoney1*$scale/100;
                        // echo  $month_tmoney.'<br>';
                        // echo $group->id."<br>";
                      $child_groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                      $child_moneys=0;
                      foreach ($child_groups as $child_group) {
                            if($child_group->bottom){
                                 $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                              }else{
                                    $bottom2='('.$child_group->id.')';
                              }

                            //echo $bottom2."<br>";
                            $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                            $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                            //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                            $sku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>',$month_ltmoney)->find();
                            if(!$sku->id){
                                $fsku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                                if(!$fsku->id){
                                   $scale=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                                }else{
                                    $scale=0;
                                }
                            }else{
                                $scale=$sku->scale;
                            }
                            $child_money= $month_ltmoney*$scale/100;
                            $child_moneys+=$child_money;
                      }
                      //echo  $child_moneys.'<br>';
                      $month_pmoney+=$month_tmoney-$child_moneys;
                    }

                    //echo  $month_pmoney.'<br>';当月个人团队奖励
                    $day_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as day_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$daytype')='$day' ")->execute()->as_array();
                    $day_pxmoney=$day_pxmoney[0]['day_pxmoney'];
                    //当天销售利润
                    $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                    //当月销售利润
                    //echo  $month_pxmoney.'<br>';
                    $all_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as all_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                    $all_pxmoney=$all_pxmoney[0]['all_pxmoney'];
                    //累计销售利润
                    $fname = '';
                    if($v->tid){
                      $fname = '自主购买';
                    }
                    if($v->code){
                      $fname = '来自邀请码：'.$v->code;
                    }
                    $nickname=ORM::factory('qwt_dldqrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                    if($nickname){
                      $fname = $nickname;
                    }
                    // echo $all_pxmoney.'<br>';
                    // exit();
                ?>
                <tr>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->suites->name?$v->suites->name:'无'?></td>
                  <td><a href="/qwtdld/memberpage/<?=$v->openid?>"><?=$v->nickname?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  $fuser = ORM::factory('qwt_dldqrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->find();
                  if ($v->lv == 1)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lv == 2)
                    echo '<span class="label label-warning">待审核</span>';
                  if ($v->lv == 3)
                    echo '<span class="label label-danger">已取消</span>';
                  ?>
                  </td>
                  <td><a href="/qwtdlda/qrcodes_m?qid=<?=$v->id?>"><?=$group_num?></td>
                  <td><a href="/qwtdlda/customers?qid=<?=$v->id?>"><?=$qr_num?></td>
                  <td><a href="/qwtdlda/history_trades?flag=dayp&qid=<?=$v->id?>"><?=$day_pnum?$day_pnum:0?></td>
                  <td><a href="/qwtdlda/history_trades?flag=monthp&qid=<?=$v->id?>"><?=$month_pnum?$month_pnum:0?></td>
                  <td><a href="/qwtdlda/history_trades?flag=allp&qid=<?=$v->id?>"><?=$all_pnum?$all_pnum:0?></td>
                  <td><a href="/qwtdlda/history_trades?flag=dayt&qid=<?=$v->id?>"><?=$day_tnum?></td>
                  <td><a href="/qwtdlda/history_trades?flag=montht&qid=<?=$v->id?>"><?=$month_tnum?></td>
                  <td><a href="/qwtdlda/history_trades?flag=allt&qid=<?=$v->id?>"><?=$all_tnum?></td>
                  <td><?=$month_tmoney?></td>
                  <td><?=$month_pmoney?></td>
                  <td><?=$day_pxmoney?$day_pxmoney:0?></td>
                  <td><?=$month_pxmoney?$month_pxmoney:0?></td>
                  <td><?=$all_pxmoney?$all_pxmoney:0?></td>
                  <td><?=$fname?></td>
                  <td nowrap="">
                  <a class="edit am-btn am-btn-xs am-btn-danger am-btn-secondary" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-bz='<?=$v->bz?>' data-suite='<?=$v->group_id?>' data-fid='<?=$fuser->id?>' data-fname='<?=$fuser->nickname?>'>
                      <span class="am-icon-edit"></span>修改用户
                    </a>
                  </td>
                </tr>

                <?php endforeach;?>


                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

            </div>
                    </div>
                </div>
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>

    </div>
 <div class="shadow useredit" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;margin-left:0;width:60%;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      用户名
                    </div>
                  </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper" style="overflow:scroll;">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">用户状态： </label>
                                    <div class="am-u-sm-9">
                            <div class="actions" style="float:left;">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">正常</li>
                                    <li id="switch-4" class="switch-type red">取消代理商资格</li>
                                </ul>
                                <input type="hidden" name="form[lv]" value="1" id="flock">
                            </div>
                            </div>
                </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">修改当前用户注册手机号：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="ftel" name="form[tel]" style="width:150px" type="number">
                            </div>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">修改当前用户分组： </label>
                                    <div class="am-u-sm-9">
                                        <select id='groupselect' name="form[suite]" data-am-selected="{searchBox: 1}">
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">修改当前用户的上级代理为： </label>
                                    <div class="am-u-sm-9">
          <select name='form[fuser]' class='select_choose' style="width:150px;" data-am-selected="{searchBox: 1}">
              <option value="nochange">不修改</option>
              <option value="zerofopenid">设为无上级(只能修改真正没有上级的用户)</option>
              <?php foreach ($alls as $k => $v):?>
                <option value="<?=$v->id?>"><?=$v->nickname?></option>
              <?php endforeach?>
          </select>
                                    </div>
                                </div>
                <input id="userid" type="hidden" name="form[id]" value="">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">保存</button>
                            </div>
                          </div>
                          </form>

                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
</div>
</div>
</div>
</div>


<script>
$(document).ready(function() {
  <?php if($gnum>0):?>
    // gselect = '<label for="fscore">选择分组：</label>';
    select = '<option value=\"0\">不加入分组</option>';
    <?php foreach ($suite as $k => $v):?>
        select = select+ "<option value=\"<?=$v->id?>\"><?=$v->name?></option>";
    <?php endforeach?>
    $('#groupselect').html(select);
  <?php else:?>
  $('#groupselect').parent().parent().hide();
  <?php endif?>
});
$('.edit').on('click',function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var uname = $(this).data('uname')
  var tel = $(this).data('tel')
  var bz = $(this).data('bz')
  var lv = $(this).data('lv')
  var fid = $(this).data('fid');
  var fname = $(this).data('fname');
  var suite = $(this).data('suite');
  $('#userid').val(id);
  $('.nickname').text(name);
  $('#fname').val(uname);
  $('#ftel').val(tel);
  $('#bz').val(bz);
  $('#flock').val(lv);
  $('#groupselect').val(suite);
  if (lv==1) {$('#switch-1').addClass('green-on')}else{$('#switch-4').addClass('red-on')};
  $('.useredit').fadeIn();
})
$(document).on('click','#take_user',function(){
    $(".mask_user").fadeIn(500);
});
$(document).on('click','#take_group',function(){
    $(".mask_group").fadeIn(500);
});
$(document).on('click','.close',function(){
    $(".shadow").fadeOut(500);
});
                    $('#switch-1').click(function(){
                      $('#flock').val(1);
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-4').click(function(){
                      $('#flock').val(3);
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                    });
</script>
