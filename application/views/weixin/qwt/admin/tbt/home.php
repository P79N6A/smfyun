
<style type="text/css">
    label{
        text-align: left !important;
    }
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                导入
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">蒙牛数据开发</a></li>
                <li class="am-active">导入</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>导入</span>
                            </div>
                        </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                        <div class="am-tabs-bd">
                        <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                        <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                 <div class="am-u-sm-12">
                                    <div class="am-form-group am-form-file">
                                        <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                            <i class="am-icon-cloud-upload"></i>
                                            点此上传excel文件（不可超过2MB）
                                        </button>
                                        <div id="file-pic" style="display:inline-block;"></div>
                                        <input name="pic" id="cert" type="file"  accept="text/csv/xls">
                                    </div>
                                    <div id="cert-list"></div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">导入</button>
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
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
</script>
