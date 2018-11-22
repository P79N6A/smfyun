<style type="text/css">
  th,td{
    white-space: nowrap;
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
  label{
    text-align: left !important;
  }
</style>
  <script src="http://jfb.dev.smfyun.com/qwt/clipboard/clipboard.min.js"></script>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                预约分组管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">预约宝</a></li>
                <li><a>预约分组管理</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$countall?> 个预约分组
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtyyba/appointment_add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 新建预约分组</a>
                        </div>
                        <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="搜索">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                      <div class="am-u-sm-12" style="overflow:scroll;">
                          <form class="am-form">
                              <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                  <thead>
                                      <tr>
                  <th>新建时间</th>
                  <th>预约分组名称</th>
                  <th>已预约人数</th>
                  <th>预约链接</th>
                  <th>复制链接</th>
                  <th>操作</th>
                          </tr>
                      </thead>
                      <tbody>
                <?php
                foreach ($result['appointments'] as $appointment):
                  $num=ORM::factory('qwt_yybrecord')->where('bid','=',$bid)->where('aid','=',$appointment->id)->count_all();
                ?>
                <tr>
                  <td><?=date('Y-m-d H:i:s',$appointment->jointime)?></td>
                  <td><?=$appointment->name?></td>
                  <td><?=$num?></td>
                  <td><span id="copy<?=$appointment->id?>">http://<?=$_SERVER['HTTP_HOST']?>/qwtyyb/storefuop/<?=base64_encode($bid.'$'.$appointment->id)?></span></td>
                  <td nowrap="">
                  <button id="copybutton<?=$appointment->id?>" type="button" style="background-color:#fff;" class='copy am-btn am-btn-default am-btn-xs am-text-secondary' data-clipboard-action="copy" data-clipboard-target="#copy<?=$appointment->id?>">
                      <span>点击复制</span> <i class="am-icon-copy"></i>
                    </button>
                    </td>
                  <td nowrap="">
                    <a style="background-color:#fff;" data-name="<?=$appointment->name?>" data-id="<?=$appointment->id?>" class='edit am-btn am-btn-default am-btn-xs am-text-secondary'>
                      <span>修改</span> <i class="am-icon-edit"></i>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$appointment->id?>" >
                      <span>删除</span> <i class="am-icon-times"></i>
                    </a>
                  </td>
                  <script type="text/javascript">
var clipboard<?=$appointment->id?> = new Clipboard('#copybutton<?=$appointment->id?>');
clipboard<?=$appointment->id?>.on('success', function(e) {
  alert('复制成功！');
});
                  </script>
                </tr>
              <?php endforeach?>
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
 <div class="shadow kmi" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="font-green bold nickname">
                      预约分组修改
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
                            <label for="shiptype" class="am-u-sm-3 am-form-label">预约分组名称：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="name" name="appointment[name]" style="width:150px" type="text" value="">
                            </div>
                          </div>
    <input type="hidden" name="appointment[id]" id="id">
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
  $('#name').val(name);
  $('#id').val(id);
  $('.kmi').fadeIn();
});
//   $('.copy').click(function(){
//   var str = $(this).childrean('input');
//   str.select();
//   document.execCommand("Copy");
//   alert("已复制好，可贴粘。");
// });
$('.close').click(function(){
    $('.shadow').fadeOut();
})
    $('.delete').click(function(){
      var aid = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "删除后不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtyyba/appointment?flag=delete&aid="+aid;
    })
  })
</script>
