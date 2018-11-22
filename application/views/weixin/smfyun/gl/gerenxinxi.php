<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>实物奖品领取</title>
	<link rel="stylesheet" type="text/css" href="../../../sqb/css/ui.css">
	<link href="../../../sqb/favicon.ico" type="image/x-icon" rel="icon">
	<link href="../../../sqb/iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="../../../sqb/css/layout.min.css" rel="stylesheet" />
    <link href="../../../sqb/css/scs.min.css" rel="stylesheet" />
	<style type="text/css">
	.avator{
		height: 30px;
		position: absolute;
		right: 45px;
		border-radius: 15px;
	}
	#myAddrs{
    color: #80a049;
    padding-right: 30px;
	}
 textarea{
  width: 100%;
  height: 200px;
 }
.add{
    background-color: #fff;
    position: relative;
    height: 60px;
    border-bottom: 1px solid #f4f4f4;
}
.addimg{
    float: left;
    /*width: 40px;*/
    height: 100%;
    padding: 10px;
}
.addtext{
    line-height: 60px;
    color: #666666;
    font-size: 14px;
    float: left;
}
.goin{
    float: right;
    /*width: 20px;*/
    height: 100%;
    padding: 20px;
}
.edit{
    background-color: #fff;
    position: relative;
    border-bottom: 1px solid #f4f4f4;
}
.position{
    width: 9%;
    /*height: 15px;*/
    padding-left: 3%;
    padding-right: 2%;
    padding-top: 15px;
    display: inline-block;
    float: left;
}
.address{
    display: inline-block;
    width: 84%;
    padding: 10px 0 10px 0;
}
.basic{
    font-size: 14px;
    color: #333;
}
.phone{
    float: right;
}
.location{
    margin-top: 10px;
    font-size: 12px;
    color: #999;
}
.goin2{
    float: right;
    width: 6%;
    /*height: 10px;*/
    padding-left: 1%;
    padding-right: 2%;
    padding-top: 30px;
}
	</style>
</head>
<body>
	<div class="aui-container">
		<div class="aui-page">
			<div class="header header-color">
				<div class="header-background"></div>
				<div class="toolbar statusbar-padding">
					<div class="header-title">
						<div class="title"><?=$item['km_content']?></div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
                <?php if ($result['status']==0):?>
				<form method="post" id="newForm">
				<div class="fade-main aui-menu-list aui-menu-list-clear">
					<ul>
						<div class="devider b-line"></div>
                        <li class="b-line">
                            <a>
                                <img style="width:100%;" src="<?=$item['pic']?>">
                            </a>
                        </li>
                        <li class="b-line">
                            <a>
                                <h3>奖品名称</h3>
                                <span style="right:16px;"><?=$item['km_content']?><?=$item['need_money']?'（需付'.$item['need_money'].'元)':''?></span>
                            </a>
                        </li>
            <div class="edit" style="display:none">
                <image class="position" src="/qwt/wfb/position.png"></image>
                <div class="address">
                    <div class="basic">
                        <text class="name">收货人：</text>
                        <text class="phone">电话：</text>
                    </div>
                    <div class="location">收货地址：</div>
                </div>
                <image class="goin2" src="/qwt/wfb/goin.png"></image>
            </div>
            <div class="add">
                <image class="addimg" src="/qwt/wfb/add.png"></image>
                <div class="addtext">点击添加地址</div>
                <image class="goin" src="/qwt/wfb/goin.png"></image>
            </div>
            <input id="name"  type="hidden" name="data[name]" >
            <input id="tel" type="hidden" name="data[tel]" >
            <input id="prov" class='prov' type="hidden" name="s_province" >
            <input id="city" class='city' type="hidden" name="s_city" >
            <input id="dist" class='dist' type="hidden" name="s_dist">
            <input id="address"  type="hidden" name="data[address]">
					</ul>
					<div class="aui-btn-item" style="padding-top:20px;background-color:#f5f5f5;">
				<button type="submit" class="btn btn-confirms" style="margin:0 10%;width:80%;">提交</button>
			</div>
				</div>
				</form>
            <?php else:?>
                <div class="fade-main aui-menu-list aui-menu-list-clear">
                    <ul>
                        <div class="devider b-line"></div>
                        <li class="b-line">
                            <a class="link-input" data-name="name">
                                <h3>收货人</h3>
                                <span id="text-name" style="right:16px;"><?=$order->receive_name?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>联系电话</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->tel?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3 style="min-width:80px;">收货地址</h3>
                                <span id="text-tel" style="right:16px;position:inherit;"><?=$order->address?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="addr">
                                <h3 style="color:#0cc0cc;"><?=$result['neirong']?></h3>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>快递单位</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->shiptype?$order->shiptype:'无'?></span>
                            </a>
                        </li>
                        <li class="b-line">
                            <a class="link-input" data-name="tel">
                                <h3>快递单号</h3>
                                <span id="text-tel" style="right:16px;"><?=$order->shipcode?$order->shipcode:'无'?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php endif?>
				<div id="fade-input" class="aui-menu-list aui-menu-list-clear" style="display:none;">
				<div class="aui-form-cell b-line">
						<div class="aui-form-cell-tb">
							<input id="data-value" type="text" pattern="" value="" placeholder="请输入">
							<input id="data-key" type="hidden" pattern="" value="">
						</div>
					</div>
					<div class="aui-btn-item" style="padding-top:20px;background-color:#f5f5f5;">
				<a id="confirms" href="javascript:;" class="btn btn-confirms" style="margin:0 40px;">完成</a>
			</div>
				</div>
			</div>
		</div>
	</div>
</body>
    <script src="../../../sqb/js/jquery.min.js"></script>
    <script src="../../../sqb/js/jquery.scs.min.js"></script>
    <script src="../../../sqb/js/CNAddrArr.min.js"></script>
    <!-- <script type="text/javascript" src="https://www.w3cways.com/demo/vconsole/vconsole.min.js?v=2.2.0"></script> -->
<script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
	<script type="text/javascript">
    <?php if ($result['ok']==1):?>
    $(document).ready(function(){
        alert('修改成功！');
    })
    <?php endif?>

                <?php if ($result['status']==0):?>
    $('.link-input').on('click', function() {
    	var value = $(this).children('span').text();
    	var key = $(this).data('name');
    	$('#data-value').val(value);
    	$('#data-key').val(key);
    	$('.fade-main').hide();
    	$('#fade-input').show();
    });
    <?php endif?>
$(function(){
    var need_pay = <?=$item['need_money']?$item['need_money']:0?>;
    var iid = <?=$item['id']?$item['id']:0?>;
    wx.config({
        debug: 0,
        appId: '<?php echo $jsapi["appId"];?>',
        timestamp: '<?php echo $jsapi["timestamp"];?>',
        nonceStr: '<?php echo $jsapi["nonceStr"];?>',
        signature: '<?php echo $jsapi["signature"];?>',
        jsApiList: [
          // 所有要调用的 API 都要加到这个列表中
          'checkJsApi',
          'chooseWXPay'
          ]
    });
    $("#citys").citySelect({
        required: false,
        prov: "<?=$prov?>",
        city: "<?=$city?>",
        dist: "<?=$dist?>"
    });
    $('.add,.edit').click(function() {
        wx.openAddress({
            success: function (res) {
                $('#name').val(res.userName);
                $('#tel').val(res.telNumber);
                $('.prov').val(res.provinceName);
                $('.city').val(res.cityName);
                $('.dist').val(res.countryName);
                $('#address').val(res.detailInfo);
                var userName = res.userName; // 收货人姓名
                // var postalCode = res.postalCode; // 邮编
                var provinceName = res.provinceName; // 国标收货地址第一级地址（省）
                var cityName = res.cityName; // 国标收货地址第二级地址（市）
                var countryName = res.countryName; // 国标收货地址第三级地址（国家）
                var detailInfo = res.detailInfo; // 详细收货地址信息
                // var nationalCode = res.nationalCode; // 收货地址国家码
                var telNumber = res.telNumber; // 收货人手机号码
                if(userName&&provinceName&&cityName&&detailInfo&&telNumber){
                    $('.name').text('收货人：'+userName);
                    $('.phone').text('电话：'+telNumber);
                    $('.location').text('地址：'+provinceName+cityName+countryName+detailInfo);
                    $('.add').css({
                        'display': 'none'
                    });
                    $('.edit').css({
                        'display': 'block'
                    });
                }
            }
        });
    });
    $('#newForm').submit(function() {
            if ( (!$('#name').val() || !$('#tel').val() || !$('#address').val() || !$('.prov').val() || !$('.city').val()) && !$('#url').val()) {//实物
                alert('请填写完整收货信息哦！');
                return false;
            }else{
                if(need_pay>0){
                    $.ajax({
                        url: '/qwtgl/wxpay',
                        type: 'post',
                        dataType: 'json',
                        data: {iid:iid,bid:<?=$bid?>,oid:<?=$oid?>},
                    })
                    .done(function(res) {
                        console.log(res);
                        console.log("success");
                        wx.chooseWXPay({
                            timestamp: res.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                            nonceStr: res.nonceStr, // 支付签名随机串，不长于 32 位
                            package: res.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                            signType: res.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                            paySign: res.paySign, // 支付签名
                            success: function (res) {
                                // 支付成功后的回调函数
                                a = 1
                                var form = document.getElementById("newForm");
                                form.submit();
                            }
                        });
                    })
                    .fail(function(res) {
                        console.log(JSON.stringify(res));
                        console.log("error");
                        return false;
                    })
                    .always(function(res) {
                        console.log(JSON.stringify(res));
                        console.log("complete");
                        return false;
                    });
                    return false;
                }else{
                    return true;
                }
            }
            return false;
        if(type == 3){
            if(!$('#name').val() || !$('#tel').val()){
                alert('请填写完整信息哦！');
                return false;
            }
            return true;
        }
      //  if(type!=4){//4 红包

      //       if(type==3){//话费
      //           if(!$('#name').val() || !$('#tel').val()){
      //               alert('请填写完整信息哦！');
      //               return false;
      //           }
      //           return true;
      //       }else if (type==5 || type==6) {//5 优惠券  6赠品
      //           return true;
      //       }else if ( (!$('#name').val() || !$('#tel').val() || !$('#address').val() || !$('.prov').val() || !$('.city').val()) && !$('#url').val()) {//实物
      //           alert('请填写完整收货信息哦！');
      //           return false;
      //       }
      // }
      //   return true;
    });
});
				$('#confirms').on('click', function() {
    	var value = $('#data-value').val();
    	var key = $('#data-key').val();
    	$('#text-'+key).text(value);
    	$('#input-'+key).val(value);
    	$('#fade-input').hide();
    	$('.fade-main').show();
    });
    $('#complete').on('click', function() {
     $('#fade-have').hide();
     $('.fade-main').show();
    });
    $('#complete2').on('click', function() {
     $('#fade-need').hide();
     $('.fade-main').show();
    });
    $('.btnneed').on('click', function() {
     $('.fade-main').hide();
     $('#fade-need').show();
    });
    $('.btnhave').on('click', function() {
     $('.fade-main').hide();
     $('#fade-have').show();
    });

  $(function() {
    $('#avator').on('change', function() {
    		$('.avator').remove();
      $('#avator-name').html('已上传，保存提交后生效');
    });
  });
		$(document).ready(function () {
			var aMenuOneLi = $(".aui-fold-master > li");
			var aMenuTwo = $(".aui-fold-genre");
			$(".aui-fold-master > li > .aui-fold-title").each(function (i) {
				$(this).click(function () {
					if ($(aMenuTwo[i]).css("display") == "block") {
						$(aMenuTwo[i]).slideUp(300);
						$(aMenuOneLi[i]).removeClass("menu-show")
					} else {
						for (var j = 0; j < aMenuTwo.length; j++) {
							$(aMenuTwo[j]).slideUp(300);
							$(aMenuOneLi[j]).removeClass("menu-show");
						}
						$(aMenuTwo[i]).slideDown(300);
						$(aMenuOneLi[i]).addClass("menu-show")
					}
				});
			});
		});

		//地区选择

    $(function() {
        /**
         * 通过数组id获取地址列表数组
         *
         * @param {Number} id
         * @return {Array}
         */
        function getAddrsArrayById(id) {
            var results = [];
            if (addr_arr[id] != undefined)
                addr_arr[id].forEach(function(subArr) {
                    results.push({
                        key: subArr[0],
                        val: subArr[1]
                    });
                });
            else {
                return;
            }
            return results;
        }
        /**
         * 通过开始的key获取开始时应该选中开始数组中哪个元素
         *
         * @param {Array} StartArr
         * @param {Number|String} key
         * @return {Number}
         */
        function getStartIndexByKeyFromStartArr(startArr, key) {
            var result = 0;
            if (startArr != undefined)
                startArr.forEach(function(obj, index) {
                    if (obj.key == key) {
                        result = index;
                        return false;
                    }
                });
            return result;
        }

        //bind the click event for 'input' element
        $("#myAddrs").click(function() {
            var PROVINCES = [],
                startCities = [],
                startDists = [];
            //Province data，shall never change.
            addr_arr[0].forEach(function(prov) {
                PROVINCES.push({
                    key: prov[0],
                    val: prov[1]
                });
            });
            //init other data.
            var $input = $(this),
                dataKey = $input.attr("data-key"),
                provKey = 1, //default province 北京
                cityKey = 36, //default city 北京
                distKey = 37, //default district 北京东城区
                distStartIndex = 0, //default 0
                cityStartIndex = 0, //default 0
                provStartIndex = 0; //default 0

            if (dataKey != "" && dataKey != undefined) {
                var sArr = dataKey.split("-");
                if (sArr.length == 3) {
                    provKey = sArr[0];
                    cityKey = sArr[1];
                    distKey = sArr[2];

                } else if (sArr.length == 2) { //such as 台湾，香港 and the like.
                    provKey = sArr[0];
                    cityKey = sArr[1];
                }
                startCities = getAddrsArrayById(provKey);
                startDists = getAddrsArrayById(cityKey);
                provStartIndex = getStartIndexByKeyFromStartArr(PROVINCES, provKey);
                cityStartIndex = getStartIndexByKeyFromStartArr(startCities, cityKey);
                distStartIndex = getStartIndexByKeyFromStartArr(startDists, distKey);
            }
            var navArr = [{//3 scrollers, and the title and id will be as follows:
                title: "省",
                id: "scs_items_prov"
            }, {
                title: "市",
                id: "scs_items_city"
            }, {
                title: "区",
                id: "scs_items_dist"
            }];
            SCS.init({
                navArr: navArr,
                onOk: function(selectedKey, selectedValue) {
                    $input.val(selectedValue).attr("data-key", selectedKey);
                }
            });
            var distScroller = new SCS.scrollCascadeSelect({
                el: "#" + navArr[2].id,
                dataArr: startDists,
                startIndex: distStartIndex
            });
            var cityScroller = new SCS.scrollCascadeSelect({
                el: "#" + navArr[1].id,
                dataArr: startCities,
                startIndex: cityStartIndex,
                onChange: function(selectedItem, selectedIndex) {
                    distScroller.render(getAddrsArrayById(selectedItem.key), 0); //re-render distScroller when cityScroller change
                }
            });
            var provScroller = new SCS.scrollCascadeSelect({
                el: "#" + navArr[0].id,
                dataArr: PROVINCES,
                startIndex: provStartIndex,
                onChange: function(selectedItem, selectedIndex) { //re-render both cityScroller and distScroller when provScroller change
                    cityScroller.render(getAddrsArrayById(selectedItem.key), 0);
                    distScroller.render(getAddrsArrayById(cityScroller.getSelectedItem().key), 0);
                }
            });
        });
    });
	</script>
</html>
