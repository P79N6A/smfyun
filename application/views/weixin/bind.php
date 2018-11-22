
<script>
var hash = '<?=$hash?>';
</script>

<div data-role="page">
	<div data-role="content" id="content">
		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="email">邮箱</label>
				<input id="email" placeholder="您在订购男人袜时填写的邮箱" value="" type="email" />
			</fieldset>
		</div>

		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="tel">手机号</label>
				<input id="tel" placeholder="您最近订购男人袜时的手机号码" value="" type="tel" />
			</fieldset>
		</div>

		<div data-role="fieldcontain" style="display:none">
			<fieldset data-role="controlgroup">
				<label for="password">密码</label>
				<input id="password" placeholder="您在男人袜的密码" value="" type="password" />
			</fieldset>
		</div>

		<a data-role="button" data-transition="fade" id="bind-btn" data-theme="a" data-icon="check">绑定微信</a>
		<!-- <a href="/weixin/forgot" data-role="button" data-transition="fade" data-theme="c" data-icon="alert">找回密码</a> -->

	</div>
</div>

<script>
$('#bind-btn').on('click',function(){
	$.mobile.showPageLoadingMsg();
	$.ajax({
		url:'/weixin/bind/<?=$hash?>',
		type:'POST',
		data:'email='+$('#email').val()+'&tel='+$('#tel').val()+'&password='+$('#password').val(),
		dataType:'json',
		success:function(e){
			$.mobile.hidePageLoadingMsg();
			if(e.msg=='success'){
				$('#content').html('<div style="margin-top:10px;"><center>绑定成功喽，爱死你了！</center></div>');
			}else{
				alert(e.data);
			}
		},
		error:function(e){
			$.mobile.hidePageLoadingMsg();
			alert('绑定失败,请检查网络');
		}
	});
});
</script>