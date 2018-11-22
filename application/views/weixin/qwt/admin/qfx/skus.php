

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                分销商等级管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li><a href="#">分销商设置</a></li>
                <li class="am-active">分销商等级管理</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($result['skus'])?> 条
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtqfxa/skus/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新分销商等级</a>
                        </div>

                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                    <tr>
                  <th>分销商等级</th>
                  <th>分销商等级名称</th>
                  <th>达到多少金额自动到达该级别（元）</th>
                  <th>该级别下的返还比例</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['skus'] as $key=>$sku):?>

                <tr>
                  <td><?=$sku->lv?></td>
                  <td><?=$sku->name?></td>
                  <td><?=$sku->money?></td>
                  <td><?=$sku->scale?>%</td>
                  <td><a href="/qwtqfxa/skus/edit/<?=$sku->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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

