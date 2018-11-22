(function(window){
	var mine={};
	mine.alertArr=new Array(4);
	//积分消耗弹窗
	mine.alertArr[0]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>参加本次活动将扣除您100积分</div><div class='bottom_1'><button class='button confirm'>确定</button><button class='button cancel'>取消</button></div></div></div>";
	//注册弹窗
	mine.alertArr[1]="<div class='show'><div class='bg'></div><div class='content_2'><div class='top_2'><div class='phone'><span>输入手机号</span><span><input type='text' placeHolder='请输入手机号' class='phone_num'/></span></div><div class='code'><span>输入验证码</span><span><input type='text' class='code_num'/></span><button class='button getcode'>获取验证码</button></div><div class='alert'></div></div><div class='bottom_2'><button class='button cancel cancel_2'>取消</button><button class='button confirm confirm_2'>确定</button></div></div></div>";
	mine.alertArr[2]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>%s</div><div class='bottom_1'><button class='button get'>领取</button></div></div></div>";//领取奖品弹窗
	mine.alertArr[3]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>%s</div><div class='bottom_1'><button class='button close'>关闭</button></div></div></div>";//未中奖弹窗，其他提示消息弹窗
	//领取奖品会员弹窗
	mine.alertArr[4]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>%s</div><div class='bottom_1'><button class='button look'>查看</button><button class='button more'>再来一次</button></div></div></div>";//领取奖品是会员的弹窗
	//非会员弹窗模板
	mine.alertArr[5]="<div class='show'><div class='bg'></div><div class='content_3'><div class='top_3'><div class='word_top'>辛巴克买一送一券</div><div class='word_middle'>已存入您的钱包</div><div class='word_bottom'>进入您波波意向撒的发生的发生大厦粉丝粉丝发发发啥爱上大幅度发斯蒂芬</div></div><div class='bottom_3'><div class='bottom_top'>关注您拨印象成</div><div class='erweima'><img src='http://s.demo.shangquanquan.com/wx/img/yinni_comm/goldEggs/er.jpg'></div><div class='bottom_bottom'>长按上方二维码即可关注宁波印象城微信公众号</div></div></div></div>";
	mine.show=function(){
		var width=document.documentElement.clientWidth;
		var height=document.documentElement.clientHeight;
		$(this).css(
			{'left':(width-$(this).width())/2,'top':(height-$(this).height())/2});
		// $('.show').css('height',20000);
		// $('.bg').css('height',20000);
		$('.show').addClass('animated fadeIn'); 
	}
	mine.close=function(){
		$('.show').removeClass('animated fadeIn').addClass('animated fadeOut');
		$('.show').remove();
	}
	window.mine=mine;
})(window)