
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
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">语音红包</a></li>
                <li><a href="/qwtyyhba/stats_totle">数据统计</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title" style="overflow: inherit;">
                        <form method="post">
                                    <label for="user-name">选择活动</label>
                                    <!-- <div class="am-u-sm-3"> -->
                                    <select name="event" class="input-group goalb" data-am-selected="{searchBox: 1}">
                                    <?php foreach ($tasks as $k => $v):?>
                  <option value="<?=$v->id?>"><?=$v->name?></option>
                <?php endforeach?>
                                    </select>
                                    <label for="user-name">选择活动时间</label>
  <input name="time[begintime]" id="datetimepicker1" size="16" type="text" value="<?=$_POST['data']['begintime']?date("Y-m-d H:i:s",$_POST['data']['begintime']):''?>" class="am-form-field" readonly>
  <span>-</span>
  <input name="time[endtime]" id="datetimepicker2" size="16" type="text" value="<?=$_POST['data']['endtime']?date("Y-m-d H:i:s",$_POST['data']['endtime']):''?>" class="am-form-field" readonly>
                                <span class="am-input-group-btn" style="display:inline-block">
            <button id="ssbtn" class="search-btn1 am-btn  am-btn-default am-btn-success am-icon-search" type="submit"></button>
          </span>
          </form>
                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-id">活动参与人数</th>
                                            <th class="table-author am-hide-sm-only">新增粉丝数量</th>
                                            <th class="table-date am-hide-sm-only">奖品发送数量</th>
                                            <th>UV</th>
                                            <th>PV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                  <tr>
                    <td><?=$join_num[0]['num']?></td>
                    <td><?=$fans_num?></td>
                    <td><?=$gift_num?></td>
                    <td><?=$uv[0]['num']?></td>
                    <td><?=$pv?></td>
                  </tr>
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
