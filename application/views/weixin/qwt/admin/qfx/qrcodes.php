

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
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                用户
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
    <li>分销商设置</li>
    <li><a href="/qwtwfba/qrcodes">分销商审核</a></li>
    <li class="am-active"><?=$title?></li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称，姓名，电话，备注，地址搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                </form>
                        <div class="am-u-sm-12 am-u-md-3">

                                        <select id='type' data-am-selected="{searchBox: 1}">
                    <option value="all" <?=!$_GET['type']?'selected':''?>>全部</option>
                    <option value="2" <?=$_GET['type']==2?'selected':''?>>待审核</option>
                    <option value="4" <?=$_GET['type']==4?'selected':''?>>已驳回</option>
</select>
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
                  <th>姓名</th>
                  <th>电话</th>
                  <th>店铺地址</th>
                  <th>性别</th>
                  <th>关注状态</th>
                  <th>备注</th>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qfx_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                ?>

                <tr>
                  <!-- <td><?=$v->id?></td> -->
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lv == 1)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lv == 2)
                    echo '<span class="label label-warning">待审核</span>';
                  if ($v->lv == 3)
                    echo '<span class="label label-danger">已取消</span>';
                  if ($v->lv == 4)
                    echo '<span class="label label-primary">被驳回</span>';
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/qfx/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/qfx/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->shop?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->subscribe==0)
                      echo '<span class="label label-danger">已跑路</span>';
                    else
                      echo '<span class="label label-success">关注</span>';
                    ?>
                  </td>
                  <td><?=$v->bz?></td>
                  <td nowrap="">
                  <a class="edit am-btn am-btn-default am-btn-xs am-text-secondary" href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-bz='<?=$v->bz?>' data-uname='<?=$v->name?>'>
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">
                                        <?=$pages?>
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
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;margin-left:0;width:60%;">
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
                            <label for="user-name" class="am-u-sm-3 am-form-label">姓名：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="fname" name="form[name]" style="width:150px" type="text">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">电话：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="ftel" name="form[tel]" style="width:150px" type="number">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">备注：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="fbz" name="form[bz]" style="width:150px" type="text">
                            </div>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">选择分组： </label>
                                    <div class="am-u-sm-9">
                                        <select id='groupselect' name="form[groupid]" data-am-selected="{searchBox: 1}">
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">用户状态： </label>
                                    <div class="am-u-sm-9">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">通过</li>
                                    <li id="switch-4" class="switch-type red">不通过</li>
                                </ul>
                                <input type="hidden" name="form[lv]" value="1" id="flock">
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
$(document).ready(function() {
  <?php if($gnum>0):?>
    // gselect = '<label for="fscore">选择分组：</label>';
    select = '';
    <?php foreach ($group as $key => $v):?>
        select = select+ "<option value=<?=$v->id?>><?=$v->name?></option>";
    <?php endforeach?>
    $('#groupselect').html(select);
  <?php else:?>
  $('#groupselect').parent().parent().hide();
    <?php endif?>
});
$(document).on('change','#type',function(){
  console.log($('#type').val());
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/qfxa/qrcodes?type='+$('#type').val();
})

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#flock').val(1);
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-4').click(function(){
                      $('#flock').val(4);
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                    });
$('.edit').on('click', function () {
  var id = $(this).data('id')
  var name = $(this).data('name')
  var uname = $(this).data('uname')
  var tel = $(this).data('tel')
  var bz = $(this).data('bz')
  var lv = $(this).data('lv')
  $('.edithidden').val(id);
  $('.nickname').text(name);
  $('#fname').val(uname);
  $('#ftel').val(tel);
  $('#fbz').val(bz);
  $('#flock').val(lv);
  if (lv==1) {$('#switch-1').addClass('green-on')}else{$('#switch-4').addClass('red-on')};
  $('.shadow').fadeIn();
})
</script>
