
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                营销规则
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>营销规则</li>
                <li class="am-active">营销规则</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共<?=$count?>个营销规则
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwthbya/rules/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加营销规则</a>
                        </div>
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                          <th class="table-type">编号</th>
                                          <th class="table-type">是否有效</th>
                                          <th class="table-type">营销规则名称</th>
                                          <th class="table-type">营销类型</th>
                                          <th class="table-type">奖励内容</th>
                                          <th class="table-id">分享朋友圈后是否需要关注后才能领取</th>
                                          <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($account as $k => $v):?>
                                    <tr>
                                      <td><?=$v->id?></td>
                                      <td><?=$v->status==1?'<span class="label label-success">有效</span>':'<span class="label label-danger">已取消</span>'?></td>
                                      <td><?=$v->name?></td>
                                      <td><?=$v->type==1?'分享朋友圈后发送微信红包':'分享朋友圈后发送微信卡券'?></td>
                                      <td>
                                      <?=$v->type==1?'微信红包：'.number_format($v->money/100,2).'元':'微信卡券：'.$v->couponname?></td>
                                      <td><?=$v->issub==1?'<span class="label label-success">是</span>':'<span class="label label-danger">否</span>'?></td>
                                      <td>
                                      <?php if($v->status==1):?>
                                        <a style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwthbya/rules/edit/<?=$v->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                                      <?php endif?>
                                      </td>
                                    </tr>
                                <?php endforeach?>
                                    </tbody>
                                </table>
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
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
