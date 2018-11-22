
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                大客户订单
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li class="am-active">大客户订单</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        大客户订单
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtyyxa/item_add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新订单</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form" method="post">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                    <tr>
                        <th>订单名称</th>
                        <th>订单金额</th>
                        <th>订单地点</th>
                        <th>订单时间</th>
                        <th>操作</th>
                    </tr>
                                    </thead>
                                    <tbody>
                <?php
                 foreach ($result['item'] as $pri):
                ?>
                    <tr>
                        <td><?=$pri->title?></td>
                        <td><?=$pri->payment?></td>
                        <td><?=$pri->receiver_state.$pri->receiver_city.$pri->receiver_district.$pri->receiver_address?></td>
                        <td><?=date('Y-m-d H:i:s',$pri->update_time)?></td>
                        <td><a href="/qwtyyxa/item_detele/<?=$pri->id?>"><span>删除</span><i class="fa fa-edit"></i></a></td>
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
