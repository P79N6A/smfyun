<div data-role="page">

	<div data-role="header">
		<h1>微信订购男人袜</h1>
	</div>

	<?php if($result):?>
	<div data-role="content" id="content" style="text-align:center">
		<img src="/_img/edm/goal.png" width="120" height="120" alt="成功了" />
		<h3>成功了！<br />男人妹将在第一时间联系您 :)</h3>
	</div>

	<?php else:?>

	<div data-role="content" id="content">

	<div style="color:#060">微信订购采用货到付款。<br />我们会在发货前与您电话确认。</div>
	<hr />

	<form action="" method="post" id="codform">
		<div data-role="fieldcontain">
			<label for="plans" class="select">选择要订购的套装</label>
			<select name="plan_id" id="plans">
				<option value="7">体验套装（6 双）￥58</option>
				<option value="1">体验套装（3 双）￥30</option>
				<option value="2">包年套装（一年 12 双）￥108</option>
				<option value="13">内裤套装（3 条）￥58</option>
				<option value="10">船袜套装（6 双）￥56</option>
				<option value="16">男人袜 Pro（防弹袜）￥168</option>
				<option value="17">防雾霾口罩（10只）￥58</option>
			</select>
		</div>

		<?php
		//老用户不用输邮箱
		if($_REQUEST['email']):?>
			<input type="hidden" name="email" value="<?=$_REQUEST['email']?>" />
		<?php else:?>
		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="email">邮箱 *</label>
				<input name="email" id="email" placeholder="将做为您在男人袜的帐号" value="" type="email" />
			</fieldset>
		</div>
		<?php endif?>

		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="tel">收货电话 *</label>
				<input name="tel" id="tel" placeholder="怎么联系到您" value="<?=$_REQUEST['tel']?>" type="tel" />
			</fieldset>
		</div>

		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="linkman">收货人 *</label>
				<input name="linkman" id="linkman" placeholder="怎么称呼您" value="<?=$_REQUEST['linkman']?>" type="text" />
			</fieldset>
		</div>

		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="address">收货地址 *</label>
				<input name="address" id="address" placeholder="快递送货地址" value="<?=$_REQUEST['address']?>" type="text" />
			</fieldset>
		</div>

		<div data-role="fieldcontain">
			<fieldset data-role="controlgroup">
				<label for="memo">其它要求</label>
				<input name="memo" id="memo" placeholder="不要太过份哦" value="<?=$_REQUEST['memo']?>" type="text" />
			</fieldset>
		</div>

		<input type="hidden" name="shipcyc" value="360" />
		<input type="hidden" name="shipnum" value="6" />

		<input type="hidden" name="color_id" value="10" />
		<input type="hidden" name="city" value="<?=$_REQUEST['city']?>" />
		<input type="submit" data-theme="a" data-icon="check" value="请男人妹尽快联系我" />

	</form>
	</div>
	<?php endif?>

</div>

<script>
$('#codform').on('submit',function(){
	$.mobile.showPageLoadingMsg();
	if ($('#email').val() == '' || $('#tel').val() == '' || $('#linkman').val() == '') {
		alert('带 * 号的项目必须要填写呢！');
		$.mobile.hidePageLoadingMsg();
		return false;
	}
});
</script>

