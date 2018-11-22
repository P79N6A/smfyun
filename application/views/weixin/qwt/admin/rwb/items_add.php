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
</style>
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            <?=$result['title']?>
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">任务宝服务号版</a></li>
            <li>奖品设置</li>
            <li>奖品管理</li>
            <li class="am-active"><?=$result['title']?></li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <?=$result['title']?>
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12">
                <div class="tpl-form-body tpl-form-line">
                    <form class="am-form tpl-form-line-form" method="post" enctype="multipart/form-data">
                        <?php
                            $type = $item['type'];
                        ?>
      <?php if($result['action']=='add'):?>
                        <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">请选择奖品类型（保存后不能再修改奖品类型）
                            </label>
                            <div class="am-u-sm-9">
                                <div class="actions" style="float:left">
                                    <input id="datatype" type="hidden" name="type" value="0">
                                    <ul class="actions-btn">
                                        <li id="switch-0" onclick="change(0)" data-type="0" class="switch-type green <?=$type==0||!$type ? 'green-on' : ''?>">实物奖品
                                        </li>
                                        <li id="switch-1" onclick="change(1)" data-type="1" class="switch-type green <?=$type==1 ? 'green-on' : ''?>">微信卡券
                                        </li>
                                        <li id="switch-4" onclick="change(4)" data-type="4" class="switch-type green <?=$type==4 ? 'green-on' : ''?>">微信红包
                                        </li>
                                        <li id="switch-5" onclick="change(5)" data-type="5" class="switch-type green <?=$type==5 ? 'green-on' : ''?>">有赞优惠券
                                        </li>
                                        <li id="switch-6" onclick="change(6)" data-type="6" class="switch-type green <?=$type==6 ? 'green-on' : ''?>">有赞赠品
                                        </li>
                                        <li id="switch-7" onclick="change(7)" data-type="7" class="switch-type green <?=$type==7 ? 'green-on' : ''?>">特权商品
                                        </li>
                                        <li id="switch-8" onclick="change(8)" data-type="8" class="switch-type green <?=$type==8 ? 'green-on' : ''?>">卡密
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
      <?php else:?>
                                    <input id="datatype" type="hidden" name="type" value="<?=$type?>">
                                <?php endif?>
                        <div id="realgoods"  class="row typebox" <?=($type==0||!$type)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">奖品名称
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="shiwu[km_content]" placeholder="输入奖品名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">产品图片 </label>
                                    <div class="am-u-sm-9">
        <?php if ($result['action'] == 'edit' && $item['pic']):?>
            <a href="/qwtrwba/images/item/<?=$result['item']['id']?>.v<?=$item['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtrwba/images/item/<?=$result['item']['id']?>.v<?=$item['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传产品图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic1" accept="image/jpeg"  multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 400KB。</small>

                                    </div>
                                </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">所需付款金额（单位：分）
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" step="1" class="form-control" name="need_money" placeholder="输入金额" value="<?=$item['need_money']?>">
                                </div>
                            </div>
                        </div>
                        <div id="tequan" class="row typebox" <?=($type==7)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">特权商品名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="kmi[km_content]" placeholder="输入特权商品名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">可购买该商品的用户标签
                                    <span class="tpl-form-line-small-title">Tags</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="kmi[value]" placeholder="输入标签名称" value="<?=explode('&',$item['value'])[0]?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">特权商品链接
                                    <span class="tpl-form-line-small-title">Links</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="kmi[url]" placeholder="http://" value="<?=explode('&',$item['value'])[1]?>">
                                </div>
                            </div>
                        </div>
                        <div id="kami" class="row typebox" <?=($type==8)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡密名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="yhm[km_content]" placeholder="输入卡密名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">文件上传 File Uploader
                                    <span class="tpl-form-line-small-title">
                                        <?php if($result['cert_file_exists']) echo ' <span class="label label-warning">已上传</span>'?>
                                    </span>
                                </label>
                                <div class="am-u-sm-9">
                                    <div class="am-form-group am-form-file">
                                        <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                            <i class="am-icon-cloud-upload"></i>
                                            点此上传excel文件(不可超过2MB)
                                        </button>
                                        <input name="pic" id="cert" type="file" multiple>
                                    </div>
                                    <div id="cert-list"></div>
                                </div>
                            </div>
                            <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）
                            </label>
                            <div class="am-u-sm-9">
                                <textarea class="textarea" name="yhm[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$item['km_text']?htmlspecialchars($item['km_text']):'亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!'?></textarea>
                            </div>
                        </div>
                        </div>
                        <div class="row typebox" id="wecoupons" <?=($type==1)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">微信卡券名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="coupon[km_content]" placeholder="输入微信卡劵名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">微信卡券</label>
                                <div class="am-u-sm-9">
                                    <select name="coupon[value]" data-am-selected="{searchBox: 1}">
                                        <?php if($result['wxcards']):?>
                                        <?php foreach ($result['wxcards'] as $wxcard):?>
                                        <option <?=$item['value']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                                        <?php endforeach; ?>
                                        <?php endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row typebox" id="yzcoupons" <?=($type==5)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞优惠券名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="yhq[km_content]" placeholder="请输入有赞优惠券名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group" >
                                <label for="user-phone" class="am-u-sm-3 am-form-label">选择有赞优惠券</label>
                                <div class="am-u-sm-9">
                                    <select name="yhq[value]" data-am-selected="{searchBox: 1}">
                                        <?php if($result['yzcoupons']):?>
                                        <?php foreach ($result['yzcoupons'] as $yzcoupon):?>
                                        <option <?=$item['value']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                                        <?php endforeach; ?>
                                        <?php endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row typebox" id="yzgift" <?=($type==6)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">赠品名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="gift[km_content]" placeholder="输入赠品名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">有赞赠品</label>
                                <div class="am-u-sm-9">
                                    <select name="gift[value]" data-am-selected="{searchBox: 1}">
                                        <?php if($result['yzgifts']):?>
                                        <?php foreach ($result['yzgifts'] as $yzgift):?>
                                        <option <?=$item['value']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
                                        <?php endforeach; ?>
                                        <?php endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="hongbao-content"  class="row typebox" <?=($type==4)?'':'style="display:none"'?>>
                            <div class="tpl-content-scope">
                                <div class="note note-danger">
                                    <p> 添加微信红包奖品时，请按照使用教程完成【绑定我们】-【微信支付】的相关设置！</p>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">红包名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="hongbao[km_content]" placeholder="输入红包名称" value="<?=$item['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">红包金额(单位：分，最低100分)
                                    <span class="tpl-form-line-small-title">Amount</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" step="1" class="form-control" name="hongbao[value]" value="<?=$item['value']?>">
                                </div>
                            </div>
                        </div>
                        <div class="am-u-sm-12" style="padding:0">
                                <hr>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i><?=$result['title']?></button>
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

<?php if($result['error']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['error']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>


                    var editor = new Simditor({
                      textarea: $('.textarea'),
                      toolbar: ['link']
                    });

    $('#datetimepicker').datetimepicker({
  format: 'yyyy-mm-dd hh:ii'
});
    </script>


<script>
function change(i){
        $('.typebox').hide();
      if(i=='0'){
        $('#realgoods').show();
      }
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
        <?php if ($result['yz']==1):?>
        $('#yzcoupons').show();
        <?php else:?>
    swal({
    title: "不能选择有赞优惠券",
    text: "您还没有进行有赞授权，不能选择有赞相关奖品，您可以前往主菜单“绑定我们”->“有赞一键授权”进行授权",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '我知道了',
    confirmButtonText: '立刻前往',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwta/yzoauth";
    })
    <?php endif?>

      }
      if (i=='6') {
        <?php if ($result['yz']==1):?>
        $('#yzgift').show();
              <?php else:?>
    swal({
    title: "不能选择有赞赠品",
    text: "您还没有进行有赞授权，不能选择有赞相关奖品，您可以前往主菜单“绑定我们”->“有赞一键授权”进行授权",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '我知道了',
    confirmButtonText: '立刻前往',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwta/yzoauth";
    })
    <?php endif?>
      }
      if (i=='7') {
        $('#tequan').show();
      }
      if (i=='8') {
        $('#kami').show();
      }
    }
    $('.switch-type').click(function(){
      $('.switch-type').removeClass('green-on');
      $(this).addClass('green-on');
      $('#datatype').val($(this).data('type'));
    });
    $('#switch-on').click(function(){
        $('#show0').val(1);
      $('#switch-on').addClass('green-on');
      $('#switch-off').removeClass('red-on');
    })
    $('#switch-off').click(function(){
        $('#show0').val(0);
      $('#switch-on').removeClass('green-on');
      $('#switch-off').addClass('red-on');
    })
    $('#delete').click(function(){
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
      window.location.href = "/qwtrwba/items_delete/<?=$result['item']['id']?>";
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


