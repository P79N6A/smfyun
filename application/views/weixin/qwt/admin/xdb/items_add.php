
    <link rel="stylesheet" href="/qwt/assets/css/simditor.css">
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
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
                添加新奖品
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
                <li>奖品设置</li>
                <li>奖品管理</li>
                <li class="am-active"><?=$result['title']?></li>
            </ol>
            <div class="tpl-portlet-components" style="overflow:-webkit-paged-y">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            <?=$result['title']?>
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">兑换截止时间（为空则不限制）</label>
                                    <div class="datetimepickerbox am-u-sm-12">
  <input id="datetimepicker" size="16" type="text" name="data[endtime]" size="16" value="<?=$_POST['data']['endtime']?>" readonly="" class="am-form-field" readonly>
                </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">是否在列表中显示</label>
                                    <div class="am-u-sm-12">
                            <div class="switch-box">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$_POST['data']['show'] == 1 || $_POST['data']['show']!=='0' ? 'green-on' : ''?>">显示</li>
                                    <li id="switch-off" class="red <?=$_POST['data']['show'] === "0" ? 'red-on' : ''?>">隐藏</li>
            <input type="hidden" name="data[show]" id="show0" value="<?=$_POST['data']['show'] == 1 || $_POST['data']['show']!=='0' ? '1' : '0'?>">
                                </ul>
                            </div>
                            </div>
                </div>
      <?php
      $type = $_POST['data']['type'];
      ?>
      <?php if($result['action']=='add'):?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">请选择奖品类型（保存后不能再修改奖品类型） </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-0" onclick="change(0)" class="switch-type green <?=$type==0 ? 'green-on' : ''?>">实物奖品</li>
                                    <li id="switch-1" onclick="change(1)" class="switch-type green <?=$type==1 ? 'green-on' : ''?>">微信卡券</li>
                                    <?php if ($bid==111320||$bid==6):?>
                                    <!-- <li id="switch-2" onclick="change(2)" class="switch-type green <?=$type==2 ? 'green-on' : ''?>">虚拟奖品</li> -->
                                <?php endif?>
                                    <!-- <li id="switch-3" onclick="change(3)" class="switch-type green <?=$type==3 ? 'green-on' : ''?>">话费流量</li> -->
                                    <li id="switch-4" onclick="change(4)" class="switch-type green <?=$type==4 ? 'green-on' : ''?>">微信红包</li>
                                    <li id="switch-5" onclick="change(5)" class="switch-type green <?=$type==5 ? 'green-on' : ''?>">有赞优惠券</li>
                                    <li id="switch-6" onclick="change(6)" class="switch-type green <?=$type==6 ? 'green-on' : ''?>">有赞赠品</li>
                                    <!-- <li id="switch-7" onclick="change(7)" class="switch-type green <?=$type==7 ? 'green-on' : ''?>">特权商品</li> -->
            <input type="hidden" id="data_type" name="data[type]" value="0">
                        </ul>
                            </div>
                            </div>
                            </div>
                        <?php else:?>
            <input type="hidden" id="data_type" name="data[type]" value="<?=$type?>">
        <?php endif?>

                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">优先级（数字越大越靠前）</label>
                                    <div class="am-u-sm-12">
          <input type="number" class="form-control" id="pri" name="data[pri]" placeholder="展示优先级" value="<?=intval($_POST['data']['pri'])?>">
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">奖品名称 </label>
                                    <div class="am-u-sm-12">
        <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入奖品名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-12 am-form-label">奖品图片 </label>
                                    <div class="am-u-sm-12">
        <?php if ($result['action'] == 'edit' && $result['item']['pic']):?>
                  <a href="/qwtxdba/images/item/<?=$result['item']['id']?>.v<?=$result['item']['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtxdba/images/item/<?=$result['item']['id']?>.v<?=$result['item']['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传奖品图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic" accept="image/jpeg" class="form-control" multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 300KB。</small>

                                    </div>
                                </div>
           <!--  <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p> 注意事项：
1、请根据积分奖励的规则，核算单个奖品兑换所需要的积分数量，单个奖品兑换的门槛不能过低，兑换门槛过低会导致奖品被刷，特别是单个奖品兑换所需要的积分要高于用户首次关注奖励的积分；
2、剩余数量即库存，请控制每天的数量，按照情况少量添加；
3、每人限购请设置为1；
4、请商户按照上述要求操作，因为运营操作不当、兑换门槛过低导致的损失，与我方无关；</p>
                </div>
            </div> -->
            <div class="am-form-group">
                <div class="am-u-sm-12">
                                <div class="am-form-group">
                                    <div>
                                    <label for="user-weibo" class="am-form-label">剩余数量</label>
          <input type="number" class="form-control" id="stock" name="data[stock]" placeholder="输入库存数量" value="<?=intval($_POST['data']['stock'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="user-weibo" class="am-form-label">奖品原价</label>
          <input type="number" class="form-control" id="price" name="data[price]" placeholder="市场价" value="<?=floatval($_POST['data']['price'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-12">
                                <div class="am-form-group">
                                    <div>
                                    <label for="user-weibo" class="am-form-label">消耗兑换券</label>
          <input type="number" class="form-control" id="score" name="data[score]" placeholder="消耗兑换券数量" value="<?=intval($_POST['data']['score'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-12">
                                <div class="am-form-group">
                                    <div>
                                    <label for="user-weibo" class="am-form-label">单个用户限换数量（为零则不限购）</label>
          <input type="number" class="form-control" id="limit" name="data[limit]" placeholder="单个用户限换数量" value="<?=intval($_POST['data']['limit'])?>">
                                    </div>
                                </div>
                </div>
            </div>
                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">每人限购几件（为0则不限购）</label>
                                    <div class="am-u-sm-12">
          <input type="number" class="form-control" id="score" name="data[limit]" placeholder="消耗积分数量" value="<?=intval($_POST['data']['limit'])?>">
                                    </div>
                                </div> -->

                                <div class="am-form-group typebox" id="realgifts" <?=($type==0)? '' : 'style="display:none;"'?>>
                                    <label id="explain" for="payment" class="am-u-sm-12 am-form-label">所需付款金额（单位：分，为0则不设置；请按照教程完成相关设置。）</label>
                                    <div class="am-u-sm-12">
          <input type="number" step="0.01" class="form-control shiwu" id="payment" name="data[need_money]"  placeholder="" value="<?=intval($_POST['data']['need_money'])?>">
                                    </div>
                                </div>

                                <div class="am-form-group typebox" id="tab-content" <?=($type==2)? '' : 'style="display:none;"'?>>
                                    <label id="explain" for="user-name" class="am-u-sm-12 am-form-label">虚拟产品url</label>
                                    <div class="am-u-sm-12">
          <input type="text" class="form-control zengpin" id="url" name="data[url]"  placeholder="http://" value="<?=htmlspecialchars($_POST['data']['url'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="tequanshangpin" <?=($type==7)? '' : 'style="display:none;"'?>>
                                    <div class="am-form-group">
                                        <label for="user-name" class="am-u-sm-3 am-form-label">特权商品名称
                                        </label>
                                        <div class="am-u-sm-9">
                                            <input type="text" class="form-control" name="data[km_content]" placeholder="输入特权商品名称" value="<?=htmlspecialchars($_POST['data']['km_content'])?>">
                                        </div>
                                    </div>
                                    <div class="am-form-group">
                                        <label for="user-name" class="am-u-sm-3 am-form-label">可购买该商品的用户标签
                                        </label>
                                        <div class="am-u-sm-9">
                                            <input type="text" class="form-control" name="data[value]" placeholder="输入标签名称" value="<?=htmlspecialchars($_POST['data']['value'])?>">
                                        </div>
                                    </div>
                                    <div class="am-form-group">
                                        <label for="user-name" class="am-u-sm-3 am-form-label">特权商品链接
                                        </label>
                                        <div class="am-u-sm-9">
                                            <input type="text" class="form-control" name="data[url]" placeholder="http://" value="<?=htmlspecialchars($_POST['data']['url'])?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="wecoupons" <?=($type==1)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">微信卡券</label>
                                    <div class="am-u-sm-12">
                                        <select name="wecoupons" data-am-selected="{searchBox: 1}">
                                  <?php if($result['wxcards']):?>
                                  <?php foreach ($result['wxcards'] as $wxcard):?>
                                  <option <?=$_POST['data']['url']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                                  <?php endforeach; ?>
                                   <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="yzcoupons" <?=($type==5)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">有赞优惠券</label>
                                    <div class="am-u-sm-12">
                                        <select name="yzcoupons" data-am-selected="{searchBox: 1}">
                                  <?php if($result['yzcoupons']):?>
                                  <?php foreach ($result['yzcoupons'] as $yzcoupon):?>
                                  <option <?=$_POST['data']['url']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                                  <?php endforeach; ?>
                                   <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="yzgift" <?=($type==6)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">有赞赠品</label>
                                    <div class="am-u-sm-12">
                                        <select name="yzgift" data-am-selected="{searchBox: 1}">
                                    <?php if($result['yzgifts']):?>
                                    <?php foreach ($result['yzgifts'] as $yzgift):?>
                                    <option <?=$_POST['data']['url']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
                                    <?php endforeach; ?>
                                     <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                            <div id="hongbao-content" class="tpl-content-scope" <?=($type==4)?'':'style="display:none"'?>>
                                <div class="note note-danger">
                                    <p> 添加微信红包奖品时，请按照使用教程完成【绑定我们】-【微信支付】的相关设置！</p>
                                </div>
                                <div class="am-form-group">
                                    <div>
                                    <label for="user-weibo" class="am-form-label">红包金额（单位：分，最低1块）</label>
          <input type="number" class="form-control" id="price" name="data[price]" placeholder="市场价" value="<?=floatval($_POST['data']['price'])?>">
                                    </div>
                                </div>
                            </div>
                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">详细说明 <span class="tpl-form-line-small-title">Detail</span></label>
                                    <div class="am-u-sm-12">
        <textarea class="textarea" id="desc" name="data[desc]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=htmlspecialchars($_POST['data']['desc'])?></textarea>
                                    </div>
                                </div> -->
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
                      toolbar: ['link','title','bold','italic','underline','strikethrough','fontScale','color','ol','ul','blockquote','code','table','link','hr','indent','outdent','alignment']
                    });

    $('#datetimepicker').datetimepicker({
  format: 'yyyy-mm-dd hh:ii'
});
    </script>


<script>
function change(i){
        $('.typebox').hide();
        $('#hongbao-content').hide();
      if(i=='0'){
                                <?php if ($config->bid==6):?>
                $('#realgifts').show();
                <?php endif?>
              $('.switch-type').removeClass('green-on');
              $('#switch-0').addClass('green-on');
              $('#data_type').val(0);
          }
      if(i=='1'){
        <?php if ($result['wx']==1):?>
                $('#wecoupons').show();
              $('.switch-type').removeClass('green-on');
              $('#switch-1').addClass('green-on');
              $('#data_type').val(1);
        <?php else:?>
    swal({
    title: "不能选择微信卡券",
    text: "您还没有进行微信授权，不能选择微信相关奖品，您可以前往主菜单“绑定我们”->“微信一键授权”进行授权",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '我知道了',
    confirmButtonText: '立刻前往',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwta/oauth";
    })
    <?php endif?>
      }
      if(i=='2'){
        $('#tab-content').show();
              $('.switch-type').removeClass('green-on');
              $('#switch-2').addClass('green-on');
              $('#data_type').val(2);
      }
      if(i=='3'){
              $('.switch-type').removeClass('green-on');
              $('#switch-3').addClass('green-on');
              $('#data_type').val(3);
      }
      if(i=='4'){
        <?php if ($result['wx']==1):?>
        $('#hongbao-content').show();
              $('.switch-type').removeClass('green-on');
              $('#switch-4').addClass('green-on');
              $('#data_type').val(4);
              <?php else:?>
    swal({
    title: "不能选择微信红包",
    text: "您还没有进行微信授权，不能选择微信相关奖品，您可以前往主菜单“绑定我们”->“微信一键授权”进行授权",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '我知道了',
    confirmButtonText: '立刻前往',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwta/oauth";
    })
    <?php endif?>
      }
      if(i=='5'){
        <?php if ($result['yz']==1):?>
        $('#yzcoupons').show();
              $('.switch-type').removeClass('green-on');
              $('#switch-5').addClass('green-on');
              $('#data_type').val(5);
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
              $('.switch-type').removeClass('green-on');
              $('#switch-6').addClass('green-on');
              $('#data_type').val(6);
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
      if(i=='7'){
        $('#tequanshangpin').show();
              $('.switch-type').removeClass('green-on');
              $('#switch-7').addClass('green-on');
              $('#data_type').val(7);
        }
    }


    $('#switch-on').click(function(){
      $('#switch-on').addClass('green-on');
      $('#switch-off').removeClass('red-on');
      $('#show0').val(1);
    })
    $('#switch-off').click(function(){
      $('#switch-on').removeClass('green-on');
      $('#switch-off').addClass('red-on');
      $('#show0').val(0);
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
      window.location.href = "/qwtxdba/items/edit/<?=$result['item']['id']?>?DELETE=1";
    })
  })
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


