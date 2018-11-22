!function(e){function t(n){if(i[n])return i[n].exports;var a=i[n]={exports:{},id:n,loaded:!1};return e[n].call(a.exports,a,a.exports,t),a.loaded=!0,a.exports}var i={};return t.m=e,t.c=i,t.p="/",t(0)}([function(e,t,i){e.exports=i(29)},,,,,,,,,,function(e,t){e.exports=function(e){for(var t,i,n,a,r=e.substr(e.lastIndexOf("?")+1),o=r.split("&"),c=o.length,s={},d=0;c>d;d++)o[d]&&(a=o[d].split("="),t=a[0],i=a[1],"undefined"!=typeof i&&(i=decodeURIComponent(i),n=s[t],"undefined"==typeof n?s[t]=i:"[object Array]"==Object.prototype.toString.call(n)?n.push(i):s[t]=[n,i]));return s}},,function(e,t){!function(){function t(e,t){return(/string|function/.test(typeof t)?s:c)(e,t)}function i(e,t){return"string"!=typeof e&&(t=typeof e,"number"===t?e+="":e="function"===t?i(e.call(e)):""),e}function n(e){return p[e]}function a(e){return i(e).replace(/&(?![\w#]+;)|[<>"']/g,n)}function r(e,t){if(f(e))for(var i=0,n=e.length;n>i;i++)t.call(e,e[i],i,e);else for(i in e)t.call(e,e[i],i)}function o(e,t){var i=/(\/)[^\/]+\1\.\.\1/,n=("./"+e).replace(/[^\/]+$/,""),a=n+t;for(a=a.replace(/\/\.\//g,"/");a.match(i);)a=a.replace(i,"/");return a}function c(e,i){var n=t.get(e)||d({filename:e,name:"Render Error",message:"Template not found"});return i?n(i):n}function s(e,t){if("string"==typeof t){var i=t;t=function(){return new u(i)}}var n=l[e]=function(i){try{return new t(i,e)+""}catch(n){return d(n)()}};return n.prototype=t.prototype=h,n.toString=function(){return t+""},n}function d(e){var t="{Template Error}",i=e.stack||"";if(i)i=i.split("\n").slice(0,2).join("\n");else for(var n in e)i+="<"+n+">\n"+e[n]+"\n\n";return function(){return"object"==typeof console&&console.error(t+"\n\n"+i),t}}var l=t.cache={},u=this.String,p={"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"},f=Array.isArray||function(e){return"[object Array]"==={}.toString.call(e)},h=t.utils={$helpers:{},$include:function(e,t,i){return e=o(i,e),c(e,t)},$string:i,$escape:a,$each:r},g=t.helpers=h.$helpers;t.get=function(e){return l[e.replace(/^\.\//,"")]},t.helper=function(e,t){g[e]=t},e.exports=t}()},function(e,t,i){var n=i(14),a=$(".js-page-view"),r=i(15),o=i(16),c=i(10),s={};$.extend(s,{justElementHeight:function(e,t,i,n){var a=$(document).width();e.height(a*i*n/t)},loadLazyImg:function(e){var t,i,n=$(window).height(),a=$(window).scrollTop(),r=e||$(document),o=50;i=r.find("img[data-src]"),i.each(function(e,i){var r=$(this);t=r.offset(),t.top<=n+a+o&&r.attr("src",r.attr("data-src")).removeAttr("data-src")})},isScrollBottom:function(e){var t=$(window).height(),i=$(window).scrollTop(),n=$(document).height(),e=e||50;return t+i>=n-e},showLoading:function(){var e=didi.dialog({html:'<div class="loading-logo"></div>',width:"121px",height:"121px",type:"loading",icon:"loading"});e.show(),this._loadingDialog=e},hideLoading:function(){this._loadingDialog&&this._loadingDialog.hide(),this._loadingDialog=null},showPageLoading:function(){$('<div class="page-loading" id="js-page-loading"></div>').appendTo("body")},hidePageLoading:function(){$("#js-page-loading").remove()},init:function(e){e=e||{},this.checkAppleDeclare(),this.configShare(e.share),this.bindEvents(),this.simulateClick(),this.log()},bindEvents:function(){var e=this,t=$(".js-page-view");t.on("click",".js-click",function(t){e.simulateClick(this,t)})},simulateClick:function(e,t){var i=$(e).data("link"),n=$(e).data("bind"),a=$(e).data("prevent")||!0;return a&&t&&t.preventDefault(),n?!1:void(i&&this.redirectPage(i))},checkAppleDeclare:function(){var e;didi.is("ios")&&(e=$(n()).appendTo(a))},configShare:function(e){e=e||{},r.config(e)},error:function(e){var t={},i=this;switch(e.code){case-301:didi.alert(e.msg,function(){i.redirectPage(i.router("login"),{redirecturl:i.safeUrl(location.href)})},t);break;case-999:didi.alert(e.msg,null,t);}},safeUrl:function(e){var t=["token"];return-1!=e.lastIndexOf("?")&&$.each(t,function(t,i){e=e.replace(new RegExp("(&|\\?)"+i+"=(.*?)(&|$)"),"&")}),e},getUrlParams:function(e){return c(e||location.href)},addUrlParams:function(e,t){-1==e.indexOf("?")&&(e+="?");for(var i in t)e.indexOf(i)>-1&&(e=e.replace(i+"=",""));return e+"&"+o(t)},redirectPage:function(e,t,i){var e=this.redirectUrl(e,t),i=i="undefined"==typeof i?"self":i;"self"==i?location.href=e:window.open(e)},redirectUrl:function(e,t){var i,n=t||{},a=[];return a=["topicUrl","topicName","business_id","source","channel","openid"],pageParams=this.getUrlParams(),selfParams=this.getUrlParams(e),$.each(a,function(e,t){t in pageParams&&(n[t]=pageParams[t])}),i=e.indexOf("?")>-1?e.substr(0,e.indexOf("?")):e,$.extend(selfParams,n),e=this.addUrlParams(i,selfParams),e=this.safeUrl(e)},router:function(e,t){var i="/imall/",n="",t=t||".htm";return n=i+e+t},log:function(e){var t=e||{},i={},n=["law","count-rule"],a=$("body").data("page");return-1!=$.inArray(a,n)?!1:(i=this.getUrlParams(),$.extend(t,i),t.page=a,void $.get("/xmall/api/addPvLog.json",t).then(function(e){}).fail(function(e){}))}}),e.exports=s},function(e,t,i){var n=i(12);e.exports=n("src/imall/static/component/common/apple-tip",'<div class="apple-tip" id="iphone-tip">活动由滴滴出行提供，与设备生产商Apple Inc.公司无关</div>')},function(e,t){function i(e){return o.indexOf("?")<=-1&&(o+="?"),o+"&source="+a+"&channel="+e}var n={title:"「滴滴商城」滴币兑车费，天天换新品",url:location.href.replace(/&token=(.*?)/,"&"),icon:"http://static.diditaxi.com.cn/activity/img-mall/share.jpg",content:"滴币可以当钱花，还有各种精美礼品，天天有惊喜，快快来兑换吧~ 『戳』",qzone:!1,sina_weibo:!1,qq_appmsg:!1,weixin_timeline_title:"「滴滴出行」点我兑走车费 ，每日均有新品，等你兑换！『戳』",weixin_timeline_url:""},a="weixin_source",r={timeline:{index:3009,detail:3010,activity:3011},appmsg:{index:3012,detail:3013,activity:3014}},o=location.href.replace(/&token=(.*?)/,"&");n.url=i(r.appmsg.index),n.weixin_timeline_url=i(r.timeline.index),e.exports={config:function(e){e=e||{},e.page&&(e.url=i(r.appmsg[e.page]),e.weixin_timeline_url=i(r.timeline[e.page]));var t=$.extend({},n,e);didi.bridge.setShare(t)}}},function(e,t){function i(e,t){var i,n,a;if("function"==typeof t)for(n in e)if(e.hasOwnProperty(n)&&(a=e[n],i=t.call(e,a,n),i===!1))break;return e}e.exports=function(e,t){var n,a=[];return i(e,function(e,t){if("[object Array]"==Object.prototype.toString.call(e))for(n=e.length;n--;)a.push(t+"="+encodeURIComponent(e[n],t));else a.push(t+"="+encodeURIComponent(e,t))}),a.join("&")}},function(e,t,i){var n=i(10),a=i(16);i(18),e.exports=function(e){function t(){var e=s.get();return d[e]=$.Deferred(),didi.bridge.getUserInfo(function(t){d[e].resolve(t)}),d[e].promise()}function i(){var e=s.get();return d[e]=$.Deferred(),didi.bridge.getLocationInfo(function(t){d[e].resolve(t)}),d[e].promise()}var r=n(location.href),o=$.cookie("vinfo")?n($.cookie("vinfo")):{},c={uid:r.uid||o.uid||"",token:r.token||o.token||"",lat:r.lat||o.lat||"",lng:r.lng||o.lng||"",city_id:r.city_id||o.city_id||""};c.is_login=c.token?1:0,c.has_location=(c.lat&&c.lng)|c.city_id?1:0;var s=function(){var e=0;return{get:function(){return e++}}}();window.userInfo={},window.d=[];var d=window.d;!c.is_login&&didi.is("didi")?(t(),i(),$.when.apply(this,d).then(function(){for(var t={},i=0;i<arguments.length;i++)$.extend(t,arguments[i]);$.cookie("vinfo",a(t),{domain:".xiaojukeji.com",path:"/","max-age":5184e3,expires:30}),$.cookie("vinfo",a(t),{domain:".diditaxi.com.cn",path:"/","max-age":5184e3,expires:30}),t.is_login=1,t.has_location=1,window.userInfo=t,e()})):(c.uid|c.is_login|c.has_location&&($.cookie("vinfo",a(c),{domain:".xiaojukeji.com",path:"/","max-age":5184e3,expires:30}),$.cookie("vinfo",a(c),{domain:".diditaxi.com.cn",path:"/","max-age":5184e3,expires:30})),window.userInfo=c,e())}},function(e,t){/*!
	 * jQuery Cookie Plugin v1.4.1
	 * https://github.com/carhartl/jquery-cookie
	 *
	 * Copyright 2006, 2014 Klaus Hartl
	 * Released under the MIT license
	 */
!function(e){
	function t(e){
		return c.raw?e:encodeURIComponent(e)
	}
	function i(e){
		return c.raw?e:decodeURIComponent(e)
	}
	function n(e){
		return t(c.json?JSON.stringify(e):String(e))
	}
	function a(e){
		0===e.indexOf('"')&&(e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return e=decodeURIComponent(e.replace(o," ")),c.json?JSON.parse(e):e}catch(t){}
	}
	function r(t,i){
		var n=c.raw?t:a(t);return e.isFunction(i)?i(n):n}var o=/\+/g,c=e.cookie=function(a,o,s)
		{
			if(arguments.length>1&&!e.isFunction(o)){
				if(s=e.extend({},c.defaults,s),"number"==typeof s.expires){
					var d=s.expires,l=s.expires=new Date;l.setMilliseconds(l.getMilliseconds()+864e5*d)
				}return document.cookie=[t(a),"=",n(o),s.expires?"; expires="+s.expires.toUTCString():"",s.path?"; path="+s.path:"",s.domain?"; domain="+s.domain:"",s.secure?"; secure":""].join("")
			}
			for(var u=a?void 0:{},p=document.cookie?document.cookie.split("; "):[],f=0,h=p.length;h>f;f++){
				var g=p[f].split("="),m=i(g.shift()),v=g.join("=");if(a===m){
					u=r(v,o);break}a||void 0===(v=r(v))||(u[m]=v)}return u
				};c.defaults={},e.removeCookie=function(t,i){
					return e.cookie(t,"",e.extend({},i,{expires:-1})),!e.cookie(t)}
				}($)},,,,,,,,,,,
function(e,t,i){
	function n(){
		this.data=null,this.price={},
		this.actType=0,
		this.$view=$("#page-view-detail"),
		this.$footer=$("#page-footer"),
		this.init()
	}
	i(30);
	var a=i(32),r=i(33),o=i(34),c=i(13),s=i(17),d=i(10),l=i(35),u=i(36),
		p={
			comShortage:{msg:"您的滴币不够,打车赚取更多滴币",
			icon:"s-exchange.png",
			iconType:"no-full",width:"28px",height:"23px",val:"确定"
				},
				win:{
					msg:"恭喜您,中奖啦~",
					icon:"yes_win.png",
					iconType:"luck-draw",
					width:"30px",
					height:"26px",
					val:"查看详情",
					url:c.router("record")
				},
				noWin:{
					msg:"很遗憾呢,没有中奖~",
				    icon:"no_win.png",
				    iconType:"luck-draw-fail",
				    width:"30px",height:"26px",
				    val:"我知道了"
				},
				exchangeOk:{
					msg:"兑换成功",
					icon:"yes_win.png",
					iconType:"luck-draw",
					width:"30px",height:"26px",
					val:"查看详情",
					url:c.router("record")
				},
				noProduct:{
					msg:"商品不存在"
				},
				categoryNoProduct:{
					msg:"该分类下没有商品"
				},
				modNoProduct:{
					msg:"模块没有商品"
				},
				todayShortage:{
					msg:"今日库存不足"
				},
				
			},
			f={
				exchange:"立即兑换",
				draw:"立即抽奖",
				cash:"立即购买",
				todayShortage:"今日已抢完",
				maxOneCount:"已兑完",
				dayMaxOneCount:"今日已兑完",
				comShortage:"滴币不足",
				activityEnd:"兑换已结束",
				userLevelNo:"等级不符",
				offShelf:"商品已下架"
			},
			h={"-1":"todayShortage","-2":"userLevelNo","-3":"activityEnd","-4":"comShortage","-5":"offShelf"
		};
		$.extend(
			n.prototype,{
				init:function(){
					var e=this,t={},i={};t=d(location.href),
					$.extend(t,i,{_t:(new Date).getTime()}),
					$.getJSON("/xmall/api/detail.json",t).then(function(t){
						e.prepare(t),e.render(),e.bindEvents(),e.mounted(),c.init({share:{page:"detail"}})}).fail(function(e){c.error({code:e.status})}).always(function(e){c.hidePageLoading()})},
					prepare:function(e){return 0!=e.ret.code?void c.error(e.ret):(this.data=e.data,this.data.coupon=u,void(this.actType=e.data.detail.activity_type))},render:function(){this.$view.html(a(this.data))},mounted:function(){var e,t=this,i=this.data,n=d(location.href),a=($(".js-pay"),"#detail-banners"),r=0;r=$(a).find(".swiper-slide").length,new Swiper(a,{loop:r>1,pagination:r>1?".swiper-pagination":void 0}),e=n.exchangePriceType,e?t.setPrice(e):$.each(i.detail.prices,function(e,i){i.selected&&t.setPrice(i.type)}),this.checkButtonState(),this.storeOrderData()},storeOrderData:function(){var e={name:"",link:"",imgUrl:"",merchant_name:"",price:{}},t={},i=this.data.detail;e.name=i.name,e.price=this.price,e.merchant_name=i.merchant_name,e.imgUrl=i.detail_img_list[0],e.link=location.href,e.stock=8,e.id=i.product_id||c.getUrlParams().product_id||0,t={goods:[e],comCount:this.data.userInfo.points},l.set("_order-data",t)},checkButtonState:function(){var e=this.data.btnStatus,t=$(".js-pay");return this.data.userInfo.login?void(0>e?t.text(f[h[e]]):(t.removeClass("disabled"),this.updatePayBtnText())):(t.removeClass("disabled"),void this.updatePayBtnText())},updatePayBtnText:function(){var e=$(".js-pay"),t=this.price.type;this.price.comCount;if(10==this.actType)e.text(f.draw);else switch(t){case 1:case 2:e.text(f.cash);break;case 0:e.text(f.exchange)}},onPriceSelected:function(e){var t,i,n="state-selected";return e.is("."+n)?!1:(i=e.parent().find(".js-price-select.state-selected"),t=e.data("type"),void("undefined"!=t&&(i.removeClass(n).find(".mall-radio").removeClass("checked"),e.addClass(n).find(".mall-radio").addClass("checked"),this.setPrice(t))))},setPrice:function(e){var t=this.data.detail.prices,i=this;$.each(t,function(t,n){n.type==e&&(i.price=n,i.priceUpdated())})},priceUpdated:function(){var e=$(".js-total-price");$(".js-pay");e.html(r(this.price)),this.updatePayBtnText()},exChangeResponse:function(e){},alert:function(e){var t,i;i={},t="string"==typeof e?e:e.msg||"",i.icon=e.iconType||"alert",e.val&&(i.text=e.val),didi.alert(t,function(){e.url&&(location.href=e.url)},i)},payHandler:function(e){if(!e.is(".disabled")){if(!userInfo.is_login)return void c.redirectPage(c.router("login"),{redirecturl:c.addUrlParams(c.safeUrl(location.href),{exchangePriceType:this.price.type})});var t=this.actType;10==t?this.luckDraw():0!=t&&1!=t&&2!=t||this.exchangeGoods()}},luckDraw:function(){didi.dialog({html:o()}).show(),this.doExchange()},exchangeGoods:function(){var e=this,t=this.price;return 0!=t.type?(this.storeOrderData(),void c.redirectPage("/views/imall/order-confirm.htm?a=3",{})):void didi.confirm("确认兑换吗？",function(){e.doExchange()},function(){},{icon:"exchange"})},doExchange:function(){var e=this,t={};t=d(location.href),$.extend(t,{}),$.post("/xmall/api/exchange.json",t).then(function(t){e.exchangeResponse(t)}).fail(function(e){c.error({code:e.status})}).always(function(e){})},exchangeResponse:function(e){var t,i=this,n=e.ret.code,a=(e.ret.msg,$(".js-pay")),r=3e3;switch(n){case 0:t=e.data.productType,0==t||2==t?this.alert(p.exchangeOk):10==t&&setTimeout(function(){i.alert(p.win)},r);break;case-101:this.alert(p.noProduct);break;case-102:this.alert(p.categoryNoProduct);break;case-103:this.alert(p.modNoProduct);break;case-104:this.alert(p.todayShortage),a.addClass("disabled").text(f.todayShortage);break;case-105:this.alert(p.allShortage);break;case-106:this.alert(p.comShortage),a.addClass("disabled").text(f.comShortage);break;case-107:this.alert(p.maxOneCount),a.addClass("disabled").text(f.maxOneCount);break;case-108:this.alert(p.dayMaxOneCount),a.addClass("disabled").text(f.dayMaxOneCount);break;case-109:setTimeout(function(){i.alert(p.noWin)},r);break;case-201:this.alert(p.missParams);break;default:c.error(e.ret)}},bindEvents:function(){var e=$(document),t=this;e.touch(".js-price-select",function(e){t.onPriceSelected($(this))}).touch(".js-pay",function(e){t.payHandler($(this))})}}),s(function(){new n})},function(e,t){},,function(e,t,i){var n=i(12);i(33),e.exports=n("src/imall/static/component/detail/main",function(e,t){"use strict";var i=this,n=(i.$helpers,e.detail),a=i.$each,r=(e.imgurl,e.$index,i.$escape),o=(e.price,function(n,a){a=a||e;var r=i.$include(n,a,t);return u+=r}),c=e.btnText,s=e.isDescJson,d=(e.row,i.$string),l=e.coupon,u="";return u+='<div class="detail-header" id="detail-header"> <div class="goods-imgs swiper-container with-img-holder" id="detail-banners"> <div class="swiper-wrapper"> ',n.detail_img_list&&n.detail_img_list.length>0?(u+=" ",a(n.detail_img_list,function(e,t){u+=' <div class="swiper-slide"> <img src="',u+=r(e),u+='" alt=""> </div> '}),u+=" "):u+=' <img src="//static.diditaxi.com.cn/activity/img-mall/didi_2.png" alt="" width="100%"> ',u+=' </div> <div class="swiper-pagination"></div> </div> <div class="goods-info"> <span class="type">',u+=r(n.merchant_name),u+='</span> <span class="name">',u+=r(n.name),u+='</span> </div> </div> <div id="chose-price" class="chose-price" ',1==n.prices.length&&(u+='style="display:none"'),u+="> ",a(n.prices,function(e,t){u+=' <div class="price-row js-price-select ',e.selected&&(u+="state-selected"),u+='" data-type="',u+=r(e.type),u+='"> <!-- ',0==e.type?(u+=" <span> <b>",u+=r(e.comCount),u+="</b>滴币+<b>",u+=r(e.cashCount),u+="</b>元 </span> "):1==e.type?(u+=" <span> <b>",u+=r(e.comCount),u+="</b>滴币 </span> "):(u+=" <span> <b>",u+=r(e.cashCount),u+="</b>元 </span> "),u+=" --> ",o("./totalPrice",e),u+=' <span class="check mall-radio ',e.selected&&(u+="checked"),u+='"> </span> </div> '}),u+=' </div> <div id="pay-bar" class="pay-bar"> 单价： <span class="pay-total js-total-price"> ',a(n.prices,function(e,t){u+=" ",e.selected&&(u+=" ",o("./totalPrice",e),u+=" "),u+=" "}),u+=' </span> <a href="javascript:;" class="pay-btn js-pay disabled">',u+=r(c),u+='</a> </div> <div class="desc-main" id="desc-main"> ',s?(u+='  <div class="desc-content"> ',a(n.description,function(e,t){u+=' <div class="row"> <span class="hd">',u+=r(e.title),u+='</span> <p class="bd">',u+=d(e.cont),u+="</p> </div> "}),u+=" </div> "):(u+='  <div class="rich-txt"> ',u+=d(n.description),u+=" </div> "),u+=' </div> <div id="coupon-area" class="coupon-area"> <a href="',u+=r(l.link),u+='"> <img src="',u+=r(l.imgLarge),u+='" alt=""> </a> </div> <div class="important-tip"> <span class="hd">重要说明</span> <div class="bd"> 商品兑换流程请仔细参照商品详情页的“兑换流程”、“注意事项”与“使用时间”，除商品本身不能正常兑换外，商品一经兑换，一律不退还滴币。（如商品过期、兑换流程操作失误、仅限新用户兑换） </div> </div> ',new String(u)})},function(e,t,i){var n=i(12);e.exports=n("src/imall/static/component/detail/totalPrice",function(e,t){"use strict";var i=this,n=(i.$helpers,e.type),a=i.$escape,r=e.comCount,o=e.cashCount,c="";return 2==n?(c+=' <b class="coins">',c+=a(r),c+="</b>滴币",o&&(c+='&nbsp;+&nbsp;<b class="cash">',c+=a(o),c+="</b>元"),c+=" "):0==n?(c+=' <b class="coins">',c+=a(r),c+="</b>滴币 "):(c+=' <b class="cash">',c+=a(o),c+="</b>元 "),new String(c)})},function(e,t,i){var n=i(12);e.exports=n("src/imall/static/component/detail/draw",'<div class="luck-draw" id="draw"> <div class="draw-bg"> <div class="draw-icon"></div> </div> <div class="draw-text">奋力开奖中, 稍等哦~</div> </div>')},function(e,t,i){var n,n;(function(t){!function(t){e.exports=t()}(function(){return function e(t,i,a){function r(c,s){if(!i[c]){if(!t[c]){var d="function"==typeof n&&n;if(!s&&d)return n(c,!0);if(o)return o(c,!0);var l=new Error("Cannot find module '"+c+"'");throw l.code="MODULE_NOT_FOUND",l}var u=i[c]={exports:{}};t[c][0].call(u.exports,function(e){var i=t[c][1][e];return r(i?i:e)},u,u.exports,e,t,i,a)}return i[c].exports}for(var o="function"==typeof n&&n,c=0;c<a.length;c++)r(a[c]);return r}({1:[function(e,i,n){(function(e){"use strict";i.exports=function(){function t(){try{return o in a&&a[o]}catch(e){return!1}}var i,n={},a="undefined"!=typeof window?window:e,r=a.document,o="localStorage",c="script";if(n.disabled=!1,n.version="1.3.20",n.set=function(e,t){},n.get=function(e,t){},n.has=function(e){return void 0!==n.get(e)},n.remove=function(e){},n.clear=function(){},n.transact=function(e,t,i){null==i&&(i=t,t=null),null==t&&(t={});var a=n.get(e,t);i(a),n.set(e,a)},n.getAll=function(){},n.forEach=function(){},n.serialize=function(e){return JSON.stringify(e)},n.deserialize=function(e){if("string"==typeof e)try{return JSON.parse(e)}catch(t){return e||void 0}},t())i=a[o],n.set=function(e,t){return void 0===t?n.remove(e):(i.setItem(e,n.serialize(t)),t)},n.get=function(e,t){var a=n.deserialize(i.getItem(e));return void 0===a?t:a},n.remove=function(e){i.removeItem(e)},n.clear=function(){i.clear()},n.getAll=function(){var e={};return n.forEach(function(t,i){e[t]=i}),e},n.forEach=function(e){for(var t=0;t<i.length;t++){var a=i.key(t);e(a,n.get(a))}};else if(r&&r.documentElement.addBehavior){var s,d;try{d=new ActiveXObject("htmlfile"),d.open(),d.write("<"+c+">document.w=window</"+c+'><iframe src="/favicon.ico"></iframe>'),d.close(),s=d.w.frames[0].document,i=s.createElement("div")}catch(l){i=r.createElement("div"),s=r.body}var u=function(e){return function(){var t=Array.prototype.slice.call(arguments,0);t.unshift(i),s.appendChild(i),i.addBehavior("#default#userData"),i.load(o);var a=e.apply(n,t);return s.removeChild(i),a}},p=new RegExp("[!\"#$%&'()*+,/\\\\:;<=>?@[\\]^`{|}~]","g"),f=function(e){return e.replace(/^d/,"___$&").replace(p,"___")};n.set=u(function(e,t,i){return t=f(t),void 0===i?n.remove(t):(e.setAttribute(t,n.serialize(i)),e.save(o),i)}),n.get=u(function(e,t,i){t=f(t);var a=n.deserialize(e.getAttribute(t));return void 0===a?i:a}),n.remove=u(function(e,t){t=f(t),e.removeAttribute(t),e.save(o)}),n.clear=u(function(e){var t=e.XMLDocument.documentElement.attributes;e.load(o);for(var i=t.length-1;i>=0;i--)e.removeAttribute(t[i].name);e.save(o)}),n.getAll=function(e){var t={};return n.forEach(function(e,i){t[e]=i}),t},n.forEach=u(function(e,t){for(var i,a=e.XMLDocument.documentElement.attributes,r=0;i=a[r];++r)t(i.name,n.deserialize(e.getAttribute(i.name)))})}try{var h="__storejs__";n.set(h,h),n.get(h)!=h&&(n.disabled=!0),n.remove(h)}catch(l){n.disabled=!0}return n.enabled=!n.disabled,n}()}).call(this,"undefined"!=typeof t?t:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}]},{},[1])(1)})}).call(t,function(){return this}())},function(e,t){e.exports={imgLarge:"//webapp.didistatic.com/static/webapp/ios/6171615detail.jpg",imgSmall:"//webapp.didistatic.com/static/webapp/ios/6171615index.jpg",link:"http://xmall.didistatic.com/static/xmall/web_mis/%E6%89%93%E8%BD%A6%E5%88%B8%E7%AB%AF%E5%8D%88%E8%8A%82%E6%B4%BB%E5%8A%A81465281547435.html"}}]);