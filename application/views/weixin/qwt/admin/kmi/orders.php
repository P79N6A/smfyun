
<?php
    function convert1($a){
        switch ($a) {
            case 'gift':
            echo '赠品';
                break;
            case 'coupon':
            echo '卡券';
                break;
            case 'kmi':
            echo '卡密';
                break;
            case 'hongbao':
            echo '红包';
                break;
            case 'yzcoupon':
            echo '有赞优惠券';
                break;
            case 'freedom':
            echo '自定义文本消息';
                break;
            case 0:
            echo '未处理';
                break;
            case 1:
            echo '已处理';
                break;
            case 2:
            echo '已处理';
                break;
            case 3:
            echo '已处理';
                break;
            case 4:
            echo '已处理';
                break;
            case 5:
            echo '已处理';
                break;
            default:
            echo '';
                break;
        }
    }
?>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">发送记录</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        发送记录
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?bid=<?=$result['bid']?>&amp;export=csv" class="am-btn am-btn-default am-btn-success"><span class="am-icon-save"></span> 导出全部发送记录</a>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="模糊搜索(收货人，商品名称，奖品类型)" value="">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
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
                        <th>订单编号</th>
                        <th>商品名称</th>
                        <th>收货人</th>
                        <th>时间</th>
                        <th>发送的卡密名称</th>
                        <th>卡密内容</th>
                        <th>状态</th>
                        <th>原因</th>
                        <th>重发</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($result['orders'] as $orders):
                ?>
                    <tr>
                        <td><img src="<?=$orders->heardimageurl?>" style="width:25px;height"></td>
                        <td><?=$orders->nikename?></td>
                        <td><?=$orders->tid?></td>
                        <td><?=$orders->tradename?></td>
                        <td><?=$orders->name?></td>
                        <td><?=$orders->time?></td>
                        <td><?=convert1($orders->km_type)?></td>
                        <td><?=$orders->km_comtent?></td>
                        <td><?=convert1($orders->state)?></td>
                        <?php if($orders->state==0):?>
                        <td><?=$orders->log?></td>
                        <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtkmia/kmi_again/<?=$orders->id?>"><span class="am-icon-pencil-square-o"></span>重发</a>
                    </td>
                        <?php else:?>
                        <td></td>
                        <td></td>
                        <?php endif;?>
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

    </div><!-- 发送管理 --><!-- 卡密记录 -->
