<!-- 卡密配置 -->
<link rel="stylesheet" href="/qwt/assets/css/simditor.css">
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    .am-badge{
        background-color: green;
    }
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
    .typebox{
        overflow: visible;
    }
    .simditor-icon{
      margin-top: 12px;
      display: block !important;
    }
    label{
      text-align: left !important;
    }
</style>
                <script type="text/javascript">
                  $(function () {
                    $(".datepicker").datetimepicker({
                      format: "yyyy-mm-dd hh:ii",
                      language: "zh-CN",
                      minView: "0",
                      autoclose: true
                    });
                  });
                </script>
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            <?=$result['title']?>
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
            <li class="am-active"><?=$result['title']?></li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <?=$result['title']?>
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12 tpl-amazeui-form">
                <div class="tpl-form-body tpl-form-line">
                    <form class="am-form  am-form-horizontal" method="post" enctype="multipart/form-data">
                        <?php if ($result['error']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']?></p>
                              </div>
                            </div>
                          <?php endif?>
                           <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">卡密名称
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="kmi[km_content]" placeholder="输入卡密名称" value="<?=$_POST['kmi']['km_content']?>">
                                </div>
                            </div>
                          <div class="am-form-group">
                              <label for="user-weibo" class="am-u-sm-12 am-form-label">有效期（为空则不限制）</label>
                              <div class="am-u-sm-12" style="float:left">
                                  <input style="width:100%" name="kmi[enddate]" value="<?=$_POST['kmi']['enddate']?date('Y-m-d H:i',$_POST['kmi']['enddate']):''?>" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                              </div>
                          </div>
                          <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">上传卡密
                                    <span class="tpl-form-line-small-title">
                                        <?php if($result['cert_file_exists']) echo ' <span class="label label-warning">已上传</span>'?>
                                    </span>
                                </label>
                                <div class="am-u-sm-12">
                                    <div class="am-form-group am-form-file">
                                        <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                            <i class="am-icon-cloud-upload"></i>
                                            点此上传excel文件（不可超过2MB）
                                        </button>
                                        <input name="pic" id="cert" type="file"  accept="text/csv/xls">
                                    </div>
                                    <div id="cert-list"></div>
                                </div>
                            </div>
                            <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-12 am-form-label">下发卡密时推送的内容（仅支持文本和超链接，不支持换行！）
                              </label>
                              <div class="am-u-sm-12">
                                  <textarea class="textarea" name="kmi[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$_POST['kmi']['km_text']?$_POST['kmi']['km_text']:'亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!'?></textarea>
                              </div>
                            </div>
                        </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>提交</button>
                                    <?php if($act=='edit'):?>
                                     <a class='delete am-btn am-btn-danger' data-id="<?=$_POST['kmi']['id']?>">
                                      <span>删除</span>
                                    </a>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/qwt/assets/js/module.min.js"></script>
<script src="/qwt/assets/js/uploader.min.js"></script>
<script src="/qwt/assets/js/hotkeys.min.js"></script>
<script src="/qwt/assets/js/simditor.min.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker').datetimepicker({
  format: 'yyyy-mm-dd hh:ii'
});
    </script>


<script>
function change(i){
        $('.typebox').hide();
      if(i=='1'){
        $('#wecoupons').show();
      }
      if(i=='2'){
        $('#tab-content').show();
      }
      if(i=='4'){
        $('#hongbao-content').show();
      }
      if(i=='5'){
        $('#yzcoupons').show();
      }
      if (i=='6') {
        $('#yzgift').show();
      }
      if (i=='7') {
        $('#tequan').show();
      }
      if (i=='8') {
        $('#kami').show();
      }
      if (i=='9') {
        $('#freetext').show();
      }
    }
    $('.switch-type').click(function(){
      $('.switch-type').removeClass('green-on');
      $(this).addClass('green-on');
      $('#datatype').val($(this).data('type'));
    });
    $('.delete').click(function(){
      var id = $(this).data('id');
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
        window.location.href = "/qwtkmia/prizes_delete/"+id;
      })
    })
 $(function() {
   $('#cert').on('change', function() {
     var fileNames = '';
     $.each(this.files, function() {
       fileNames += '<span class="am-badge">' + this.name + '</span> ';
     });
     $('#cert-list').html(fileNames);
   });
 });
</script>



