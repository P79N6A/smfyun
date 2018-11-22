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
    批量打标签
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">批量打标签/li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
        <?php
        if (!$_POST || $_POST['tag']) $active = 'tag';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">

          <style>
            .laball{
              margin-left: 3px;
              margin-bottom: 13px;
            }
            .laball1{
              margin-left: 3px;
              margin-bottom: 13px;
            }
            .lab3{
              width: 100%;
              /*margin-top:15px;*/
              display:inline-block;
              top:70px;
              margin-bottom:20px;
            }
            #lab4{
              margin-top:10px;
            }
            .add1,.reduce1{
              font-size:14px;
              cursor: pointer;
            }

          </style>
          <br>

          <div class="box3 tab-pane" id="cfg_tag">
            <?php if ($result['fresh'] == 1):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>提交成功，请耐心等候。</div>
            <?php endif?>
            <form role="form" method="post" >
              <div class="btn3">
              <!-- <span class="label label-success add1"  >å¢žåŠ </span>
              <span class="label label-danger reduce1">å‡å°‘</span> -->
            </div>


            <div class="laball">
              <div class="lab3">
                <span>标签名称(填写后不可修改)：</span>
                <input class="name3 form-control" type="text" name='tag[tag_name]' value="<?=$config['tag_name']?>" <?php if($config['tag_name']) echo 'readonly=""'?>>
                </div>
                <?php if($result['alllab']>0):?>
                  <span>已打标签人数<?=$result['islab']?>/总人数<?=$result['alllab']?></span>
                <?php endif?>
              </div>
              <br>
              <div class="alert alert-danger">注意:请区别于特权商品设置的标签</div>
              <br>
              <button type="submit" class="btn btn-success">刷新</button>
              <div class="lab3" id="lab4">
              </div>
            </div>
          </form>
        </div>


        <script>
          $(document).on('click','.reduce1',function(){
            if(parseInt($('.laball1').length)==0){
              alert('不能再减少');
            }else{
              $(".laball1").last().remove();
            }
          });
          $(document).on('click','.add1',function(){
            console.log(1);
            $(".laball").append(
              '<div class=\"laball1\">'+
              '<div class=\"lab3\">'+
              '<span>'+'标签名称：'+'</span>'+
              '<input class=\"name3\" type=\"text\">'+
              '</div>'+
              '<br>'+
              '<div class=\"lab3\" id=\"lab4\">'+
              '<span>'+'自动打标签条件：累计积分达到'+'</span>'+
              '<input class=\"num3\" type=\"text\" onkeydown=\"onlyNum();\" style=\"ime-mode:Disabled\">'+'分'+
              '</div>'+
              '</div>'
              );
          })
        </script>
        <script>
          function onlyNum()
          {
            if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
              if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
                event.returnValue=false;
            }

          </script>





        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->

