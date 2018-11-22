

<?php
    function converta1($a){
        switch ($a) {
            case 0:
            echo '实物奖品';
                break;
            case 1:
            echo '微信卡劵';
                break;
            case 4:
            echo '微信红包';
                break;
            case 5:
            echo '有赞优惠券';
                break;
            case 6:
            echo '有赞赠品';
                break;
            case 8:
            echo '卡密';
                break;
            case 7:
            echo '特权商品';
                break;
            default:
            echo '';
                break;
        }
    }
    function converta($b){
      switch ($b) {
            case 0:
            echo '未发送';
                break;
            case 1:
            echo "已发送";
            default:
            echo '';
                break;
        }
    }
    function orderstate($order){
      if($order->order_state==0&&$order->item->type==0&&$order->item->need_money>0){
        echo '未支付';
      }elseif ($order->order_state==0&&$order->item->type==0&&!$order->address) {
        echo '未填写收货地址';
      }
    }
?>

<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
    .am-badge{
        background-color: green;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                兑换记录
            </div>
<?php
if ($result['qrcode']) $title = $result['qrcode']->nickname . '的兑换明细 / ';

if ($result['status'] == 0) $title .= '未处理';
if ($result['status'] == 1) $title .= '已处理';
if ($result['status'] == 1){
  $activetype = 'done';
}
?>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">任务宝订阅号版</a></li>
                <li><a href="#">任务设置</a></li>
                <li><a href="/qwtrwda/orders">领取记录</a></li>
                <li class="active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
          <form method="get" name="ordersform">
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 0 ? 'am-active' : ''?>"><a href="/qwtrwda/orders?qid=<?=$result['qid']?>">未处理订单</a></li>
                                <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 1 ? 'am-active' : ''?>"><a href="/qwtrwda/orders/done?qid=<?=$result['qid']?>">已处理订单</a></li>
                            </ul>
                            </form>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-btn-toolbar">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv" class="am-btn am-btn-default am-btn-secondary"> <span class="am-icon-save"></span> <span class="downloadbtn">导出实物奖品未处理订单 </span></a>
                            </div>
                        </div>
                        <?php if ($result['status']==0):?>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-btn-toolbar">
                                    <a id="piliangfahuo" data-toggle="modal" data-target="#shipModel" class="csv am-btn am-btn-default am-btn-secondary"><span class="am-icon-shopping-cart"></span> 实物奖品批量发货</a>
                            </div>
                        </div>
                    <?php endif?>
          <form method="get" name="ordersform">
                        <div class="am-u-sm-12 am-u-md-4">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称、手机号、收货人、收货地址搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>
                    </div>

          <div class="tab-pane active" id="orders<?=$result['status']?>">
                    <div class="am-g">
                        <div class="am-u-sm-12">
            <form method="post" class="am-form">
                                <table class="inline-block am-scrollable-horizontal am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>快递公司</th>
                <th>快递单号</th>
                <th>活动名称</th>
                <th>奖品名称</th>
                <th>奖品类型</th>
                <th>收货人付款</th>
                <th>时间</th>
                <th>发送状态</th>
                <th>原因</th>
                <th>收货人</th>
                <th>联系号码</th>
                <th>收货地址</th>
                    <?php if ($result['status'] == 0):?>
                <th>操作</th>
                    <?php endif?>
                                        </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['orders'] as $order):

                ?>
                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td>
                    <a href="/qwtrwda/qrcodes?id=<?=$order->user->id?>"><?=$order->name?></a>
                  </td>
                  <td><?=$order->shiptype?></td>
                  <td><?=$order->shipcode?></td>
                  <td><?=$order->task_name?></td>
                  <td><?=$order->item_name?></td>
                  <td><?=converta1($order->item->type)?></td>
                  <td><?=$order->item->type==0?$order->pay_money.'元':''?></td>
                  <td><?=date('m-d H:i',$order->lastupdate)?></td>
                  <th><?=converta($order->state)?><?=orderstate($order)?></th>
                  <td><?=$order->log?></td>
                  <td><?=$order->receive_name?></td>
                  <td><?=$order->tel?></td>
                  <td><?=$order->address?></td>
                    <?php if ($result['status'] == 0):?>
                  <td nowrap="">
                    <a class="edit am-btn am-btn-default am-btn-xs am-text-secondary" data-toggle="modal"  data-openid="<?=$order->user->openid?>" data-name="<?=$order->item_name?>" data-id="<?=$order->id?>" data-linkman="<?=$order->receive_name?>" data-tel="<?=$order->tel?>" data-addr="<?=$order->address?>" style="background-color:#fff;">
                      <span class="am-icon-pencil-square-o"></span> 处理
                    </a>
                    <input type="hidden" name="id[]" value="<?=$order->id?>">
                    <?php if ($order->item->type==0):?>
                      <a class="edit2 am-btn am-btn-default am-btn-xs am-text-secondary" data-toggle="modal" data-id="<?=$order->id?>" data-name="<?=$order->item_name?>" data-linkman="<?=$order->receive_name?>" data-tel="<?=$order->tel?>" data-addr="<?=$order->address?>" style="background-color:#fff;">
                      <span class="am-icon-pencil-square-o"></span> 修改快递信息
                    </a>
                    <?php endif?>
                  </td>
                    <?php endif?>
                </tr>

                <?php endforeach;?>
                <input type="hidden" name="action" value="oneship">
                                    </tbody>
                                </table>
              <?php if ($result['status'] == 0 && $order->id):?>
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-3" style="float:right;">
                            <div class="am-btn-toolbar">
                                    <button type="submit" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-pencil-square-o"></span> 一键处理本页订单</button>
                            </div>
                        </div>
                    </div>
                  <?php endif?>
                                <div class="am-cf">

                                    <div class="am-fr">
                                    <?=$pages?>
                                    </div>
                                </div>
                                <hr>

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
                <div class="tpl-alert"></div>
            </div>



          </div><!-- tab-pane -->
          </div>

 <div class="shadow chuli" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      姓名
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
                            <label for="shiptype" class="am-u-sm-3 am-form-label">openID：</label>
                            <div class="am-u-sm-9">
                            <label class="openID">openID</label>
                            </div>
                          </div>
                          <div class="am-form-group chulibox type1 type2cd">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">收货人：</label>
                            <div class="am-u-sm-9">
                            <label class="shouhuoren">收货人</label>
                            </div>
                          </div>
                          <div class="am-form-group chulibox type1">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">手机号：</label>
                            <div class="am-u-sm-9">
                            <label class="tel">手机号</label>
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">收货地址：</label>
                            <div class="am-u-sm-9">
                            <label class="addr">收货地址</label>
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">快递公司：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" name="shiptype" maxlength="6" style="width:150px" type="text" value="<?=$_SESSION['qwtwfba']['shiptype']?>">
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">快递单号：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" name="shipcode" maxlength="20" style="width:150px" type="text" value="<?=$_SESSION['qwtwfba']['shipcode']?>">
                            </div>
                          </div>
    <input type="hidden" name="action" value="ship">
    <input type="hidden" name="id" id="id">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">处理该订单</button>
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
 <div class="shadow kuaidi" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="font-green bold kuaidititle">
                      修改快递信息
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
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">收件人姓名：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" name="edit[receive_name]" id="kuaidiname" maxlength="20" type="text" value="">
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">电话号码：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" name="edit[tel]" id="kuaiditel" maxlength="20" type="text" value="">
                            </div>
                          </div>
                          <div class="am-form-group chulibox type2">
                            <label for="shiptype" class="am-u-sm-3 am-form-label">地址：</label>
                            <div class="am-u-sm-9">
            <input class="form-control" name="edit[address]" id="kuaidiaddress" maxlength="100" type="text" value="">
                            </div>
                          </div>
    <input type="hidden" name="edit_oid" id="kuaidiid">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">提交</button>
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
 <div class="shadow piliangfahuo" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      批量发货步骤说明
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
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform" enctype="multipart/form-data">

            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>批量发货步骤说明<br>1、下载待发货订单 CSV 文件<br>2、发货后将物流信息补充到 CSV 文件的最后两列中<br>3、上传 CSV 文件，完成批量发货</p>
                </div>
            </div>
                          <div class="am-form-group">
                            <div class="am-u-sm-9">
                            <div class="am-form-group am-form-file">
                              <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                <i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
<div id="file-pic" style="display:inline-block;"></div>
                              <input id="csv" name="csv" accept="text/csv" type="file" multiple>
                            </div>
                            <div id="cert-list"></div>
                            </div>
                          </div>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">上传 CSV 文件</button>
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
  $(function() {
    $('#csv').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
$(document).ready(function(){
  var type = '<?=$result['status']?>';
  switch (type)
  {
    case "0":
    var str = '实物奖品未';
    break;
    case "1":
    var str = '全部已';
    break;
  }
  $('.downloadbtn').html('导出'+str+'处理订单');
})
                    $('#piliangfahuo').click(function(){
                      $('.piliangfahuo').fadeIn();
                    });

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });
                    $('.edit').click(function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var man = $(this).data('linkman')
  var addr = $(this).data('addr')
  var tel = $(this).data('tel')
  var openid=$(this).data('openid')
  $('.nickname').text(name);
  $('.openID').text(openid);
  $('.shouhuoren').text(man);
  $('.tel').text(tel);
  $('.addr').text(addr);
  $('#id').val(id);
  $('.chuli').fadeIn();
});
                    $('.edit2').click(function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var man = $(this).data('linkman')
  // var city = $(this).data('city')
  var addr = $(this).data('addr')
  var tel = $(this).data('tel')
  $('#kuaidititle').text(name);
  $('#kuaidiname').val(man);
  $('#kuaiditel').val(tel);
  // $('#kuaidicity').val(city);
  $('#kuaidiaddress').val(addr);
  $('#kuaidiid').val(id);
  $('.kuaidi').fadeIn();
});
</script>
