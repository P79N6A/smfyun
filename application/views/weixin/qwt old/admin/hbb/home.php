<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
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

<section class="wrapper" style="width:85%;float:right;background:#eff0f4">
  <h3>
    基础设置
    <small>核心参数配置</small>
  </h3>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>

<!--body wrapper start-->

        <div class="wrapper" style="background:#eff0f4">
            <div class="row">
                <div class="col-md-14">
                    <section class="panel">
                        <header class="panel-heading custom-tab dark-tab">
                            <ul class="nav nav-tabs">
                                <li class="active" id="cfg_text_li">
                                    <a href="#cfg_text" data-toggle="tab">个性设置</a>
                                </li>
                                <li class="" id="cfg_order_li">
                                    <a href="#cfg_order" data-toggle="tab">口令获取、素材</a>
                                </li>
                            </ul>
                            <?php
        if (!$_POST || $_POST['cfg']) $active = 'wx';

        if ($_POST['cus']) $active = 'text';

        if (isset($_POST['password'])) $active = 'account';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>
                        </header>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="cfg_text">
                                    <?php if ($result['ok2']>0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>配置保存成功!</div>
            <?php endif?>

            <form role="form" method="post" enctype='multipart/form-data'>
              <div class="box-body">

                <div class='form-group'>
                  <label for="moneyMin">单个红包最小金额(单位:分，最少100分）</label>
                  <input type="text" class="form-control" id="moneyMin" name="cus[moneyMin]" placeholder="moneyMin" value="<?=$config["moneyMin"]?>">
                </div>

                <div class='form-group'>
                  <label for="money">单个红包最大金额(单位:分，最大20000分)</label>
                  <input type="text" class="form-control" id="money" name="cus[money]" placeholder="money" value="<?=$config["money"]?>">
                </div>
                <div class='form-group'>
                  <label for="rate">单个红包领取概率(填入0~100的整数，比如填写99，即领取概率为99%。建议领取概率不低于90%)
                  </label>
                  <input type="text" class="form-control" id="rate" name="cus[rate]" placeholder="rate" value="<?=$config["rate"]?>">
                </div>
                <hr>
                <div class='form-group'>
                  <label for="success">口令兑换成功自动回复文案
                  </label>
                  <input type="text" class="form-control" id="success" name="cus[success]" placeholder="" value="<?=htmlspecialchars($config["success"])?>">
                </div>
                <div class='form-group'>
                  <label for="rate">口令红包领取失败自动回复文案
                  </label>
                  <input type="text" class="form-control" id="rate" name="cus[success2]" placeholder="rate" value="<?=htmlspecialchars($config["success2"])?>">
                </div>
                <div class='form-group'>
                  <label for="got">本人已经领取过自动回复文案
                  </label>
                  <input type="text" class="form-control" id="got" name="cus[got]" placeholder="" value="<?=htmlspecialchars($config["got"])?>">
                </div>
                <div class='form-group'>
                  <label for="payed">红包口令已经被兑换过自动回复文案
                  </label>
                  <input type="text" class="form-control" id="payed" name="cus[payed]" placeholder="" value="<?=htmlspecialchars($config["payed"])?>">
                </div>
                <div class='form-group'>
                  <label for="rate">口令输入错误自动回复文案
                  </label>
                  <input type="text" class="form-control" id="hack" name="cus[hack]" placeholder="" value="<?=htmlspecialchars($config["hack"])?>">
                </div>

              </div>

              <div class="box-body">
                <div class="form-group">
                  <label for="show" style="font-size:16px;">是否开启裂变？</label>
                  <div>
                    <label class="checkbox-inline" onclick="hide()">
                      <input type="radio" name="cus[split]" id="show0" value="0" <?=$config['split'] ==0 ? ' checked=""' : ''?>>
                      <span class="label label-danger"  style="font-size:14px">关闭</span>
                    </label>
                    <label class="checkbox-inline"  onclick="show()">
                      <input type="radio" name="cus[split]" id="show1" value="1" <?=$config['split'] >0 ? ' checked=""' : ''?>>
                      <span class="label label-success"  style="font-size:14px">开启</span>
                    </label>
                  </div>

                  <script type="text/javascript">
                    function hide(){
                      var h2=document.getElementById("hide");
                      h2.style.display="none";
                    }

                    function show(){
                      var h2=document.getElementById("hide");
                      h2.style.display="block";
                    }
                  </script>
                  <br>
                  <div id="hide" style="display:<?=$config['split']==0?'none':'block'?>">
                    <label id="lab" for="show" style="font-size:16px;">裂变口令个数（小于10的整数)</label>
                    <?if($config['split_count']>0):?>
                    <input type="text" class="form-control" id="split_count" name="cus[split_count]" value=<?=$config['split_count']?>>
                    <?else:?>
                    <input type="text" class="form-control" id="split_count" name="cus[split_count]" placeholder='裂变个数'>
                    <?endif;?>
                    <div class='form-group'>
                      <label for="rate">裂变发送文案
                      </label>
                      <input type="text" class="form-control" id="hack" name="cus[splits_txt]" placeholder="" value="<?=htmlspecialchars($config["splits_txt"])?>">
                    </div>
                  </div>
                </div>
              </div>

              <div class="box-footer">
                <div class='form-gropup'>
                  <button type="submit" class="btn btn-success">保存个性化配置</button>
                </div>
              </div>
            </form>
                                </div>
                                <div class="tab-pane" id="cfg_order">
                                    <?php if ($success['ok']!=null):?>
                          <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>配置保存成功!</div>
                        <?php elseif($success['ok'] =='file'):?>
                            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>文件更新成功!</div>
                        <?php endif?>

                        <?php if($config==null):?>
                            <p><a class="btn btn-success" href="download/<?=$bid?>">下载红包素材</a></p>
                        <?php else:?>
                            <?php if($left==1):?>
                              <p><a class="btn btn-success" href="generate/<?=$bid?>">获取红包口令</a></p>
                            <?php else:?>
                            <p><a class="btn btn-danger" href="#">口令已经获取</a>(7天后才能再次获取)</p>
                            <?php endif;?>
                            <p><a class="btn btn-success" href="<?php echo URL::site('qwthbba/download/'.$buy_id)?>">下载红包素材</a></p>
            <?php endif;?>

            <!-- <p><button class="btn btn-success" id="check_use">查看使用情况</button></p> -->
                        <div class="total">
                            <table  class="table" style="display:none;">
                            <!-- <thead> -->
                            <tr>
                            <th rowspan="2">购买的口令配额</th>
                            <th colspan="2">已生成的口令数</th>
                            <th colspan="2">已消耗的口令数</th>
                            <th rowspan="2">剩余的口令配额</th>
                            </tr>
                            <!-- </thead> -->
                            <!-- <tbody> -->

                            <tr>
                            <td>生成的原始口令数</td>
                            <td>裂变出来的口令数</td>
                            <td>原始口令已消耗</td>
                            <td>裂变口令已消耗 </td>
                            </tr>

                             <tr id="value">
                             <td rowspan="2"></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td></td>
                             <td rowspan="2"></td>
                             </tr>

                             <tr id="value1" >
                                 <td colspan="2"></td>
                                 <td colspan="2"></td>
                              </tr>

                              <tr> <td colspan="4"><p style="color:red">注意:裂变出来的口令不计入消耗，请根据剩余的口令配额判断是否需要续费。</p>
                              <td>
                              </tr>
                            </tbody>

                            </table>
            </div>
          </div>
          <div class="tab-pane" id="cfg_account">

            <?php if ($result['ok4'] > 0):
            $_SESSION['qwthbba'] = null;
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
                    </section>
                </div>
            </div>
        </div>

        <!--body wrapper end-->
<!-- Main content -->

  <!-- <div class="row">
    <div class="col-xs-9">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性设置</a></li>
          <li id="cfg_order_li"><a href="#cfg_order" data-toggle="tab">口令获取、素材</a></li>
        </ul>



        <div class="tab-content">
          <div class="tab-pane" id="cfg_text">

          </div>

          <div class="tab-pane" id="cfg_order">

            </div>
        </div>
      </div>
    </div>
  </div> -->
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
                  url:'/qwthbba/getdata/<?=$bid?>',
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






































