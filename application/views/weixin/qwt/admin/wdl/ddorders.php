
<style type="text/css">
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
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
    text-align: left!important;
  }
</style>



    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$_SESSION['qwta']['admin'] ==0?'销售记录':'订单管理'?>
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">管理后台</a></li>
                <li class="am-active"><?=$_SESSION['qwta']['admin'] ==0?'销售记录':'订单管理'?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <div class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-2">
                    <div class="caption font-green bold">
                        共<?=$result['countall']?>条订单
                    </div>
                    </div>
                    <?php if($_SESSION['qwta']['admin'] !=0):?>
              <form id="searchtype" method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                                <div class="am-form-group">
                                    <div class="am-u-sm-9">
                                        <select id="type" name="type" data-am-selected="{searchBox: 1}" value="<?=$type?>">
  <option value="0" <?=$type==0?'selected':''?>>全部</option>
  <option value="1" <?=$type==1?'selected':''?>>代理商</option>
  <option value="2" <?=$type==2?'selected':''?>>普通用户</option>
</select>
                                    </div>
                                </div>
                                </div>
                                </form>
                              <?php endif;?>

              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-4">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按手机号，微信公众号，商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出全部订单信息</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <th>公众号名称</th>
                  <th>登陆名</th>
                  <th>应用名称</th>
                  <th>应用规格</th>
                  <th>零售价</th>
                  <th>付款金额</th>
                  <th>付款时间</th>
                  <th>代理商佣金</th>
                  <th>订单状态</th>
                  <th>订单类型</th>
                  <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <th>所属代理商</th>
                  <th>操作</th>
                <?php endif;?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['ddorders'] as $ddorder):
                    $login=ORM::factory('qwt_login')->where('id','=',$ddorder->bid)->find();
                    $fulogin=ORM::factory('qwt_login')->where('id','=',$login->fubid)->find();
                    $score=ORM::factory('qwt_score')->where('rid','=',$ddorder->id)->find();
                    if($ddorder->price==0.00){
                      $price=$ddorder->rebuy_price;
                    }else {
                      $price=$ddorder->price;
                    }
                    // $buy_num=ORM::factory('qwt_rebuy')->where('buy_id','=',$ddorder->buy_id)->count_all();
                    $first_buy=ORM::factory('qwt_rebuy')->where('buy_id','=',$ddorder->buy_id)->order_by('rebuy_time','ASC')->find();
                ?>
                <tr>
                  <td><?=$login->weixin_name?></td>
                  <td><?=$login->user?></td>
                  <td><?=$ddorder->pro->item->name?></td>
                  <td><?=$ddorder->pro->name?></td>
                  <td><?=$price?></td>
                  <td><?=$ddorder->rebuy_price?></td>
                  <td><?=date('Y-m-d H:i:s',$ddorder->lastupdate)?></td>
                  <td><?=$score->score?></td>
                  <td><?=$ddorder->refund==1?'已退款':'已付款'?></td>
                  <td><?=$first_buy->id==$ddorder->id?'首次购买':'续费'?></td>
                  <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <td><?=$score->login->dlname?></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-toggle="modal" data-target="#actionModel" data-id="<?=$ddorder->id?>" data-weixin_name="<?=$login->weixin_name?>" data-user="<?=$login->user?>" data-item="<?=$ddorder->pro->item->name?>" data-pro="<?=$ddorder->pro->name?>" data-price="<?=$price?>" data-refund="<?=$ddorder->refund?>" data-time="<?=date('Y-m-d H:i:s',$ddorder->lastupdate)?>" >
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <?php endif;?>
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

                        </div>

                    </div>
                </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>

        <script type="text/javascript">
        $('#type').change(function(){
            var a = $(this).val();
            console.log(a);
            $('#searchtype').submit();
        })
        </script>
<div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:30%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      用户名称
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

                          <div class="am-form-group">
                            <label for="user-name" id ='weixin_name' class="am-u-sm-12 am-form-label"></label>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" id ='user' class="am-u-sm-12 am-form-label"></label>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" id ='price' class="am-u-sm-12 am-form-label"></label>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" id ='time' class="am-u-sm-12 am-form-label"></label>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">订单状态 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">已付款</li>
                                    <li id="switch-2" class="switch-type purple">已退款</li>
                                    <input type="hidden" name="form[refund]" id="flock0" value="">
                                </ul>
                            </div>
                            </div>
                </div>
            <!-- <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>1、加入白名单后不会自动锁定<br>2、隐身用户不会出现在积分排行中</p>
                </div>
            </div> -->
            <input class='edithidden' name="form[id]" value='' type="hidden">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改</button>
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
<script type="text/javascript">
    $('.edit').click(function(){
      $(".edithidden").attr("value",$(this).data('id'));
      $(".nickname").text($(this).data('item')+':'+$(this).data('pro'));
      document.getElementById('weixin_name').innerHTML = '公众号名称:'+$(this).data('weixin_name');
      document.getElementById('user').innerHTML = '登录名:'+$(this).data('user');
      document.getElementById('price').innerHTML = '付款金额'+$(this).data('price');
      document.getElementById('time').innerHTML = '付款时间'+$(this).data('time');
      var i = $(this).data('refund');
      $('#flock0').val(i);
      if (i==0) {
        $('#switch-1').addClass('green-on');
      }else {
        $('#switch-2').addClass('purple-on');
      }
      $('.shadow').fadeIn();
    });
    $('.close').click(function(){
      $('.shadow').fadeOut();
    });
    $('#switch-1').click(function(){
      $('#flock0').val(0);
      $('#switch-2').removeClass('purple-on');

      $(this).addClass('green-on');
    });
    $('#switch-2').click(function(){
      $('#flock0').val(1);
      $('#switch-1').removeClass('green-on');

      $(this).addClass('purple-on');
    });
</script>