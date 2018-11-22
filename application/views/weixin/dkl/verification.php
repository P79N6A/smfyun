<!DOCTYPE html>
<html lang="zh-cmn-Hans">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>核销后台</title>
    <link rel="stylesheet" href="http://cdn.jfb.smfyun.com/dkl/weui.css" />
    <link rel="stylesheet" href="http://cdn.jfb.smfyun.com/dkl/example.css" />
    <style type="text/css">
    table{
    	width: 100%;
    }
    td{
    	padding: 8px;
    	border-top: 1px solid #ddd;
    	min-width:48px;
    }
    .weui-tab__panel{
    	height: auto;
    }
    .list-table{
    	position: absolute;
    	top: 44px;
    	bottom: 53px;
    	left: 0;
    	right: 0;
    	padding: 10px;
    	overflow: scroll;
    	font-size: 8px;
    	color: #999999;
    	text-align: center;
    	background-color: #fff
    }

    .weui-search-bar__enter-btn {
        display: none;
        margin-left: 10px;
        line-height: 28px;
        color: #09BB07;
        white-space: nowrap;
    }

    .weui-search-bar__cancel-btn {
        padding-left: 10px;
        border-left: 1px solid #e5e5e5;
    }

    .weui-search-bar.weui-search-bar_focusing .weui-search-bar__enter-btn {
        display: block;
    }
    .weui-form-preview__ft{
        border-bottom: 2px solid #e5e5e5;
    }
    .weui-form-preview__bd{
        border-bottom: 1px solid #e5e5e5;
    }
    #nomore{

    height: 30px;
    text-align: center;
    line-height: 30px;
    font-size: 14px;
    color: #666666;
    }
    .orders{

    margin-top: 5px;
    border-top: 1px solid #e5e5e5;
    background-color: #efefef;
    }
    </style>
</head>

<body ontouchstart>
    <div class="weui-toptips weui-toptips_warn js_tooltips">错误提示</div>
    <div class="container" id="container"></div>
    <div class="page navbar js_show">
        <!--主页面-->
        <div class="page__bd" style="height: 100%;">
            <div class="weui-tab">
            	<!--搜索栏-->
                <div class="search-box" style="display:block">
                    <div class="page__bd">
                        <div class="weui-search-bar" id="searchBar">
                            <form class="weui-search-bar__form">
                                <div class="weui-search-bar__box">
                                    <i class="weui-icon-search"></i>
                                    <input type="search" class="weui-search-bar__input" id="searchInput" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="搜索" required/>
                                    <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                                </div>
                                <label class="weui-search-bar__label" id="searchText">
                                    <i class="weui-icon-search"></i>
                                    <span>搜索</span>
                                </label>
                            </form>
                            <a href="javascript:" class="weui-search-bar__enter-btn" id="searchStart">确认</a>
                            <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
                        </div>
                    </div>
                </div>
                <!--我要核销view-->
                <div id="verify-do" class="weui-tab__panel" style="display:block">
                    <div class="page__bd">
                        <div id="detail" class="weui-form-preview" style="display:none"><!--
                            <div class="weui-form-preview__hd">
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">电话号码</label>
                                    <em class="weui-form-preview__value">+86 123455677</em>
                                </div>
                            </div>
                            <div class="weui-form-preview__bd">
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">微信昵称</label>
                                    <span class="weui-form-preview__value">王振</span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">兑换产品</label>
                                    <span class="weui-form-preview__value">1</span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">兑换时间</label>
                                    <span class="weui-form-preview__value">2012年3月4日</span>
                                </div>
                                <div class="weui-form-preview__item">
                                    <label class="weui-form-preview__label">核销状态</label>
                                    <span class="weui-form-preview__value">未核销</span>
                                </div>
                            </div>
                            <div class="weui-form-preview__ft">
                                <a class="weui-form-preview__btn weui-form-preview__btn_primary" href="javascript:">核销</a>
                            </div> -->
                        </div>
                    </div>
                </div>
                <!--确认核销警告-->
                <div id="dialogs">
                    <div class="js_dialog" id="iosDialog1" style="display: none;">
                        <div class="weui-mask"></div>
                        <div class="weui-dialog">
                            <div class="weui-dialog__hd"><strong class="weui-dialog__title">确认核销</strong></div>
                            <div class="weui-dialog__bd">是否确定核销，确认后无法修改</div>
                            <div class="weui-dialog__ft">
                                <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
                                <a href="javascript:;" id="verify-doing" class="weui-dialog__btn weui-dialog__btn_primary" value="">确认</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--该账号核销客户列表-->
                <div id="verify-done" class="weui-tab__panel" style="display:none">
                    <div class="page__bd">
                        <div id="detail-readonly" class="weui-form-preview" style="display:none;z-index:1">
                            <div id="detail-record">

                            </div>
                            <div class="weui-form-preview__ft">
                               	<a class="weui-form-preview__btn weui-form-preview__btn_default" href="javascript:">关闭<!-- 如果已核销不显示？ --></a>
                            </div>
                    <div id="nomore" class="page__bd">没有更多了
                    </div>
                        </div>
                        <div class="list-table">
                        	<table>
                        		<tbody>
                        			<tr>
                        				<td>微信昵称</td>
                        				<td>电话号码</td>
                        				<td>兑换产品</td>
                        				<td>核销时间</td>
                        			</tr>
                                    <?php
                                    foreach($result['order2s'] as $order):
                                    ?>
                        			<tr>
                        				<td><?=$order->user->nickname?></td>
                        				<td><?=$order->tel?></td>
                        				<td><?=$order->item->name?></td>
                        				<td><?=date('m-d H:i',$order->tag_time)?></td>
                        			</tr>
                                    <?php endforeach;?>
                        		</tbody>
                        	</table>
                        </div>
                    </div>
                </div>
                <!--底部菜单栏-->
                <div class="weui-tabbar">
                    <a href="javascript:;" id="verification" class="weui-tabbar__item weui-bar__item_on">
                        <img src="http://cdn.jfb.smfyun.com/dkl/verification.png" alt="" class="weui-tabbar__icon">
                        <p class="weui-tabbar__label">我要核销</p>
                    </a>
                    <a href="javascript:;" id="record" class="weui-tabbar__item">
                        <img src="http://cdn.jfb.smfyun.com/dkl/record.png" alt="" class="weui-tabbar__icon">
                        <p class="weui-tabbar__label">我的核销记录</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
    window.type=1;
            console.log(window.type);

    var a= $('.weui-tab').height();
    var b= $('.search-box').height();
    var c= $('.weui-tabbar').height();
    $('#verify-do').css('height',a-b-c);
    $('#verify-done').css('height',a-b-c);
    </script>
    <script type="text/javascript">

    //底部菜单栏
    $(function(){
    	$('#verification').on('click',function(){
    		$('.search-box').show();
    		$('#verify-do').show();
    		$('#verify-done').hide();
        	$('#detail').hide();
        	$('#detail-readonly').hide();
            window.type=1;
            console.log(window.type);
    	});
    	$('#record').on('click',function(){
    		$('.search-box').show();
    		$('#verify-do').hide();
    		$('#verify-done').show();
        	$('#detail').hide();
        	$('#detail-readonly').hide();
            window.type=2;
            console.log(window.type);
    	});
    });
    $(function() {
        $('.weui-tabbar__item').on('click', function() {
            $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
        });
    });
    //搜索栏
    $(function() {
        var $searchBar = $('#searchBar'),
            $searchResult = $('#searchResult'),
            $searchText = $('#searchText'),
            $searchInput = $('#searchInput'),
            $searchClear = $('#searchClear'),
            $searchCancel = $('#searchCancel'),
            $searchStart = $('#searchStart');

        function hideSearchResult() {
            $searchResult.hide();
            $searchInput.val('');
        }

        function cancelSearch() {
            hideSearchResult();
            $searchBar.removeClass('weui-search-bar_focusing');
            $searchText.show();
        }

        $searchText.on('click', function() {
            $searchBar.addClass('weui-search-bar_focusing');
            $searchInput.focus();
        });
        $searchInput
            .on('blur', function() {
                if (!this.value.length) cancelSearch();
            })
            .on('input', function() {
                if (this.value.length) {
                    $searchResult.show();
                } else {
                    $searchResult.hide();
                }
            });
        $searchClear.on('click', function() {
            hideSearchResult();
            $searchInput.focus();
        });
        $searchCancel.on('click', function() {
            cancelSearch();
            $searchInput.blur();
        });
        $searchStart.on('click',function(){
        	$('#detail').show();
        	$('#detail-readonly').show();
        });
    });
//核销
    $('#searchStart').click(function() {
        $('#detail').empty();
        $('#detail-record').empty();
        if (window.type==1) {
            $.ajax({
              url: '/dkl/verification/<?=$bid?>',
              type: 'post',
              dataType: 'json',
              data: {tel:$('#searchInput').val()},
              timeout:15000,
              success: function (res){
                var str = '';
                var tArray = new Array();  //先声明一维
                for(var k=0;k<i;k++){    //一维长度为i,i为变量，可以根据实际情况改
                    tArray[k]=new Array();  //声明二维，每一个一维数组里面的一个元素都是一个数组；
                    for(var j=0;j<p;j++){   //一维数组里面每个元素数组可以包含的数量p，p也是一个变量；
                        tArray[k][j]="";    //这里将变量初始化，我这边统一初始化为空，后面在用所需的值覆盖里面的值
                    }
                }
                json = eval(res.res);
                stel=$('#searchInput').val()
                str=str+
                            "<div class=\"weui-form-preview__hd\">"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"电话号码"+"</label>"+
                                    "<em class=\"weui-form-preview__value\">"+stel+"</em>"+
                                "</div>"+
                            "</div>"
                  for(var i=0; i<json.length; i++)
                  {
                     console.log('id:'+json[i].id+'name:'+json[i].name+'tel:'+json[i].tel+'good:'+json[i].good+'time:'+json[i].time);
                     str = str +
                        "<div class=\"orders\">"+
                            "<div class=\"weui-form-preview__bd\">"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"微信昵称"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].name+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"兑换产品"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].good+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"兑换时间"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].time+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"核销状态"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+"未核销"+"</span>"+
                                "</div>"+
                            "</div>"+
                            "<div class=\"weui-form-preview__ft\">"+
                                "<a class=\"weui-form-preview__btn weui-form-preview__btn_primary\" id=\""+json[i].id+"\" onclick=\"veri("+json[i].id+")\" href=\"javascript:\">"+"核销"+"</a>"+
                            "</div>"+
                        "</div>"

                  }
                  str=str+
                        "<div id=\"nomore\" class=\"page__bd\">"+"没有更多了"+
                        "</div>"
                    $("#detail").append(str);
                    },
                     error:function(XMLHttpRequest,textStatus,errorThrown){
                        alert('当前参与人数太多，请稍后再试。');
                        // alert('当前参与人数太多，请稍后再试。'+'error...状态文本值：'+textStatus+" 异常信息："+errorThrown);
                    }
             });
        }else{
            //核销记录
            $.ajax({
              url: '/dkl/verification/<?=$bid?>',
              type: 'post',
              dataType: 'json',
              data: {rtel:$('#searchInput').val()},
              timeout:15000,
              success: function (res){
                var str = '';
                var tArray = new Array();  //先声明一维
                for(var k=0;k<i;k++){    //一维长度为i,i为变量，可以根据实际情况改
                    tArray[k]=new Array();  //声明二维，每一个一维数组里面的一个元素都是一个数组；
                    for(var j=0;j<p;j++){   //一维数组里面每个元素数组可以包含的数量p，p也是一个变量；
                        tArray[k][j]="";    //这里将变量初始化，我这边统一初始化为空，后面在用所需的值覆盖里面的值
                    }
                }
                json = eval(res.res);
                stel=$('#searchInput').val();
                str=str+
                            "<div class=\"weui-form-preview__hd\">"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"电话号码"+"</label>"+
                                    "<em class=\"weui-form-preview__value\">"+stel+"</em>"+
                                "</div>"+
                            "</div>"
                  for(var i=0; i<json.length; i++)
                  {
                     console.log('name:'+json[i].name+'tel:'+json[i].tel+'good:'+json[i].good+'time:'+json[i].time+'ttime:'+json[i].ttime);
                     str = str +
                        "<div class=\"orders\">"+
                            "<div class=\"weui-form-preview__bd\">"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"微信昵称"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].name+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"兑换产品"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].good+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"兑换时间"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].time+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"核销状态"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+"已核销"+"</span>"+
                                "</div>"+
                                "<div class=\"weui-form-preview__item\">"+
                                    "<label class=\"weui-form-preview__label\">"+"核销时间"+"</label>"+
                                    "<span class=\"weui-form-preview__value\">"+json[i].ttime+"</span>"+
                                "</div>"+
                            "</div>"+
                        "</div>"

                  }
                    $("#detail-record").append(str);
                    },
                     error:function(XMLHttpRequest,textStatus,errorThrown){
                        alert('当前参与人数太多，请稍后再试。');
                        // alert('当前参与人数太多，请稍后再试。'+'error...状态文本值：'+textStatus+" 异常信息："+errorThrown);
                    }
             });

        }
        });
    //核销确认
    $(function() {
        var $iosDialog1 = $('#iosDialog1');

        $('#dialogs').on('click', '.weui-dialog__btn', function() {
            $(this).parents('.js_dialog').fadeOut(200);
        });

        $('.weui-form-preview__btn_primary').on('click', function() {
            $iosDialog1.fadeIn(200);
        });
        $('.weui-form-preview__btn_default').on('click', function() {
            $('#detail-readonly').hide();
        });
    });
    //新核销确认
    function veri(i){
        $('#iosDialog1').fadeIn(200);
        $('#verify-doing').attr("value",i);
    };
    $('#verify-doing').click(function() {
            $.ajax({
              url: '/dkl/verification/<?=$bid?>',
              type: 'post',
              dataType: 'json',
              data: {veri:$('#verify-doing').attr('value')},
              timeout:15000,
              success: function (res){
                if(res.status="SUCCESS"){
                    var a=$('#verify-doing').attr('value');
                    $('#'+a).parent().parent().remove();
                    alert('核销成功');
                }else{
                    alert('核销失败');
                };
                },

                     error:function(XMLHttpRequest,textStatus,errorThrown){
                        alert('当前参与人数太多，请稍后再试。');
                        // alert('当前参与人数太多，请稍后再试。'+'error...状态文本值：'+textStatus+" 异常信息："+errorThrown);
                    }
            });
    });

    </script>
</body>
</html>
