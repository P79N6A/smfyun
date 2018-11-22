

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
                        <a href="/qwtwzba/other_setgood/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新商品</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <th>缩略图</th>
                  <th>优先级</th>
                  <th>标题</th>
                  <th>价格</th>
                  <th>更新时间</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody><?php foreach ($result['items'] as $key=>$item):?>

                <tr>
                  <td><img src="/qwtwzba/dbimages/setgood/<?=$item->id?>.v<?=$item->time?>.jpg" width="32" height="32"></td>
                  <td><?=$item->priority?></td>
                  <td><?=$item->title?></td>
                  <td><?=$item->price?></td>
                  <td><?=date('Y-m-d H:i', $item->time)?></td>
                  <td><a href="/qwtwzba/other_setgood/edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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

