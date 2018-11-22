
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
                <li><a href="#" class="am-icon-home">语音红包</a></li>
                <li><a href="#">用户明细</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
              <tr>
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>活动参与次数</th>
                  <th>已领取奖品</th>
                  <th>性别</th>
                  <th><a href="/qwtrwba/qrcodes?sort=jointime&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按关注时间排序">关注时间</a></th>
                  <th>关注状态</th>
                  <th>来源活动</th>
                  <!-- <th>操作</th> -->
                </tr>
                                    </thead>
                                    <tbody>
               <?php
                foreach ($result['qrcodes'] as $v):
                  $task_name=ORM::factory('qwt_yyhbrecord')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find()->task->name;
                  $count4 =ORM::factory('qwt_yyhborder')->where('bid','=',$v->bid)->where('qid','=',$v->id)->count_all();
                  $sql = DB::query(Database::SELECT,"SELECT `tid`, count(*) AS t_num from qwt_rwborders where `qid` = $v->id  group by tid ");
                  $result2=$sql->execute()->as_array();
                ?>

                <tr>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock == 1)
                    echo '<span class="label label-danger">已锁定</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td ><?=$result2[0]['t_num']?$result2[0]['t_num']:0?></td>

                  <td id="score1<?=$v->id?>"><a href="/qwtrwba/orders?qid=<?=$v->id?>" title="查看奖品明细"><?=$count4?></a></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><?=date('Y-m-d H:i', $v->jointime)?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->subscribe==0)
                      echo '<span class="label label-danger">已跑路</span>';
                    else
                      echo '<span class="label label-success">关注</span>';
                    ?>
                  </td>
                  <td><?=$task_name?></td>
                  <!-- <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>">
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td> -->
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
                <div class="tpl-alert"></div>
            </div>
            </form>










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
                                    <label for="user-name" class="am-u-sm-12 am-form-label">用户状态 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">正常</li>
                                    <li id="switch-2" class="switch-type purple">已锁定</li>
                                    <input type="hidden" name="form[lock]" id="flock0" value="">
                                </ul>
                            </div>
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

