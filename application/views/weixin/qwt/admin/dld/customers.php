
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
                <li><a href="#" class="am-icon-home">代理哆</a></li>
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
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称,手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出全部客户信息</a>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>手机号</th>
                  <th>累计订单数</th>
                  <th>累计订单金额</th>
                  <th>所属代理</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['customers'] as $customer):
                $num=ORM::factory('qwt_dldtrade')->where('bid','=',$customer->bid)->where('deletedd','=',0)->where('openid','=',$customer->openid)->count_all();
                $allmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as allmoney from qwt_dldtrades where bid=$customer->bid and deletedd = 0 and `openid` = '$customer->openid' ")->execute()->as_array();
                    $allmoney=$allmoney[0]['allmoney'];
                  $fname=ORM::factory('qwt_dldqrcode')->where('bid','=',$customer->bid)->where('openid','=',$customer->fopenid)->where('lv','=',1)->find()->nickname;
                ?>
                <tr>
                  <td><img src="<?=$customer->headimgurl?>" width="32" height="32" title="<?=$customer->openid?>"></td>
                  <td><?=$customer->nickname?></td>
                  <td><?=$customer->receiver_mobile==0?'无':$customer->receiver_mobile?></td>
                  <td><a href="/qwtdlda/history_trades?flag=cnum&qid=<?=$customer->id?>"><?=$num?></td>
                  <td><?=$allmoney?$allmoney:0?></td>
                  <td><?=$fname?></td>
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
