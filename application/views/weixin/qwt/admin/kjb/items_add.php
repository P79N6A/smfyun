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
</style>
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            <?=$result['title']?>
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">砍价宝</a></li>
            <li>商品管理</li>
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
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品名称
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[name]" placeholder="输入商品名称" value="<?=$_POST['item']['name']?>">
                                </div>
                            </div>
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-12 am-form-label">商品图片 </label>
                                    <div class="am-u-sm-12">
        <?php if ($result['action'] == 'edit' && $item['pic']):?>
            <a href="/qwtkjba/images/item/<?=$result['item']['id']?>.v<?=$item['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtkjba/images/item/<?=$result['item']['id']?>.v<?=$item['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传商品图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic1" accept="image/jpeg"  multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式。建议大小为宽400px*高300px，不得超过400kb</small>
                                    </div>
                                </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品库存
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" class="form-control" name="item[stock]" placeholder="输入商品库存" value="<?=$_POST['item']['stock']?>">
                                </div>
                            </div>
                            <?php if ($result['action']=='add'):?>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品原价（单位：分，设置后不能修改）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" name="item[old_price]" placeholder="输入商品原价" value="<?=$_POST['item']['old_price']?>">
                                </div>
                            </div>
                          <?php else:?>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品原价
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" value="<?=$_POST['item']['old_price']?>" readonly="">
                                </div>
                            </div>
                          <?php endif?>
                            <!-- <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">是否开启砍价活动
                                </label>
                                <div class="am-u-sm-12">
                                    <div class="actions" style="float:left">
                                        <ul class="actions-btn">
                                            <li id="switch-off" class="red <?=$_POST['item']['status']==2?'':'red-on'?>">关闭</li>
                                            <li id="switch-on" class="green <?=$_POST['item']['status']==2?'green-on':''?>">开启</li>
                                            <input type="hidden" name="item[status]" id="show0" value="<?=$_POST['item']['status']?$_POST['item']['status']:1?>">
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">是否设置为关注用户才能发起砍价
                                </label>
                                <div class="am-u-sm-12">
                                    <div class="actions" style="float:left">
                                        <ul class="actions-btn">
                                            <li id="switch-off-1" class="red <?=$_POST['item']['need_sub']==2?'':'red-on'?>">关闭</li>
                                            <li id="switch-on-1" class="green <?=$_POST['item']['need_sub']==2?'green-on':''?>">开启</li>
                                            <input type="hidden" name="item[need_sub]" id="show1" value="<?=$_POST['item']['need_sub']?$_POST['item']['need_sub']:1?>">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">是否设置为关注用户才能帮忙砍价
                                </label>
                                <div class="am-u-sm-12">
                                    <div class="actions" style="float:left">
                                        <ul class="actions-btn">
                                            <li id="switch-off-2" class="red <?=$_POST['item']['cut_sub']==2?'':'red-on'?>">关闭</li>
                                            <li id="switch-on-2" class="green <?=$_POST['item']['cut_sub']==2?'green-on':''?>">开启</li>
                                            <input type="hidden" name="item[cut_sub]" id="show2" value="<?=$_POST['item']['cut_sub']?$_POST['item']['cut_sub']:1?>">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">是否设置为每个用户此商品只能帮砍一人次
                                </label>
                                <div class="am-u-sm-12">
                                    <div class="actions" style="float:left">
                                        <ul class="actions-btn">
                                            <li id="switch-off-3" class="red <?=$_POST['item']['cut_onece']==2?'':'red-on'?>">关闭</li>
                                            <li id="switch-on-3" class="green <?=$_POST['item']['cut_onece']==2?'green-on':''?>">开启</li>
                                            <input type="hidden" name="item[cut_onece]" id="show2" value="<?=$_POST['item']['cut_onece']?$_POST['item']['cut_onece']:1?>">
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">砍价活动标题（最好包含商品名称）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[title]" placeholder="输入标题" value="<?=$_POST['item']['title']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">砍价活动副标题
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="item[subtitle]" placeholder="输入副标题" value="<?=$_POST['item']['subtitle']?>">
                                </div>
                            </div>
                            <?php if ($result['action']=='add'):?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">砍价活动开始时间（保存后不可再编辑修改）</label>
                                    <div class="am-u-sm-12">
  <input name="item[begintime]" id="datetimepicker1" size="16" type="text" value="<?=$_POST['item']['begintime']?date("Y-m-d H:i:s",$_POST['item']['begintime']):''?>" class="am-form-field" style="width:200px;" readonly>
                                    </div>
                                </div>
                              <?php else:?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">砍价活动开始时间（保存后不可再编辑修改）</label>
                                    <div class="am-u-sm-12">
  <input id="datetimepicker1" size="16" type="text" value="<?=$_POST['item']['begintime']?date("Y-m-d H:i:s",$_POST['item']['begintime']):''?>" class="am-form-field" style="width:200px;" disabled="">
                                    </div>
                                </div>
                              <?php endif?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">砍价活动结束时间</label>
                                    <div class="am-u-sm-12">
  <input name="item[endtime]" id="datetimepicker2" size="16" type="text" value="<?=$_POST['item']['endtime']?date("Y-m-d H:i:s",$_POST['item']['endtime']):''?>" class="am-form-field" style="width:200px;" readonly>
                                    </div>
                                </div>
                            <?php if ($result['action']=='add'):?>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品最低价（单位：分，设置后不能修改）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" name="item[price]" placeholder="输入商品最低价" value="<?=$_POST['item']['price']?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品最多需砍刀数（设置后不能修改）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" name="item[cut_num]" placeholder="输入商品刀数" value="<?=$_POST['item']['cut_num']?>">
                                </div>
                            </div>
                          <?php else:?>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品最低价（单位：分）
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" value="<?=$_POST['item']['price']?>" readonly="">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-12 am-form-label">商品最多需砍刀数
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="number" step="1" class="form-control" value="<?=$_POST['item']['cut_num']?>" readonly="">
                                </div>
                            </div>
                          <?php endif?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">奖品详情</label>
                                    <div class="am-u-sm-12">
                                        <div id="div1">
                                            <?=$_POST['item']['desc']?>
                                        </div>
                                        <div style="display:none">
                                        <textarea type="hidden" name='item[desc]' id="text1" style="width:100%; height:200px;"><?=$_POST['item']['desc']?></textarea></div>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">砍价规则</label>
                                    <div class="am-u-sm-12">
                                        <div id="div2">
                                            <?=$_POST['item']['rule']?$_POST['item']['rule']:'
        <p>1. 点击我要参加进行报名。报名时候请填写收货地址(如需要);</p>
        <p>2. 每个人只能砍价一次，自己不能给自己砍价;</p>
        <p>3. 砍到自己心仪的价格即可购买，活动以付款成功为准，砍到底价后请及时付款，避免商品售完造成无法购买;</p>
        <p>4. 如有问题请及时联系我们，详细联系方式见活动页面商家信息栏;</p>
        <p>5. 本次活动的优惠资格不可赠送或转让；活动解释权归本机构所有。</p>'?>
                                        </div>
                                        <div style="display:none">
                                        <textarea type="hidden" name='item[rule]' id="text2" style="width:100%; height:200px;"><?=$_POST['item']['rule']?$_POST['item']['rule']:'
        <p>1. 点击我要参加进行报名。报名时候请填写收货地址(如需要);</p>
        <p>2. 每个人只能砍价一次，自己不能给自己砍价;</p>
        <p>3. 砍到自己心仪的价格即可购买，活动以付款成功为准，砍到底价后请及时付款，避免商品售完造成无法购买;</p>
        <p>4. 如有问题请及时联系我们，详细联系方式见活动页面商家信息栏;</p>
        <p>5. 本次活动的优惠资格不可赠送或转让；活动解释权归本机构所有。</p>'?></textarea></div>
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                                <hr>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://unpkg.com/wangeditor@3.1.0/release/wangEditor.min.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            <?php if ($result['err']):?>
            swal({
                title: "失败",
                text: "<?=$result['err']?>",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "我知道了",
                closeOnConfirm: true,
            })
            <?php endif?>
        })
        <?php if (!$_POST['item']['begintime'] || time()<$_POST['item']['begintime']):?>
          $(function () {
            $("#datetimepicker1").datetimepicker({
              format: "yyyy-mm-dd hh:ii",
              language: "zh-CN",
              minView: "0",
              autoclose: true
            });
          });
        <?php endif?>
          $(function () {
            $("#datetimepicker2").datetimepicker({
              format: "yyyy-mm-dd hh:ii",
              language: "zh-CN",
              minView: "0",
              autoclose: true
            });
          });

    $('#switch-on').click(function(){
        $('#show0').val(2);
      $('#switch-on').addClass('green-on');
      $('#switch-off').removeClass('red-on');
      $('.eventtime').show();
    })
    $('#switch-off').click(function(){
        $('#show0').val(1);
      $('#switch-on').removeClass('green-on');
      $('#switch-off').addClass('red-on');
      $('.eventtime').hide();
    })
    $('#switch-on-1').click(function(){
        $('#show1').val(2);
      $('#switch-on-1').addClass('green-on');
      $('#switch-off-1').removeClass('red-on');
    })
    $('#switch-off-1').click(function(){
        $('#show1').val(1);
      $('#switch-on-1').removeClass('green-on');
      $('#switch-off-1').addClass('red-on');
    })
    $('#switch-on-2').click(function(){
        $('#show2').val(2);
      $('#switch-on-2').addClass('green-on');
      $('#switch-off-2').removeClass('red-on');
    })
    $('#switch-off-2').click(function(){
        $('#show2').val(1);
      $('#switch-on-2').removeClass('green-on');
      $('#switch-off-2').addClass('red-on');
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
  var E1 = window.wangEditor
  var editor1 = new E1('#div1')
  var $text1 = $('#text1')
  editor1.customConfig.uploadImgShowBase64 = true   // 使用 base64 保存图片
  editor1.customConfig.onchange = function (html) {
      // 监控变化，同步更新到 textarea
      $text1.val(html)
  }
  editor1.create();
  // 初始化 textarea 的值
  $text1.val(editor1.txt.html());
  var E2 = window.wangEditor
  var editor2 = new E2('#div2')
  var $text2 = $('#text2')
  editor2.customConfig.uploadImgShowBase64 = true   // 使用 base64 保存图片
  editor2.customConfig.onchange = function (html) {
      // 监控变化，同步更新到 textarea
      $text2.val(html)
  }
  editor2.create();
  // 初始化 textarea 的值
  $text2.val(editor2.txt.html());
</script>


