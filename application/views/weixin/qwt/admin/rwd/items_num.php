
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                任务管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">任务宝订阅号版</a></li>
                <li><a href="#">任务设置</a></li>
                <li><a href="#">任务管理</a></li>
                <li class="am-active">任务奖品发放情况</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 级别
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>任务级别</th>
                  <th>对应奖品名称</th>
                  <th>奖品发放数量</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['items_num'] as $k=>$v):
                  $count = ORM::factory('qwt_rwdorder')->where('bid','=',$bid)->where('iid','=',$v->item->id)->count_all();
                ?>
                <tr>
                  <td><?=$k+1?></td>
                  <td><?=$v->item->km_content?></td>
                  <td><?=$count?></td>
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

