
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
    <style type="text/css">
    .am-badge{
        background-color: green;
    }
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
        display: inline-block;
    }
    .help-block{
    color: #999;
    font-weight: normal;
    font-size: 14px;

    }
    label{
        text-align: left !important;
    color: #999;
    font-weight: normal;
    font-size: 14px;
    }
    .hide{
        height: 0;
        overflow: hidden;
    }
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
    .inputtxt{
        width: 50px !important;
        display: inline-block !important;
    }
    .switch-content-1,.switch-content-2{
  -webkit-transition: all 0.5s ease;
  -moz-transition: all 0.5s ease;
  -ms-transition: all 0.5s ease;
  -o-transition: all 0.5s ease;
  transition: all 0.5s ease;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div id="name1" class="tpl-content-page-title">
                幸运抽奖轮盘
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
                <li>营销模块</li>
                <li class="am-active">幸运抽奖轮盘</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li id="tab1-bar" class="am-active"><a>幸运大转盘</a></li>
                                <li id="tab2-bar"><a href="/qwtwzba/lottery_history">获奖记录</a></li>
                            </ul>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 是否开启本功能</p>
                </div>
            </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['switch']== 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['switch'] === "0" || !$config['switch'] ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="lottery[switch]" id="show0" value="<?=$config['switch']==1?1:0?>">
                                </ul>
                            </div>
                </div>
                <div class="am-form-group switch-content <?=$config['switch'] == 1 ? '':'hide'?>">
                                <div class="am-form-group">
                                    <label for="menu4" class="am-u-sm-12 am-form-label">综合中奖率</label>
                                    <div class="am-u-sm-12">
                          <input type="text" placeholder="请输入综合总奖率" name='lottery[probability]' value='<?=$config['probability']?>'>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu4" class="am-u-sm-12 am-form-label">单个用户每次直播可抽奖次数：</label>
                                    <div class="am-u-sm-12">
                          <input type="text" placeholder="请输入抽奖次数" name='lottery[times]' value='<?=$config['times']?>'>
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 奖品设置（请保证每个奖品都设置完，若奖品类型不足四个可选无）</p>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="giftlv green green-on">一等奖 <i class="check-1"></i></li>
                                    <li id="switch-2" class="giftlv green">二等奖 <i class="check-2"></i></li>
                                    <li id="switch-3" class="giftlv green">三等奖 <i class="check-3"></i></li>
                                    <li id="switch-4" class="giftlv green">四等奖 <i class="check-4"></i></li>
                                    <input type="hidden" class="giftnum" value='1'>
                                </ul>
                            </div>
                </div>
                <div class="am-form-group prize-content switch-content-1">
                              <div class="tab-pane active" id="cfg_1">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="1" <?=$data1->type==1||!$data1->id?"checked":''?>>
                                  <span>赠送有赞积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="2" <?=$data1->type==2?"checked":''?>>
                                  <span>有赞优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="3" <?=$data1->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="4" <?=$data1->type==4?"checked":''?>>
                                  <span>有赞赠品</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data1->type==1||!$data1->id):?>
                                  <div class="type type1">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入一等奖的积分数量" name='data1[other]' value="<?=$data1->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data1->type==3):?>
                                  <div class="type type1">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入一等奖的红包金额" name='data1[other]' value="<?=$data1->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data1->type==2):?>
                                  <div class="type type1">
                                    <label>选择有赞优惠券：</label>
                                    <select name="data1[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data1->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data1->type==4):?>
                                  <div class="type type1">
                                    <label>选择有赞赠品：</label>
                                    <select name="data1[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data1->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <div class="forgift">
                                    <label>奖品数量（单位：份）：</label>
                                    <input type="text" placeholder="请输入一等奖的总数量" name='data1[num]' value="<?=$data1->num?>">
                                    <br>
                                    <br>
            <?php if($data1->pic):?>
              <div class="form-group">
                <a href="/qwtwzba/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwzba/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic1" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div></div>
                                </div>
                </div>
                <div class="am-form-group prize-content switch-content-2 hide">

                              <div class="tab-pane" id="cfg_2">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="1" <?=$data2->type==1||!$data2->id?"checked":''?>>
                                  <span>赠送有赞积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="2" <?=$data2->type==2?"checked":''?>>
                                  <span>有赞优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="3" <?=$data2->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="4" <?=$data2->type==4?"checked":''?>>
                                  <span>有赞赠品</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="5" <?=$data2->type==5?"checked":''?>>
                                  <span>无</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data2->type==1||!$data2->id):?>
                                  <div class="type type2">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入二等奖的积分数量" name='data2[other]' value="<?=$data2->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data2->type==3):?>
                                  <div class="type type2">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入二等奖的红包金额" name='data2[other]' value="<?=$data2->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data2->type==2):?>
                                  <div class="type type2">
                                    <label>选择有赞优惠券：</label>
                                    <select name="data2[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data2->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data2->type==4):?>
                                  <div class="type type2">
                                    <label>选择有赞赠品：</label>
                                    <select name="data2[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data2->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data2->type==5):?>
                                    <div class="type type2"></div>
                                <?php endif?>
                                <div class="forgift" <?=$data2->type==5?'style="display:none"':''?>>
                                    <label>奖品数量（单位：份）：</label>
                                    <input type="text" placeholder="请输入二等奖的总数量" name='data2[num]' value="<?=$data2->num?>">
                                    <br>
                                    <br>
            <?php if($data2->pic):?>
              <div class="form-group">
                <a href="/qwtwzba/images/lottery/<?=$data2->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwzba/images/lottery/<?=$data2->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div></div>
                                </div>
                </div>
                <div class="am-form-group prize-content switch-content-3 hide">

                              <div class="tab-pane" id="cfg_3">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="1" <?=$data3->type==1||!$data3->id?"checked":''?>>
                                  <span>赠送有赞积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="2" <?=$data3->type==2?"checked":''?>>
                                  <span>有赞优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="3" <?=$data3->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="4" <?=$data3->type==4?"checked":''?>>
                                  <span>有赞赠品</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="5" <?=$data3->type==5?"checked":''?>>
                                  <span>无</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data3->type==1||!$data3->id):?>
                                  <div class="type type3">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入三等奖的积分数量" name='data3[other]' value="<?=$data3->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data3->type==3):?>
                                  <div class="type type3">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入三等奖的红包金额" name='data3[other]' value="<?=$data3->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data3->type==2):?>
                                  <div class="type type3">
                                    <label>选择有赞优惠券：</label>
                                    <select name="data3[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data3->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data3->type==4):?>
                                  <div class="type type3">
                                    <label>选择有赞赠品：</label>
                                    <select name="data3[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data3->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data3->type==5):?>
                                    <div class="type type3"></div>
                                <?php endif?>
                                <div class="forgift" <?=$data3->type==5?'style="display:none"':''?>>
                                    <label>奖品数量（单位：份）：</label>
                                    <input type="text" placeholder="请输入三等奖的总数量" name='data3[num]' value="<?=$data3->num?>">
                                    <br>
                                    <br>
            <?php if($data3->pic):?>
              <div class="form-group">
                <a href="/qwtwzba/images/lottery/<?=$data3->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwzba/images/lottery/<?=$data3->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic3" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div></div>
                              </div>
                </div>
                <div class="am-form-group prize-content switch-content-4 hide">

                              <div class="tab-pane" id="cfg_4">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="1" <?=$data4->type==1||!$data4->id?"checked":''?>>
                                  <span>赠送有赞积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="2" <?=$data4->type==2?"checked":''?>>
                                  <span>有赞优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="3" <?=$data4->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="4" <?=$data4->type==4?"checked":''?>>
                                  <span>有赞赠品</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="5" <?=$data4->type==5?"checked":''?>>
                                  <span>无</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data4->type==1||!$data4->id):?>
                                  <div class="type type4">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入四等奖的积分数量" name='data4[other]' value="<?=$data4->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data4->type==3):?>
                                  <div class="type type4">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入四等奖的红包金额" name='data4[other]' value="<?=$data4->other?>">
                                  </div>
                                <?php endif?>
                                <?php if($data4->type==2):?>
                                  <div class="type type4">
                                    <label>选择有赞优惠券：</label>
                                    <select name="data4[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data4->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data4->type==4):?>
                                  <div class="type type4">
                                    <label>选择有赞赠品：</label>
                                    <select name="data4[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data4->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>
                                  </div>
                                <?php endif?>
                                <?php if($data4->type==5):?>
                                    <div class="type type4"></div>
                                <?php endif?>
                                <div class="forgift" <?=$data4->type==5?'style="display:none"':''?>>
                                    <label>奖品数量（单位：份）：</label>
                                    <input type="text" placeholder="请输入四等奖的总数量" name='data4[num]' value="<?=$data4->num?>">
                                    <br>
                                    <br>
            <?php if($data4->pic):?>
              <div class="form-group">
                <a href="/qwtwzba/images/lottery/<?=$data4->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwzba/images/lottery/<?=$data4->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>
            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic4" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div>
            </div>
                                </div>
                </div>
                                <hr>


                </div>

                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
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
<script type="text/javascript">

<?php if($result['err']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show0').val(1);
  check();
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show0').val(0);
  check();
})
$('#switch-1').on('click', function() {
    $('.giftlv').removeClass('green-on');
    $(this).addClass('green-on');
    $('.prize-content').addClass('hide');
    $('.switch-content-1').removeClass('hide');
    $('.giftnum').val(1);
  check();
})
$('#switch-2').on('click', function() {
    $('.giftlv').removeClass('green-on');
    $(this).addClass('green-on');
    $('.prize-content').addClass('hide');
    $('.switch-content-2').removeClass('hide');
    $('.giftnum').val(2);
  check();
})
$('#switch-3').on('click', function() {
    $('.giftlv').removeClass('green-on');
    $(this).addClass('green-on');
    $('.prize-content').addClass('hide');
    $('.switch-content-3').removeClass('hide');
    $('.giftnum').val(3);
  check();
})
$('#switch-4').on('click', function() {
    $('.giftlv').removeClass('green-on');
    $(this).addClass('green-on');
    $('.prize-content').addClass('hide');
    $('.switch-content-4').removeClass('hide');
    $('.giftnum').val(4);
  check();
})
  window.coupons = '';
  window.gifts = '';
  <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
    coupons = coupons + '<option value=\"<?=$v['group_id']?>\">'+'<?=$v['title']?>'+'</option>'
  <?php endforeach?>
  <?php foreach ($gifts['response']['presents'] as $k => $v):?>
    gifts = gifts + '<option value=\"<?=$v['present_id']?>\">'+'<?=$v['title']?>'+'</option>'
  <?php endforeach?>
$(document).on('click', '.typeselect', function() {
  n = $(this).val();
  m = $('.giftnum').val();
  console.log(m);
  str='';
  if (n=='1') {
    str=str+'<label>'+'积分数量：'+'</label>'+'<input type=\"text\" placeholder=\"'+'请输入奖品'+m+'的积分数量'+'\"'+'style=\"margin-left:4px\" name=\"data'+m+'[other]\">';
  };
  if (n=='2') {
    str=str+'<label>'+'选择优惠券：'+'</label>'+
            '<select name=\"data'+m+'[other]\">'+
              coupons+
            '</select>';
  };
  if (n=='3') {
    str=str+'<label>'+'红包金额(单位：分)：'+'</label>'+'<input type=\"text\" placeholder=\"'+'请输入奖品'+m+'的红包金额'+'\"'+'style=\"margin-left:4px\" name=\"data'+m+'[other]\">';
  };
  if (n=='4') {
    str=str+'<label>'+'选择赠品：'+'</label>'+
            '<select name=\"data'+m+'[other]\">'+
              gifts+
            '</select>';
  };
  $(this).parent().parent().children('.type').empty();
  $(this).parent().parent().children('.type').append(str);
  if (n=='5') {
  $(this).parent().parent().children('.forgift').hide();
  }else{
  $(this).parent().parent().children('.forgift').show();
  }
});
$('input').blur(function(){
  check();
})

function check(){
  for (var i = 1; i <= 4; i++) {
    $a = $('.type'+i);
    var c = $a.parent().children('.forgift').children('input:first').val().length;
    if (c==0) {
      $('.check-'+i).removeClass('am-icon-check');
    }else{
      var d = $a.find('input').length;
      if (d==0) {
        $('.check-'+i).addClass('am-icon-check');
      }else{
        var b = $a.children('input:first').val().length;
        if (b==0) {
          $('.check-'+i).removeClass('am-icon-check');
        }else{
          $('.check-'+i).addClass('am-icon-check');
        }
      }
    }
  }
}
</script>
