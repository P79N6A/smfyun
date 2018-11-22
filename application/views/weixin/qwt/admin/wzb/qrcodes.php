
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
                用户管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">神码云直播</a></li>
    <li class="am-active">用户管理</li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>是否订阅</th>
                  <th>性别</th>
                  <th>加入时间</th>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                ?>

                <tr>
                  <!-- <td><?=$v->id?></td> -->
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock == 0)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lock == 1)
                    echo '<span class="label label-danger">禁言中</span>';
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td>
                    <?php
                  if ($v->sub == 1)
                    echo '<span class="label label-success">已订阅</span>';
                  if ($v->sub == 0)
                    echo '<span class="label label-danger">未订阅</span>';
                  ?>
                  </td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><?=date("Y-m-d H:i:s",$v->jointime)?></td>
                  <td nowrap="">
                  <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>" data-lock="<?=$v->lock?>">
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
                      修改用户
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
                                    <label for="user-name" class="am-u-sm-12 am-form-label">用户状态： </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">不禁言</li>
                                    <li id="switch-4" class="switch-type red">禁言</li>
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
  var id = $(this).data('id')
  var lock = $(this).data('lock')
  $('.edithidden').val(id);
  $('#flock0').val(lock);
  if (lock==0) {$('#switch-1').addClass('green-on');}else{$('#switch-4').addClass('red-on');};
  $('.shadow').fadeIn();
                    });
                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                      $('#flock0').val(0);
                    });
                    $('#switch-4').click(function(){
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                      $('#flock0').val(1);
                    });
</script>

