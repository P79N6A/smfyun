
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



    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                客户管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">管理后台</a></li>
                <li class="am-active">客户管理</li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共<?=$result['countall']?>个客户
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <!-- <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出全部客户信息</a>
                        </div> -->
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <th>公众号名称</th>
                  <th>登录名</th>
                  <th>累计订单数</th>
                  <th>累计订单金额</th>
                  <th>累计创造佣金</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result['customers'] as $customer):
                    $ddnum=ORM::factory('qwt_score')->where('bid','=',$bid)->where('cbid','=',$customer->id)->where('type','=',0)->count_all();
                    $ddmoney=DB::query(Database::SELECT,"SELECT SUM(money) as ddmoney from qwt_scores where `bid` = $bid and `cbid` = $customer->id and `type` = 0 ")->execute()->as_array();
                    $ddmoney=$ddmoney[0]['ddmoney'];
                    $yjmoney=DB::query(Database::SELECT,"SELECT SUM(score) as yjmoney from qwt_scores where `bid` = $bid and `cbid` = $customer->id and `type` = 0 ")->execute()->as_array();
                    $yjmoney=$yjmoney[0]['yjmoney'];
                ?>
                <tr>
                  <td><?=$customer->weixin_name?></td>
                  <td><?=$customer->user?></td>
                  <td><a href="/qwtwdla/ddorder?cbid=<?=$customer->id?>"><?=$ddnum?$ddnum:0?></td>
                  <td><a href="/qwtwdla/ddorder?cbid=<?=$customer->id?>"><?=$ddmoney?$ddmoney:0?></td>
                  <td><a href="/qwtwdla/ddorder?cbid=<?=$customer->id?>"><?=$yjmoney?$yjmoney:0?></td>
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
