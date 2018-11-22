
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
                <li><a class="am-icon-home">订单宝</a></li>
                <li><a>数据统计</a></li>
                <li class="am-active">订单记录</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>: 共 <?=$result['countall']?> 条记录
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
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th>性别</th>
                  <th>商品名称</a></th>
                  <th>购买数量</a></th>

                  <th>购买金额</a></th>
                  <th>购买时间</th>
                  <th>上线</th>
                  <th>需结算的佣金</th>
                </tr>
                                    </thead>
                                    <tbody>

                <?php
                foreach ($result['trades'] as $v):
                //   $count2 = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                //   $count3 = ORM::factory('qwt_fxbscore')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                //   $fuser = ORM::factory('qwt_fxbqrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  //$information=ORM::factory('qwt_fxbqrcode',array('id'=>$v->qid,'bid'=>$v->bid))->find();
                $information=ORM::factory('qwt_fxbqrcode')->where('id','=',$v->qid)->find();
                $fid = ORM::factory('qwt_fxbscore')->where('tid', '=', $v->id)->where('type', '=', 2)->find();
                $fuser = ORM::factory('qwt_fxbqrcode')->where('id', '=', $fid->qid)->find();

                ?>

                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><?=$information->nickname?></td>
                  <td><?=$sex[$information->sex]?></td>
                  <td><?=$v->title?></td>
                  <td ><?=$v->num?></a></td>

                  <td><?=$v->payment?></td>
                  <td><?=$v->pay_time?></td>
                  <td><a href="/fxba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td><?=$v->money0+$v->money1+$v->money2+$v->money3?></td>
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










        </div>

    </div>


