

<style type="text/css">
  label{
    text-align: left !important;
  }
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                获奖记录
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
    <li><a>营销模块</a></li>
    <li class="am-active">获奖记录</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li id="tab1-bar"><a href="/qwtwzba/lottery">幸运大转盘</a></li>
                                <li id="tab2-bar" class="am-active"><a>获奖记录</a></li>
                            </ul>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
<form method="get" name="qrcodesform">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>序号</th>
                  <th>中奖用户昵称</th>
                  <th>抽奖所在直播ID</th>
                  <th>中奖时间</th>
                  <th>中奖等级</th>
                  <th>中奖内容</th>
                  </tr>
                </thead>
                                    <tbody>
                <?php foreach ($result['sweep'] as $k => $v):?>
                  <tr>
                  <th><?=$k+1?></th>
                  <th><?=$v->qrcode->nickname?></th>
                  <th><?=$v->lid?></th>
                  <th><?=date('Y-m-d H:i:s',$v->lastupdate)?></th>
                  <th><?=$v->item->item?>等奖</th>
                  <th><?=$v->content?></th>
                  </tr>
                <?php endforeach?>
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
</form>









        </div>
        </div>
        </div>
        </div>
        </div>
        </div>
