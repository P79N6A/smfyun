
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
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
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
            <?=$_SESSION['qwta']['admin'] ==0?'对账单':'结算管理'?>
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">管理后台</a></li>
                <li><?=$_SESSION['qwta']['admin'] ==0?'对账单':'结算管理'?></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                <?php if($_SESSION['qwta']['admin'] >=1):?>
                        <div class="am-u-sm-12 am-u-md-2">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个代理
                    </div>
                    </div>
                  <?php endif;?>
              <form method="get" name="qrcodesform">
<?php if($_SESSION['qwta']['admin'] ==0):?>
                          <div class="am-u-sm-12 am-u-md-10">
            <input id="datetimepicker1" size="16" type="text" name="data[begin]" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
                                <span class="am-input-group-btn" style="display:inline-block">
            <button id="ssbtn" class="search-btn1 am-btn  am-btn-default am-btn-success am-icon-search" type="submit"></button>
          </span>
            </div>
<?php else:?>
                          <div class="am-u-sm-12 am-u-md-2">
            <input id="datetimepicker1" size="16" type="text" name="data[begin]" value="<?=$_GET['data']['begin']?>" class="am-form-field" readonly>
            </div>

                        <div class="am-u-sm-12 am-u-md-5">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="代理商名称，手机号，商户备注，微信公众号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success am-btn-secondary" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出代理结算账单</a>
                        </div>
<?php endif?>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>代理商名称</th>
                  <th>登录名</th>
                  <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <th>备注</th>
                <?php endif;?>
                  <th>当月佣金</th>
                  <th>已结算佣金</th>
                  <th>累计佣金</th>
                  <th>是否结算</th>
                  <?php if($_SESSION['qwta']['admin'] >=1):?>
                  <th>操作</th>
                <?php endif;?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                  $monthtype='%Y-%m';
                  $month_money=DB::query(Database::SELECT,"SELECT SUM(score) as month_money from qwt_scores where bid=$v->id and type =0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                  $month_money=$month_money[0]['month_money'];
                  $all_money=DB::query(Database::SELECT,"SELECT SUM(score) as all_money from qwt_scores where bid=$v->id and type =0 ")->execute()->as_array();
                  $all_money=$all_money[0]['all_money'];
                  $gave_money=DB::query(Database::SELECT,"SELECT SUM(score) as gave_money from qwt_scores where bid=$v->id and type =1  ")->execute()->as_array();
                  $gave_money=$gave_money[0]['gave_money'];
                  // $dai_money=DB::query(Database::SELECT,"SELECT SUM(score) as dai_money from qwt_scores where bid=$v->id ")->execute()->as_array();
                  // $dai_money=$dai_money[0]['dai_money'];
                  $score=ORM::factory('qwt_score')->where('bid','=',$v->id)->where('bz','=',$_GET['data']['begin'])->find();
                  if($score->id){
                    $flag=1;
                  }else{
                    $flag=0;
                  }
                ?>
                <tr>
                  <td><a href="/qwtwdla/logins?qid=<?=$v->id?>"><?=$v->dlname?></td>
                  <td><?=$v->user?></td>
                   <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <td><?=$v->memo?></td>
                  <?php endif;?>
                  <td><?=number_format($month_money,2)?></td>
                  <td><a href="/qwtwdla/history_scores?qid=<?=$v->id?>"><?=number_format(-$gave_money,2)?></td>
                  <td><a href="/qwtwdla/ddorder?qid=<?=$v->id?>"><?=number_format($all_money,2)?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($flag==1)
                    echo '<span class="label label-success">已结算</span>';
                  else
                    echo '<span class="label label-warning">未结算</span>';
                  ?>
                  </td>
                  <td nowrap="">
                  <?php if($flag==0&&$_SESSION['qwta']['admin'] >=1):?>
                  <a class="edit am-btn am-btn-secondary am-btn-danger am-btn-xs" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-flag="<?=$v->flag?>" data-name="<?=$v->name?>" data-tel='<?=$v->user?>' data-money='<?=$month_money?>'>
                      <span class="am-icon-edit"></span>结算
                    </a>
                  <?php endif;?>
                  </td>
                </tr>
                <?php endforeach;?>
                                    </tbody>
                                </table>
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
                             <?php if($_SESSION['qwta']['admin'] >=1):?>
                            <div style="color:red;font-size:24px;margin-left:50px">结算说明</div>
            <small style="color:red">1、每个月10号结算上一个月的佣金；<br>
            2、结算方式为人工结算；<br>
            3、请联系微信：ws350590398；</small>
                          <?php endif;?>
                        </div>

                    </div>
                </div>
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
 <div class="shadow useredit" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:30%;margin-left:0;width:40%;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      用户名
                    </div>
                  </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">
                          <div class="am-form-group modal-body">
                          <label id="stime" for="fscore" class="am-u-sm-12 am-form-label">时间</label>
                          </div>
                          <div class="am-form-group modal-body">
                          <label for="fscore" class="am-u-sm-12 am-form-label">当前个人可结算奖金<span id="smoney"></span>元</label>
                          </div>
                </div>
                <input id="userid" type="hidden" name="form[id]" value="">
                <input id="usertime" type="hidden" name="form[time]" value="">
                <input id="usermoney" type="hidden" name="form[money]" value="">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">结算</button>
                            </div>
                          </div>
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
</div>
</div>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
    format: "yyyy-mm",
    language: "zh-CN",
    autoclose: true,
    startView:3,
    minView:3,
    todayBtn:false,
    startDate: "<?=$duringtime['begin']?>",
    <?php $time=strtotime("+1 Months");?>
    endDate: "<?=date("Y-m",$time)?>",
});
$('.edit').on('click',function(){
  var id = $(this).data('id')
  var name = $(this).data('name')
  var uname = $(this).data('uname')
  var tel = $(this).data('tel')
  var bz = $(this).data('bz')
  var lv = $(this).data('lv')
  var time = "<?=$_GET['data']['begin']?>";
  var money = $(this).data('money')?$(this).data('money'):0;
  var account_type = "<?=$config['money_type']==1?'企业付款':'手动结算'?>"
  $('#stime').text(time);
  $('#smoney').text(money);
  $('#userid').val(id);
  $('#usertime').val(time);
  $('#usermoney').val(money);
  $('.nickname').text(name);
  // if (<?=$config['money_type']?>==1) {$('#switch-1').addClass('green-on')}else{$('#switch-4').addClass('red-on')};
  $('.useredit').fadeIn();
})
$(document).on('click','.close',function(){
    $(".shadow").fadeOut(500);
});
                    $('#switch-1').click(function(){
                      $('#sformtype').val(1);
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-4').click(function(){
                      $('#sformtype').val(2);
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                    });
    </script>


