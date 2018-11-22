
<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
?>

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
                扫码统计
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">一物一码</a></li>
              <li class="am-active">扫码统计</li>
            </ol>
  <form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品
                    </div>
                    </div>
                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="搜索">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>零售价(元)</th>
                  <th>已生成的红包码数量</th>
                  <th>已扫码的红包码数量</th>
                  <th>红包码数量分享次数</th>
                  <th>已发送的红包数量</th>
                  <th>已发送的红包金额</th>
                  <th>红包码累计uv</th>
                  <th>红包码累计pv</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result['goods'] as $k => $v):
                $iid=$v->id;
                $has_created=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->count_all();
                $has_scan=ORM::factory('qwt_ywmkl')->where('bid','=',$bid)->where('iid','=',$iid)->where('used','!=',0)->count_all();
                $has_share=ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('iid','=',$iid)->where('share','=',1)->count_all();
                $hongbaonum=ORM::factory('qwt_ywmweixin')->where('bid','=',$bid)->where('iid','=',$iid)->where('ct','=',1)->count_all();
                $hongbaomoney=DB::query(Database::SELECT,"SELECT SUM(money) as hongbaomoney from qwt_ywmweixins where bid = $bid and iid = $iid and ct=1 ")->execute()->as_array();
                $hongbaomoney=$hongbaomoney[0]['hongbaomoney'];
                $pvs=DB::query(Database::SELECT,"SELECT SUM(pv) as pvs from qwt_ywmweixins where bid = $bid and iid = $iid ")->execute()->as_array();
                $pvs=$pvs[0]['pvs'];
                $uvs=DB::query(Database::SELECT,"SELECT SUM(uv) as uvs from qwt_ywmweixins where bid = $bid and iid = $iid ")->execute()->as_array();
                $uvs=$uvs[0]['uvs'];
                ?>
                <tr>
                  <td><img src="<?=$v->pic?>" width="32" height="32"></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>
                  <td><?=$v->price?></td>
                  <td><?=$has_created?></td>
                  <td><?=$has_scan?></td>
                  <td><?=$has_share?></td>
                  <td><?=$hongbaonum?></td>
                  <td><?=$hongbaomoney?$hongbaomoney/100:0?></td>
                  <td><?=$uvs?$uvs:0?></td>
                  <td><?=$pvs?$pvs:0?></td>
                </tr>
              <?php endforeach;?>
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


