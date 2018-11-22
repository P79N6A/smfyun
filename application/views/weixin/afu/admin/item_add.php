
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
</style>

<section class="content-header">
  <h1>
    添加大客户订单
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">大客户订单</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
             <?php if ($result['ok3'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>订单录入成功!</div>
            <?php endif?>
            <?php if ($result['err3']):?>
              <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err3']?></div>
            <?php endif?>
            <form role="form" method="post">
              <div class="box-body">
                <div class="row">
                  <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label for="startdate">订单时间</label>
                    <div class="input-group">
                      <input type="text" class="form-control pull-right formdatetime" name="order[time]" value="" readonly="">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-sm-4">
                    <div class="form-group">
                      <label for="goal0">订单名称</label>
                      <input type="text" class="form-control" id="name" name="order[name]" value="">
                    </div>
                  </div>
                </div>
                  <div class="row">
                  <div class="col-lg-2 col-sm-4">
                    <div class="form-group">
                      <label for="goal0">订单金额</label>
                      <input type="number" step="0.01" class="form-control" id="money" name="order[money]" value="">
                    </div>
                  </div>
                </div>
                  <div class="row">
                  <div class="col-lg-4 col-sm-4">
                    <div id="city1" class="form-group">
                      <label for="goal0">订单地点(请至少必精确到【市】)</label><br>
                        <select class="prov" name="order[pro]"></select>
                        <select class="city" name="order[city]" disabled="disabled"></select>
                        <select class="dist" name="order[dis]" disabled="disabled"></select>
                    </div>
                  </div>
                </div>

                <script>
                $(function () {
                  $(".formdatetime").datetimepicker({
                    format: "yyyy-mm-dd hh:ii",
                    language: "zh-CN",
                    minView: "0",
                    autoclose: true
                  });
                });
                </script>
                <script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/yyx/plugins/citySelect/city.min1.js"></script>
                <script type="text/javascript">
                  $("#city1").citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                </script>
              </div><!-- /.box-body -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-success">录入订单信息信息</button>
                </div>
            </form>
          </div>
  </div>
</section>












