
<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';
$title = '概览';
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
                待审核用户
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">特别特</a></li>
                <li><a href="#">待审核用户</a></li>
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
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称，手机号，姓名搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                         <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出待审核用户信息</a>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                        <table class="am-table am-table-striped am-table-hover table-main">
                            <thead>
              <tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>门头照片</th>
                  <th>身份证或本人照片</th>
                  <th>地址</th>
                  <th>行业类型</th>
                  <th>姓名</th>
                  <th>手机号</th>
                  <th>审核</th>
                </tr>
                  </thead>
                  <tbody>
               <?php
                foreach ($result['qrcodes'] as $v):
                ?>
                <tr>
                <?php if($v->headimgurl):?>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <?php else:?>
                  <td></td>
                <?php endif;?>
                  <td><?=$v->nickname?></td>
                  <?php if($v->shop_pic):?>
                  <td><a href="/qwttbta/image1s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" target="_blank"><img src="/qwttbta/image1s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" width="32" height="32" title="<?=$v->openid?>"></a></td>
                  <?php else:?>
                  <td></td>
                <?php endif;?>
                <?php if($v->ic_pic):?>
                  <td><a href="/qwttbta/image2s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" target="_blank"><img src="/qwttbta/image2s/qrcode/<?=$v->id?>.v<?=time()?>.'jpg'" width="32" height="32" title="<?=$v->openid?>"></a></td>
                  <?php else:?>
                  <td></td>
                  <?php endif;?>
                  <td><?=$v->address?></td>
                  <td><?=$v->type?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->telphone?></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwttbta/qrcodes_edit/<?=$v->id?>">
                      <span>详情</span> <i class="am-icon-edit"></i>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>">
                      <span>删除</span> <i class="am-icon-times"></i>
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
                                    <label for="user-name" class="am-u-sm-12 am-form-label">是否审核通过 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">不通过</li>
                                    <li id="switch-2" class="switch-type purple">通过</li>
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
                      }
                      $('.shadow').fadeIn();
                    });

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });
    $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwttbta/qrcodes_delete/qrcodes/"+id;
    })
  })

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
</script>

