
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
                结算记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">管理后台</a></li>
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
                        <div class="am-u-sm-12 am-u-md-6">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按代理商名，手机号，备注" value="<?=htmlspecialchars($_POST['s']['text'])?>">
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
                  <th>代理商名称</th>
                  <th>登陆名</th>
                  <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <th>商户备注</th>
                  <?php endif;?>
                  <th>结算时间</th>
                  <th>结算金额</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['scores'] as $v):
                ?>
                <tr>
                  <td><?=$v->login->dlname?></td>
                  <td><?=$v->login->user?></td>
                  <?php if($_SESSION['qwta']['admin'] !=0):?>
                  <th><?=$v->login->memo?></th>
                  <?php endif;?>
                  <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                  <td><?=number_format(-$v->score,2)?></td>
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
