<?
  if(!$item['type']) $item['type']=0;
?>
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
    label{
      text-align: left !important;
    }
    .simditor-icon{
      margin-top: 12px;
      display: block !important;
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
                    // var editor = new Simditor({
                    //   textarea: $('.textarea'),
                    //   toolbar: ['link']
                    // });
                    // var editor = new Simditor({
                    //   textarea: $('.textarea2'),
                    //   toolbar: ['link']
                    // });
                    // var editor = new Simditor({
                    //   textarea: $('.textarea3'),
                    //   toolbar: ['link']
                    // });
                    // var editor = new Simditor({
                    //   textarea: $('.textarea4'),
                    //   toolbar: ['link']
                    // });
                    // var editor = new Simditor({
                    //   textarea: $('.textarea5'),
                    //   toolbar: ['link']
                    // });
                    // var editor = new Simditor({
                    //   textarea: $('.textarea6'),
                    //   toolbar: ['link']
                    // });
                  });
                </script>
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            奖品设置
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">微信盖楼</a></li>
            <li class="am-active">奖品设置</li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    奖品设置
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12">
                <div class="tpl-form-body tpl-amazeui-form">
                    <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
                        <?php
                            $type = $item['type'];
                        ?>
      <?php if($result['action']=='add'):?>
                        <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">请选择奖品类型（保存后不能再修改奖品类型）
                            </label>
                            <div class="am-u-sm-12">
                                <div class="actions" style="float:left">
                                    <input id="datatype" type="hidden" name="type" value="<?=$type?>">
                                    <ul class="actions-btn">
                                    
                                        <li id="switch-0" onclick="change(0)" data-type="0" class="switch-type green <?=$type==0||!$type ? 'green-on' : ''?>">实物奖品
                                        </li>
                                
                                        <li id="switch-4" onclick="change(4)" data-type="4" class="switch-type green <?=$type==4 ? 'green-on' : ''?>">微信红包
                                        </li>
                                        <li id="switch-5" onclick="change(5)" data-type="5" class="switch-type green <?=$type==5 ? 'green-on' : ''?>">有赞优惠券
                                        </li>
                                        <li id="switch-6" onclick="change(6)" data-type="6" class="switch-type green <?=$type==6 ? 'green-on' : ''?>">有赞赠品
                                        </li>
                                        <!-- <li id="switch-7" onclick="change(7)" data-type="7" class="switch-type green <?=$type==7 ? 'green-on' : ''?>">特权商品
                                        </li> -->
                                        <!-- <li id="switch-8" onclick="change(8)" data-type="8" class="switch-type green <?=$type==8 ? 'green-on' : ''?>">卡密
                                        </li> -->
                                        <!-- <li id="switch-9" onclick="change(9)" data-type="8" class="switch-type green <?=$type==9 ? 'green-on' : ''?>">自定义回复文本
                                        </li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                      <?php else:?>
                                    <input id="datatype" type="hidden" name="type" value="<?=$type?>">
                                  <?php endif?>
                        <!-- <div id="tequan" class="am-form-group typebox" <?=($type==7)?'':'style="display:none"'?>>
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
                        </div> -->
                        <!-- <div id="kami" class="am-form-group typebox" <?=($type==8)?'':'style="display:none"'?>>
                          <div class="am-form-group">
                              <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期（为空则不限制）</label>
                              <div class="am-u-sm-3" style="float:left">
                                  <input style="width:100%" name="kmi[enddate]" value="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                              </div>
                          </div>
                          <?php if ($result['error']['kmi']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']['kmi']?></p>
                              </div>
                            </div>
                          <?php endif?>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡密名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="kmi[km_content]" placeholder="输入卡密名称" value="<?=$kmi['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡密数量
                                    <span class="tpl-form-line-small-title">Num</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="kmi[km_num]" placeholder="输入卡密数量" value="<?=$kmi['km_num']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡密限购
                                    <span class="tpl-form-line-small-title">Limit</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="kmi[km_limit]" placeholder="输入卡密限购数量" value="<?=$kmi['km_limit']?>">
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
                                            点此上传excel文件
                                        </button>
                                        <input name="pic" id="pic" type="file"  accept="text/csv/xls">
                                    </div>
                                    <div id="cert-list"></div>
                                </div>
                            </div>
                            <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea" name="kmi[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!</textarea>
                              </div>
                            </div>
                        </div> -->
                        <!-- <div class="am-form-group typebox" id="wecoupons" <?=($type==1||!$type)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期（为空则不限制）</label>
                                <div class="am-u-sm-3" style="float:left">
                                    <input style="width:100%" name="coupon[enddate]" value="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                </div>
                            </div>
                          <?php if ($result['error']['coupon']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']['coupon']?></p>
                              </div>
                            </div>
                          <?php endif?>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">微信卡券名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="coupon[km_content]" placeholder="输入微信卡劵名称" value="<?=$coupon['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡券数量
                                    <span class="tpl-form-line-small-title">Num</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="coupon[km_num]" placeholder="输入卡券数量" value="<?=$coupon['km_num']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">卡券限购
                                    <span class="tpl-form-line-small-title">Limit</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="coupon[km_limit]" placeholder="输入卡券限购数量" value="<?=$coupon['km_limit']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">微信卡券</label>
                                <div class="am-u-sm-9">
                                    <select name="coupon[value]" data-am-selected="{searchBox: 1}">
                  <?php if($wxcards):?>
                  <?php foreach ($wxcards as $wxcard):?>
                  <option <?=$_POST['data']['url']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                  <?php endforeach; ?>
                   <?php endif;?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea2" name="coupon[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">赶紧点击链接「%s」领取卡券吧</textarea>
                              </div>
                            </div>
                        </div> -->
                        <div class="am-form-group typebox" id="yzcoupons" <?=($type==5)?'':'style="display:none"'?>>
                            <!-- <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期（为空则不限制）</label>
                                <div class="am-u-sm-3" style="float:left">
                                    <input style="width:100%" name="yhq[enddate]" value="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                </div>
                            </div>
                          <?php if ($result['error']['coupon']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']['coupon']?></p>
                              </div>
                            </div>
                          <?php endif?>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞优惠券名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="yhq[km_content]" placeholder="请输入有赞优惠券名称" value="<?=$yhq['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞优惠券数量
                                    <span class="tpl-form-line-small-title">Num</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="yhq[km_num]" placeholder="输入有赞优惠券数量" value="<?=$yhq['km_num']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞优惠券限购
                                    <span class="tpl-form-line-small-title">Limit</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="yhq[km_limit]" placeholder="输入有赞优惠券限购数量" value="<?=$yhq['km_limit']?>">
                                </div>
                            </div> -->
                            <div class="am-form-group" >
                                <label for="user-phone" class="am-u-sm-3 am-form-label">选择有赞优惠券</label>
                                <div class="am-u-sm-12">
                                    <select name="item[groupid]" data-am-selected="{searchBox: 1}">
                      <?php if($coupon['response']['coupons']){?>
                        <?php foreach ($coupon['response']['coupons'] as $coupon):?>
                            <option <?=$item['code']==$coupon['fetch_url']?'selected':''?> value="<?=$coupon['group_id']?>"><?=$coupon['title']?></option>
                        <?php endforeach;?>
                      <?php }?>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea3" name="yhq[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">优惠券已下发到您账户，请查收!</textarea>
                              </div>
                            </div> -->
                        </div>
                        <div class="am-form-group typebox" id="yzgift" <?=($type==6)?'':'style="display:none"'?>>
                            <!-- <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期（为空则不限制）</label>
                                <div class="am-u-sm-3" style="float:left">
                                    <input style="width:100%" name="gift[enddate]" value="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                </div>
                            </div>
                          <?php if ($result['error']['yhq']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']['yhq']?></p>
                              </div>
                            </div>
                          <?php endif?>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">赠品名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="gift[km_content]" placeholder="输入赠品名称" value="<?=$gift['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞赠品数量
                                    <span class="tpl-form-line-small-title">Num</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="gift[km_num]" placeholder="输入有赞赠品数量" value="<?=$gift['km_num']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">有赞赠品限购
                                    <span class="tpl-form-line-small-title">Limit</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="gift[km_limit]" placeholder="输入有赞赠品限购数量" value="<?=$gift['km_limit']?>">
                                </div>
                            </div> -->
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-12 am-form-label">有赞赠品选择</label>
                                <div class="am-u-sm-12">
                                    <select name="item[presentid]" data-am-selected="{searchBox: 1}">

                    <?php if($gift['response']['presents']){?>
                        <?php foreach ($gift['response']['presents'] as $gift):?>
                            <option <?=$item['code']==$gift['present_id']?'selected':''?> value="<?=$gift['present_id']?>"><?=$gift['title']?></option>
                        <?php endforeach;?>
                    <?php }?>
                                    </select>
                                </div>
                            </div><!--
                            <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">下发赠品时推送的内容（仅支持加入文本和超链接，不要换行！）
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea4" name="gift[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">赠品已下发，请查收</textarea>
                              </div>
                            </div> -->
                        </div>
                        <div id="shiwu-content"  class="am-form-group typebox" <?=($type==0)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">实物奖品名称
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[shiwuname]" placeholder="输入实物奖品名称" value="<?=$item['name']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-12 am-form-label">产品图片 </label>
                                    <div class="am-u-sm-12">
        <?php if ($result['action'] == 'edit' && $item['pic']):?>
            <a href="/qwtgla/images/item/<?=$item['id']?>.v<?=$item['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtgla/images/item/<?=$item['id']?>.v<?=$item['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传产品图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic1" accept="image/jpeg"  multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 400KB。</small>

                                    </div>
                                </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">所需付款金额（单位：分，为0则不设置；请按照教程完成相关设置。）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" name="item[need_money]" placeholder="输入金额" value="<?=$item['need_money']?>">
                                </div>
                            </div>
                        </div>
                        <div id="hongbao-content"  class="am-form-group typebox" <?=($type==4)?'':'style="display:none"'?>>
                            <!-- <div class="am-form-group">
                                <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期（为空则不限制）</label>
                                <div class="am-u-sm-3" style="float:left">
                                    <input style="width:100%" name="hongbao[enddate]" value="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                </div>
                            </div>
                          <?php if ($result['error']['hongbao']):?>
                            <div class="tpl-content-scope">
                              <div class="note note-danger">
                                <p> <?=$result['error']['hongbao']?></p>
                              </div>
                            </div>
                          <?php endif?> -->
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">红包名称
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[title]" placeholder="输入红包名称" value="<?=$item['name']?>">
                                </div>
                            </div>
                           <!--  <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">红包数量
                                    <span class="tpl-form-line-small-title">Num</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="hongbao[km_num]" placeholder="输入红包数量" value="<?=$hongbao['km_num']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">红包限购
                                    <span class="tpl-form-line-small-title">Limit</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="hongbao[km_limit]" placeholder="输入红包限购数量" value="<?=$hongbao['km_limit']?>">
                                </div>
                            </div> -->
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">红包金额(单位：分，最低100分)
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" name="item[code]" value="<?=$item['code']?>">
                                </div>
                            </div>
                            <!-- <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">下发红包时推送的内容（仅支持加入文本和超链接，不要换行！）
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea5" name="hongbao[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">恭喜发财，红包已发送，请查收!</textarea>
                              </div>
                            </div> -->
                        </div><!--
                        <div id="freetext"  class="am-form-group typebox" <?=($type==9)?'':'style="display:none"'?>>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">文本消息名称：
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="form-control" name="freedom[km_content]" placeholder="输入文本名称" value="<?=$freedom['km_content']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                              <label for="user-name" class="am-u-sm-3 am-form-label">个性化文本消息（仅支持加入文本和超链接，不要换行！）：
                              </label>
                              <div class="am-u-sm-9">
                                  <textarea class="textarea5" name="freedom[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">感谢小主在我家店铺购买!</textarea>
                              </div>
                            </div>
                            </div> -->
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">请输入该奖品中奖回复文案
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[word]" value="<?=htmlspecialchars($item['word'])?>">
                                </div>
                            </div>
                        <div class="am-u-sm-12" style="padding:0">
                                <hr>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                      <input name='item[id]' type="hidden" value="<?=$item['id']?>">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存奖品设置</button>
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
      if(i=='0'){
        $('#shiwu-content').show();
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
      window.location.href = "/qwtwfba/items/edit/<?=$result['item']['id']?>?DELETE=1";
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


