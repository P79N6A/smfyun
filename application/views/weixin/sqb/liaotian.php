<!doctype html>
<html lang="en">

	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>会话</title>
		<link rel="stylesheet" type="text/css" href="/sqb/chat.css" />

		<script src="/sqb/js/jquery.min.js"></script>
		<script src="/sqb/js/flexible.js"></script>
	</head>

	<body>
		<header class="header">
			<a class="back" href="javascript:history.back()"></a>
			<h5 class="tit"><?=$tuser->nickname?></h5>
			<!-- <div class="right">资料</div> -->
		</header>
		<div class="message">
		<?php foreach ($msg as $k => $v):?>
			<?php if ($v->type==1):?>
			<div class="show">
				<div class="time"><?=date('Y-m-d H:i',$v->lastupdate)?></div>
				<div class="msg">
					<img src="<?=$user->headimgurl?>"/>
					<p><?=$v->content?></p>
				</div>
			</div>
		<?php endif?>
		<?php if ($v->type==2):?>
			<div class="send">
				<div class="time"><?=date('Y-m-d H:i',$v->lastupdate)?></div>
				<div class="msg">
					<img src="<?=$tuser->headimgurl?>" alt="" />
					<p><?=$v->content?></p>
				</div>
			</div>
		<?php endif?>
	<?php endforeach?>
		</div>
		<div class="footer">
			<!-- <img src="images/hua.png" alt="" />
			<img src="images/xiaolian.png" alt="" /> -->
			<input type="text"  style="width:5rem;" />
			<p>发送</p>
		</div>
	</body>

</html>
<script type="text/javascript">/*发送消息*/
var time = '05/22 06:30'
function send(headSrc,str){
	var html="<div class='send'><div class='msg'><img src="+headSrc+" />"+
	"<p><i class='msg_input'></i>"+str+"</p></div></div>";
	upView(html);
}
/*接受消息*/
function show(time,str){
	var html="<div class='show'><div class='time'>"+time+"</div><div class='msg'><img src=<?=$user->headimgurl?> />"+
	"<p><i class='msg_input'></i>"+str+"</p></div></div>";
	upView(html);
}
/*更新视图*/
function upView(html){
	$('.message').append(html);
	$('body').animate({scrollTop:$('.message').outerHeight()-window.innerHeight},200)
}
function sj(){
	return parseInt(Math.random()*10)
}
$(function(){
	$('.footer').on('keyup','input',function(){
		if($(this).val().length>0){
			$(this).next().css('background','#114F8E').prop('disabled',true);

		}else{
			$(this).next().css('background','#ddd').prop('disabled',false);
		}
	})
	$('.footer p').click(function(){
		var a = $(this).prev().val();
		$.ajax({
	   dataType:"json",
    data: {content:a},
				type:'get',
				url:'../liaotian/<?=$tuser->id?>',
			 success:function(result){

						show(result.time,result.content);
			 },
			 error:function(e){
			   console.log('请求失败')
			 }
		})
	})
})
</script>
