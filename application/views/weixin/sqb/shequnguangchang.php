<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="format-detection" content="telephone=no"/>
		<meta name="viewport"
          content="width=device-width,height=device-height,user-scalable=no,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0"/>
		<title>社群广场</title>
		<link href="finishing/css/swiper.min.css" rel="stylesheet" type="text/css" />
		<link href="finishing/css/reset.css" type="text/css" rel="stylesheet" />
		<link href="finishing/css/find.css" type="text/css" rel="stylesheet" />
		<style type="text/css">
		.writenew{
			position: fixed;
			bottom: 50px;
			right: 30px;
			width: 50px;
			height: 50px;
			z-index: 1;
		}
		.gohome{
			position: fixed;
			bottom: 120px;
			right: 30px;
			width: 50px;
			height: 50px;
			z-index: 1;
		}
  .tab-bar{
    height: 40px;
    background-color: #f8f8f8;
    /*position: fixed;*/
    top: 0;
    width: 100%;
    border-bottom: 1px solid #e9e9e9;
  }
  .search-box{
    width: 85%;
    display: inline-block;
    position: absolute;
    /*top: 0;*/
    left: 5%;
    /*bottom: 0;*/
    /*right: 15%;*/
    padding: 10px 0;
  }
  .search-button{
    width: 10%;
    display: inline-block;
    /*text-align: center;*/
    float: right;
    /*position: absolute;*/
    /*top: 0;*/
    /*right: 0;*/
    /*padding: 0;*/
    border: 0;
    height: 100%;
    /*margin: 0;*/
    background: bottom;
    /*vertical-align: middle;*/
  }
  .search-button img{
    height: 20px;
  }
  input{

    position: absolute;
    /* left: 10px; */
    /* right: 10px; */
    width: 100%;
    border-radius: 5px;
    border: 0;
    /* height: 100%; */
    height: 20px;
    text-align: center;
  }
  .second-tab{
    top: 41px;
  }
  .update{
    color: #ff7000 !important;
    border: 1px solid #ff7000;
    padding: 2px;
    border-radius: 5px;
  }
		</style>
	</head>
	<body>
		<div class="container">
			<a href="../sqb/index"><img class="gohome" src="images/icon-png/icon-read5.png"></a>
			<a href="../sqb/xinfabu"><img class="writenew" src="images/icon-png/icon-read2.png"></a>
			<form method="post">
<div class="tab-bar">
		<div class="search-box">
				<input type="text" name="keyword" value="<?=$_POST['keyword']?>" placeholder="按标题，内容搜索">
  </div><button class="search-button">
      <img src="/wsd/img/search.png">
    </button>
</div>
			</form>
			<input type="hidden" id="search" value="<?=$_POST['keyword']?>">
			<nav class="swiper-container nav-container">
			<?php if($result['range']=='all'):?>
				<ul class="swiper-wrapper nav-ul">
					<li class="swiper-slide <?=$result['type']==1? 'active-li' : '' ?>">
						<a href="../sqb/shequnguangchang" class="slide-a">我要找产品</a>
					</li>
					<li class="swiper-slide <?=$result['type']==2? 'active-li' : '' ?>">
						<a href="../sqb/woyaozhaoqudao" class="slide-a">我要找渠道</a>
					</li>
				</ul>
			<?php endif?>
			<?php if($result['range']=='me'):?>
				<ul class="swiper-wrapper nav-ul">
					<li class="swiper-slide <?=$result['type']==1? 'active-li' : '' ?>">
						<a href="../sqb/wodefabu" class="slide-a">我要找产品</a>
					</li>
					<li class="swiper-slide <?=$result['type']==2? 'active-li' : '' ?>">
						<a href="../sqb/wodezhaoqudao" class="slide-a">我要找渠道</a>
					</li>
				</ul>
			<?php endif?>
			</nav><!--nav-container end-->
			<aside class="fall-box grid">
			<?php foreach ($partys as $k => $v):?>
			<?php if($result['range']=='me'):?>
				<div class="grid-item item">
					<img src="<?=$v['headimgurl']?>" class="item-img" />
					<section class="section-p">
     <?php if($v['lastupdate']<$time):?>
        <p id="done-<?=$v['id']?>" class="update title-p" onclick="update(<?=$v['id']?>)">已过期，点击重新发布</p>
       <?php endif?>
					   <p class="title-p"><?=$v['title']?></p>
				       <p class="name-p"><?=$v['content']?></p>
				       <p class="price-p"><?=$v['job']?></p>
					</section>
				</div>
			<?php endif?>
			<?php if($result['range']=='all'):?>
				<a href="../sqb/liaotian/<?=$v['qid']?>" class="grid-item item">
					<img src="<?=$v['headimgurl']?>" class="item-img" />
					<section class="section-p">
					   <p class="title-p"><?=$v['title']?></p>
				       <p class="name-p"><?=$v['content']?></p>
				       <p class="price-p"><?=$v['job']?></p>
					</section>
				</a>
			<?php endif?>
			<?php endforeach?>
			</aside>
			<a href="javascript:;" class="more-a">
				<img src="finishing/img/ic_loading.gif" />
		    </a>
		</div>

		<script src="finishing/js/jquery.min.js"></script>
		<script src="finishing/js/imagesloaded.pkgd.min.js"></script>
        <script src="finishing/js/masonry.pkgd.min.js"></script>
		<script src="finishing/js/swiper.min.js"></script>
		<script type="text/javascript">
		var pullnum = 1
		$(function(){

	/*顶部nav*/
	var swiper = new Swiper('.nav-container', {
        slidesPerView: 'auto',
        paginationClickable: true
    });
    $(".nav-ul .swiper-slide").click(function(){
    	$(this).addClass("active-li").siblings().removeClass("active-li");
    });
    /*瀑布流初始化设置*/
	var $grid = $('.grid').masonry({
		itemSelector : '.grid-item',
		gutter:10
    });
    // layout Masonry after each image loads
	$grid.imagesLoaded().done( function() {
		console.log('uuuu===');
	  $grid.masonry('layout');
	});
	   var pageIndex = 0 ; var dataFall = [];
	   var totalItem = 10;
	   $(window).scroll(function(){
	   	var keyword = $('#search').val();
	   	$grid.masonry('layout');
                var scrollTop = $(this).scrollTop();var scrollHeight = $(document).height();var windowHeight = $(this).height();
                if(scrollTop + windowHeight == scrollHeight){
                        $.ajax({
	               		dataType:"json",
            						data: {scroll:pullnum,search:keyword},
				        type:'get',
				        url:'<?=$url?>',
			            success:function(result){
			            	pullnum++;
			            	dataFall = result;
			            	setTimeout(function(){
			            		appendFall();
			            	},500)
			            },
			            error:function(e){
			            	console.log('请求失败')
			            }

	                   })

                }

         })
        function appendFall(){
          $.each(dataFall, function(index ,value) {
          	var dataLength = dataFall.length;
          	$grid.imagesLoaded().done( function() {
	        $grid.masonry('layout');
	           });
	      var detailUrl;
	      <?php if($result['range']=='me'):?>
      	  var $griDiv = $('<div class="grid-item item">');
	      <?php endif?>
	      <?php if($result['range']=='all'):?>
      	  var $griDiv = $('<a class="grid-item item">');
      	  $griDiv.attr('href','../sqb/liaotian/'+value.id);
	      <?php endif?>
      	  var $img = $("<img class='item-img'>");
      	  $img.attr('src',value.headimgurl).appendTo($griDiv);
      	  var $section = $('<section class="section-p">');
      	  $section.appendTo($griDiv);
       <?php if($result['range']=='me'):?>
        if (value.lastupdate<<?=$time?>) {
         var $update = $("<p id='done-"+value.id+"' class='update title-p' onclick='update("+value.id+")'>");
         $update.html('已过期，点击重新发布').appendTo($section);
         };
       <?php endif?>
      	  var $p1 = $("<p class='title-p'>");
      	  $p1.html(value.title).appendTo($section);
      	  var $p2 = $("<p class='name-p'>");
      	  $p2.html(value.content).appendTo($section);
      	  var $p3 = $("<p class='price-p'>");
      	  $p3.html(value.job).appendTo($section);
      	  var $items = $griDiv;
		  $items.imagesLoaded().done(function(){
				 $grid.masonry('layout');
	             $grid.append( $items ).masonry('appended', $items);
			})
           });
        }
})
function update(pid){
  $.ajax({
    dataType:"text",
    data: {update:pid},
    type:'get',
    url:'../sqb/update',
    success:function(result){
      alert(result);
      $('#done-'+pid).remove();
    },
    error:function(e){
      console.log('请求失败')
    }

  })

}


		</script>

	</body>
</html>
