<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>个人信息</title>
	<link rel="stylesheet" type="text/css" href="css/ui.css">
	<link href="favicon.ico" type="image/x-icon" rel="icon">
	<link href="iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="css/layout.min.css" rel="stylesheet" />
    <link href="css/scs.min.css" rel="stylesheet" />
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
	</style>
</head>
<body>
	<div class="aui-container">
		<div class="aui-page">
			<div class="header header-color">
				<div class="header-background"></div>
				<div class="toolbar statusbar-padding">
					<button class="bar-button back-button"  onclick="history.go(-1);"><i class="icon icon-back-sx"></i></button>
					<div class="header-title">
						<div class="title">个人信息</div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
				<form method="post">
				<div class="fade-main aui-menu-list aui-menu-list-clear">
					<ul>
						<li class="b-line">
							<a href="javascript:;">
								<div class="aui-icon"><img src="images/icon-png/icon-read4.png"></div>
								<h3>头像</h3>
								<span id="avator-name" class="aui-an-red"></span>
								<img class="avator" src="<?=$avator?>">
							</a>
						<!-- <input placeholder="" name="avator" id="avator" class="aui-up-input" type="file" accept="image/*" multiple=""> -->
						</li>
						<li class="b-line">
							<a data-name="nickname">
								<div class="aui-icon"><img src="images/icon-png/icon-read1.png"></div>
								<h3>昵称</h3>
								<span id="text-nickname" class="aui-an-red"><?=$user->nickname?></span>
								<input id="input-nickname" name="text[nickname]" type="hidden" value="<?=$user->nickname?>">
							</a>
						</li>
                        <?php if ($user->admin==1):?>
                        <div class="devider b-line"></div>
                        <li class="b-line">
                            <a class="link-input" data-name="notice">
                                <h3>公告设置</h3>
                                <span id="text-notice" class="aui-an-red"><?=$notice->value?></span>
                                <input id="input-notice" name="notice" type="hidden" value="<?=$notice->value?>">
                                <div class="aui-time"><i class="aui-jump"></i></div>
                            </a>
                        </li>
                    <?php endif?>
						<div class="devider b-line"></div>
						<li class="b-line">
							<a class="link-input" data-name="job">
								<h3>行业/方向</h3>
								<span id="text-job"><?=$user->job?></span>
								<input id="input-job" name="text[job]" type="hidden" value="<?=$user->job?>">
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
						<li class="b-line">
							<a class="link-input" data-name="company">
								<h3>公司</h3>
								<span id="text-company"><?=$user->company?></span>
								<input id="input-company" name="text[company]" type="hidden" value="<?=$user->company?>">
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
						<li class="b-line">
							<a href="javascript:;">
								<h3>地区</h3>
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
        <input type="text" readonly id="myAddrs" name="text[area]" data-key="4-84-1298" value="<?=$user->area?>" />
						</li>
						<div class="devider b-line"></div>
						<li class="b-line">
							<a class="btnhave">
								<div class="aui-icon"><img src="images/icon-png/icon-read11.png"></div>
								<h3>手上能对接的资源</h3>
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
						<li class="b-line">
							<a class="btnneed">
								<div class="aui-icon"><img src="images/icon-png/icon-read12.png"></div>
								<h3>我需要对接什么资源</h3>
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
						<div class="devider b-line"></div>
						<li class="b-line">
							<a class="link-input" data-name="fansnum">
								<h3>平台粉丝量</h3>
								<span id="text-fansnum" class="aui-an-red"><?=$user->fansnum?></span>
								<input id="input-fansnum" name="text[fansnum]" type="hidden" value="<?=$user->fansnum?>">
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
						<li class="b-line">
							<a class="link-input" data-name="sellsnum">
								<h3>年销量</h3>
								<span id="text-sellsnum" class="aui-an-red"><?=$user->sellsnum?></span>
								<input id="input-sellsnum" name="text[sellsnum]" type="hidden" value="<?=$user->sellsnum?>">
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
					</ul>
					<div class="aui-btn-item" style="padding-top:20px;background-color:#f5f5f5;">
				<button type="submit" class="btn btn-confirms" style="margin:0 10%;width:80%;">保存并提交</button>
			</div>
				</div>
				<div id="fade-have" class="aui-menu-list aui-menu-list-clear" style="display:none;">
				<div class="aui-form-cell b-line">
      <textarea name="text[ihave]" value="<?=$user->ihave?>">
      </textarea>
					</div>
					<div class="aui-btn-item" style="padding-top:20px;background-color:#f5f5f5;">
				<a id="complete" href="javascript:;" class="btn btn-confirms" style="margin:0 40px;">完成</a>
			</div>
				</div>
				<div id="fade-need" class="aui-menu-list aui-menu-list-clear" style="display:none;">
    <div class="aui-form-cell b-line">
      <textarea name="text[ineed]" value="<?=$user->ineed?>">
      </textarea>
     </div>
					<div class="aui-btn-item" style="padding-top:20px;background-color:#f5f5f5;">
				<a id="complete2" href="javascript:;" class="btn btn-confirms" style="margin:0 40px;">完成</a>
			</div>
				</div>
				</form>
				<div id="fade-input" class="aui-menu-list aui-menu-list-clear" style="display:none;">
				<div class="aui-form-cell b-line">
						<div class="aui-form-cell-tb">
							<input id="data-value" type="text" pattern="" value="">
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
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.scs.min.js"></script>
    <script src="js/CNAddrArr.min.js"></script>
	<script type="text/javascript">
    <?php if ($result['ok']==1):?>
    $(document).ready(function(){
        alert('修改成功！');
    })
    <?php endif?>

    $('.link-input').on('click', function() {
    	var value = $(this).children('span').text();
    	var key = $(this).data('name');
    	$('#data-value').val(value);
    	$('#data-key').val(key);
    	$('.fade-main').hide();
    	$('#fade-input').show();
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
