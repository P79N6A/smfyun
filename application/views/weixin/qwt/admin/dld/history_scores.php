
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
                账单结算记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li>结算管理</li>
                <li>账单结算记录</li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-3">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 条记录
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                                    <div class="am-u-sm-3">
                <select name='s[type]' data-am-selected="{searchBox: 1}">
                  <option value="all">全部</option>
                  <option value="1">个人团队奖励结算</option>
                  <option value="2">销售利润结算</option>
                </select>
                                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称，注册手机号，支付宝账号搜索" value="<?=htmlspecialchars($_POST['s']['text'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出个人销售利润结算账单</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>电话</th>
                  <th>支付宝账号</th>
                  <th>金额</th>
                  <th>账单时间</th>
                  <th>结算时间</th>
                  <th>结算类型</th>
                  <th>账单类型</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['scores'] as $v):
                ?>
                <tr>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32" title="<?=$v->id?>"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=$v->qrcode->tel?></td>
                  <th><?=$v->qrcode->alipay_name?></th>
                  <td><?=-$v->score?>元</td>
                  <td><?=$v->bz?$v->bz:'销售利润灵活结算'?></td>
                  <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                  <td id="lock1<?=$v->id?>">
                  <?php
                  if ($v->type == 2||$v->type == 5)
                    echo '<span class="label label-success">手动企业付款</span>';
                  if ($v->type == 3||$v->type == 6)
                    echo '<span class="label label-warning">手动转账</span>';
                  ?>
                  </td>
                  <td id="lock2<?=$v->id?>">
                  <?php
                  if ($v->type == 2||$v->type == 3)
                    echo '<span class="label label-success">个人团队奖励</span>';
                  if ($v->type == 5||$v->type == 6)
                    echo '<span class="label label-warning">个人销售利润</span>';
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
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
