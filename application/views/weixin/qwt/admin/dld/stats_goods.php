
<?php

 if ($result['s']) $title = '搜索结果';

?>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                商品统计
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">代理哆</a></li>
                <li><a>数据统计</a></li>
                <li class="am-active">商品统计</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
           <?php if ($result['s']):?>
                        搜索出<?=count($goods)?>种商品
                      <?php else:?>
                        <?=count($goods)?>种有销售记录的商品
                      <?php endif?>
                    </div>
                    </div>
                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="商品名称模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                  <tr>
                    <th>商品名</th>
                    <th nowrap=""><a href="/qwtdlda/stats_goods?sort=toprice" title="按交额">有赞成交金额</a></th>
                    <th nowrap=""><a href="/qwtdlda/stats_goods?sort=totle" title="按订单数">有赞订单数</th>
                    <th nowrap=""><a href="/qwtdlda/stats_goods?sort=tonum" title="按销售数量">销售数量</th>

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

