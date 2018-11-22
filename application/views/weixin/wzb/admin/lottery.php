<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
.nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
.giftselect{
  border-top: 1px solid #f4f4f4;
}
input{
  line-height: 40px;
}
.type{
  margin-bottom: 20px;
}

</style>
<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<section class="content-header">
  <h1>
    幸运抽奖轮盘
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">幸运抽奖轮盘</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_yz_li" class="active"><a data-toggle="tab">幸运大转盘</a></li>
            <li id="cfg_wx_li"><a data-toggle="tab">获奖记录</a></li>
          </ul>

          <script>
    $(document).on('click','#cfg_wx_li',function(){
      window.location.href = '/wzba/lottery_history';
    });
          </script>

          <div class="tab-content">

            <div class="tab-pane active" id="cfg_yz">

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存配置成功!</div>
                <?php endif?>

                <?php if ($result['err']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="coupon">是否开启幸运抽奖转盘功能</label>
                      <div class="radio">
                        <label class="checkbox-inline"  onclick="showfunc()">
                          <input type="radio" name="lottery[switch]" id="pshow1" value="1" <?=$config['switch'] == 1 ? ' checked=""' : ''?>>
                          <span class="label label-success"  style="font-size:14px">开启</span>
                        </label>
                        <label class="checkbox-inline" onclick="hidefunc()">
                          <input type="radio" name="lottery[switch]" id="pshow2" value="0" <?=$config['switch'] == 0 ||!$config['switch']? ' checked=""' : ''?>>
                          <span class="label label-danger"  style="font-size:14px">关闭</span>
                        </label>
                      </div>
                    </div>
                    <div class="dolottery" <?=$config['switch']==1? '':'style="display:none;"'?>>
                      <div class="form-group">
                        <div>
                          <label>综合中奖率：</label>
                          <input type="text" placeholder="请输入综合总奖率" name='lottery[probability]' value='<?=$config['probability']?>'>
                          <span>%</span>
                          <br>
                          <br>
                          <label>单个用户每次直播可抽奖次数：</label>
                          <input type="text" placeholder="请输入抽奖次数" name='lottery[times]' value='<?=$config['times']?>'>
                          <span>次</span>
                        </div>
                      </div>
                      <div>
                        <div class="nav-tabs-custom">

                            <ul class="nav nav-tabs giftselect">
                              <li id="cfg_1_li" class="giftnum active" value="1"><a onclick="change(1)" data-toggle="tab">一等奖</a></li>
                              <li id="cfg_2_li" class="giftnum" value="2"><a onclick="change(2)" data-toggle="tab">二等奖</a></li>
                              <li id="cfg_3_li" class="giftnum" value="3"><a onclick="change(3)" data-toggle="tab">三等奖</a></li>
                              <li id="cfg_4_li" class="giftnum" value="4"><a onclick="change(4)" data-toggle="tab">四等奖</a></li>
                            </ul>
                            <div class="tab-content giftdetail">
                              <div class="tab-pane active" id="cfg_1">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="1" <?=$data1->type==1||!$data1->id?"checked":''?>>
                                  <span>赠送积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="2" <?=$data1->type==2?"checked":''?>>
                                  <span>优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="3" <?=$data1->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data1[type]" value="4" <?=$data1->type==4?"checked":''?>>
                                  <span>赠品</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data1->type==1||!$data1->id):?>
                                  <div class="type type1">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入一等奖的积分数量" name='data1[other]' value="<?=$data1->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入一等奖的总数量" name='data1[num]' value="<?=$data1->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data1->type==3):?>
                                  <div class="type type1">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入一等奖的红包金额" name='data1[other]' value="<?=$data1->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入一等奖的总数量" name='data1[num]' value="<?=$data1->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data1->type==2):?>
                                  <div class="type type1">
                                    <label>选择优惠券：</label>
                                    <select name="data1[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data1->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入一等奖的总数量" name='data1[num]' value="<?=$data1->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data1->type==4):?>
                                  <div class="type type1">
                                    <label>选择赠品：</label>
                                    <select name="data1[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data1->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入一等奖的总数量" name='data1[num]' value="<?=$data1->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
            <?php if($data1->pic):?>
              <div class="form-group">
                <a href="/wzba/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/lottery/<?=$data1->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic1" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div>
                                </div>
                              <div class="tab-pane" id="cfg_2">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="1" <?=$data2->type==1||!$data2->id?"checked":''?>>
                                  <span>赠送积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="2" <?=$data2->type==2?"checked":''?>>
                                  <span>优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="3" <?=$data2->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data2[type]" value="4" <?=$data2->type==4?"checked":''?>>
                                  <span>赠品</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data2->type==1||!$data2->id):?>
                                  <div class="type type2">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入二等奖的积分数量" name='data2[other]' value="<?=$data2->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入二等奖的总数量" name='data2[num]' value="<?=$data2->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data2->type==3):?>
                                  <div class="type type2">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入二等奖的红包金额" name='data2[other]' value="<?=$data2->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入二等奖的总数量" name='data2[num]' value="<?=$data2->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data2->type==2):?>
                                  <div class="type type2">
                                    <label>选择优惠券：</label>
                                    <select name="data2[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data2->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入二等奖的总数量" name='data2[num]' value="<?=$data2->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data2->type==4):?>
                                  <div class="type type2">
                                    <label>选择赠品：</label>
                                    <select name="data2[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data2->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入二等奖的总数量" name='data2[num]' value="<?=$data2->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
            <?php if($data2->pic):?>
              <div class="form-group">
                <a href="/wzba/images/lottery/<?=$data2->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/lottery/<?=$data2->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div>
                                </div>
                              <div class="tab-pane" id="cfg_3">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="1" <?=$data3->type==1||!$data3->id?"checked":''?>>
                                  <span>赠送积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="2" <?=$data3->type==2?"checked":''?>>
                                  <span>优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="3" <?=$data3->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data3[type]" value="4" <?=$data3->type==4?"checked":''?>>
                                  <span>赠品</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data3->type==1||!$data3->id):?>
                                  <div class="type type3">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入三等奖的积分数量" name='data3[other]' value="<?=$data3->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入三等奖的总数量" name='data3[num]' value="<?=$data3->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data3->type==3):?>
                                  <div class="type type3">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入三等奖的红包金额" name='data3[other]' value="<?=$data3->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入三等奖的总数量" name='data3[num]' value="<?=$data3->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data3->type==2):?>
                                  <div class="type type3">
                                    <label>选择优惠券：</label>
                                    <select name="data3[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data3->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入三等奖的总数量" name='data3[num]' value="<?=$data3->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data3->type==4):?>
                                  <div class="type type3">
                                    <label>选择赠品：</label>
                                    <select name="data3[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data3->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入三等奖的总数量" name='data3[num]' value="<?=$data3->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
            <?php if($data3->pic):?>
              <div class="form-group">
                <a href="/wzba/images/lottery/<?=$data3->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/lottery/<?=$data3->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">上传奖品图片（不上传图片将无法正常使用）：</label>
              <input type="file" class="form-control" id="pic2" name="pic3" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，大小在65px * 40px左右。</p>
            </div>
                              </div>
                              <div class="tab-pane" id="cfg_4">
                                <label>选择奖品：</label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="1" <?=$data4->type==1||!$data4->id?"checked":''?>>
                                  <span>赠送积分</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="2" <?=$data4->type==2?"checked":''?>>
                                  <span>优惠券</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="3" <?=$data4->type==3?"checked":''?>>
                                  <span>现金红包</span>
                                </label>
                                <label class="checkbox-inline">
                                  <input class="typeselect" type="radio" name="data4[type]" value="4" <?=$data4->type==4?"checked":''?>>
                                  <span>赠品</span>
                                </label>
                                <br>
                                <br>
                                <?php if($data4->type==1||!$data4->id):?>
                                  <div class="type type4">
                                    <label>积分数量：</label>
                                    <input type="text" placeholder="请输入四等奖的积分数量" name='data4[other]' value="<?=$data4->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入四等奖的总数量" name='data4[num]' value="<?=$data4->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data4->type==3):?>
                                  <div class="type type4">
                                    <label>红包金额(单位：分)：</label>
                                    <input type="text" placeholder="请输入四等奖的红包金额" name='data4[other]' value="<?=$data4->other?>">
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入四等奖的总数量" name='data4[num]' value="<?=$data4->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data4->type==2):?>
                                  <div class="type type4">
                                    <label>选择优惠券：</label>
                                    <select name="data4[other]">
                                      <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
                                        <option value="<?=$v['group_id']?>" <?=$data4->other==$v['group_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入四等奖的总数量" name='data4[num]' value="<?=$data4->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
                                <?php if($data4->type==4):?>
                                  <div class="type type4">
                                    <label>选择赠品：</label>
                                    <select name="data4[other]">
                                      <?php foreach ($gifts['response']['presents'] as $k => $v):?>
                                        <option value="<?=$v['present_id']?>" <?=$data4->other==$v['present_id']?'selected':''?>><?=$v['title']?></option>
                                      <?php endforeach?>
                                    </select>;
                                  </div>
                                    <label>奖品数量：</label>
                                    <input type="text" placeholder="请输入四等奖的总数量" name='data4[num]' value="<?=$data4->num?>">
                                    <span>份</span>
                                    <br>
                                    <br>
                                <?php endif?>
            <?php if($data4->pic):?>
              <div class="form-group">
                <a href="/wzba/images/lottery/<?=$data4->id?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/lottery/<?=$data4->id?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
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
                      </div>
                    </div>

                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存</button>
                  </div>
                </form>
            </div>
          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->
<script type="text/javascript">
  window.coupons = '';
  window.gifts = '';
  <?php foreach ($coupons['response']['coupons'] as $k => $v):?>
    coupons = coupons + '<option value=\"<?=$v['group_id']?>\">'+'<?=$v['title']?>'+'</option>'
  <?php endforeach?>
  <?php foreach ($gifts['response']['presents'] as $k => $v):?>
    gifts = gifts + '<option value=\"<?=$v['present_id']?>\">'+'<?=$v['title']?>'+'</option>'
  <?php endforeach?>
$(document).ready(function() {
  check();
  if($('#pshow1').is(":checked")==1){

  }else{
    $('.dolottery').hide();
  }
});
function showfunc(){
  $('.dolottery').fadeIn(500);
}
function hidefunc(){
  $('.dolottery').fadeOut(500);
}
function change(i){
  $('.giftdetail').children().removeClass('active');
  if (i=='1') {
    $('#cfg_1').addClass('active');
  }
  if (i=='2') {
    $('#cfg_2').addClass('active');
  }
  if (i=='3') {
    $('#cfg_3').addClass('active');
  }
  if (i=='4') {
    $('#cfg_4').addClass('active');
  }
  check();
}
$(document).on('click', '.typeselect', function() {
  n = $(this).val();
  m = $('.giftnum.active').val();
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
});
$('input').blur(function(){
  check();
})

function check(){
  for (var i = 1; i <= 4; i++) {
    $a = $('.type'+i);
    var c = $a.parent().children('input:first').val().length;
    if (c==0) {
      $('#cfg_'+i+'_li').children('a:first').removeClass('fa fa-check');
    }else{
      var d = $a.find('input').length;
      if (d==0) {
        $('#cfg_'+i+'_li').children('a:first').addClass('fa fa-check');
      }else{
        var b = $a.children('input:first').val().length;
        if (b==0) {
          $('#cfg_'+i+'_li').children('a:first').removeClass('fa fa-check');
        }else{
          $('#cfg_'+i+'_li').children('a:first').addClass('fa fa-check');
        }
      }
    }
  }
}
</script>
