<?php defined('SYSPATH') or die('No direct script access.');
// 看样子
// 账号：baonikan@smfyun.com
// 密码：www19910523
// wxbc550991f98c2c7b
// f5fb50132783f898ceb3aaef37c5bc2d
// 复用神码浮云公众号的微信支付
class Controller_qwthfc extends Controller_Base{
  public $template = 'tpl/blank';
  public $appid = 'wxbc550991f98c2c7b';
  public $secret = 'f5fb50132783f898ceb3aaef37c5bc2d';
  public $bid = 6;
  public $mch_id = '1229635702';
  public $mch_key = 'vdY25BlR1U58kBDiuJ1DFHPgldXnOkD6';
  public function before() {
      Database::$default = "wdy";
      parent::before();
  }
  public function action_list(){
      // $res = model::factory('hfcapi')->goodsList();
      $arr['mobile'] = '15819235432';//广东空号
      $arr['goods_id'] = 1;
      $arr['out_trade_no'] = 'E'.rand(0,9).time().rand(0,9);

      $res = model::factory('hfcapi')->todoapi($arr,'recharge');
      $res_arr = json_decode($res,true);
      echo '<pre>';
      var_dump($res_arr);
      exit;
  }
  public function action_hfc_notify(){
      $postStr = file_get_contents("php://input");
      $arr = json_decode($postStr,true);
      kohana::$log->add('hfc_notify',print_r($postStr,true));
      kohana::$log->add('hfc_notify2',print_r($arr,true));
  }
  public function action_onLogin(){
      if($_GET['code']){
        $charge = $this->charge;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$this->appid."&secret=".$this->secret."&js_code=".$_GET['code']."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output,true);
        kohana::$log->add('hfconLogin',print_r($result,true));
        //kohana::$log->add('lopost',print_r($result1,true));
        // var_dump($result);
        // echo $output;
        $user = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$result['openid'])->find();
        $men=Cache::instance('memcache');
        $men->set($result['openid'],$result['session_key'],7200);
        if($user->id){
          $result['hasin'] = 'yes';//存在表中
        }else{
          $result['hasin'] = 'no';//不存在该用户
        }
        $result['tel'] = $user->tel;
        echo json_encode($result);
        exit;
      }
  }
  public function action_getPhoneNumber($openid){
      $postStr = file_get_contents("php://input");
      $arr = json_decode($postStr,true);
      $user = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
      if($arr['iv']&&$arr['encryptedData']){
          $encryptedData = $arr['encryptedData'];
          $iv = $arr['iv'];
          require_once Kohana::find_file('vendor/lib', 'wxBizDataCrypt');
          $appid = $this->appid;
          $men = Cache::instance('memcache');
          $sessionKey=$men->get($openid);
          // echo $appid.'<br>';
          // echo $sessionKey.'<br>';
          // echo $encryptedData.'<br>';
          // echo $iv.'<br>';
          $pc = new WXBizDataCrypt($appid, $sessionKey);
          $errCode = $pc->decryptData($encryptedData, $iv, $data );
          if ($errCode == 0) {
              $result=json_decode($data,true);
              // var_dump($result);
              $result['state'] = 1;
              $user->tel = $result['phoneNumber'];
              $user->countryCode = $result['countryCode'];
              $user->save();
              echo json_encode($result);
          } else {
              $result['state'] = 1;
              $result['errCode'] = $errCode;
              echo json_encode($result);
          }
      }
      exit;
  }
  public function action_qrcode(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('hfc_qrpost1',print_r($postStr,true));
      // if($_POST){
      //   kohana::$log->add('qrpost2s',print_r($_POST,true));
      // }
      if($postStr){
        $result1=json_decode($postStr,true);
        kohana::$log->add('hfc_qrpost',print_r($result1,true));
        $qrcode = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$result1['openid'])->find();
        if($qrcode->id){
            $qrcode->avatarUrl = $result1['avatarUrl'];
            $qrcode->nickName = $result1['nickName'];
        }else{
            $qrcode->values($result1);
            $qrcode->sex = $result1['gender'];
        }
        if($qrcode->save()){
          $result['code'] = 'success';
        }else{
          $result['code'] = 'fail';
        }
        echo json_encode($result);
        exit;
      }
  }
  public function action_payfee(){
      $postStr = file_get_contents("php://input");
      $arr = json_decode($postStr,true);
      if($arr['openid']&&$arr['iid']&&$arr['tel']){
          require_once Kohana::find_file('vendor/lib', 'WeixinPay');
          $appid = $this->appid;
          $openid = $arr['openid'];
          $iid = $arr['iid'];
          $tel = $arr['tel'];
          $ftid = $arr['ftid'];//团长订单
          $type = $arr['type'];//1拼团 2自购

          $user = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
          $item = ORM::factory('qwt_hfcitem')->where('bid','=',$this->bid)->where('id','=',$iid)->find();
          if($ftid>0){//有团长，通分享进来的
              $floow_trades= ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('ftid','=',$ftid)->where('status','=',1);//团队数量
              $ftrade = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('status','=',1)->where('id','=',$ftid)->find();//找出团长订单
              $floow_trades = $floow_trades->reset(FALSE);
              $f_count = $floow_trades->count_all();
              if($f_count>=$item->groupnum){//验证是否满员
                  $return['status'] = 'fail';
                  $return['content'] = '该团已经满员了';
                  echo json_encode($return);
                  exit;
              }
              if($item->timeouttype==1){//拼团团长的时间+offsettime  判断团是否过期
                  $timeout = $ftrade->pay_time + $item->timeout;
                  if($timeout<=time()){
                      $return['status'] = 'fail';
                      $return['content'] = '拼团时间已经结束了';
                      echo json_encode($return);
                      exit;
                  }
              }
          }
          if($item->timeouttype==0){
              if($item->timeout<=time()){
                  $return['status'] = 'fail';
                  $return['content'] = '拼团时间已经结束了';
                  echo json_encode($return);
                  exit;
              }
          }
          $trade = ORM::factory('qwt_hfctrade');
          $trade->bid = $this->bid;
          $trade->qid = $user->id;
          $trade->iid = $iid;
          // $trade->teamid = $team->id;
          $trade->title = $item->name;
          $trade->tid = 'E'.time();

          $trade->tel = $tel;
          $trade->save();
          if($type == 1){
            $trade->payment = $item->price;
            $trade->ftid = $ftid?$ftid:$trade->id;
          }else{
            $trade->payment = $item->old_price;
            $trade->ftid = $trade->id;
          }
          $trade->pintype = $type;//待支付
          $trade->status = 0;//待支付
          $trade->save();

          $mch_id = $this->mch_id;
          $key = $this->mch_key;
          $out_trade_no = $mch_id. time();
          $body = $item->name;
          $total_fee = floatval($trade->payment*100);
          $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
          $return = $weixinpay->pay();
          $return['trade_id'] = $trade->id;
          $return['status'] = 'success';
          echo json_encode($return);
      }
      exit;
  }
  public function action_notify($trade_id){
      $trade = ORM::factory('qwt_hfctrade')->where('id','=',$trade_id)->find();
      if($trade->id){
        $trade->pay_time = time();
        $trade->status = 1;
        $trade->save();
        if($trade->pintype == 1){//代表是团购
          //自己就是团长 插入team表
          if($trade->ftid == $trade->id){
            $team = ORM::factory('qwt_hfcteam');
            $team->bid = $this->bid;
            $team->iid = $trade->iid;
            $team->tid = $trade->id;
            $team->state = 0;//未拼团成功
            $team->status = 0;//待处理
            $team->save();
            $trade->teamid = $team->id;
          }else{
          //自己不是团长 判断是否拼团成功
            $floow_trades= ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('ftid','=',$trade->ftid)->where('status','=',1);//团队数量
            $floow_trades = $floow_trades->reset(false);
            $f_count = $floow_trades->count_all();
            $ftrade = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('id','=',$trade->ftid)->find();
            $team = ORM::factory('qwt_hfcteam')->where('bid','=',$this->bid)->where('tid','=',$ftrade->id)->find();
            Kohana::$log->add("hfc1", $ftrade->id);
            Kohana::$log->add("hfc2", $team->id);
            if($trade->items->groupnum<=$f_count){
              //拼团成功了
              $team->state = 1;
              $team->save();
            }
            $trade->teamid = $team->id;
          }
        }else{//自购 自己就是团长
          $team = ORM::factory('qwt_hfcteam');
          $team->bid = $this->bid;
          $team->iid = $trade->iid;
          $team->tid = $trade->id;
          $team->state = 1;//未拼团成功
          $team->status = 0;//待处理
          $team->save();
          $trade->teamid = $team->id;
        }
        $trade->save();
        Kohana::$log->add("hfc3", $trade->teamid);
      }
  }
  public function action_items(){
      $list = ORM::factory('qwt_hfcitem')->where('bid','=',$this->bid)->where('show','=',1)->order_by('pri','desc')->find_all();
      foreach ($list as $k => $v) {
          $items[$k]['name'] = $v->name;
          $items[$k]['old_price'] = $v->old_price;
          $items[$k]['price'] = $v->price;
          $items[$k]['timeouttype'] = $v->timeouttype;
          $items[$k]['timeout'] = $v->timeout;
          $items[$k]['groupnum'] = $v->groupnum;
          $items[$k]['id'] = $v->id;
          $items[$k]['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwthfca/images/item/'.$v->id.'?v='.time();
          // $items[$k]['desc'] = $v->desc;
      }
      echo json_encode($items);
      exit;
  }
  public function action_detail($id){
      $user = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$_GET['openid'])->find();
      $item = ORM::factory('qwt_hfcitem')->where('bid','=',$this->bid)->where('show','=',1)->where('id','=',$id)->find();
      $items['name'] = $item->name;
      $items['old_price'] = $item->old_price;
      $items['price'] = $item->price;
      $items['timeouttype'] = $item->timeouttype;
      $items['timeout'] = $item->timeout;
      $items['groupnum'] = $item->groupnum;
      // $items['desc'] = $item->desc;
      $items['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwthfca/images/item/'.$item->id.'?v='.time();
      $items['groupnum'] = $item->groupnum;
      $items['has_tuan'] = ORM::factory('qwt_hfcteam')->where('bid','=',$this->bid)->where('iid','=',$item->id)->where('state','=',1)->count_all();
      $items['canstartpin'] = 1;//只能开启一键拼团
      $descs = ORM::factory('qwt_hfcdesc')->where('bid','=',$this->bid)->where('iid','=',$item->id)->order_by('pri','asc')->find_all();
      foreach ($descs as $k => $v) {
          $items['desc'][$k]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwthfca/images/desc/'.$v->id.'?v='.time();
      }
      if($_GET['tid']>0){//分享进来
          $trade = ORM::factory('qwt_hfctrade')->where('status','=',1)->where('id','=',$_GET['tid'])->find();
          $team = ORM::factory('qwt_hfcteam')->where('id','=',$trade->teamid)->find();
          $floow_trades = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('teamid','=',$team->id)->where('status','=',1)->find_all();
          foreach ($floow_trades as $k => $v) {
              $items['team'][$k]['id'] = $v->id;
              $items['team'][$k]['headimg'] = $v->qrcodes->avatarUrl;
              $items['team'][$k]['ftid'] = $v->ftid;
              $all_trades[$k] = $v->qid;
          }
          $less = 0;
          for ($i = $k+1; $i <= $item->groupnum-1 ; $i++) {
              $items['team'][$i]['id'] = 0;
              $items['team'][$i]['headimg'] = 0;
              $items['team'][$i]['ftid'] = 0;
              $less ++ ;
          }
          if($less>0){//没有拼团完
              if($item->timeouttype==1){//拼团团长的时间+offsettime  判断团是否过期
                  $timeout = $trade->pay_time + $item->timeout;
              }else{
                  $timeout = $item->timeout;
              }
              // echo $timeout;
              $lasttime = $timeout-time();
              if($lasttime>0){//还可以拼团
                  $d = floor($lasttime/(3600*24)); //天
                  $h = floor(($lasttime%(3600*24))/3600); //小时
                  $i = floor(($lasttime%(3600))/60); //分
                  $s = $lasttime%60; //秒
                  // echo $d.'天'.$h.'时'.$i.'分'.$s.'秒';
                  $lest = $d.'天'.$h.'时'.$i.'分';
                  $items['lasttime'] = '还剩'.$less.'个名额，'.$lest.'后结束拼团';
                  $items['btn']['text'] = '邀请好友参与拼团';
                  $items['canstartpin'] = 2;//可以拼团
              }else{//到期不能拼团
                  $items['lasttime'] = '拼团时间已与'.date('Y-m-d H:i:s',$timeout).'过期';
              }
          }else{
              $items['lasttime'] = '拼团已完成！';
          }
          if(in_array($user->id, $all_trades)){//是团长或者团员进来
              $items['canstartpin'] = 1;//只能开启一键拼团
          }
      }
      echo json_encode($items);
      exit;
  }
  public function action_myjoin($openid){
      $user = ORM::factory('qwt_hfcqrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
      $trades = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('status','>',0)->order_by('createdtime','desc');
      $trades = $trades->reset(FALSE);
      $trades_num = $trades->count_all();//数量
      $trades_obj = $trades->find_all();
      if($trades_num>0){
        $result['state'] = 1;
        foreach ($trades_obj as $k => $v) {
          if($v->ftid == $v->id){//自己发起的
            $arr[$k]['pintype'] = '你发起的拼团';
            if($v->pintype == 2){
                $arr[$k]['pintype'] = '单独购买单独';
            }
          }else{
            $arr[$k]['pintype'] = '你参与的拼团';
          }
          $arr[$k]['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwthfca/images/item/'.$v->iid.'?v='.time();
          $arr[$k]['time'] = date('Y-m-d H:i:s',$v->createdtime);
          $item = ORM::factory('qwt_hfcitem')->where('id','=',$v->iid)->find();
          $team = ORM::factory('qwt_hfcteam')->where('id','=',$v->teamid)->find();
          if($team->state==1){
            $arr[$k]['state'] = '拼团成功';
          }else{
            if($item->timeouttype==1){//拼团团长的时间+offsettime  判断团是否过期
                $timeout = $team->trades->pay_time + $item->timeout;
            }else{
                $timeout = $time->timeout;
            }
            if($timeout>time()){//还可以继续拼团
              $floow_count = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('ftid','=',$team->tid)->where('status','=',1)->count_all();
              // echo $item->groupnum.'<br>';
              // echo $floow_count.'<br>';
              // echo $timeout.'<br>';
              $num = $item->groupnum-$floow_count;
              $arr[$k]['state'] = '拼团中，还剩'.$num.'人';
              $lasttime = $timeout-time();
              // echo $lasttime.'<br>';
              $d = floor($lasttime/(3600*24)); //天
              $h = floor(($lasttime%(3600*24))/3600); //小时
              $i = floor(($lasttime%(3600))/60); //分
              $s = $lasttime%60; //秒
              // echo $d.'天'.$h.'时'.$i.'分'.$s.'秒';
              $less = $d.'天'.$h.'时'.$i.'分';
              $arr[$k]['lasttime'] = '剩余'.$less;
              $arr[$k]['canshare'] = 1;
            }else{
              if($v->status==2){
                $arr[$k]['state'] = '未成团，退款成功';
              }
              if($v->status==1){
                $arr[$k]['state'] = '未成团，退款中';
              }
            }
          }
          $arr[$k]['title'] = $item->name;
          $arr[$k]['tid'] = $v->id;
          $arr[$k]['iid'] = $item->id;
          $arr[$k]['price'] = $item->price;
          $arr[$k]['old_price'] = $item->old_price;
          $arr[$k]['pin'] = $item->groupnum.'人拼单';
          $arr[$k]['pintype_state'] = 'tuan_buy';
          if($v->pintype == 2){
            $arr[$k]['pintype'] = '单独购买';
            $arr[$k]['state'] = '';
            $arr[$k]['price'] = $item->old_price;
            $arr[$k]['pintype_state'] = 'only_buy';
          }
          $result['items'] = $arr;
          $result['state'] = 1;
        }
      }else{
        $result['state'] = 0;
      }
      echo json_encode($result);
      exit;
  }
  public function action_joindetail($trade_id){
      $trade = ORM::factory('qwt_hfctrade')->where('status','>',0)->where('id','=',$trade_id)->find();
      $item = ORM::factory('qwt_hfcitem')->where('id','=',$trade->iid)->find();
      $team = ORM::factory('qwt_hfcteam')->where('id','=',$trade->teamid)->find();
      $ftrade = ORM::factory('qwt_hfctrade')->where('id','=',$trade->ftid)->find();
      $result['img'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwthfca/images/item/'.$item->id.'?v='.time();
      $result['title'] = $item->name;
      $result['price'] = $item->price;
      $result['iid'] = $item->id;
      $result['tid'] = $trade->id;
      $result['ftid'] = $ftrade->id;
      $result['dif'] = number_format($item->old_price-$item->price,2);
      $floow_trades = ORM::factory('qwt_hfctrade')->where('bid','=',$this->bid)->where('teamid','=',$team->id)->where('status','>',0)->find_all();
      foreach ($floow_trades as $k => $v) {
          $result['team'][$k]['id'] = $v->id;
          $result['team'][$k]['headimg'] = $v->qrcodes->avatarUrl;
          $result['team'][$k]['ftid'] = $v->ftid;
      }
      $less = 0;
      for ($i = $k+1; $i <= $item->groupnum-1 ; $i++) {
          $result['team'][$i]['id'] = 0;
          $result['team'][$i]['headimg'] = 0;
          $result['team'][$i]['ftid'] = 0;
          $less ++ ;
      }
      if($trade->ftid == $trade->id){
          $result['time_text'] = '开团时间';
          $result['time'] = date('Y-m-d H:i:s',$ftrade->pay_time);
      }else{
          $result['time_text'] = '参团时间';
          $result['time'] = date('Y-m-d H:i:s',$trade->pay_time);
      }
      if($less>0){//没有拼团完
          if($item->timeouttype==1){//拼团团长的时间+offsettime  判断团是否过期
              $timeout = $ftrade->pay_time + $item->timeout;
          }else{
              $timeout = $time->timeout;
          }
          $lasttime = $timeout-time();
          if($lasttime>0){//还可以拼团
              $d = floor($lasttime/(3600*24)); //天
              $h = floor(($lasttime%(3600*24))/3600); //小时
              $i = floor(($lasttime%(3600))/60); //分
              $s = $lasttime%60; //秒
              // echo $d.'天'.$h.'时'.$i.'分'.$s.'秒';
              $lest = $d.'天'.$h.'时'.$i.'分';
              $result['lasttime'] = '还剩'.$less.'个名额，'.$lest.'后结束拼团';
              $result['btn']['text'] = '邀请好友参与拼团';
              $result['lestnum'] = $less;
          }else{//到期不能拼团
              $result['lasttime'] = '拼团时间已与'.date('Y-m-d H:i:s',$timeout).'过期';
          }
      }else{
          $result['lasttime'] = '拼团已完成！';
      }
      echo json_encode($result);
      exit;
  }
  public function action_moneyBack($bid){
    $team=ORM::factory('qwt_hfcteam')->where('bid','=',$bid)->where('state','=',0)->find_all();
    $teams[]=0;
    foreach ($team as $k => $v){
          $teams[] = $v->id;
    }
    // echo '<pre>';
    // var_dump($teams);
    $trades = ORM::factory('qwt_hfctrade')->where('bid','=',$bid)->where('teamid','IN',$teams)->where('status','=',1)->where('payment','>=',1)->order_by('lastupdate','ASC')->limit(10)->find_all();
    foreach ($trades as $k => $v) {
      $money=100*$v->payment;
      if($v->items->timeouttype==0){
        $time=$v->items->timeout;
      }elseif($v->items->timeouttype==1){
        $time=$v->items->timeout+$v->pay_time;
      }
      // echo 'tid:'.$v->id.'<br>';
      // echo 'time:'.$time.'<br>';
      // echo 'nowtime:'.time().'<br>';
      $trade=ORM::factory('qwt_hfctrade')->where('id','=',$v->id)->find();
      if($time<=time()){
        $moneyresult=$this->sendMoney($bid,$v->qid,$money);
        // var_dump($moneyresult);
        if($moneyresult['result_code']=='SUCCESS'){
          $trade->status=2;
        }else{
          $trade->error=$moneyresult['err_code_des'];
        }
      }else{
        $trade->lastupdate=time();
      }
      $trade->save();
    }
  }
  private function sendMoney($bid,$qid,$money){
    $wx['appid']='wxbc550991f98c2c7b';
    $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d
';
    $key='vdY25BlR1U58kBDiuJ1DFHPgldXnOkD6';
    $qrcode=ORM::factory('qwt_hfcqrcode')->where('id','=',$qid)->find();
    require_once Kohana::find_file('vendor', 'weixin/inc');
    require_once Kohana::find_file('vendor', 'weixin/wechat.class');
    $we = new Wechat($wx);
    $mch_id='1229635702';
    $mch_billno = $mch_id. date('YmdHis').rand(1000, 9999); //订单号
    $data["mch_appid"] = $wx['appid'];
    $data["mchid"] = $mch_id; //商户号
    $data["nonce_str"] = $we->generateNonceStr(32);
    $data["partner_trade_no"] = $mch_billno; //订单号
    $data["openid"] = $qrcode->openid;
    $data["re_user_name"] = $qrcode->nickName;
    $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
    // $data["re_user_name"] = $name; //收款用户姓名
    $data["amount"] = $money;
    $data["desc"] = '未成团退款';
    $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
    $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $key));
    $postXml = $we->xml_encode($data);
    $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    // Kohana::$log->add('weixin_fxb:hongbaopost', print_r($data, true));
    $resultXml = $this->curl_post_ssl($url, $postXml, 10);
    $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
    // echo "<pre>";
    // var_dump($response);
    // echo "</pre>";
    $result['xml'] = $resultXml;
    $result['return_code'] = (string)$response->return_code;
    $result['return_msg'] = (string)$response->return_msg[0];
    $result['result_code'] = (string)$response->result_code[0];
    $result['re_openid'] = (string)$response->re_openid[0];
    $result['total_amount'] = (string)$response->total_amount[0];
    $result['err_code'] = (string)$response->err_code[0];
    $result['err_code_des'] = (string)$response->err_code_des[0];
    return $result;
    // if($result['result_code']=='SUCCESS'){
    //   ORM::factory('yjb_score')->scoreOut($qrcode,3,$money1);
    //   $result1['state']='SUCCESS';
    //   $result1['code']=$money1;
    //   echo json_encode($result1);
    // }else{
    //   $result1['state']='FAIL';
    //   $result1['code']=$result['err_code_des'];
    //   echo json_encode($result1);
    // }
    // exit();
  }
  private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        // $config = $this->config;
        // $bid = $this->bid;
        $cert_file = DOCROOT."bnk/cert/apiclient_cert.pem";
        $key_file = DOCROOT."bnk/cert/apiclient_key.pem";
        //证书分布式异步更新
        // $file_cert = ORM::factory('fxb_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_cert')->find();
        // $file_key = ORM::factory('fxb_cfg')->where('bid', '=', $bid)->where('key', '=', 'fxb_file_key')->find();

        // if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        // if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        // if (!file_exists($cert_file)) {
        //     @mkdir(dirname($cert_file));
        //     @file_put_contents($cert_file, $file_cert->pic);
        // }

        // if (!file_exists($key_file)) {
        //     @mkdir(dirname($key_file));
        //     @file_put_contents($key_file, $file_key->pic);
        // }

        // Kohana::$log->add("weixin_fxb:$bid:curl_post_ssl:cert_file", $cert_file);

        //超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $cert_file);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $key_file);

        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);

        $data = curl_exec($ch);

        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo curl_error($ch);
            curl_close($ch);
            return false;
        }
  }
}

