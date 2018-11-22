<script>
  $(function () {
    $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
  });
</script>
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
  .inputtxt{
    width:5%;
  }
  label{
    display: block;
  }
  .input-group{
    width: 400px;
  }
</style>

<section class="content-header">
  <h1>
    <?=$result['title']?>
    <small><?=$result['title']?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="nav-tabs-custom">
        <style type="text/css">
            .textSet>span{
              font-size: 15px;
              font-weight: 900;
            }
        </style>
        <div class="">
          <div class="" id="cfg_km">
            <form role="form" method="post" enctype='multipart/form-data'>
              <div class="box-body textSet">
                      <?php if ($result['error']['dgb']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['dgb']?></div>
                      <?php endif?>
                      <label for="startdate">品牌名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="item[name]" value="<?=$_POST['item']['name']?>" style="width:400px;"><br>
                      <label for="startdate">备注：</label><input type="text" class="form-control" id="" placeholder=""  name="item[remark]" value="<?=$_POST['item']['remark']?>" style="width:400px;"><br>
                  </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存</button>
                  </div>
             </form>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">

    $(document).ready(function(){
      $('#selectBtn').on('click',function(){
        $.areaSelect();
      })

    })
</script>
