

<?php
    function converta1($a){
        switch ($a) {
            // case 0:
            // echo '实物奖品';
            //     break;
            case 1:
            echo '实物奖品';
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
?>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                任务管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">语音红包</a></li>
                <li><a href="#">活动管理</a></li>
                <li><a href="#">新建活动</a></li>
                <li class="am-active">活动中奖情况</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 人次中奖
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                <th>头像</th>
                <th>昵称</th>
                <!-- <th>快递公司</th>
                <th>快递单号</th>
                <th>活动名称</th> -->
                <th>奖品名称</th>
                <th>奖品类型</th>
                <!-- <th>收货人付款</th> -->
                <th>时间</th>
                <!-- <th>发送状态</th>
                <th>原因</th>
                <th>收货人</th>
                <th>联系号码</th>
                <th>收货地址</th> -->
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['items_num'] as $k=>$v):
                  $count = ORM::factory('qwt_rwborder')->where('bid','=',$bid)->where('iid','=',$v->item->id)->count_all();
                ?>
                <tr>
                  <td><img src="<?=$v->user->headimgurl?>" width="32" height="32" title="<?=$v->user->openid?>"></td>
                  <td>
                    <a href="/qwtyyhba/qrcodes?id=<?=$order->user->id?>"><?=$v->name?></a>
                  </td>
                  <!-- <td><?=$v->shiptype?></td>
                  <td><?=$v->shipcode?></td>
                  <td><?=$v->task_name?></td> -->
                  <td><?=$v->item_name?></td>
                  <td><?=converta1($v->item->type)?></td>
                  <!-- <td><?=$v->item->type==0?$order->pay_money.'元':''?></td> -->
                  <td><?=date('m-d H:i',$v->lastupdate)?></td>
                  <!-- <th><?=converta($v->state)?></th>
                  <td><?=$v->state==1?'':$order->log?></td>
                  <td><?=$v->receive_name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->address?></td> -->
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                    <?=$pages?>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
            </form>










        </div>

    </div>

