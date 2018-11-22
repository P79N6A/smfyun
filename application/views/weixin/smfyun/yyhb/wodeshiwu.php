<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>我的实物奖品记录</title>
	<link rel="stylesheet" type="text/css" href="../../sqb/css/ui.css">
	<link href="../../sqb/favicon.ico" type="image/x-icon" rel="icon">
	<link href="../../sqb/iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="../../sqb/css/layout.min.css" rel="stylesheet" />
    <link href="../../sqb/css/scs.min.css" rel="stylesheet" />
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
						<div class="title">我的实物奖品</div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
				<form method="post" id="newForm">
				<div class="fade-main aui-menu-list aui-menu-list-clear">
					<ul>
						<div class="devider b-line"></div>
                        <?php foreach ($prize as $k => $v):?>
                            <?php if ($v->item->type==1):?>
                        <li class="b-line">
                            <a href="http://yingyong.smfyun.com/qwtyyhb/shiwu/<?=$v->id?>">
                                <h3><?=$v->item_name?></h3>
                                <span style="right:16px;<?=$v->tel?'':'color:red;'?>"><?=$v->tel?'已填写':'未填写'?></span>
                            </a>
                        </li>
                    <?php else:?>
                        <li class="b-line">
                            <a>
                                <h3><?=$v->item_name?></h3>
                            </a>
                        </li>
                    <?php endif?>
                    <?php endforeach?>
					</ul>
				</div>
				</form>
			</div>
		</div>
	</div>
</body>
    <script src="../../sqb/js/jquery.min.js"></script>
    <script src="../../sqb/js/jquery.scs.min.js"></script>
    <script src="../../sqb/js/CNAddrArr.min.js"></script>
</html>
