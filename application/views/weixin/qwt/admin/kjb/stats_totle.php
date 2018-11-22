
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
</style>

  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                数据统计
            </div>
<?php
if ($status == 1) $title .= '按天统计';
if ($status == 2) $title .= '按月统计';
if ($status == 3) $title .= '按日期筛选';
?>

            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">砍价宝</a></li>
                <li><a href="/qwtkjba/stats_totle">数据统计</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>数据统计</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs">
              <li id="orders<?=$status?>" class="<?=$status== 1 ? 'am-active' : ''?>"><a href="/qwtkjba/stats_totle?qid=1">按天统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 2? 'am-active' : ''?>"><a href="/qwtkjba/stats_totle/month?qid=2">按月统计</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 3? 'am-active' : ''?>"><a href="/qwtkjba/stats_totle/shaixuan?qid=3">按日期筛选</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
              <? if($status == 3):?>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-9">
  <input name="data[begin]" id="datetimepicker1" size="16" type="text" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
  <span>-</span>
  <input name="data[over]" id="datetimepicker2" size="16" type="text" value="<?=$_GET['data']['over']?>" class="am-form-field" readonly>
                                <span class="am-input-group-btn" style="display:inline-block">
            <button id="ssbtn" class="search-btn1 am-btn  am-btn-default am-btn-success am-icon-search" type="submit"></button>
          </span>
</div>
                        </div>
                    </div>
                  <?php endif?>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-id">时间段</th>
                                            <th class="table-title">新参加活动人数</th>
                                            <th class="table-type">发起砍价数</th>
                                            <th class="table-author am-hide-sm-only">参与砍价数</th>
                                            <th class="table-date am-hide-sm-only">完成订单数量</th>
                                            <th class="table-date am-hide-sm-only">完成订单金额</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <?php //foreach($newadd as $newadd):
                  for($i=0;$newadd[$i];$i++)
                  {
                  ?>
                  <tr>
                    <td><?=$newadd[$i]['time']?></td>
                    <td><?=$newadd[$i]['fansnum']?$newadd[$i]['fansnum']:0?></td>
                    <td><?=$newadd[$i]['eventnum']?$newadd[$i]['eventnum']:0?></td>
                    <td><?=$newadd[$i]['cutnum']?$newadd[$i]['cutnum']:0?></td>
                    <td><?=$newadd[$i]['ordernum']?$newadd[$i]['ordernum']:0?></td>
                    <td><?=$newadd[$i]['ordermoney']?round($newadd[$i]['ordermoney']/100,2):0?></td>
                  </tr>

                  <?php //endforeach;
                  }?>
                                    </tbody>
                                </table>
                  <? if($status != 3):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p class="warning-text"> 注意:没有显示的日期表示所有数据都为0。</p>
                </div>
            </div>
          <?php endif?>
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










        </div>

<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    $('#datetimepicker2').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    </script>
