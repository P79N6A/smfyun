<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title></title>
    <link rel="stylesheet" href="./weiui/css/weui.css"/>
    <link rel="stylesheet" href="./weiui/css/example.css"/>
</head>
<style type="text/css">
 .weui_cell_ft{
  cursor: pointer;
  border-left: 1px solid darkgrey;
  padding-left: 10px;
 }
 .error{
  text-align: center;
  background: red;
  color: white;
  font-size: 13px;
  line-height: 26px;
 }
</style>
<body>
<?php if($txtReply):?>
<div class="page"><!-- 不在活动时间范围内 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_warn"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc"><?=$txtReply?></p>
        </div>
    </div>
</div>
<?php else:?>
    <form method="post">
  <?php if($result['error']):?>
    <div class="error"><?=$result['error']?></div>
  <?php endif?>
   <div class="weui_cells_title">表单</div>
   <div class="weui_cells weui_cells_form">
       <div class="weui_cell">
           <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
           <div class="weui_cell_bd weui_cell_primary">
               <input class="weui_input tel" name="hb[tel]" type="number" pattern="[0-9]*" placeholder="请输入手机号"/>
           </div>
       </div>
       <div class="weui_cell">
           <div class="weui_cell_hd"><label class="weui_label">验证码</label></div>
           <div class="weui_cell_bd weui_cell_primary">
               <input class="weui_input" name="hb[code]" type="number" placeholder="请输入验证码"/>
           </div>
           <div class="weui_cell_ft code" style="font-size: inherit;">
               点此获取
           </div>
       </div>
   </div>
   <div class="weui_btn_area">
      <input class="weui_btn weui_btn_primary" type="submit" value="确定">
   </div>
</form>
<script src="//cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
  $(document).on('click', '.code', function() {
    if($('.tel').val()!=''){
        $.ajax({
          url: '/whb/hongbao?code=true',
          type: 'post',
          dataType: 'text',
          data:{tel:$('.tel').val()},
          timeout:15000,
          success: function (res){
             alert(res);
             window.times = 60;
             $('.code').removeClass('code');
             var settime = setInterval(function(){
                window.times--;
                $('.weui_cell_ft').text(window.times+'秒后可重新获取');
                if(window.times==0){
                    $('.weui_cell_ft').addClass('code');
                    $('.code').text('点击重新获取');
                    clearInterval(settime);
                }
             },1000)
          }
       });
    }else{
      alert('请输入正确的手机号格式！');
    }
  });
  <?php if($lefttime>0):?>
      window.times = <?=$lefttime?>;
      $('.code').removeClass('code');
      var settime = setInterval(function(){
          window.times--;
          $('.weui_cell_ft').text(window.times+'秒后可重新获取');
          if(window.times==0){
              $('.weui_cell_ft').addClass('code');
              $('.code').text('点击重新获取');
              clearInterval(settime);
          }
       },1000)
  <?php endif?>
</script>
<?php endif?>
</body>
</html>
