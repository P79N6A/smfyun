
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
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                分销商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">订单宝</a></li>
    <li class="am-active">分销商品管理</li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共<? if($result['total_results']) echo $result['total_results'];else echo 0;?> 种出售的商品
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
<?php
            $config['kaiguan_needpay']=ORM::factory('qwt_fxbcfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
            ?>
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th>库存</th>
                  <th>订单宝上的销量</th>
                  <th>自购佣金比例</th>
                  <th>一级佣金比例</th>
                  <th>二级佣金比例</th>
                  <?php if($bid==19):?>
                  <th>是否允许分享</th>
                  <?endif?>
                  <?php if($config['kaiguan_needpay']==1):?>
                  <th>三级佣金比例</th>
                  <?endif?>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                if($result['items']==null)
                  $result['items']=array();
                foreach ($result['items'] as $v)
                {
                  $mon=ORM::factory('qwt_fxbsetgood')->where('goodid','=',$v['num_iid'])->where('bid','=',$bid)->find();
                   if($mon->id)
                   {
                    $money0=$mon->money0;
                    $money1=$mon->money1;
                    $money2=$mon->money2;
                    $money3=$mon->money3;
                    $status=$mon->status;
                   }
                   else
                   {
                    $monn = ORM::factory('qwt_fxbcfg')->getCfg($bid,1);
                    $money0=$monn['money0'];
                    $money1=$monn['money1'];
                    $money2=$monn['money2'];
                    $money3=$monn['money3'];
                    $status=$monn['status'];
                   }
                   $goodid=$v['num_iid'];
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT qwt_fxborders.* FROM `qwt_fxbtrades`,qwt_fxborders WHERE qwt_fxborders.tid=qwt_fxbtrades.tid and qwt_fxbtrades.status!='TRADE_CLOSED' and qwt_fxbtrades.status!='TRADE_CLOSED_BY_USER' and qwt_fxbtrades.status!='NO_REFUND' and qwt_fxborders.goodid=$goodid) as temp where temp.bid=$bid")->execute()->as_array();
                    //var_dump($soldednum);
                ?>
                <tr>
                  <td><img src="<?=$v['pic_url']?>" width="32" height="32"></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v['title']?></td>

                  <td><?=$v['price']?></td>
                  <td><?=$v['num']?></td>
                  <td><?=empty($soldednum[0]['tonum'])?0:$soldednum[0]['tonum']?><?//=$v['sold_num']?></td>

                  <td><?=$money0?></td>
                  <td><?=$money1?></td>
                  <td><?=$money2?></td>
                  <?php if($config['kaiguan_needpay']==1):?><td><?=$money3?></td><?endif?>
                  <?php if($bid==19):?>
                  <td>
                  <?php
                  if ($status == 0)
                    echo '<span class="label label-warning">不允许</span>';
                  if ($status == 1)
                    echo '<span class="label label-danger">允许</span>';
                  ?>
                  </td>
                  <?endif?>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v['num_iid']?>" data-price="<?=$v['price']?>" data-name="<?=$v['title']?>" data-pic="<?=$v['pic_url']?>" data-url="<?=$v['detail_url']?>" data-money0="<?=$money0?>" data-money1="<?=$money1?>" data-money2="<?=$money2?>" data-money3="<?=$money3?>" data-status="<?=$status?>">
                      <span class="am-icon-pencil-square-o"></span>修改佣金
                    </a>
                  </td>
                  <input type="hidden" value='<?=$goodid?>'>
                </tr>
                <?php }?>
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
                            <!-- modal -->
 <div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      修改佣金比例
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
                            <label for="user-name" class="am-u-sm-12 am-form-label">自购佣金比例（相对价格的百分比）</label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore0" name="form[money0]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">一级佣金比例（相对价格的百分比）</label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore1" name="form[money1]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">二级佣金比例（相对价格的百分比）</label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore2" name="form[money2]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
  <?php if($config['kaiguan_needpay']==1):?>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">三级佣金比例（相对价格的百分比）</label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore3" name="form[money3]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                        <?php endif?>
  <?php if($bid==19):?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">商品审核（审核通过后用户可以分享）：</label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type red">不通过</li>
                                    <li id="switch-2" class="switch-type green">通过</li>
                                    <input type="hidden" name="form[status]" id="status0" value="">
                                </ul>
                            </div>
                            </div>
                </div>
              <?php endif?>
                        <input id="id" type="hidden" name="form[num_iid]" value="">
                        <input id="title" type="hidden" name="form[title]" value="">
                        <input id="pic" type="hidden" name="form[pic]" value="">
                        <input id="price" type="hidden" name="form[price]" value="">
                        <input id="url" type="hidden" name="form[url]" value="">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改佣金比例</button>
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
  var id = $(this).data('num_id');
  var name = $(this).data('name');
  var pic= $(this).data('pic');
  var url=  $(this).data('url');
  var price=$(this).data('price');
  var money0=$(this).data('money0');
  var money1=$(this).data('money1');
  var money2=$(this).data('money2');
  var money3=$(this).data('money3');
  var status=$(this).data('status');
  var information=name+"  (价格:"+price+"元)";
  $('#id').val(id);
  $('#title').val(name);
  $('#pic').val(pic);
  $('#price').val(price);
  $('#url').val(url);
  $('#fscore0').val(money0);
  $('#fscore1').val(money1);
  $('#fscore2').val(money2);
  $('#fscore3').val(money3);
  $('#status0').val(status);
  $('.nickname').text(information);
  if (status==0) {
    $('#switch-1').addClass('red-on');
  }else{
    $('#switch-2').addClass('green-on');
  }
                      $('.shadow').fadeIn();
                    });
                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#switch-2').removeClass('green-on');
                      $(this).addClass('red-on');
                      $('#status0').val(0);
                    });
                    $('#switch-2').click(function(){
                      $('#switch-1').removeClass('red-on');
                      $(this).addClass('green-on');
                      $('#status0').val(1);
                    });
</script>

