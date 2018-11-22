<?php
function convert_key($key){
    switch ($key) {
      case 'hongbao':
        echo '微信红包';
        break;
      case 'coupon':
        echo '微信卡券';
        break;
      case 'gift':
        echo '赠品';
        break;
      case 'kmi':
        echo '卡密';
        break;
      case 'yzcoupon':
        echo '有赞优惠券';
        break;
      case 'freedom':
        echo '自定义文本消息';
        break;
      default:
        break;
    }
  }
?>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                卡密列表
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">卡密列表</li>
            </ol>
            <form method="post">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        卡密列表
                    </div>
                    </div>
                        <!-- <div class="am-u-sm-12 am-u-md-3"> -->
                        <!-- <a href="/qwtkmia/kmi" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新奖品</a> -->
                        <!-- </div> -->
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="模糊搜索(卡密名称)" value="<?=$_POST['s']?>">
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
                        <th>卡密名称</th>
                        <th>创建时间</th>
                        <th>已发送卡密</th>
                        <th>未发送卡密</th>
                        <th>操作</th>
                    </tr>
                                    </thead>
                                    <tbody>
                <?php
                 foreach ($result['prizes'] as $pri):
                    $num1=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$pri->value)->where('live','=',0)->count_all();
                    $num2=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$pri->value)->where('live','=',1)->where('flag','=',1)->count_all();
                ?>
                    <tr>
                        <td><?=$pri->km_content?></td>
                        <td><?=date('Y-m-d H:i:s',$pri->startdate)?></td>
                        <td><a href="/qwtkmia/orders?pid=<?=$pri->id?>" title="订单记录"><?=$num1?></a></td>
                        <td><a href="/qwtkmia/kmi_detail/<?=$pri->id?>" title="卡密详情"><?=$num2?></a></td>
                  <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtkmia/prizes_edit/<?=$pri->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                  <?php if($num2>0):?>
                  <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="<?=$_SERVER['PATH_INFO']?>?pid=<?=$pri->id?>&amp;export=xls"><span class="am-icon-pencil-square-o"></span> 导出未发送卡密</a>
                     <?php endif;?>
                  </td>
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




