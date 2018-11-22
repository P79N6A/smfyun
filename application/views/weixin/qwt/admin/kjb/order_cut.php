
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发起的砍价
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">砍价宝</a></li>
                <li><a href="#">发起的砍价</a></li>
                <li class="am-active">发起的砍价</li>
            </ol>
            <form class="am-form" method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 条砍价
                    </div>
                    </div>
                        <!-- <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtrwba/tasks/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新任务</a>
                        </div> -->

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称，商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>砍价商品</th>
                  <th>价格（元）</th>
                  <th>最低价格（元）</th>
                  <th>当前价格（元）</th>
                  <th>已砍刀数/最多刀数</th>
                  <th>活动结束时间</th>
                  <th>是否付款</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['events'] as $k=>$v):?>
                <tr>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32" title="<?=$v->qrcode->openid?>"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=$v->item->name?></td>
                  <td><?=round($v->item->old_price/100,2)?></td>
                  <td><?=round($v->item->price/100,2)?></td>
                  <td><?=round($v->now_price/100,2)?></td>
                  <td><a href="/qwtkjba/cutlist/<?=$v->id?>"><?=ORM::factory('qwt_kjbcut')->where('bid','=',$bid)->where('eid','=',$v->id)->count_all()?></a>/<?=$v->item->cut_num?></td>
                  <td><?=$v->item->endtime?date('Y-m-d H:i', $v->item->endtime):'永不过期'?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    $order = ORM::factory('qwt_kjborder')->where('bid','=',$bid)->where('eid','=',$v->id)->find();
                    if($order->order_state==0)
                      echo '<span class="label label-danger">未付款</span>';
                    else
                      echo '<span class="label label-success">已付款</span>';
                    ?>
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

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
            </form>
        </div>
    </div>
<!--     <script type="text/javascript">
      $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要终止吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认终止',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtrwba/tasks?tid="+id+"&DELETE=1";
    })
  })
    </script> -->
