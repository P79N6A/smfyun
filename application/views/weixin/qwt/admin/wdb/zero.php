
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                积分清零
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝订阅号版</a></li>
                <li>可选功能</li>
                <li class="am-active">积分清零</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            您确定将所有的用户积分清零？
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 仅清空用户积分，用户关系保留，请商户谨慎处理。</p>
                                <p>注意：积分清零后，兑换中心-总积分归零，粉丝数保留；我的积分之前的积分记录删除。</p>
                            </div>
                        </div>
                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="#" class="am-btn am-btn-danger" id="delete" data-toggle="modal" data-target="#deleteModel">清空积分</a>
                  <!-- <div class="modal modal-danger" id="deleteModel">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <h4 class="modal-title">确认要清空用户积分吗？该操作不可恢复！</h4>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
                          <a href="<?php echo URL::site("qwtwdba/empty")?>?DELETE=1" class="btn btn-outline">确认清空</a>
                        </div>
                      </div>
                    </div>
                  </div> -->
                        </div>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">
document.querySelector('#delete').onclick = function(){
    swal({
        title: "您确认吗？",
        text: "确认要清空用户积分吗？该操作不可恢复！",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '确认清空',
        cancelButtonText: '取消',
        closeOnConfirm: false
    },
    function(){
        window.location.href = "<?php echo URL::site("qwtwdba/empty")?>?DELETE=1";
    });
};
</script>
