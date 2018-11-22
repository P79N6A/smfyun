
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
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                仓库中的商品
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
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

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
<?php
            $config['kaiguan_needpay']=ORM::factory('qwt_qfxcfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
            ?>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th>库存</th>
                  <th>全员分销上的销量</th>
                  <th>佣金比例</th>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                if($result['items']==null)
                  $result['items']=array();
                foreach ($result['items'] as $v)
                {
                  $mon=ORM::factory('qwt_qfxsetgood')->where('goodid','=',$v['num_iid'])->where('bid','=',$bid)->find();
                   if($mon->id)
                   {
                    $money1=$mon->money1;
                   }
                   else
                   {
                    $monn = ORM::factory('qwt_qfxcfg')->getCfg($bid,1);
                    $money1=$monn['money1'];
                   }
                   $goodid=$v['num_iid'];
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT qwt_qfxorders.* FROM `qwt_qfxtrades`,qwt_qfxorders WHERE qwt_qfxorders.tid=qwt_qfxtrades.tid and qwt_qfxtrades.status!='TRADE_CLOSED' and qwt_qfxtrades.status!='TRADE_CLOSED_BY_USER' and qwt_qfxtrades.status!='NO_REFUND' and qwt_qfxorders.goodid=$goodid) as temp where temp.bid=$bid")->execute()->as_array();
                    //var_dump($soldednum);
                ?>

                <tr>
                  <td><img src="<?=$v['pic_url']?>" width="32" height="32"></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v['title']?></td>

                  <td><?=$v['price']?></td>
                  <td><?=$v['num']?></td>
                  <td><?=empty($soldednum[0]['tonum'])?0:$soldednum[0]['tonum']?><?//=$v['sold_num']?></td>
                  <td><?=$money1?></td>
                  <td nowrap="">
                    <a class="edit am-btn am-btn-danger am-btn-xs" href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v[num_iid]?>" data-price="<?=$v['price']?>" data-name="<?=$v['title']?>" data-money1="<?=$money1?>" >
                      <span class="am-icon-edit"></span>修改佣金
                    </a>
                  </td>
                  <input type="hidden" value='<?=$goodid?>'>
                </tr>

                <?php }?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">
                                        <?=$pages?>
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
 <div class="shadow" style="display:none" id="actionModel">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;margin-left:0;width:60%">
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
                          <div class="am-form-group modal-body">
                          <label for="fscore" class="am-u-sm-3 am-form-label">佣金比例（相对价格的百分比） </label>
                            <div class="am-u-sm-9">
            <input class="form-control" id="fscore" name="form[money1]" max="999" type="number" style="width:150px" value="">
                            </div>
                          </div>
                          <input id="from_num_iid" type="hidden" name="form[num_iid]" value="">
                          <input id="from_title" type="hidden" name="form[title]" value="">
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

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('.edit').click(function(){
                      var id = $(this).data('num_id');
                      var name = $(this).data('name');
                      var price=$(this).data('price');
                      var money1=$(this).data('money1');
                      var information=name+"  (价格:"+price+"元)";
                      $('#fscore').val(money1);
                      $('#from_num_iid').val(id);
                      $('#from_title').val(name);
                      $('.nickname').text(information);
                      $('.shadow').fadeIn();
                    })
</script>




<script>
// $('#actionModel').on('show.bs.modal', function (event) {
//   var button = $(event.relatedTarget);
//   var id = button.data('num_id');
//   var name = button.data('name');
//   var price=button.data('price');
//   var money1=button.data('money1');
//   var information=name+"  (价格:"+price+"元)";


//   var form = '';
//   form+='<label for="fscore" class="am-u-sm-3 am-form-label">佣金比例（相对价格的百分比） </label>
//                             <div class="am-u-sm-9">
//             <input class="form-control" id="fscore" name="form[money1]" max="999" type="number" style="width:150px" value="'+money1+'">
//                             </div>';
//  // form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>';
//   form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
//   form += '<input type="hidden" name="form[title]" value="'+ name +'">';

//   var modal = $(this);
//   modal.find('.nickname').text(information);
//   modal.find('.modal-body').html(form);
// })
</script>
