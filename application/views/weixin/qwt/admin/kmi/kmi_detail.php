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
                卡密详情
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">卡密详情</li>
            </ol>
            <form method="post">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        卡密详情
                    </div>
                    </div>
                </div>
                 <?php if ($result['error']):?>
                <div class="tpl-content-scope">
                  <div class="note note-danger">
                    <p> <?=$result['error']?></p>
                  </div>
                </div>
              <?php endif?>
                <div class="tpl-block">
                <div class="am-g">
                    <div class="am-u-sm-12">
                    <table class="am-table am-table-striped am-table-hover table-main">
                        <thead>
                    <tr>
                        <th>添加时间</th>
                        <th>卡密名称</th>
                        <th>卡密内容(第一列)</th>
                        <th>卡密内容(第二列)</th>
                        <th>卡密内容(第三列)</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                <?php
                 foreach ($result['kmis'] as $kmi):
                ?>
                    <tr>
                        <td><?=$kmi->jointime?date('Y-m-d H:i:s',$kmi->jointime):date('Y-m-d H:i:s',$kmi->startdate)?></td>
                        <td><?=$prize->km_content?></td>
                        <td><?=$kmi->password1?></td>
                        <td><?=$kmi->password2?></td>
                        <td><?=$kmi->password3?></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" data-name="<?=$prize->km_content?>" data-id="<?=$kmi->id?>" data-one="<?=$kmi->password1?>" data-two="<?=$kmi->password2?>" data-three="<?=$kmi->password3?>" class='edit am-btn am-btn-default am-btn-xs am-text-secondary'>
                      <span>修改</span> <i class="am-icon-edit"></i>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-pid="<?=$prize->id?>" data-id="<?=$kmi->id?>">
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
                                    <?=$pages?>
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
 <div class="shadow kmi" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="font-green bold nickname">
                      卡密名称
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
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">卡密内容（第一列）：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="pass1" name="kmi[password1]" style="width:150px" type="text" value="">
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">卡密内容（第二列）：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="pass2" name="kmi[password2]" maxlength="20" style="width:150px" type="text" value="">
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">卡密内容（第三列）：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="pass3" name="kmi[password3]" maxlength="20" style="width:150px" type="text" value="">
                            </div>
                          </div>
    <input type="hidden" name="id" id="id">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">提交</button>
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
  var id = $(this).data('id');
  var name = $(this).data('name');
  var pass1 = $(this).data('one');
  var pass2 = $(this).data('two');
  var pass3 = $(this).data('three');
  $('.nickname').text(name);
  $('#pass1').val(pass1);
  $('#pass2').val(pass2);
  $('#pass3').val(pass3);
  $('#id').val(id);
  $('.kmi').fadeIn();
});
$('.close').click(function(){
    $('.shadow').fadeOut();
})
    $('.delete').click(function(){
      var id = $(this).data('id');
      var pid = $(this).data('pid');
  swal({
    title: "确认要删除吗？",
    text: "删除后可在回收站中恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtkmia/kmi_delete/"+pid+"/"+id;
    })
  })
</script>



