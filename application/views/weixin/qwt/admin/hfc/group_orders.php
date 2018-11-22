

<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
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
    .am-badge{
        background-color: green;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                已成团订单
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">充值拼团</a></li>
                <li><a href="#">订单管理</a></li>
                <li><a href="/qwthfca/group_orders">已成团订单</a></li>
                <li class="active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
          <form method="get" name="ordersform">
                            <ul class="am-nav am-nav-tabs" style="left:0;">
                                <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 0 ? 'am-active' : ''?>"><a href="/qwthfca/group_orders">未处理订单</a></li>
                                <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 1 ? 'am-active' : ''?>"><a href="/qwthfca/group_orders/done">已处理订单</a></li>
                            </ul>
                            </form>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
                    <!-- <div class="am-g">
          <form method="get" name="ordersform">
                        <div class="am-u-sm-12 am-u-md-4">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称、手机号、收货人、收货地址搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>
                        </div> -->

          <div class="tab-pane active" id="orders<?=$result['status']?>">
                    <div class="am-g">
                        <div class="am-u-sm-12">
            <form method="post" class="am-form">
                                <table class="inline-block am-scrollable-horizontal am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                <th>订单号</th>
                <th>类型</th>
                <th>用户昵称</th>
                <th>用户头像</th>
                <th>充值手机号</th>
                <th>下单时间</th>
                <th>商品名称</th>
                <th>商品价格</th>
                <th>实付金额</th>
                <th>团长昵称（点击查看团成员）</th>
                    <?php if ($result['status'] == 0):?>
                <th>操作</th>
                    <?php endif?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php if ($trades[0]->id):?>
                                    <?php foreach ($trades as $k => $v):?>
                                        <tr>
                                            <td><?=$v->tid?></td>
                                            <td><?=$v->pintype==1?'团购':'自购'?></td>
                                            <td><?=$v->qrcodes->nickName?></td>
                                            <td><?=$v->qrcodes->avatarUrl?'<img src="'.$v->qrcodes->avatarUrl.'" style="height:20px;">':''?></td>
                                            <td><?=$v->tel?></td>
                                            <td><?=date('Y-m-d H:i:s',$v->createdtime)?></td>
                                            <td><?=$v->items->name?></td>
                                            <td><?=$v->items->price?></td>
                                            <td><?=$v->payment?></td>
                                            <?php
                                            $leader = ORM::factory('qwt_hfctrade')->where('id','=',$v->ftid)->find()?>
                                            <td><a href=""><?=$leader->qrcodes->nickName?></a></td>
                                                <?php if ($result['status'] == 0):?>
                                            <input type="hidden" name="id[]" value="<?=$v->id?>">
                                            <th><a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwthfca/orderdone/<?=$v->id?>"><span class="am-icon-edit"></span> 处理</a></th>
                                                <?php endif?>
                                        </tr>
                                    <?php endforeach?>
                                <?php endif?>
                                 <input type="hidden" name="action" value="oneship">
                                    </tbody>
                                </table>
                                                <?php if ($result['status'] == 0):?>
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-3" style="float:right;">
                            <div class="am-btn-toolbar">
                                    <button type="submit" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-pencil-square-o"></span> 一键处理本页订单</button>
                            </div>
                        </div>
                    </div>
                                                <?php endif?>
                                <div class="am-cf">

                                    <div class="am-fr">
                                    <?=$pages?>
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



          </div><!-- tab-pane -->
          </div>

<script type="text/javascript">
</script>
