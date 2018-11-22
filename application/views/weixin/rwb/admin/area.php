<style>
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
</style>

<section class="content-header">
  <h1>
    选择可参与地区

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">选择可参与地区</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">


        <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['area']) $active = 'area';


        if ($_POST['area']) $active = 'area';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">









              <!-- 2015.12.21增加指定地区用户参与部分 -->

              <div class="tab-pane" id="cfg_area">
                <?php if ($result['ok5'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功!</div>
                <?php endif?>
                <form role="form" method="post" onsubmit="return toVaild()">
                  <div class="form-group">
                    <label for="show" style="font-size:16px;">是否开启本功能？</label>
                    <div class="radio">
                      <label class="checkbox-inline"  onclick="show()">
                        <input type="radio" name="area[status]" id="show1" value="1" <?=$config['status'] == 1 ? ' checked=""' : ''?>>
                        <span class="label label-success"  style="font-size:14px">开启</span>
                      </label>
                      <label class="checkbox-inline" onclick="hide()">
                        <input type="radio" name="area[status]" id="show0" value="0" <?=$config['status'] === "0" ||!$config['status']? ' checked=""' : ''?>>
                        <span class="label label-danger"  style="font-size:14px">关闭</span>
                      </label>
                    </div>

                    <script type="text/javascript">
                      function hide(){
                      // var h=document.getElementById("hidee");
                      // h.style.visibility="hidden";
                      var h2=document.getElementById("hide");
                      h2.style.display="none";
                    }

                    function show(){
                      // var h=document.getElementById("hidee");
                      // h.style.visibility="visible";
                      var h2=document.getElementById("hide");
                      h2.style.display="block";
                    }
                  </script>


                  <!-- 地区选择代码 -->
                  <br>
                  <div id="hide">
                    <label id="area" for="show" style="font-size:16px;">请选择可参与活动的地区：
                      <br>
                      <br>
                      <span class="label label-success add"  >添加</span>
                      <span class='label label-danger reduce'>减少</span>

                      <?php if ($config['count']){
                        $num = $config['count'];
                        for ($i=1; $i <=$num ; $i++) {
                         echo '
                         <div class=\'loc\' id=\'city'.$i.'\'>
                          <select class=\'prov\' name=\'area[pro'.$i.']\'></select>
                          <select class=\'city\' name=\'area[city'.$i.']\' disabled="disabled"></select>
                          <select class=\'dist\' name=\'area[dis'.$i.']\' disabled="disabled"></select>
                        </div>
                        ';
                      }
                    }
                    ?>
                    <?php if (!$config['count']):?>
                      <div class="loc" id="city1" class="loc">
                        <select class="prov" name="area[pro1]"></select>
                        <select class="city" name="area[city1]" disabled="disabled"></select>
                        <select class="dist" name="area[dis1]" disabled="disabled"></select>
                      </div>

                    <?php endif?>
                  </label>
                  <input id='count' name="area[count]" style="display:none" value='<?=$config['count']?>'>


                  <br><br>
                  <div class="form-group">
                    <label for="desc">页面中对不符合活动地区的用户提示文案</label><br>
                    <input name='area[reply]' type="text" style="width:100%;height:40px;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['reply']){echo $config['reply'];}else{echo '不好意思，您不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！';}?>'>
                  </div>
                  <div class="form-group">
                    <label for="desc">页面中符合活动地区的用户提示文案</label><br>
                    <input name='area[isreply]' type="text" style="width:100%;height:40px;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['isreply']){echo $config['isreply'];}else{echo '亲~恭喜您获得参与本次活动的机会，请返回到公众号对话框，点击【生成海报】菜单，获得属于你的专属海报！';}?>'>
                  </div>
                  <div class="form-group">
                    <label for="desc">微信中对不符合活动地区的用户提示文案</label><br>
                    <input name='area[replyfront]' type="text" style="width:50%;height:40px;float:left;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['replyfront']){echo $config['replyfront'];}else{echo '您好，本次活动可参与的地区为：';}?>'>
                    <div style="float:left;font-size: 14px;width:20%;text-align:center; line-height: 36px;">点击查看是否在活动范围内</div>
                    <input name='area[replyend]' type="text" style="width:29.6%;height:40px;float:left;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['replyend']){echo $config['replyend'];}else{echo '，如果您不在本次活动的范围内，请关注公众号的消息，有更多福利等着你哦！';}?>'>
                  </div><br><br>
                  <div class="form-group">
                    <label for="desc">活动说明</label>
                    <textarea class="textarea" wrap="virtual" id="" name="area[info]" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php if($config['info']){echo $config['info'];}else{echo '
                      1 、本次活动可参与的地区范围：<br>
                      2 、活动时间：<br>
                      3 、活动注意事项：';}?></textarea>
                    </div>

                  </div>
                </div>
                <br>
                <script src="http://cdn.jfb.smfyun.com/rwb/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/rwb/plugins/citySelect/city.min1.js"></script>
                <script type="text/javascript" src="/rwb/plugins/simditor-2.2.4/scripts/module.js"></script>
                <script type="text/javascript">
                  function toVaild(){
                    num = parseInt($('.prov').length);
                    $('#count').val(num);
                    var isn = $('.prov:last').val();
                    if(!isn==''){
                      return true;
                    }else{
                      alert('请至少填写省');
                      return false;
                    }
                  }
                </script>
                <!-- // <script src="/rwb/plugins/jQuery/jquery.js"></script> -->
                <script src="http://cdn.jfb.smfyun.com/rwb/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/rwb/plugins/citySelect/city.min1.js"></script>
                <script type="text/javascript">
                  $("#city1").citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                  <?php
                  if ($config['count']){
                    $num = $config['count'];
                    for ($i=1; $i <=$num ; $i++) {
                     echo '
                     $(function(){
                      $(\'#city'.$i.'\').citySelect({
                        prov:\''.$config['pro'.$i].'\',
                        city:\''.$config['city'.$i].'\',
                        dist:\''.$config['dis'.$i].'\',
                        required:false
                      });
                    })';
                  }
                }
                ?>

                $(document).on('click','.add',function(){
                  var isn = $('.prov:last').val();
                  if(!isn==''){
                    window.num = parseInt($('.prov').length);
                    num = num+1;
                    $('#count').val(num);
                    $('.add').attr('count',num);
                    $("#area").append("<div class=\"loc\" id=\"city"+num+"\">"+
                      "<select class=\"prov\" name=\"area[pro"+num+"]\"></select>"+
                      "<select class=\"city\" name=\"area[city"+num+"]\" disabled=\"disabled\"></select>"+
                      "<select class=\"dist\" name=\"area[dis"+num+"]\" disabled=\"disabled\"></select>"+
                      "</div>");
                  }else{
                    alert('请至少填写省');
                  }
                  $("#city"+num).citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                })
                $(document).on('click','.reduce',function(){
                  if(parseInt($('.prov').length)==1){
                    alert('不能再减少');
                  }else{
                    $('.loc').last().remove();
                  }
                })
              </script>


              <!-- 开启与关闭 -->
              <script>
                $(document).ready(function(){
                 var status = $('#show1').attr('checked');
                 if(status=='checked'){
                  $('#hide').show();
                }
                if(status==undefined){
                  $('#hide').hide();
                }
              })
            </script>
            <script language=javascript>

            $(function () {
              var editor = new Simditor({
                textarea: $('.textarea'),
                toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
              });
            })
          </script>
            <div class="form-group">
              <label for="show">
                <div class="box-footer">
                  <button  type="submit" class="btn btn-success">保存配置</button>
                </div>
              </label>

            </div>


          </form>
        </div>

        <!-- 2015.12.21增加指定地区用户参与部分 完-->

        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->

