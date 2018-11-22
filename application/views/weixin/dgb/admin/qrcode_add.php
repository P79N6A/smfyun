<script>
  $(function () {
    $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
  });
</script>
<link rel="stylesheet" type="text/css" href="/dgb/bootstrap/css/SG_area_select.css">
<script type="text/javascript" src="/dgb/bootstrap/js/jquery.min.js"></script>
<script type="text/javascript" src='/dgb/bootstrap/js/iscroll.js'></script>
<script type="text/javascript" src='/dgb/bootstrap/js/SG_area_select.js'></script>
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
                      <label for="startdate">会员编号：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="qrcode[No]" placeholder="" readonly="true" value="<?=$_POST['qrcode']['No']?$_POST['qrcode']['No']:'Q'.(1+$lastqrcode->id)?>" style="width:400px;"><br>
                      <label for="startdate">微信昵称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="qrcode[nickname]" value="<?=$_POST['qrcode']['nickname']?>" style="width:400px;"><br>
                      <label for="startdate">微信号：</label><input type="text" class="form-control" id="" placeholder=""  name="qrcode[weixin_id]" value="<?=$_POST['qrcode']['weixin_id']?>" style="width:400px;"><br>
                      <label for="startdate">姓名：</label><input type="text" class="form-control" id="" placeholder=""  name="qrcode[name]" value="<?=$_POST['qrcode']['name']?>" style="width:400px;"><br>
                      <label for="startdate">电话：</label><input type="number" class="form-control" id="" placeholder=""  name="qrcode[tel]" value="<?=$_POST['qrcode']['tel']?>" style="width:400px;"><br>
                      <label for="startdate">地区选择：</label>
                      <div class="input-group">
                          <input class="sg-area-result form-control" type="" name="qrcode[city]" value="<?=$_POST['qrcode']['city']?>" style="height: 30px;border: 1px solid #ccc;padding-left: 10px">
                          <span id="selectBtn" class="input-group-addon">></span>
                      </div>
                      <!-- <div>
                      <div id="selectBtn" style="width: 30px;height: 30px;border: 1px solid #ccc;cursor: pointer; text-align: center">&gt;</div>
                      </div> -->
                      <label for="startdate">详细地址：</label><input type="text" class="form-control" id="" placeholder=""  name="qrcode[address]" value="<?=$_POST['qrcode']['address']?>" style="width:400px;"><br>
                      <label for="startdate">备注：</label><input type="text" class="form-control" id="" placeholder=""  name="qrcode[remark]" value="<?=$_POST['qrcode']['remark']?>" style="width:400px;"><br>
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
