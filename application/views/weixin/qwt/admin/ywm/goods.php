
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                其他商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
                <li><a>直播商品管理</a></li>
                <li class="am-active">其他商品管理</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($result['items'])?> 件产品
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtywma/good/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新商品</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>零售价(元)</th>
                  <th>已生成的二维码数量</th>
                  <th>已扫码的二维码数量</th>
                  <th>修改</th>
                  <th>下载按钮</th>
                </tr>
                </thead>
                <tbody><?php foreach ($result['items'] as $key=>$item):
                $iid=$item->id;
                $has_created=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->count_all();
                $has_scan=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('used','!=',0)->count_all();
                $cron=ORM::factory('qwt_ywmcron')->where('bid','=',$bid)->where('iid','=',$iid)->order_by('id','DESC')->find();
                $timetext="二维码正在生成中，大概还需要".$result['time']."分钟，请稍后下载！";
                if($status==4){
                  if($cron->has_down==1){
                    $status=1;
                  }else{
                    if($cron->has_qr==1){
                      $status=3;
                    }else{
                      $status=0;
                    }
                  }
                }else{
                  if(!$cron->id||$cron->has_down==1){
                    $status=2;
                  }else{
                    if($cron->has_qr==1){
                      $status=3;
                    }else{
                      $status=0;
                    }
                  }
                }
                ?>
                <tr>
                  <td><img src="/qwtywma/dbimages/setgood/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" width="32" height="32"></td>
                  <td><?=$item->title?></td>
                  <td><?=$item->price?></td>
                  <td><?=$has_created?></td>
                  <td><?=$has_scan?></td>
                  <td><a href="/qwtywma/good/edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-iid="<?=$item->id?>"  data-price="<?=$item->price?>" data-name="<?=$item->title?>" data-status="<?=$status?>"  data-timetext="<?=$timetext?>" data-pic="<?=$item->pic?>" data-url="<?=$item->url?>" data-price="<?=$item->price?>">
                      <span>下载</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            </form>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>
    </div>


 <div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;top:0;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      是否上架直播
                    </div>
                  </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">可下载的二维码数量</label>
                            <label class="numsum am-u-sm-9 am-form-label" style="font-weight:bold;color:red;"><?=$mostnum?></label>
                </div>
                <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">本次下载</label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="fscore" name="form[priority]" max="<?=$mostnum?>" style="width:150px" type="number">
                  </div>
                </div>
                  <div class="am-form-group">
                    <div class="tpl-content-scope">
                            <div class="note note-danger" style="color:red">
                                <p class="qrcodemsg">二维码已用完</p>
                            </div>
                        </div>
                        </div>

                  <div class="am-form-group">
                    <div class="am-u-sm-9 am-u-sm-push-3">
                    <input type="hidden" id="ywmstatus" name="form[status]" value="">
                    <input type="hidden" id="ywmiid" name="form[iid]" value="">
                    <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
              <button  type="submit" class="makeqrcode am-btn am-btn-primary">生成二维码</button>
              <button  type="submit" class="downloadqrcode am-btn am-btn-primary">下载二维码</button>
                      </div>
                    </div>
                    </form>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
                    </div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
                    $('.edit').click(function(){
  var name = $(this).data('name');
  var price=$(this).data('price');
  var status=$(this).data('status');
  var url=$(this).data('url');
  var price=$(this).data('price');
  var pic=$(this).data('pic');
  var iid=$(this).data('iid');
  var timetext=$(this).data('timetext');
  var information=name+"  (价格:"+price+"元)";
  var ywmstatus = document.getElementById("ywmstatus");
  var ywmiid = document.getElementById("ywmiid");
  ywmstatus.value = status;
  ywmiid.value = iid;
  $('#title').val(name);
  $('#url').val(url);
  $('#price').val(price);
  $('#pic').val(pic);
  if (status==1) {
    $('.qrcodemsg').text('二维码已用完');
    $('.makeqrcode').attr('disabled',true);
    $('.downloadqrcode').attr('disabled',true);
  };
  if(status==0) {
    $('.qrcodemsg').text(timetext);
    $('.makeqrcode').attr('disabled',true);
    $('.downloadqrcode').attr('disabled',true);
  };
  if (status==2) {
    $('.qrcodemsg').text('请先生成二维码');
    $('.makeqrcode').attr('disabled',false);
    $('.downloadqrcode').attr('disabled',true);
  };
  if (status==3) {
    $('.qrcodemsg').text('您还有未下载的二维码请先下载');
    $('.makeqrcode').attr('disabled',true);
    $('.downloadqrcode').attr('disabled',false);
  };
  $('.nickname').text(information);
  $('.shadow').fadeIn();


                    })

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                      $('#status').val(1);
                    });
                    $('#switch-4').click(function(){
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                      $('#status').val(0);
                    });
</script>
