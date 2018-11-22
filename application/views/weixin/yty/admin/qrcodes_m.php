
<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
if ($result['ffuser']) $title=$result['ffuser']->nickname.'的二级';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    经销商列表
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/qrcodes">经销商列表</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" style="white-space: nowrap;">
                <tbody><tr>
                  <th>经销商等级</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>身份证号</th>
                  <th>地址</th>
                  <th>进货额账户</th>
                  <th>预计收益</th>
                  <th>可结算收益</th>
                  <th>当月订单数/当月订单金额</th>
                  <th>累计订单数/累计订单金额</th>
                  <th>当月补货金额/累计补货金额金额</th>
                  <!-- <th>当月补货金额</th>
                  <th>累计订单数</th>
                  <th>当月订单数</th> -->
                  <th style="width:100px;">所属上级/S级经销商</th>
                  <th style="width:100px;">所有下级经销商/累计客户</th>
                  <th style="width:100px;">操作</th>
                </tr>
                <?php
                foreach ($result['qrcodes'] as $v):
                $date=date('Y-m');
                $time=strtotime(date('Y-m'));
                $stock=DB::query(Database::SELECT,"SELECT sum(money) as stock from yty_stocks where bid=$v->bid and (type = 1 or type=0) and qid = $v->id and money>0 and lastupdate >= $time")->execute()->as_array();
                 $count1=$stock[0]['stock'];
                 $count2=ORM::factory('yty_trade')->where('bid','=',$v->bid)->where('status','!=','TRADE_CLOSED')->where('status','!=','TRADE_CLOSED_BY_USER')->where('fopenid','=',$v->openid)->count_all();
                $count3=ORM::factory('yty_trade')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('status','!=','TRADE_CLOSED')->where('status','!=','TRADE_CLOSED_BY_USER')->where('pay_time','>=',$date)->count_all();
                $count4=ORM::factory('yty_qrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','=',1)->count_all();
                $count5=ORM::factory('yty_qrcode')->where('bid','=',$v->bid)->where('fopenid','=',$v->openid)->where('lv','!=',1)->count_all();
                $money6=DB::query(Database::SELECT,"SELECT sum(payment) as money6 from yty_trades where bid=$v->bid and fopenid = '$v->openid' and status != 'TRADE_CLOSED' and  status != 'TRADE_CLOSED_BY_USER' and pay_time >= $date ")->execute()->as_array();
                $count6=$money6[0]['money6'];
                $money7=DB::query(Database::SELECT,"SELECT sum(payment) as money7 from yty_trades where bid=$v->bid and status != 'TRADE_CLOSED' and  status != 'TRADE_CLOSED_BY_USER'and fopenid = '$v->openid' ")->execute()->as_array();
                $count7=$money7[0]['money7'];
                $stock8=DB::query(Database::SELECT,"SELECT sum(money) as stock from yty_stocks where bid=$v->bid and (type=0 or type =1) and qid = $v->id and money > 0")->execute()->as_array();
                $count8=$stock8[0]['stock'];
                 $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/yty/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/yty/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $fopenid=$v->fopenid;
                  $fuser=ORM::factory('yty_qrcode')->where('bid','=',$v->bid)->where('openid','=',$fopenid)->find();
                  $suser=ORM::factory('yty_qrcode')->where('id','=',$v->agent->suser)->find();
                   $money_now = $v->scores->select(array('SUM("score")', 'money_now'))->where('paydate', '<', time())->find()->money_now;
                ?>
                <tr>
                  <td><?=$v->agent->skus->name?></td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><a href="<?=$url?>"><?=$v->nickname?></td>
                  <td><?=$v->agent->name?></td>
                  <td><?=$v->agent->tel?></td>
                  <td><?=$v->agent->id_card?></td>
                  <td><?=$v->agent->address?></td>
                  <td><?=$v->agent->stock?></td>
                  <td><?=$v->money?$v->money:'0.00'?></td>
                  <td><?=$money_now?$money_now:'0.00'?></td>
                  <td><a href="/ytya/history_trades?id=<?=$v->id?>"><?=$count3?>/<?=$count6?$count6:0?></td>
                  <td><a href="/ytya/history_trades?id=<?=$v->id?>"><?=$count2?>/<?=$count7?$count7:0?></td>
                  <td><a href="/ytya/stock_history?id=<?=$v->id?>"><?=$count1?$count1:0?>/<?=$count8?$count8:0?></td>
                  <td><?=$fuser->nickname?>/<?=$suser->nickname?></td>
                  <td><?=$count4?>/<?=$count5?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->agent->tel?>' data-uname='<?=$v->agent->name?>' data-card ='<?=$v->agent->id_card?>' data-address ='<?=$v->agent->address?>'>
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</section><!-- /.content -->
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改用户</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var uname = button.data('uname')
  var tel = button.data('tel')
  var card = button.data('card')
  var address = button.data('address')
  var lv = button.data('lv')
  var form = '<div class="form-group"><label for="fscore">姓名：</label><input class="form-control" id="fscore" name="form[name]" max="999" type="text" style="width:150px" value='+uname+'><div class="form-group"><label for="fscore">电话：</label><input class="form-control" id="fscore" name="form[tel]" max="" type="number" style="width:150px" value='+tel+'><div class="form-group"><label for="fscore">身份证号：</label><input class="form-control" id="fscore" name="form[id_card]" type="text" style="width:250px" value='+card+'><div class="form-group"><label for="fscore">地址：</label><input class="form-control" id="fscore" name="form[address]" type="text" style="width:250px" value='+address+'>'
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock0" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="3"'+ (lv==3 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">取消分销商资格</label></div></div>'
  form += '<div class="form-group"><label for="flock">是否升级：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"'+'><span class="label label-success" style="font-size:14px">升级</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"'+'><span class="label label-danger" style="font-size:14px">不升级</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
