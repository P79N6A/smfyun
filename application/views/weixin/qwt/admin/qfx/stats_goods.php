

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
                商品统计
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">商品统计</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
           <?php if ($result['s']):?>
              搜索出<?=count($goods)?>种商品
            <?else:?>
           <?=count($goods)?>种有销售记录的商品
            <?endif;?>
                    </div>
                    </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
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
                    <th>商品名</th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=toprice" title="按交额">有赞成交金额</a></th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=totle" title="按订单数">有赞订单数</th>
                    <th nowrap=""><a href="/qfxa/stats_goods?sort=tonum" title="按销售数量">销售数量</th>

                 </tr>
                                    </thead>
                                    <tbody>
                  <?php foreach($goods as $good):?>
                  <tr>
                    <td><?=$good['title']?></td>
                    <td><?=$good['toprice']?></td>
                    <td><?=$good['totle']?></td>
                    <td><?=$good['tonum']?></td>

                  </tr>
                  <?php endforeach;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">

                                    <div class="am-fr">
                                    <?=$pages?>
                                    </div>                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
            </form>










        </div>

    </div>

