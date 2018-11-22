    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                回收站
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">回收站</li>
            </ol>
            <form method="post">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        回收站
                    </div>
                    </div>
                </div>
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
                    $prize=ORM::factory('qwt_kmiprize')->where('bid','=',$bid)->where('value','=',$kmi->startdate)->find();
                ?>
                    <tr>
                        <td><?=$kmi->jointime?date('Y-m-d H:i:s',$kmi->jointime):date('Y-m-d H:i:s',$kmi->startdate)?></td>
                        <td><?=$prize->km_content?></td>
                        <td><?=$kmi->password1?></td>
                        <td><?=$kmi->password2?></td>
                        <td><?=$kmi->password3?></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='recove am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$kmi->id?>">
                      <span>恢复</span> <i class="am-icon-times"></i>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$kmi->id?>">
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
<script type="text/javascript">
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
      window.location.href = "/qwtkmia/kmi_realdelete/"+id;
    })
  })
     $('.recove').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要恢复吗？",
    text: "回复后该卡密会恢复到删除前的状态！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认恢复',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtkmia/kmi_recover/"+id;
    })
  })
</script>



