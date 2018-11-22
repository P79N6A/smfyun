<style>
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>

<section class="content-header">
  <h1>
    口令获取
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">口令获取</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">
    <div class="tab-content">
            <?php if ($success['ok']!=null):?>
                <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>配置保存成功!</div>
              <?php elseif($success['ok'] =='file'):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>文件更新成功!</div>
              <?php endif?>
                  <?php if($left==1):?>
                    <p><a class="btn btn-success" href="generate/<?=$bid?>">获取红包口令</a></p>
                  <?php else:?>
                  <p><a class="btn btn-danger" href="#">口令已经获取</a>(7天后才能再次获取)</p>
                  <?php endif;?>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- 绑定有赞js代码 -->
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var hb = '<?=URL::site("user/config/hb")?>';
    location.href = hb+'/'+url+'/<?=$buy_id?>';
  })
</script>

<!-- 微信参数js代码 -->
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var hb = '<?=URL::site("user/config/hb")?>';
    location.href = hb+'/'+url+'/<?=$buy_id?>';
  })
</script>

<!-- 个性设置js代码 -->
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var hb = '<?=URL::site("user/config/hb")?>';
    location.href = hb+'/'+url+'/<?=$buy_id?>';
  })
</script>

<!-- 用户明细js代码 -->
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var hb = '<?=URL::site("user/config/hb")?>';
    location.href = hb+'/'+url+'/<?=$buy_id?>';
  })
</script>

<!-- 口令获取及素材统计js代码 -->
<script type="text/javascript">
  $(document).on('click','.jump',function(){
    var url = $(this).data('url');
    var hb = '<?=URL::site("user/config/hb")?>';
    location.href = hb+'/'+url+'/<?=$buy_id?>';
  })

  window.i=0;
      $(function(){
          $("#check_use").on('click',function(){
              if(i%2==0)
              $.ajax({
                  type:'post',
                  url:'/yhba/getdata/<?=$bid?>',
                  data:{id:<?=$bid?>},
                  dataType:'json',
                  success:function(data){
                      if(data=='0')
                      {
                          $(".total").css("display",'block');
                          $(".table").css("display",'none');
                          $('.total').text("暂无使用情况统计！");
                      }
                      else
                      {
                          $(".total").css("display",'block');
                           $(".table").css("display",'block');
                          _td=$("#value").children();
                          _td.eq(0).text(data.buynum);
                          _td.eq(1).text(data.creatnum.normal);
                          _td.eq(2).text(data.creatnum.liebian);
                          _td.eq(3).text(data.used.normal);
                          _td.eq(4).text(data.used.liebian);
                          _td.eq(5).text(data.buynum-data.used.normal+"个");
                          _tdd=$("#value1").children();
                          _tdd.eq(0).text("共产生"+data.creatnum.total+"个口令");
                          _tdd.eq(1).text("共使用"+data.used.total+"个口令");
                          // $('.all').text(data.buynum);
                          // $('.creat').text(data.creatnum);
                          // $('.used').text(data.used);
                      }
                      i++;
                  }
              });
             else
               {
                  $(".total").css("display",'none');
                  i++;
              }
          });
  });
</script>






































