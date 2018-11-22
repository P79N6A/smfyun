

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的直接粉丝';
if ($result['ffuser']) $title = $result['ffuser']->nickname.'的间接粉丝';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
?>
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
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                用户明细
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝服务号版</a></li>
    <li><a href="/qwtdkaa/qrcodes">用户明细</a></li>
    <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>

<form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>OpenID</th> -->
                  <th><a href="/qwtdkaa/qrcodes?sort=score&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按积分排序">现有积分</a></th>
                  <th>兑换数量</th>
                  <!-- <th nowrap=""><a href="/qwtdkaa/qrcodes?sort=money" title="按总收益排序">总收益</a></th>
                  <th nowrap=""><a href="/qwtdkaa/qrcodes?sort=score" title="按收益排序">未提现收益</a></th>
                  <th nowrap=""><a href="/qwtdkaa/qrcodes?sort=paid" title="按订单收入排序">订单收入</a></th> -->
                  <th>性别</th>
                  <th>下级</th>
                  <th>二级</th>
                  <th><a href="/qwtdkaa/qrcodes?sort=jointime&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按关注时间排序">关注时间</a></th>
                  <th>关注状态</th>
                  <th>是否有生成邀请卡？</th>
                  <th>是否加入打卡？</th>
                  <th>来源用户</th>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  // $count3 = ORM::factory('qwt_dkascore')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', '=', 3)->count_all();
                  $xopenids = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->find_all();//搜出一级下线
                  $count3 = 0;
                  foreach ($xopenids as $key => $value) {
                    $count3 = $count3 + ORM::factory('qwt_dkaqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $value->openid)->count_all();
                  }
                  $tempid=array();
                    if($firstchild[0]['openid']==null)
                    {
                      $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
                    }
                    else
                    {
                      for($i=0;$firstchild[$i];$i++)
                      {
                        $tempid[$i]=$firstchild[$i]['openid'];
                      }
                    }
                  $fuser = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  $count4 =ORM::factory('qwt_dkaorder')->where('bid','=',$v->bid)->where('qid','=',$v->id)->count_all();
                  $dka_join = ORM::factory('qwt_dkaqrcode')->where('dka_join','!=',0)->where('bid', '=', $v->bid)->where('openid', '=', $v->openid)->find()->dka_join;
                ?>

                <tr>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock == 3)
                    echo '<span class="label label-warning">白名单</span>';
                  elseif ($v->lock == 4)
                    echo '<span class="label label-info">隐身用户</span>';
                  elseif ($v->lock == 1)
                    echo '<span class="label label-danger">已锁定</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <!-- <td><?=$v->openid?></td> -->
                  <?php
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m-d'));
                  $url = '/qwtdka/index/'. $v->bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看兑换前台"><?=$v->score?></a></td>
                  <td id="score1<?=$v->id?>"><a href="/qwtdkaa/orders?qid=<?=$v->id?>" title="查看兑换明细"><?=$count4?></a></td>
                  <!-- <td><?=$v->money?></td>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看未提现收益"><?=$v->cash?></a></td>
                  <td><?=$v->paid?></td> -->
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/qwtdkaa/qrcodes?fopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count2?></a></td>
                  <td><?=$count3?></td>
                  <td><?=date('Y-m-d H:i', $v->jointime)?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->subscribe==0)
                      echo '<span class="label label-danger">已跑路</span>';
                    else
                      echo '<span class="label label-success">关注</span>';
                    ?>
                  </td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td><?=$dka_join ? '是' : '否'?></td>
                  <td><a href="/qwtdkaa/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>">
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            </form>
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
                <div class="tpl-alert"></div>
            </div>









        </div>
        </div>
 <div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      用户名称
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

                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">积分增减： <span class="tpl-form-line-small-title">（正数为增加、负数为减少）</span></label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore" name="form[score]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">用户状态 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">正常</li>
                                    <li id="switch-2" class="switch-type purple">已锁定</li>
                                    <li id="switch-3" class="switch-type blue">白名单</li>
                                    <li id="switch-4" class="switch-type red">隐身用户</li>
                        <input class='checkedit' name="form[lock]" id="flock0" value="" type="hidden">
                                </ul>
                            </div>
                            </div>
                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>1、加入白名单后不会自动锁定<br>2、隐身用户不会出现在积分排行中</p>
                </div>
            </div>
            <input class='edithidden' name="form[id]" value='' type="hidden">
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
<script type="text/javascript">
                    $('.edit').click(function(){
                      $(".edithidden").attr("value",$(this).data('id'));
                      $(".nickname").text($(this).data('name'));
                      var i = $(this).data('lock');
                      $('#flock0').val(i);
                      if (i==0) {
                        $('#switch-1').addClass('green-on');
                      }else if(i==1){
                        $('#switch-2').addClass('purple-on');
                      }else if(i==3){
                        $('#switch-3').addClass('blue-on');
                      }else{
                        $('#switch-4').addClass('red-on');
                      };
                      $('.shadow').fadeIn();
                    });

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#flock0').val(0);
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-3').removeClass('blue-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-2').click(function(){
                      $('#flock0').val(1);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-3').removeClass('blue-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('purple-on');
                    });
                    $('#switch-3').click(function(){
                      $('#flock0').val(3);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('blue-on');
                    });
                    $('#switch-4').click(function(){
                      $('#flock0').val(4);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-3').removeClass('blue-on');
                      $(this).addClass('red-on');
                    });
</script>
