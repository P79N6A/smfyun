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
    密码修改
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">密码修改</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">
        <div class="tab-content">
            <?php if ($result['ok4'] > 0):
            $_SESSION['yhba'] = null;
            ?>
            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>新密码已生效，请重新登录</div>
          <?php endif?>

          <?php if ($result['err4']):?>
            <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err4']?></div>
          <?php endif?>

            <form role="form" method="post">
              <div class="box-body">

                <div class="form-group">
                  <label for="password">旧密码</label>
                  <input type="password" class="form-control" id="password" placeholder="请输入旧密码" maxlength="16" name="password">
                </div>

                <div class="form-group">
                  <label for="newpassword">新密码</label>
                  <input type="password" class="form-control" id="newpassword" placeholder="请输入新密码" maxlength="16" name="newpassword">
                </div>

                <div class="form-group">
                  <label for="newpassword2">重复新密码</label>
                  <input type="password" class="form-control" id="newpassword2" placeholder="请再次输入新密码" maxlength="16" name="newpassword2">
                </div>

                <div class="box-footer">
                  <input type="hidden" name="yz" value="1">
                  <button type="submit" class="btn btn-success">修改登录密码</button>
                </div>
            </div>
            </form>
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






































