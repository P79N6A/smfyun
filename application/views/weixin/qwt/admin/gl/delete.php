
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            清空楼层
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">微信盖楼</a></li>
            <li class="am-active">清空楼层</li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    清空楼层
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12">
                <div class="tpl-form-body tpl-form-line">
                        <?php if ($success =='delete' ):?>
                            <div class="tpl-content-scope">
                              <div class="note note-info">
                                <p> 清空楼层成功！</p>
                              </div>
                            </div>
                          <?php endif?>
                        <form fole="form" method="post" >
                          <input type="hidden" name='delete' />
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> 当前楼层<?=$lou_count?>,点击下面按钮会清空当前参与活动的楼层，请谨慎操作</p>
                              </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-danger"><i class="fa fa-edit"></i>点击清空当前活动楼层</button>
                                </div>
                            </div>
                            </form>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
