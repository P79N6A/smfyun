
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
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
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  th{
    white-space: nowrap;
  }
  td{
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
                分销商管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li><a href="#">分销商设置</a></li>
                <li><a href="#">分销商管理</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-6">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称，姓名，电话，备注，地址搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                </form>


                </div>
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                            <div class="am-form am-form-horizontal">
                <div class="row" style="overflow:visible;display:inline-block;width:100%;">
                        <div class="am-u-sm-12 am-u-md-3" style="float:right;">
                        <button id="take_user" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出所有用户情况</button>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <button id="take_group" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出所有分组情况</button>
                        </div>
                        </div>
                <div class="row" style="overflow:visible;">
                                    <label for="type" class="am-u-sm-1 am-form-label">状态：</label>
                        <div class="am-u-sm-12 am-u-md-3">

                                        <select id='type' data-am-selected="{searchBox: 1}">
                    <option value="all" <?=!$_GET['type']?'selected':''?>>全部</option>
                    <option value="1" <?=$_GET['type']==1?'selected':''?>>正常</option>
                    <option value="3" <?=$_GET['type']==3?'selected':''?>>已取消</option>
                    </select>
                                    </div>
                                    <label for="group" class="am-u-sm-1 am-form-label">分组：</label>
                        <div class="am-u-sm-12 am-u-md-3" style="float:left;">

                                        <select id='group' data-am-selected="{searchBox: 1}">
                    <option value="all" <?=!$_GET['type']?'selected':''?>>全部</option>
                  <?php if($gnum>0):?>
                    <?php foreach ($group as $key => $v):?>
                        <option value="<?=$v->id?>" <?=$_GET['group']==$v->id?'selected':''?>><?=$v->name?></option>
                    <?php endforeach?>
                  <?php endif?>
                    </select>
                                    </div>
            </div>
            </div>
            </div>
            </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll;">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>状态：
                  </th>
                  <th>分组：
                  </th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>OpenID</th> -->
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=money" title="按总收益排序">总收益</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=score" title="按收益排序">未提现收益</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=paid" title="按订单收入排序">订单收入</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=fans_num" title="按客户数量排序">客户</a></th>
                  <th>分销商等级</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>地址</th>
                  <th>性别</th>
                  <th>备注</th>
                  <th>海报生成</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->where('subscribe','=',1)->count_all();
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
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/qwtqfx/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/qwtqfx/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>
                  <td><?=$v->groups->name?></td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <!-- <td><a href='/qfxa/qrcodes_detail/<?=$v->id?>'><?=$v->nickname?></a></td> -->
                  <td><?=$v->nickname?></td>
                  <td><?=$v->money?></td>

                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td><?=$v->paid?></td>
                  <td><a href="<?=$url2?>" title="查看下线"><?=$count2?></a></td>
                  <td><?=$v->skus->name?$v->skus->name:$config['title1']?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->shop?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><?=$v->bz?></td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td nowrap="">
                  <a class="edit am-btn am-btn-xs am-btn-danger" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-bz='<?=$v->bz?>'>
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
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>

    </div>
 <div class="shadow mask_group" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:30%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      导出所有分组情况
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
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform" action="/qwtqfxa/export_data_groups">
                          <div class="am-form-group">
                          <label for="fscore" class="am-u-sm-3 am-form-label">选择起始时间： </label>
                            <div class="am-u-sm-9">
  <input name="data[begin]" id="datetimepicker1" size="16" type="text" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
                            </div>
                          </div>
                          <div class="am-form-group">
                          <label for="fscore" class="am-u-sm-3 am-form-label">选择终止时间： </label>
                            <div class="am-u-sm-9">
  <input name="data[over]" id="datetimepicker2" size="16" type="text" value="<?=$_GET['data']['over']?>" class="am-form-field" readonly>
                            </div>
                          </div>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">确定</button>
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
 <div class="shadow mask_user" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:30%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      导出所有分组情况
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
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform" action="/qwtqfxa/export_data_users">
                          <div class="am-form-group">
                          <label for="fscore" class="am-u-sm-3 am-form-label">选择起始时间： </label>
                            <div class="am-u-sm-9">
  <input name="data[begin]" id="datetimepicker1" size="16" type="text" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
                            </div>
                          </div>
                          <div class="am-form-group">
                          <label for="fscore" class="am-u-sm-3 am-form-label">选择终止时间： </label>
                            <div class="am-u-sm-9">
  <input name="data[over]" id="datetimepicker2" size="16" type="text" value="<?=$_GET['data']['over']?>" class="am-form-field" readonly>
                            </div>
                          </div>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">确定</button>
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
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">
                          <div class="am-form-group modal-body">
                          <label for="fscore" class="am-u-sm-3 am-form-label">收益增减（正数为增加、负数为减少）： </label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="fscore" name="form[score]" type="number" style="width:150px" value="">
                            </div>
                          </div>
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
                            <div class="actions" style="float:left;">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">正常</li>
                                    <li id="switch-4" class="switch-type red">取消分销商资格</li>
                                </ul>
                                <input type="hidden" name="form[lv]" value="1" id="flock">
                            </div>
                            </div>
                </div>
                <input id="userid" type="hidden" name="form[id]" value="">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-xsz am-btn-default pull-left">取消</button>
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
<script>
$(document).on('change','#type',function(){
  console.log($('#type').val());
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtqfxa/qrcodes_m?type='+$('#type').val()+'&group='+$('#group').val();
})
$(document).on('change','#group',function(){
  console.log($('#group').val());
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/qwtqfxa/qrcodes_m?group='+$('#group').val()+'&type='+$('#type').val();
})
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
$('.edit').on('click',function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var uname = $(this).data('uname')
  var tel = $(this).data('tel')
  var bz = $(this).data('bz')
  var lv = $(this).data('lv')
  $('#userid').val(id);
  $('.nickname').text(name);
  $('#fname').val(uname);
  $('#ftel').val(tel);
  $('#bz').val(bz);
  $('#flock').val(lv);
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
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    $('#datetimepicker2').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
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
