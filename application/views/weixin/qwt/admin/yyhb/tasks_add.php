
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
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
    .am-badge{
        background-color: green;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['title']?>
            </div>

            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">语音红包</a></li>
                <li><a>活动管理</a></li>
                <li class="am-active"><?=$result['title']?></li>
            </ol>
                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><?=$result['title']?></span>
                            </div>

                        </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block " style="overflow:-webkit-paged-x;">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12">
                            <form class="am-form am-form-horizontal" name="ordersform" method="post"  enctype='multipart/form-data' onsubmit="return toValid()">

                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">活动名称</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入活动名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">活动开始时间</label>
                                    <div class="am-u-sm-9">
  <input name="data[begintime]" id="datetimepicker1" size="16" type="text" value="<?=$_POST['data']['begintime']?date("Y-m-d H:i:s",$_POST['data']['begintime']):''?>" class="am-form-field" readonly>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">活动结束时间</label>
                                    <div class="am-u-sm-9">
  <input name="data[endtime]" id="datetimepicker2" size="16" type="text" value="<?=$_POST['data']['endtime']?date("Y-m-d H:i:s",$_POST['data']['endtime']):''?>" class="am-form-field" readonly>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">活动说明</label>
                                    <div class="am-u-sm-9">
            <textarea type="text" class="form-control" id="eventdetail" name="data[detail]"><?=htmlspecialchars($_POST['data']['detail'])?></textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">更多福利的链接</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="name" name="data[shopurl]" placeholder="输入店铺链接" value="<?=htmlspecialchars($_POST['data']['shopurl'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">分享图标 </label>
                                    <div class="am-u-sm-9">
        <?php if ($result['action'] == 'edit'):?>
            <a href="/qwtyyhba/images/task/<?=$_POST['data']['id']?>.v<?=$item['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtyyhba/images/task/<?=$_POST['data']['id']?>.v<?=$item['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传分享图标</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic1" accept="image/jpeg"  multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 400KB。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">分享到朋友圈或好友的标题</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="name" name="data[sharetitle]" placeholder="输入标题" value="<?=htmlspecialchars($_POST['data']['sharetitle'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">分享到好友的文字内容</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="name" name="data[sharetext]" placeholder="输入文字" value="<?=htmlspecialchars($_POST['data']['sharetext'])?>">
                                    </div>
                                </div>
                                <!-- <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">店铺头像 </label>
                                    <div class="am-u-sm-9">
        <?php if ($result['action'] == 'edit' && $result['task']['pic']):?>
            <a href="/qwtyyhba/images/task/<?=$result['task']['id']?>.v<?=$result['task']['lastupdate']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtyyhba/images/task/<?=$result['task']['id']?>.v<?=$result['task']['lastupdate']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传店铺头像</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic1" type="file" name="pic1" accept="image/jpeg"  multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 300KB。</small>
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">口令（只能是中文，不得有标点空格）</label>
                                    <div class="am-u-sm-9">
            <input type="text" class="form-control" id="keyword" name="data[keyword]" placeholder="输入活动关键词" value="<?=htmlspecialchars($_POST['data']['keyword'])?>">
                                    </div>
                                </div>
                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">单个用户最大中奖次数</label>
                                    <div class="am-u-sm-9">
            <input type="number" class="form-control" id="name" name="data[win_num]" placeholder="输入单个用户最大中奖次数" value="<?=htmlspecialchars($_POST['data']['win_num'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">单个用户最大参与次数</label>
                                    <div class="am-u-sm-9">
            <input type="number" class="form-control" id="name" name="data[join_num]" placeholder="输入单个用户最大参与次数" value="<?=htmlspecialchars($_POST['data']['join_num'])?>">
                                    </div>
                                </div> -->
                                 <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">综合中奖率(0-100)%</label>
                                    <div class="am-u-sm-9">
            <input type="number" min="0" max="100" class="form-control" id="name" name="data[probability]" placeholder="输入综合中奖概率" value="<?=htmlspecialchars($_POST['data']['probability'])?>">
                                    </div>
                                </div>
                        <div class="giftbox">
                        <?php if($result['action']=='edit'):?>
                        <?php foreach ($skus as $k => $v):
                        $ordernum=ORM::factory('qwt_yyhborder')->where('bid','=',$bid)->where('kid','=',$v->id)->where('state','=',1)->count_all();
                        ?>
                        <div class="gift">
                                <hr>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">选择奖品</label>
                                    <div class="am-u-sm-9">
                                    <select name="prize[<?=$k?>]" class="input-group goalb" data-am-selected="{searchBox: 1}">
                <?php foreach ($items as $item): ?>
                  <option value="<?=$item->id?>" <?=$v->iid==$item->id?"selected":""?>><?=$item->km_content?></option>
                <?php endforeach ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">奖品库存</label>
                                    <div class="am-u-sm-9">
                                    <input type="number" class="form-control goalc" name="stock[<?=$k?>]" placeholder="库存" value="<?=$v->stock?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif;?>
                        </div>
                        <hr>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
      <a class="add am-btn am-btn-primary tpl-btn-bg-color-danger" style="margin-left:10px"><i class="fa fa-remove"></i> <span>添加奖品</span></a>
      <a class="cut am-btn am-btn-danger tpl-btn-bg-color-danger" style="margin-left:10px"><i class="fa fa-remove\"></i> <span>删除最后一级奖品</span></a>
                                    </div>
                                </div>
                        <hr>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-3 am-form-label">是否关注后才能参与</label>
                                    <div class="am-u-sm-9">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$_POST['data']['state'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$_POST['data']['state'] === "0" || !$_POST['data']['state'] ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="data[state]" id="show0" value="<?=$_POST['data']['state']?>">
                                </ul>
                            </div>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存</button>
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
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
    $('#keyword').on('blur',function(){
        check();
    })
    function check(){
        if(!/^[\u4e00-\u9fa5]+$/gi.test(document.getElementById("keyword").value))
            alert("只能输入汉字");
    }
    function toValid(){
        if(!/^[\u4e00-\u9fa5]+$/gi.test(document.getElementById("keyword").value)){
            alert("只能输入汉字")
            return false;
        }else{
            return true;
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
  $(function() {
    $('#pic1').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
  <?php if (!$_POST['data']['begintime'] || time()<$_POST['data']['begintime']):?>
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
    $('.cut').click(function(){
        $('.gift:last').remove();
    })
  $('.add').click(function(){
    var goalnum = $(".goalb").length;
    console.log(goalnum);
    $('.giftbox').append("<div class=\"gift\">"+
                                "<hr>"+
                                "<div class=\"am-form-group\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">选择奖品</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<select name=\"prize["+goalnum+"]\" class=\"input-group goalb\" data-am-selected=\"{searchBox: 1}\">"+
                <?php foreach ($items as $item): ?>
                  "<option value=\"<?=$item->id?>\"><?=$item->km_content?></option>"+
                <?php endforeach ?>
                                    "</select>"+
                                    "</div>"+
                                "</div>"+
                                "<div class=\"am-form-group\">"+
                                    "<label for=\"user-name\" class=\"am-u-sm-3 am-form-label\">奖品份数</label>"+
                                    "<div class=\"am-u-sm-9\">"+
                                    "<input type=\"number\" class=\"form-control goalc\" name=\"stock["+goalnum+"]\" placeholder=\"库存\" value=\"\">"+
                                    "</div>"+
                                "</div>"+
                            "</div>");
  })
        </script>
