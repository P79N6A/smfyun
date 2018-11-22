<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="format-detection" content="telephone=no"/>
		<meta name="viewport"
          content="width=device-width,height=device-height,user-scalable=no,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0"/>
		<title>红包记录</title>
		<link href="../qwt/hby/finishing/css/swiper.min.css" rel="stylesheet" type="text/css" />
		<link href="../qwt/hby/finishing/css/reset.css" type="text/css" rel="stylesheet" />
		<link href="../qwt/hby/finishing/css/find.css" type="text/css" rel="stylesheet" />
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
  .more-a{
   color: #999;
  }
  .name-p{
   color: #000;
   font-size: 1.2rem;
  }
.selector{
    position: absolute;
    top: 50%;
    left: 6%;
    border-radius: 5px;
    border: 1px solid #efefef;
    box-shadow: 1px 1px 10px 1px #666;
    background-color: #fff;
    font-size: 14px;
    z-index: 2;
}
.hbqr{
    padding: 10px 20px;
    color: #000;
    /*text-decoration: underline;*/
}
.record{
    padding: 10px 20px;
    color: #999;
}
.top{
  width: 100%;
  position: fixed;
  top: 0;
  z-index: 1
}
.top img{
  width: 100%;
}
.topbox{
  width: 100%;
  position: relative;
}
.detail{
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 20%;
}
		</style>
	</head>
	<body>
  <div class="top">
    <div class="topbox">
      <img src="../qwt/hby/hbrctop.png">
      <div class="detail"></div>
      <div class="selector" style="display:none;">
          <div class="hbqr">发红包</div>
          <div class="record">领取记录</div>
      </div>
    </div>
  </div>
		<div class="container">
   <!-- <a id="show_qr"><img class="writenew" src="../qwt/hby/back.png"></a> -->
			<!-- <form method="post">
<div class="tab-bar">
		<div class="search-box">
				<input type="text" name="keyword" value="<?=$_POST['keyword']?>" placeholder="按标题，内容搜索">
  </div><button class="search-button">
      <img src="/wsd/img/search.png">
    </button>
</div>
			</form> -->
			<!-- <input type="hidden" id="search" value="<?=$_POST['keyword']?>"> -->
			<aside class="fall-box grid">
      <?php foreach ($result['users'] as $k => $v): ?>
    <div class="grid-item item">
     <img src="<?=$v['headimgurl']?$v['headimgurl']:'../qwt/hby/avator.png'?>" class="item-img" />
     <section class="section-p">
        <p class="title-p">红包ID：<?=$v['hb_id']?></p>
           <p class="title-p">领取人昵称：<?=$v['nickname']? $v['nickname']:'尚未领取'?></p>
           <p class="title-p">奖励类型：<?=$v['type']?></p>
           <p class="title-p">生成时间：<?=$v['createtime']?></p>
           <p class="title-p">领取时间：<?=$v['sendtime']?></p>
           <p class="name-p">领取状态：<?=$v['status']?></p>
           <p class="price-p">奖励项目：<?=$v['money']?></p>
     </section>
    </div>
  <?php endforeach?>
			</aside>
			<a href="javascript:;" class="more-a">
				<img src="../qwt/hby/finishing/img/ic_loading.gif" />
		    </a>
		</div>

		<script src="../qwt/hby/finishing/js/jquery.min.js"></script>
		<script src="../qwt/hby/finishing/js/imagesloaded.pkgd.min.js"></script>
        <script src="../qwt/hby/finishing/js/masonry.pkgd.min.js"></script>
		<script src="../qwt/hby/finishing/js/swiper.min.js"></script>
		<script type="text/javascript">
  $('.detail').click(function(){
   // window.location.replace('../qwthby/show_qr');
        $('.selector').toggle();
  })
  $('.container').click(function(){
    $('.selector').hide();
  })
  $('.hbqr').click(function(){
   window.location.replace('../qwthby/show_qr');
  })
  $(document).ready(function(){
    var height = $('.top').height();
    $('.container').css('margin-top',height+'px');
  })

		var pullnum = <?=$result['klid']?>;
  var nomore = 0;
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
                 if (nomore==0) {
                        $.ajax({
	               		dataType:"json",
            						data: {klid:pullnum},
				        type:'post',
				        url:'../qwthby/kl_detail',
			            success:function(result){
			            	pullnum = result.klid;
			            	dataFall = result.users;
                setTimeout(function(){
                 appendFall();
                },500)
                if (!result.next_kl) {
                 nomore = 1;
                };
               },
			            error:function(e){
			            	console.log('请求失败')
			            }

	                   })

                }
               };

         })
        function appendFall(){
         console.log(dataFall);
          $.each(dataFall, function(index ,value) {
          	var dataLength = dataFall.length;
          	// $grid.imagesLoaded().done( function() {
	        $grid.masonry('layout');
	           // });
	      var detailUrl;
      	  var $griDiv = $('<div class="grid-item item">');
      	  var $img = $("<img class='item-img'>");
         if (value.headimgurl) {
      	  $img.attr('src',value.headimgurl).appendTo($griDiv);
        }else{
         $img.attr('src','../qwt/hby/avator.png').appendTo($griDiv);
        }
      	  var $section = $('<section class="section-p">');
      	  $section.appendTo($griDiv);
      	  var $p1 = $("<p class='title-p'>");
      	  $p1.html('红包ID：'+value.hb_id).appendTo($section);
         if (value.nickname) {
         var $p4 = $("<p class='title-p'>");
         $p4.html('领取人昵称：'+value.nickname).appendTo($section);
        }else{
         var $p4 = $("<p class='title-p'>");
         $p4.html('暂未领取').appendTo($section);
        }
         var $p5 = $("<p class='title-p'>");
         $p5.html('生成时间：'+value.createtime).appendTo($section);
         var $p6 = $("<p class='title-p'>");
         $p6.html('领取时间：'+value.sendtime).appendTo($section);
      	  var $p2 = $("<p class='name-p'>");
      	  $p2.html('红包状态：'+value.status).appendTo($section);
      	  var $p3 = $("<p class='price-p'>");
      	  $p3.html('红包金额：'+value.money+'元').appendTo($section);
      	  var $items = $griDiv;
		  // $items.imagesLoaded().done(function(){
				 $grid.masonry('layout');
	             $grid.append( $items ).masonry('appended', $items);
			// })
           });
       if (nomore == 1) {
         $('.more-a').html('没有更多了');
       };
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
