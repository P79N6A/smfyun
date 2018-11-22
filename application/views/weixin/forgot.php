<div data-role="page">

	<div data-role="header">
		<a href="javascript:history.back()" data-icon="arrow-l">返回</a>
		<h1>找回密码</h1>
	</div>

	<div data-role="content" id="content">
		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<input id="email" placeholder="您在男人袜的登录邮箱" value="" type="text" />
			</fieldset>
		</div>

		<a data-role="button" data-transition="fade" id="forgot-btn" data-theme="a" data-icon="check">重新设置密码</a>

	</div>
</div>

<script>
$('#forgot-btn').on('click',function(){
	$.mobile.showPageLoadingMsg();
	$.ajax({
		url:'/account/forgot',
		type:'POST',
		data:'email='+$('#email').val(),
		dataType:'json',
		success:function(e){
			$.mobile.hidePageLoadingMsg();
			if(e.result == 'success'){
				$('#content').html('<div style="margin-top:10px;"><center>我们刚刚发了一封邮件给您，上面介绍了重置您密码的方法。请查收您的邮箱。</center></div>');
			}else{
				alert('未找到该用户!');
			}
		},
		error:function(e){
			$.mobile.hidePageLoadingMsg();
			alert('载入失败,请检查网络');
		}
	});
});
</script>