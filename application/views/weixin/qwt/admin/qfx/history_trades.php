

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
                订单记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">订单记录</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>: 共 <?=$result['countall']?> 条记录
                    </div>
                    </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
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
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th>性别</th>
                  <th>商品名称</a></th>
                  <th>购买数量</a></th>

                  <th>购买金额</a></th>
                  <th>购买时间</th>
                  <th>分销商</th>
                  <th>分销商的佣金</th>
                </tr>
                                    </thead>
                                    <tbody>

                <?php
                foreach ($result['trades'] as $v):
                //   $count2 = ORM::factory('qfx_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                //   $count3 = ORM::factory('qfx_score')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                //   $fuser = ORM::factory('qfx_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  //$information=ORM::factory('qfx_qrcode',array('id'=>$v->qid,'bid'=>$v->bid))->find();
                $information=ORM::factory('qwt_qfxqrcode')->where('id','=',$v->qid)->find();
                // $fid = ORM::factory('qfx_score')->where('tid', '=', $v->id)->where('type', '=', 2)->find();5-23
                $fuser = ORM::factory('qwt_qfxqrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                // $fuser = ORM::factory('qfx_qrcode')->where('id', '=', $fid->qid)->find();5-23

                ?>

                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><?=$information->nickname?></td>
                  <td><?=$sex[$information->sex]?></td>
                  <td><?=$v->title?></td>
                  <td ><?=$v->num?></a></td>

                  <td><?=$v->payment?></td>
                  <td><?=$v->pay_time?></td>
                  <td><a href="/qfxa/qrcodes_m?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td><?=$v->money1?></td>
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">

                                    <div class="am-fr">
                                    <?=$pages?>
                                    </div>                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
            </form>










        </div>

    </div>

