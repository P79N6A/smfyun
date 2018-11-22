
<style type="text/css">
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>



    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                订单记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
                <li>订单管理</li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>: 共 <?=$result['countall']?> 条记录
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出全部订单记录</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>订单名称</th>
                  <th>时间</th>
                  <th>金额</th>
                  <th>客户昵称</a></th>
                  <th>头像</th>
                  <th>姓名</th>
                  <th>收货地址</th>
                  <th>收货电话</th>
                  <th>备注</th>
                  <th>所属上级</a></th>
                  <!-- <th>需扣除的销售利润</th> -->
                  <th>订单状态</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['trades'] as $v):
                $information=ORM::factory('qwt_xdbqrcode')->where('id','=',$v->qid)->find();
                $fuser = ORM::factory('qwt_xdbqrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                ?>
                <tr>
                  <td><?=$v->title?></td>
                  <td><?=$v->pay_time?></td>
                  <td><?=$v->payment?></td>
                  <td><?=$information->nickname?></td>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32"></td>
                  <td><?=$v->receiver_name?></td>
                  <td><?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></td>
                  <td><?=$v->receiver_mobile?></td>
                  <td><?=$v->buyer_message?></td>
                  <td><?=$fuser->nickname?></td>
                  <!-- <td><?=$v->money1?></td> -->
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->status == 'WAIT_SELLER_SEND_GOODS')
                    echo '<span class="label label-warning">已付款</span>';
                  if ($v->status == 'WAIT_BUYER_CONFIRM_GOODS')
                    echo '<span class="label label-danger">已发货</span>';
                  if ($v->status == 'TRADE_BUYER_SIGNED')
                    echo '<span class="label label-success">已签收</span>';
                  if ($v->status == 'TRADE_CLOSED')
                    echo '<span class="label label-primary">已退款</span>';
                  if ($v->status == 'TRADE_CLOSED_BY_USER')
                    echo '<span class="label label-primary">订单已取消</span>';
                  ?>
                  </td>
                </tr>
                <?php endforeach;?>
                                    </tbody>
                                </table>
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
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
