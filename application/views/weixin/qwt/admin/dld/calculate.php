
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                个人团队奖励结算账单
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li>结算管理</li>
                <li>个人团队奖励结算账单</li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-3">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个代理
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                 <div class="am-u-sm-12 am-u-md-3">
      <input id="datetimepicker1" size="16" type="text" name="data[begin]" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
      </div>
               
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称，注册手机号，支付宝账号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success am-btn-secondary" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出个人团队奖励结算账单</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>支付宝账号</th>
                  <th>当月团队奖励</th>
                  <th>当月团队可结算奖励</th>
                  <th>当月团队待结算奖励</th>
                  <th>当月个人团队奖励</th>
                  <th>当月个人可结算团队奖励</th>
                  <th>当月个人待结算团队奖励</th>
                  <!-- <th>当月销售利润</th>
                  <th>当月可结算销售利润</th>
                  <th>当月待结算销售利润</th> -->
                  <th>已结算个人团队奖励</th>
                  <th>上级代理</th>
                  <th>是否结算</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                   $groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                    //echo $qr_num.'<br>';客户数
                    // $month=date('Y-m',time());
                     //echo $month.'<br>';
                    // $daytype='%Y-%m-%d';
                    $nawtime=time();
                    $monthtype='%Y-%m';
                    // $day=date('Y-m-d',time());
                     //echo $day.'<br>';
                    $typearry[0]=2;
                    $typearry[1]=3;
                    $score=ORM::factory('qwt_dldscore')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->where('type','IN',$typearry)->find();
                    if($score->id){
                      $flag=1;
                    }else{
                      $flag=0;
                    }
                    $month_tmoney=0;
                    $month_pmoney=0;
                    $monthjs_tmoney=0;
                    $monthjs_pmoney=0;
                    foreach ($groups as $group) {
                        if($group->bottom){
                        $bottom1='('.$group->id.','.$group->bottom.')';
                        }else{
                            $bottom1='('.$group->id.')';
                        }
                        $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney1 from qwt_dldtrades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $month_tmoney1=$month_tmoney1[0]['month_tmoney1'];
                        $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from qwt_dldtrades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                        //echo  $month_tmoney.'<br>';
                        $skujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                        if(!$skujs->id){
                            $fskujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                            if(!$fskujs->id){
                               $scalejs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scalejs=0;
                            }
                        }else{
                            $scalejs=$skujs->scale;
                        }
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
                        $month_tmoney+=$month_tmoney1*$scale/100;//团队奖励

                        $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;//可结算团队奖励
                          // echo  $month_tmoney.'<br>';
                          // echo $group->id."<br>";
                        $child_groups=ORM::factory('qwt_dldgroup')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                        $child_moneys=0;
                        $childjs_moneys=0;
                        foreach ($child_groups as $child_group) {
                              if($child_group->bottom){
                                   $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                                }else{
                                      $bottom2='('.$child_group->id.')';
                                }

                              //echo $bottom2."<br>";
                              $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from qwt_dldtrades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                              $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from qwt_dldtrades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                              $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                              $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                              //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                              $sku=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>',$month_ltmoney)->find();
                               $skujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>',$monthjs_ltmoney)->find();
                              if(!$skujs->id){
                                  $fskujs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                                  if(!$fskujs->id){
                                     $scalejs=ORM::factory('qwt_dldsku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                                  }else{
                                      $scalejs=0;
                                  }
                              }else{
                                  $scalejs=$skujs->scale;
                              }
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
                              $childjs_money= $monthjs_ltmoney*$scalejs/100;
                              $childjs_moneys+=$childjs_money;
                        }
                        //echo  $child_moneys.'<br>';
                        $month_pmoney+=$month_tmoney-$child_moneys;
                        $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
                        //echo  $month_pmoney.'<br>';当月个人团队奖励
                    }
                    // $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                    // $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                    // $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                    // //当月销售利润
                    // //echo  $month_pxmoney.'<br>';
                    // $all_money=$month_pmoney+$month_pxmoney;
                    // $alljs_money=$monthjs_pmoney+$monthjs_pxmoney;
                    //累计销售利润
                    //已结算团队奖励
                    $monthyjs_pmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthyjs_pmoney from qwt_dldscores where bid=$v->bid and qid = $v->id and type IN (2,3) and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthyjs_pmoney=$monthyjs_pmoney[0]['monthyjs_pmoney'];
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
                ?>
                <tr>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><a href="/qwtdlda/history_scores?qid=<?=$v->id?>"><?=$v->nickname?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->alipay_name?></td>
                  <td><?=number_format($month_tmoney,2)?></td>
                  <td><?=number_format($monthjs_tmoney,2)?></td>
                  <td><?=number_format($month_tmoney-$monthjs_tmoney,2)?></td>
                  <td><?=number_format($month_pmoney,2)?></td>
                  <td><?=number_format($monthjs_pmoney,2)?></td>
                  <td><?=number_format($month_pmoney-$monthjs_pmoney,2)?></td>
                  <!-- <td><?=$month_pxmoney?$month_pxmoney:0?></td>
                  <td><?=$monthjs_pxmoney?$monthjs_pxmoney:0?></td>
                  <td><?=$monthdjs_pxmoney?$monthdjs_pxmoney:0?></td> -->
                  <td><a href="/qwtdlda/history_scores?flag=jl&qid=<?=$v->id?>"><?=number_format(-$monthyjs_pmoney,2)?></td>
                  <td><?=$fname?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($flag == 1)
                    echo '<span class="label label-success">已结算</span>';
                  if ($flag == 0)
                    echo '<span class="label label-warning">未结算</span>';
                  ?>
                  </td>
                  <td nowrap="">
                  <?php if($flag==0):?>
                  <a class="edit am-btn am-btn-secondary am-btn-danger am-btn-xs" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-money='<?=$monthjs_pmoney?>' data-bz='<?=$v->bz?>'>
                      <span class="am-icon-edit"></span>结算
                    </a>
                  <?php endif;?>
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
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
 <div class="shadow useredit" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:30%;margin-left:0;width:40%;">
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
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">
                          <div class="am-form-group modal-body">
                          <label id="stime" for="fscore" class="am-u-sm-12 am-form-label">时间</label>
                          </div>
                          <div class="am-form-group modal-body">
                          <label for="fscore" class="am-u-sm-12 am-form-label">当前个人可结算奖金<span id="smoney"></span>元</label>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">结算方式： </label>
                                    <div class="am-u-sm-9">
                            <div class="actions" style="float:left;">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green green-on">微信企业付款</li>
                                    <li id="switch-4" class="switch-type red">手动转账</li>
                                </ul>
                                <input id="sformtype" type="hidden" name="form[type]" value="1">
                            </div>
                            </div>
                </div>
                <input id="userid" type="hidden" name="form[id]" value="">
                <input id="usertime" type="hidden" name="form[time]" value="">
                <input id="usermoney" type="hidden" name="form[money]" value="">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改用户</button>
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
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
    format: "yyyy-mm",
    language: "zh-CN",
    autoclose: true,
    startView:3,
    minView:3,
    todayBtn:false,
    startDate: "<?=$duringtime['begin']?>",
    <?php $time=strtotime("+1 Months");?>
    endDate: "<?=date("Y-m",$time)?>",
});
$('.edit').on('click',function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var uname = $(this).data('uname')
  var tel = $(this).data('tel')
  var bz = $(this).data('bz')
  var lv = $(this).data('lv')
  var time = "<?=$_GET['data']['begin']?>";
  var money = $(this).data('money')?$(this).data('money'):0;
  var account_type = "<?=$config['money_type']==1?'企业付款':'手动结算'?>"
  $('#stime').text(time);
  $('#smoney').text(money);
  $('#userid').val(id);
  $('#usertime').val(time);
  $('#usermoney').val(money);
  $('.nickname').text(name);
  // if (<?=$config['money_type']?>==1) {$('#switch-1').addClass('green-on')}else{$('#switch-4').addClass('red-on')};
  $('.useredit').fadeIn();
})
$(document).on('click','.close',function(){
    $(".shadow").fadeOut(500);
});
                    $('#switch-1').click(function(){
                      $('#sformtype').val(1);
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-4').click(function(){
                      $('#sformtype').val(2);
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                    });
    </script>


