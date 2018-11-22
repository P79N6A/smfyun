<section>
<style type="text/css">
    th{
        width: 25%;
    }
</style>





  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                续费信息
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">会员中心</a></li>
                <li class="am-active">续费信息</li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>共有<?=$result['countall']?>个产品</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="inline-block am-scrollable-horizontal am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-id">产品名称</th>
                                            <th class="table-title">产品状态</th>
                                            <th class="table-type">到期时间</th>
                                            <th class="table-author am-hide-sm-only">产品续费</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        <?php foreach ($result['rebuys'] as $rebuy): ?>
          <?php
          $item =ORM::factory('qwt_item')->where('id','=',$rebuy->iid)->find();
          ?>
        <tr class="gradeX">
            <td><?=$item->name?></td>
            <td><?php
                  if ($rebuy->expiretime && $rebuy->expiretime < time())
                    echo '<span class="label label-danger">已到期</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
            </td>
            <td><?=date("Y-m-d h:i:s",$rebuy->expiretime)?><?php if($item->id==1){echo "（".$rebuy->hbnum."个）";}?></td>
            <td><div class="am-btn-group am-btn-group-xs"><a href="/qwta/product/<?=$item->id?>" class="am-btn am-btn-default am-btn-xs am-text-secondary" style="background-color:#fff;">续费</i></a></div></td>
        </tr>
        <?php endforeach ?>
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

                            </form>
                        </div>

                    </div>
                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>
</body>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>

</html>
