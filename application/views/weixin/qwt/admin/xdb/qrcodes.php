

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的邀请好友';
if ($result['ffuser']) $title = $result['ffuser']->nickname.'的间接粉丝';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
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
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                用户明细
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
    <li><a href="/qwtxdba/qrcodes">用户明细</a></li>
    <li class="am-active"><?=$title?></li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
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
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                  <!-- <th>状态</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th><a href="/qwtxdba/qrcodes?sort=score&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按积分排序">现有兑换券</a></th>
                  <th>兑换数量</th>
                  <th>性别</th>
                  <th>邀请好友</th>
                  <th>有效订单数/关系订单数</th>
                  <!-- <th>间接粉丝</th> -->
                  <th><a href="/qwtxdba/qrcodes?sort=jointime&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按关注时间排序">关注时间</a></th>
                  <th>关注状态</th>
                  <th>是否有生成海报？</th>
                  <!-- 开启了可选参与地区开关 -->
              <?php if($config['status']==1):?>
                  <th>地理位置</th>
              <?php endif?>
                  <th>来源用户</th>
                  <!-- <th>备注</th> -->
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_xdbqrcodes WHERE fopenid='$v->openid'")->execute()->as_array();
                  $tempid=array();
                    if($firstchild[0]['openid']==null)
                    {
                      $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
                    }
                    else
                    {
                      for($i=0;$firstchild[$i];$i++)
                      {
                        $tempid[$i]=$firstchild[$i]['openid'];
                      }
                    }
                  $count3 = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->count_all();
                  $fuser='';
                  if($v->fopenid){
                      $fuser = ORM::factory('qwt_xdbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  }
                  $count4 =ORM::factory('qwt_xdbchange')->where('bid','=',$v->bid)->where('qid','=',$v->id)->and_where_open()->where('need_money','=',0)->or_where_open()->where('need_money','>',0)->and_where('order_state','=','1')->or_where_close()->and_where_close()->count_all();
                  $count5 = ORM::factory('qwt_xdbtrade')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('status','=','TRADE_SUCCESS')->count_all();
                  $count6 = ORM::factory('qwt_xdbtrade')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->count_all();
                ?>

                <tr>
                  <!-- <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock == 3)
                    echo '<span class="label label-warning">白名单</span>';
                  elseif ($v->lock == 4)
                    echo '<span class="label label-info">隐身用户</span>';
                  elseif ($v->lock == 1)
                    echo '<span class="label label-danger">已锁定</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td> -->

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <?php
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m-d'));
                  $url = '/qwtxdb/index2/'. $v->bid .'?url=index&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td id="score1<?=$v->id?>"><a href="/qwtxdba/orders?qid=<?=$v->id?>" title="查看兑换明细"><?=$count4?></a></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/qwtxdba/qrcodes?fopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count2?></a></td>
                  <!-- <td><a href="/qwtxdba/qrcodes?ffopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count3?></a></td> -->
                  <td><a href="/qwtxdba/yzorder?fopenid=<?=$v->openid?>"><?=$count5?>/<?=$count6?></a></td>
                  <td><?=date('Y-m-d H:i', $v->jointime)?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->subscribe==0)
                      echo '<span class="label label-danger">已跑路</span>';
                    else
                      echo '<span class="label label-success">关注</span>';
                    ?>
                  </td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
              <?php if($config['status']==1):?>
                  <th><?=$v->qrcodes->area?$v->qrcodes->area:'用户未点击允许获取地理位置上报'?></th>
              <?php endif?>
                  <td><a href="/qwtxdba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <!-- <td><?=$v->joinarea==1 ? '预绑定，该用户不允许或未操作上传地理位置，暂不加积分' : ''?></td> -->
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-toggle="modal" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>"><span class="am-icon-pencil-square-o"></span> 修改
                    </a>
                  </td>
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
<!-- /.content -->
 <!--
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title nickname">Edit Media Gallery</h4>
                                        </div>

<div class="modal-body row">

    <div class="col-md-9 img-modal">


            <div class="modal-content">

      <div class="modal-body">
      <form method="post" >
       <div class="form-group">
            <label for="fscore">积分增减（正数为增加、负数为减少）：
            </label>
            <input class="form-control" id="fscore" name="form[score]" max="999" style="width:150px" type="number">
        </div>
            <div class="form-group">
                <label for="flock">用户状态：</label>
                <div style="margin-left:-20px">
                    <label class="checkbox-inline">
                        <input class='checkedit' name="form[lock]" id="flock0" value="0" checked="" type="radio">
                        <span class="label label-success" style="font-size:14px">正常</span>
                    </label>
                    <label class="checkbox-inline" style="margin-left:-15px">
                        <input class='checkedit' name="form[lock]" id="flock1" value="1" type="radio"><span class="label label-danger" style="font-size:14px">已锁定</span></label>
                     <label class="checkbox-inline" style="margin-left:-15px">
                        <input class='checkedit' name="form[lock]" id="flock3" value="3" type="radio">
                        <span class="label label-warning" style="font-size:14px">白名单</span>
                    </label>
                    <label class="checkbox-inline" style="margin-left:-15px"><input class='checkedit' name="form[lock]" id="flock4" value="4" type="radio"><span class="label label-info" style="font-size:14px">隐身用户</span></label></div> <p class="help-block">1、加入白名单后不会自动锁定<br>2、隐身用户不会出现在积分排行中</p> </div><input class='edithidden' name="form[id]" value='' type="hidden"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
      </form>
    </div>

            <div class="col-md-7">


    </div>

</div>

            </div>
                                </div>
                            </div>-->
                            <!-- modal -->
 <div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:40%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      用户名称
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

                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">兑换券增减： <span class="tpl-form-line-small-title">（正数为增加、负数为减少）</span></label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore" name="form[score]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                                <!-- <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">用户状态 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">正常</li>
                                    <li id="switch-2" class="switch-type purple">已锁定</li>
                                    <li id="switch-3" class="switch-type blue">白名单</li>
                                    <li id="switch-4" class="switch-type red">隐身用户</li>
                                    <input type="hidden" name="form[lock]" id="flock0" value="">
                                </ul>
                            </div>
                            </div>
                </div> -->
            <!-- <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>1、加入白名单后不会自动锁定<br>2、隐身用户不会出现在积分排行中</p>
                </div>
            </div> -->
            <input class='edithidden' name="form[id]" value='' type="hidden">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改用户</button>
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
                    $('.edit').click(function(){
                      $(".edithidden").attr("value",$(this).data('id'));
                      $(".nickname").text($(this).data('name'));
                      var i = $(this).data('lock');
                      $('#flock0').val(i);
                      if (i==0) {
                        $('#switch-1').addClass('green-on');
                      }else if(i==1){
                        $('#switch-2').addClass('purple-on');
                      }else if(i==3){
                        $('#switch-3').addClass('blue-on');
                      }else{
                        $('#switch-4').addClass('red-on');
                      };
                      $('.shadow').fadeIn();
                    });

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#flock0').val(0);
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-3').removeClass('blue-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                    });
                    $('#switch-2').click(function(){
                      $('#flock0').val(1);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-3').removeClass('blue-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('purple-on');
                    });
                    $('#switch-3').click(function(){
                      $('#flock0').val(3);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('blue-on');
                    });
                    $('#switch-4').click(function(){
                      $('#flock0').val(4);
                      $('#switch-1').removeClass('green-on');
                      $('#switch-2').removeClass('purple-on');
                      $('#switch-3').removeClass('blue-on');
                      $(this).addClass('red-on');
                    });
</script>
