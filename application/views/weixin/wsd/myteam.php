<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
  <title>个人中心</title>
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="msapplication-tap-highlight" content="no">
  <link href="http://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/wsd/weiui/css/weui1.css"/>
    <link rel="stylesheet" href="/wsd/weiui/css/example1.css"/>
  <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
  <style type="text/css">
  </style>
</head>

<body>
<div class="page preview js_show">
    <?php if ($result['num']==0): ?>
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-info weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <!-- <h2 class="weui-msg__title">继续努力</h2> -->
            <p class="weui-msg__desc">您还没有直属的下级代理，再接再厉哦。</p>
        </div>
    </div>
    <?php endif ?>
    <?php if (!$result['num']==0):?>
    <div class="page__hd">
        <h1 class="page__title">下级代理</h1>
        <p class="page__desc">共<?=$result['num']?>个直属下级代理</p>
    </div>
    <div class="page__bd">
                <?php
                foreach ($result['follows'] as $v):
                    $group1=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->order_by('lastupdate','DESC')->find();
                  if($group1->bottom){
                    $bottom='('.$group1->bottom.')';
                    //echo $bottom.'<br>';
                    $group_ay=DB::query(Database::SELECT,"SELECT count(id) as group_num from wsd_groups where bid=$v->bid and id in $bottom ")->execute()->as_array();
                    $group_num=$group_ay[0]['group_num'];
                  }else{
                    $group_num=0;
                  }
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
                      //echo $bottom1.'<br>';
                      $day_tnum1=DB::query(Database::SELECT,"SELECT SUM(payment) as day_tnum from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$daytype')='$day' ")->execute()->as_array();
                      $day_tnum+=$day_tnum1[0]['day_tnum'];
                      //echo  $day_tnum.'<br>';当天团队销量
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
                        // echo  $month_tmoney.'<br>';
                        // echo $group->id."<br>";
                      $child_groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                      $child_moneys=0;
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
                            $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>=',$month_ltmoney)->find();
                            if(!$sku->id){
                                $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>',$month_ltmoney)->find();
                                if(!$fsku->id){
                                   $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
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
                    $fname=ORM::factory('wsd_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                    // echo $all_pxmoney.'<br>';
                    // exit();
                ?>
        <div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">微信昵称</label>
                    <em class="weui-form-preview__value"><?=$v->nickname?></em>
                </div>
            </div>
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">电话号码</label>
                    <span class="weui-form-preview__value"><?=$v->tel==0?'无':$v->tel?></span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">日销量</label>
                    <span class="weui-form-preview__value"><?=$day_pnum?$day_pnum:0?></span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">月销量</label>
                    <span class="weui-form-preview__value"><?=$month_pnum?$month_pnum:0?></span>
                </div>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">总销量</label>
                    <span class="weui-form-preview__value"><?=$all_pnum?$all_pnum:0?></span>
                </div>
            </div>
            <div class="weui-form-preview__ft">
                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">他的团队（<?=$group_num?>人）</a>
            </div>
        </div>
        <br>
    <?php endforeach;?>
    </div>
<?php endif?>
</div>
</body>

</html>
