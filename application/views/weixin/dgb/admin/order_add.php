<script>
  $(function () {
    $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
  });
</script>
<link rel="stylesheet" type="text/css" href="/dgb/bootstrap/css/SG_area_select.css">
<link rel="stylesheet" href="/dgb/styles/chosen.css">
<script type="text/javascript" src="/dgb/bootstrap/js/jquery.min.js"></script>
<script type="text/javascript" src='/dgb/bootstrap/js/iscroll.js'></script>
<script type="text/javascript" src='/dgb/bootstrap/js/SG_area_select.js'></script>
<script src="/dgb/js/chosen.jquery.js"></script>
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
                      <label for="user-phone" class="am-u-sm-12 am-form-label">选择用户:</label>
                          <div class="am-u-sm-12">
                              <select id="qrcode" name="order[qid]" class="dept_select">
                   <?php if($qrcodes):?>
                    <?php foreach ($qrcodes as $qrcode):?>
                    <option <?=$_POST['order']['qid']==$qrcode->id?"selected":""?> value="<?=$qrcode->id?>"><?=$qrcode->name?></option>
                   <?php endforeach; ?>
                     <?php endif;?>
                            </select>
                          </div>
                      <label for="user-phone" class="am-u-sm-12 am-form-label">选择品牌:</label>
                          <div class="am-u-sm-12">
                              <select id="item" name="order[iid]" class="dept_select">
                   <?php if($items):?>
                    <?php foreach ($items as $item):?>
                    <option <?=$_POST['order']['iid']==$item->id?"selected":""?> value="<?=$item->id?>"><?=$item->name?></option>
                   <?php endforeach; ?>
                     <?php endif;?>
                            </select>
                          </div>
                      <label for="startdate">货号：</label><input type="number"  class="form-control" id="" placeholder="" maxlength="50" name="order[style_id]" value="<?=$_POST['order']['style_id']?>" style="width:400px;"><br>
                      <label for="startdate">单价/件：</label><input type="number" step="0.01" class="form-control" id="" placeholder=""  name="order[price]" value="<?=$_POST['order']['price']?>" style="width:400px;"><br>
                      <label for="startdate">代购费/件：</label><input type="number" step="0.01" class="form-control" id="" placeholder=""  name="order[fee]" value="<?=$_POST['order']['fee']?>" style="width:400px;"><br>
                      <label for="startdate">件数：</label><input type="number" class="form-control" id="" placeholder=""  name="order[num]" value="<?=$_POST['order']['num']?>" style="width:400px;"><br>
                      <label for="startdate">备注：</label><input type="text" class="form-control" id="" placeholder=""  name="order[remark]" value="<?=$_POST['order']['remark']?>" style="width:400px;"><br>
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
    $('.dept_select').chosen();

    })
</script>
