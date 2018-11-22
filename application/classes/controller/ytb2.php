<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ytb extends Controller_Base {
    public $template = 'weixin/ytb/tpl/fftpl';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    var $baseurl = 'http://dd.smfyun.com/ytb/';
    var $we;
    var $client;
    public $methodVersion='3.0.0';
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "ytb";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        if (Request::instance()->action == 'storefuop') return;
        if (Request::instance()->action == 'getopenid') return;
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'acount') return;
        //if (Request::instance()->action == 'index_oauth') return;

        $this->config = $_SESSION['ytb']['config'];
        $this->openid = $_SESSION['ytb']['openid'];
        $this->bid = $_SESSION['ytb']['bid'];
        $this->uid = $_SESSION['ytb']['uid'];

        $this->access_token = $_SESSION['ytb']['access_token'];


        if ($_GET['debug']) print_r($_SESSION['ytb']);


        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['ytba']['bid']) die('请通过微信访问。');
    }

    public function after() {
        $this->config=$config=ORM::factory('ytb_cfg')->getCfg($this->bid,1);
        $user = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find()->as_array();
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$this->openid'")->execute()->as_array();
         $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
         $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；

          for($i=0;$firstchild[$i];$i++)
          {
            $tempid[$i]=$firstchild[$i]['openid'];
          }

        $customer=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $this->openid)->order_by('all_score', 'DESC');
        $user['follows'] =$customer->count_all();
        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();

        $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
        for($i=0;$firstchild[$i];$i++)
        {
            $tempid[$i]=$firstchild[$i]['openid'];
        }

        $user['follows_month']=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $this->openid)->where('jointime','>=',$month)->count_all();
        //待结算
        $score_sum1=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','=',$user['id'])->select(array('SUM("score1")', 'score1s'))->find()->score1s;
        $firstchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $this->bid and fopenid='$this->openid'")->execute()->as_array();
        $tempid=array();
        $tempids=('!!!');
        if($firstchild[0]['openid']==null){
        $tempid=array('0' =>'!!!');
        $tempids=('!!!');//没有二级时 匹配一个不存在的；
        }else{
            for($i=0;$firstchild[$i];$i++){
              $tempid[$i]=$firstchild[$i]['id'];
              //$tempids[$i]=$firstchild[$i]['openid'];
              //$tempids=$tempids."'".$firstchild[$i]['openid']."',";
              $tempids=$tempids."','".$firstchild[$i]['openid'];
            }
        //$the_uname ="uname in(".$uname."'')";
        }
        //$the_uname ="fopenid in(".$tempids."'')";
        $the_uname ="fopenid in('$tempids')";
        // $firstchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $user->bid and `fopenid`='$user->openid' ")->execute()->as_array();
        // $tempids=('!!!');
        // $tempid=array();
        // if($firstchild[0]['openid']==null){
        //     $tempid=array('0' =>'!!!');
        //     $tempids=('!!!');//没有二级时 匹配一个不存在的；
        // }else{
        //     for($i=0;$firstchild[$i];$i++){
        //       $tempid[$i]=$firstchild[$i]['id'];
        //       //$tempids[$i]=$firstchild[$i]['openid'];
        //       $tempids=$tempids."','".$firstchild[$i]['openid'];
        //     }
        // }
        // $the_uname ="fopenid in('$tempids')";
        $score_sum2=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid)->select(array('SUM("score2")', 'score2s'))->find()->score2s;
        //$lastchild=DB::query(Database::SELECT,"SELECT  * from ytb_qrcode where  FIND_IN_SET(fopenid,(select openid  from ytb_qrcode where  `bid` = $v->bid and `fopenid`='$v->openid' )")->execute()->as_array();
        $lastchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $this->bid and ".$the_uname." ")->execute()->as_array();
        $tempid1=array();
        if($lastchild[0]['openid']==null){
        $tempid1=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        }else{
        for($i=0;$lastchild[$i];$i++){
          $tempid1[$i]=$lastchild[$i]['id'];
        }
        }

        $score_sum3=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid1)->select(array('SUM("score3")', 'score3s'))->find()->score3s;
        $score_sum=number_format($score_sum1+$score_sum2+$score_sum3,2);
        $user['wait_score'] = $score_sum;
        //待结算

        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$this->openid' and jointime>='$month'")->execute()->as_array();


        $user['trades'] = ORM::factory('ytb_score')->where('qid', '=', $user['id'])->where('type', 'IN', array(1,2))->count_all();
        View::bind_global('openid', $this->openid);
        View::bind_global('bid', $this->bid);
        View::bind_global('config', $this->config);
        View::bind_global('user2', $user);

        $this->template->user = $user;
        parent::after();
    }
    //入口
    public function action_index($bid) {
        //只能通过微信打开
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['ytba']['bid']) return $this->action_msg('请通过微信打开！', 'warn');

        $config = ORM::factory('ytb_cfg')->getCfg($bid,1);
        $this->access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
        if (!$_GET['openid']) $_SESSION['ytb'] = NULL;

        //OpenId 解密
        if ($config && $_GET['openid']) {
            $openid = base64_decode($_GET['openid']);
            if ($_GET['cksum'] != md5($openid.$config['appsecret'].date('Y-m'))) {
                $_SESSION['ytb'] = NULL;
                die('该页面已过期！');
            }

            $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();

            $_SESSION['ytb']['config'] = $config;
            $_SESSION['ytb']['openid'] = $openid;
            $_SESSION['ytb']['bid'] = $bid;
            $_SESSION['ytb']['uid'] = $userobj->id;
            $_SESSION['ytb']['access_token'] =$this->access_token;
            Request::instance()->redirect('/ytb/'.$_GET['url']);
        }
    }
    //默认页面
    public function action_test(){
        $this->template = 'tpl/blank';
        self::before();
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('ytb', print_r($postStr, true));
        $result11=json_decode($postStr,true);
        Kohana::$log->add('ytbesult11', print_r($result11, true));
        if($postStr){
            Kohana::$log->add('bbbbbbbb', 'aaaaaaa');
            $enddata = array('code' => 0,'msg'=>'success');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
        $appid =$result11['app_id'];
        $msg=$result11['msg'];
        $kdt_id=$result11['kdt_id'];
        $status=$result11['status'];
        if($status=='WAIT_SELLER_SEND_GOODS'||$status=='WAIT_BUYER_CONFIRM_GOODS'||$status=='TRADE_BUYER_SIGNED'){
            $bid = ORM::factory('ytb_login')->where('shopid','=',$kdt_id)->find()->id;
            $this->bid=$bid;
            $this->access_token=ORM::factory('ytb_login')->where('id', '=', $this->bid)->find()->access_token;
            $config=ORM::factory('ytb_cfg')->getCfg($bid,1);
            $this->config=$config;
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $this->we=new Wechat($this->config);
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            if($this->access_token){
                $this->client = new YZTokenClient($this->access_token);
            }else{
                Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
            }
            $posttid=urldecode($msg);
            $jsona=json_decode($posttid,true);
            $weixin_user_id =$jsona['trade']['fans_info']['fans_id'];
            $tid=$jsona['trade']['tid'];
            Kohana::$log->add("weixin_user_id", print_r($weixin_user_id, true));
            if($weixin_user_id){
                $method = 'youzan.users.weixin.follower.get';
                $params = [
                'fans_id'=>$weixin_user_id,
                ];
                $qrcodes = $this->client->post($method, $this->methodVersion, $params, $files);
                Kohana::$log->add("qrcodes", print_r($qrcodes, true));
                $openid=$qrcodes['response']['user']['weixin_openid'];
                Kohana::$log->add("openid", print_r($openid, true));
                $qrcode  = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $qrcode->bid=$bid;
                $qrcode->openid=$qrcodes['response']['user']['weixin_openid'];
                $qrcode->nickname=$qrcodes['response']['user']['nick'];
                $qrcode->headimgurl=$qrcodes['response']['user']['avatar'];
                $qrcode->subscribe=$qrcodes['response']['user']['is_follow'];
                if($qrcodes['response']['user']['sex']=='m'){
                    $qrcode->sex=1;
                }elseif ($qrcodes['response']['user']['sex']=='f') {
                    $qrcode->sex=2;
                }else{
                    $qrcode->sex=0;
                }
                $qrcode->save();
            }
            $trade = ORM::factory('ytb_trade')->where('bid','=',$bid)->where('tid','=',$tid)->where('status','=','TRADE_BUYER_SIGNED')->find();
            if($trade->id) die();
            $trade = ORM::factory('ytb_trade')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            if($weixin_user_id){
                $qid=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
                $trade->qid=$qid;
            }
            $trade->bid=$bid;
            $trade->tid=$jsona['trade']['tid'];
            $trade->pic_thumb_path=$jsona['trade']['pic_thumb_path'];
            $trade->num=$jsona['trade']['num'];
            $trade->title=$jsona['trade']['title'];
            $trade->payment=$jsona['trade']['payment'];
            $trade->pay_time=strtotime($jsona['trade']['pay_time']);
            $trade->status=$status;
            $payment=$jsona['trade']['payment'];
            if($status=='WAIT_SELLER_SEND_GOODS'){
                $model_q1=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $lv=$model_q1->lv;
                if($lv==1){
                   $scores=$payment*$config['score_shao1']/100;
                }elseif ($lv==2) {
                    $scores=$payment*$config['score_er1']/100;
                }else{
                    $scores=$payment*$config['score_da1']/100;
                }
                $trade->score1=$scores;
                $score_dai=$this->action_acount($model_q1->openid,$bid);
                // echo $score_dai."<br>";
                // echo $scores."<br>";
                $score_dais=$score_dai+$scores;
                $msg1 = str_replace("「%a」",$trade->title,$config['shopping1']);
                $msg2 = str_replace("「%b」",$scores,$msg1);
                //$keyword=$msg2;
                $keyword=$msg2."\n您的累计{$config['score_name']}:{$model_q1->all_score}\n可用{$config['score_name']}:{$model_q1->score}\n待结算{$config['score_name']}:{$score_dais}";
                $result=$this->sendtext($model_q1->openid,$keyword);
                if($model_q1->fopenid){
                    $fopenid1=$model_q1->fopenid;
                    $fuser1=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid1)->find();
                    $lv=$fuser1->lv;
                    if($lv==1){
                        $scores=$payment*$config['score_shao1']/100;
                    }elseif ($lv==2) {
                        $scores=$payment*$config['score_er1']/100;
                    }else{
                        $scores=$payment*$config['score_da1']/100;
                    }
                    $trade->score2=$scores;
                    $score_dai=$this->action_acount($fuser1->openid,$bid);
                    $score_dais=$score_dai+$scores;
                    $msg1 = str_replace("「%a」",$model_q1->nickname,$config['shopping2']);
                    $msg2 = str_replace("「%b」",$scores,$msg1);
                    $keyword=$msg2."\n您的累计{$config['score_name']}:{$fuser1->all_score}\n可用{$config['score_name']}:{$fuser1->score}\n待结算{$config['score_name']}:{$score_dais}";
                    $result=$this->sendtext($fuser1->openid,$keyword);
                    if($fuser1->fopenid){
                        $ffopenid1=$fuser1->fopenid;
                        $ffuser1=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid1)->find();
                        $lv=$ffuser1->lv;
                        if($lv==1){
                            $scores=$payment*$config['score_shao2']/100;
                        }elseif ($lv==2) {
                            $scores=$payment*$config['score_er2']/100;
                        }else{
                            $scores=$payment*$config['score_da2']/100;
                        }
                        $trade->score3=$scores;
                        $score_dai=$this->action_acount($ffuser1->openid,$bid);
                        $score_dais=$score_dai+$scores;
                        $msg1 = str_replace("「%a」",$fuser1->nickname,$config['shopping3']);
                        $msg2 = str_replace("「%b」",$scores,$msg1);
                        $keyword=$msg2."\n您的累计{$config['score_name']}:{$ffuser1->all_score}\n可用{$config['score_name']}:{$ffuser1->score}\n待结算{$config['score_name']}:{$score_dais}";
                        $result=$this->sendtext($ffuser1->openid,$keyword);
                    }
                }

            }
            $trade->save();
            if($status=='TRADE_BUYER_SIGNED'){
                $trade = ORM::factory('ytb_trade')->where('bid','=',$bid)->where('tid','=',$tid)->find();

                $model_q=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $lv=$model_q->lv;
                Kohana::$log->add('lv1', print_r($lv, true));
                $model_q=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                $scores=$trade->score1;
                $model_q->scores->scoreIn($model_q, 0,$scores,0,$trade->id);
                if($lv==1){
                    Kohana::$log->add('msg2', print_r($msg2, true));
                    $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->all_score;
                    $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();
                    $model_q=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                    if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                        $model_q->lv=3;
                        $model_q->save();
                        $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                        $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                        $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                        $keyword=$msg5;
                        $result=$this->sendtext($model_q->openid,$keyword);
                    }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                        $model_q->lv=2;
                        $model_q->save();
                        $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                        $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                        $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                        $keyword=$msg5;
                        $result=$this->sendtext($model_q->openid,$keyword);
                    }
                }elseif ($lv==2) {
                    $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find()->all_score;
                    $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$openid)->count_all();
                    if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                        $model_q=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                        $model_q->lv=3;
                        $model_q->save();
                        $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                        $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                        $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                        $keyword=$msg5;
                        $result=$this->sendtext($model_q->openid,$keyword);
                    }
                }
                $model_q=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($model_q->fopenid){
                    $fopenid=$model_q->fopenid;
                    $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                    $lv=$fuser->lv;
                    Kohana::$log->add('lv2', print_r($lv, true));
                    $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                    $scores=$trade->score2;
                    $fuser->scores->scoreIn($fuser, 1,$scores,$model_q->id,$trade->id);
                    if($lv==1){
                        $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find()->all_score;
                        $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->count_all();
                        $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                        if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                            $fuser->lv=2;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }elseif ($lv==2) {
                        $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find()->all_score;
                        $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->count_all();
                        if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }
                    $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
                    if($fuser->fopenid){
                        $ffopenid=$fuser->fopenid;
                        $ffuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find();
                        $lv=$ffuser->lv;
                        $ffuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find();
                        $scores=$trade->score3;
                        $ffuser->scores->scoreIn($ffuser, 2,$scores,$model_q->id,$trade->id);
                        Kohana::$log->add('lv3', print_r($lv, true));
                        if($lv==1){
                            $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find()->all_score;
                            $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$ffopenid)->count_all();
                            $fuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find();
                            if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                                $ffuser->lv=3;
                                $ffuser->save();
                                $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                                $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                                $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                                $keyword=$msg5;
                                $result=$this->sendtext($ffuser->openid,$keyword);
                            }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                                $fuser->lv=2;
                                $fuser->save();
                                $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                                $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                                $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                                $keyword=$msg5;
                                $result=$this->sendtext($ffuser->openid,$keyword);
                            }
                        }elseif ($lv==2) {
                            $all_score=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find()->all_score;
                            $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$ffopenid)->count_all();
                            if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                                $ffuser=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffopenid)->find();
                                $ffuser->lv=3;
                                $ffuser->save();
                                $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                                $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                                $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                                $keyword=$msg5;
                                $result=$this->sendtext($ffuser->openid,$keyword);
                            }
                        }
                    }
                }
            }
        }
        exit;
    }
    public function action_home() {
        $view = "weixin/ytb/home";

        // die('系统维护中...');
        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('ytb_qrcode', $this->uid);
        if ($userobj->id) $userobj->save();

        $this->template->title = '我的'.$this->config['score_name'];
        $this->template->content = View::factory($view)->bind('result', $result);
    }
    public function action_storefuop($bid){// 静默授权
        $config=ORM::factory('ytb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/ytb/getopenid/'.$bid;
        $we = new Wechat($config);
        $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
        header("Location:$auth_url");
        exit;
    }
    public function action_getopenid($bid){//通过code获取openid
        $config=ORM::factory('ytb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($config);
        $token = $we->getOauthAccessToken();
        $openid=$token['openid'];
        echo $openid.'<br>';
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
        $client = new YZTokenClient($access_token);
        
        $method = 'youzan.users.weixin.follower.get';
        $params = [
            'weixin_openid'=>$openid,
         ];
        $results = $client->post($method, $this->methodVersion, $params, $files);
        echo '<pre>';
        var_dump($results);
        echo '</pre>';
        $user = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
        if($results['response']['user']['sex']=='m'){
            $sex=1;//男
        }else if($results['response']['user']['sex']=='f'){
            $sex=2;//女
        }else{
            $sex=0;//人妖
        }
        $user->openid = $openid;
        $user->nickname = $results['response']['user']['nick'];
        if($user->subscribe!=1){//一旦关注为1 就不允许撤销
            $user->subscribe = $results['response']['user']['is_follow'];
        }
        $user->sex = $sex;
        $user->bid = $bid;
        $user->headimgurl = $results['response']['user']['avatar'];
        $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
        $user->save();

        $_SESSION['ytb']['config'] = $config;
        $_SESSION['ytb']['openid'] = $openid;
        $_SESSION['ytb']['bid'] = $bid;
        $_SESSION['ytb']['uid'] = $user->id;
        $_SESSION['ytb']['access_token'] =$this->access_token;
        Request::instance()->redirect('/ytb/commends/'.$openid.'/'.$bid);
    }
    public function action_commends($mopenid,$bid){//奖品list分享页面
        $fopenid = $mopenid;
        $config=ORM::factory('ytb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/ytb/commends/'.$mopenid.'/'.$bid;
        $this->we = $we = new Wechat($config);

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_url);
        $userobj = ORM::factory('ytb_qrcode', $this->uid);
        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/ytb/tpl/tpl2';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }

            $user=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $user->bid=$bid;
            $user->openid=$openid;
            $user->lv = 1;
            $user->save();

            $_SESSION['ytb']['config'] = $config;
            $_SESSION['ytb']['openid'] = $openid;
            $_SESSION['ytb']['bid'] = $bid;
            $_SESSION['ytb']['uid'] = $user->id;

            $this->config = $_SESSION['ytb']['config'];
            $this->openid = $_SESSION['ytb']['openid'];
            $this->bid = $_SESSION['ytb']['bid'];
            $this->uid = $_SESSION['ytb']['uid'];

            $user = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
                $result['title'] = $fuser->nickname.'的推荐商品';
            }else{
                $result['title'] = $fuser->nickname.'的推荐商品';
                if($fopenid != $openid&&$fuser->id < $user->id){//上线id大于本人id
                    require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                    $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                    $client = new YZTokenClient($access_token);
                    
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                        'weixin_openid'=>$openid,
                     ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);

                    if($results['response']['user']['sex']=='m'){
                        $sex=1;//男
                    }else if($results['response']['user']['sex']=='f'){
                        $sex=2;//女
                    }else{
                        $sex=0;//人妖
                    }
                    $user->nickname = $results['response']['user']['nick'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $sex;
                    $user->headimgurl = $results['response']['user']['avatar'];
                    $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();
                    $status = 1;
                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的支持者!';

                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }

                    if($fuser->fopenid){//上上线存在
                        $ffuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                        $text = $ffuser->nickname.',恭喜您的好友'.$fuser->nickname.'增加了一个新的支持者!';
                        if($config['coupontpl']){
                            $this->sendtplcoupon($ffuser->fopenid,$config,$text,$we);
                        }else{
                            $msg['touser'] = $ffuser->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $text;
                            $we->sendCustomMessage($msg);
                        }
                    }
                    //判断是否升级，发送升级文案
                    $all_score=$fuser->all_score;
                    $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->count_all();
                    $lv=$fuser->lv;
                    if($lv==1){
                        if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                            $fuser->lv=2;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }elseif($lv==2){
                        if ($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }
                    //关系绑定之后 统计当事人的上线 有多少粉丝关注了 公众号 达到一定下发优惠券
                    $num = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->where('subscribe','=',1)->count_all();

                    if(($num-$fuser->used) >= $config['rw_num']){
                        //发优惠券
                        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                        $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                        $client = new YZTokenClient($access_token);
                        
                        $method = 'youzan.ump.coupon.take';
                        $params = [
                            'coupon_group_id'=>$config['rw_value'],
                            'weixin_openid'=>$fopenid,
                         ];
                        $results = $client->post($method, $this->methodVersion, $params, $files);
                        // echo $config['rw_value'];
                        // var_dump($results);
                        if($results['response']){
                            $text = '恭喜您，分享人数已达8人，30元优惠券已经精准砸向您的会员中心。欲查看请点击进入“并不贵－我的订单－－我的优惠劵”，在购买的时候可以自动使用哦~';
                            $fuser->used = $fuser->used+$config['rw_num'];
                            $fuser->save();
                        }else{
                            $text = $results['error_response']['code'].':'.$results['error_response']['msg'];
                        }
                        if($config['coupontpl']){
                            $this->sendtplcoupon($fopenid,$config,$text,$we);
                        }else{
                            $msg['touser'] = $fopenid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $text;
                            $we->sendCustomMessage($msg);
                        }
                    }
                }
            }
            $view = "weixin/ytb/commendsother";//别人直接是url进，不需要加密
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                $result['title'] = $user->nickname.'的推荐商品';
                $view = "weixin/ytb/commends";//自己进 跳入shareopenid 需要加密url
            }
            $this->template->title = $fuser->nickname.'推荐商品';
            $result['commends'] = ORM::factory('ytb_good')->where('bid','=',$bid)->where('status','=',1)->find_all();
            $result['openid'] = $user->openid;

            $this->template->content = View::factory($view)->bind('result', $result)->bind('config', $this->config)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    public function action_shareopenid($mopenid,$gid,$bid){//商品分享页面 自己可以打开 别人也可以打开
        $fopenid = base64_decode($mopenid);
        $config=ORM::factory('ytb_cfg')->getCfg($bid,1);
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/ytb/shareopenid/'.$mopenid.'/'.$gid.'/'.$bid;
        $this->we = $we = new Wechat($config);

        $callback_urlsdk = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_urlsdk = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_urlsdk);

        if($_GET['code']){//静默授权当事人
            $this->template = 'weixin/ytb/tpl/tpl';
            self::before();
            $token = $we->getOauthAccessToken();
            $openid=$token['openid'];
            if(!$openid){//当别人打开网页时候 code 失效 openid无法获得 重新授权获得当事人openid和上级fopenid进行绑定
                $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
                header("Location:$auth_url");exit;
            }
            $user=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $user->bid=$bid;
            $user->openid=$openid;
            $user->lv = 1;
            $user->save();

            $_SESSION['ytb']['config'] = $config;
            $_SESSION['ytb']['openid'] = $openid;
            $_SESSION['ytb']['bid'] = $bid;
            $_SESSION['ytb']['uid'] = $user->id;

            $this->config = $_SESSION['ytb']['config'];
            $this->openid = $_SESSION['ytb']['openid'];
            $this->bid = $_SESSION['ytb']['bid'];
            $this->uid = $_SESSION['ytb']['uid'];
            $user = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            $fuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fopenid)->find();
            if($user->fopenid){
                // echo '有上线';
                $status = 1;
            }else{
                $status = 1;
                if($fopenid != $openid&&$fuser->id < $user->id){

                    require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                    $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                    $client = new YZTokenClient($access_token);
                    
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                        'weixin_openid'=>$openid,
                     ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);

                    if($results['response']['user']['sex']=='m'){
                        $sex=1;//男
                    }else if($results['response']['user']['sex']=='f'){
                        $sex=2;//女
                    }else{
                        $sex=0;//人妖
                    }
                    $user->nickname = $results['response']['user']['nick'];
                    if($user->subscribe!=1){//一旦关注为1 就不允许撤销
                        $user->subscribe = $results['response']['user']['is_follow'];
                    }
                    $user->sex = $sex;
                    $user->headimgurl = $results['response']['user']['avatar'];
                    $user->subscribe_time = strtotime($results['response']['user']['follow_time']);
                    $user->bid = $bid;
                    $user->openid = $openid;
                    $user->fopenid = $fopenid;
                    $user->save();

                    //关系绑定之后 发送消息通知
                    $text = $fuser->nickname.'，恭喜你增加了一个新的支持者!';
                    if($config['coupontpl']){
                        $this->sendtplcoupon($fopenid,$config,$text,$we);
                    }else{
                        $msg['touser'] = $fopenid;
                        $msg['msgtype'] = 'text';
                        $msg['text']['content'] = $text;
                        $we->sendCustomMessage($msg);
                    }
                    if($fuser->fopenid){//上上线存在
                        $ffuser = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser->fopenid)->find();
                        $text = $ffuser->nickname.',恭喜您的好友'.$fuser->nickname.'增加了一个新的支持者!';
                        if($config['coupontpl']){
                            $this->sendtplcoupon($ffuser->fopenid,$config,$text,$we);
                        }else{
                            $msg['touser'] = $ffuser->openid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $text;
                            $we->sendCustomMessage($msg);
                        }
                    }
                    //判断是否升级，发送升级文案
                    $all_score=$fuser->all_score;
                    $last_num=ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->count_all();
                    $lv=$fuser->lv;
                    if($lv==1){
                        if($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }elseif ($all_score>=$config['pool_score1']&&$last_num>=$config['pool_num1']){
                            $fuser->lv=2;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['second'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_er1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_er2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }elseif($lv==2){
                        if ($all_score>=$config['pool_score2']&&$last_num>=$config['pool_num2']){
                            $fuser->lv=3;
                            $fuser->save();
                            $msg3 = str_replace("「%a」",$config['first'],$config['lv_shao']);
                            $msg4 = str_replace("「%b」",$config['score_da1'],$msg3);
                            $msg5 = str_replace("「%c」",$config['score_da2'],$msg4);
                            $keyword=$msg5;
                            $result=$this->sendtext($fuser->openid,$keyword);
                        }
                    }
                    //关系绑定之后 统计当事人的上线 有多少粉丝关注了 公众号 达到一定下发优惠券
                    $num = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('fopenid','=',$fopenid)->where('subscribe','=',1)->count_all();
                    if(($num-$fuser->used) >= $config['rw_num']){
                        //发优惠券
                        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
                        $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
                        $client = new YZTokenClient($access_token);
                        
                        $method = 'youzan.ump.coupon.take';
                        $params = [
                            'coupon_group_id'=>$config['rw_value'],
                            'weixin_openid'=>$fopenid,
                         ];
                        $results = $client->post($method, $this->methodVersion, $params, $files);
                        if($results['response']){
                            $text = '恭喜您，分享人数已达8人，30元优惠券已经精准砸向您的会员中心。欲查看请点击进入“并不贵－我的订单－－我的优惠劵”，在购买的时候可以自动使用哦~';
                            $fuser->used = $fuser->used+$config['rw_num'];
                            $fuser->save();
                        }else{
                            $text = $results['error_response']['code'].':'.$results['error_response']['msg'];
                        }
                        if($config['coupontpl']){
                            $this->sendtplcoupon($fopenid,$config,$text,$we);
                        }else{
                            $msg['touser'] = $fopenid;
                            $msg['msgtype'] = 'text';
                            $msg['text']['content'] = $text;
                            $we->sendCustomMessage($msg);
                        }
                    }
                }
            }
            if($fopenid == $openid){
                // echo '自己';
                $status = 2;
                // $result['content'] = '快将该商品推荐给你的好友吧';
            }
            $commend = ORM::factory('ytb_good')->where('bid','=',$bid)->where('id','=',$gid)->find();
            $view = "weixin/ytb/share";
            $this->template->title = $fuser->nickname.':'.$commend->name;
            $this->template->content = View::factory($view)->bind('status', $status)->bind('commend', $commend)->bind('result', $result)->bind('signPackage', $signPackage)->bind('nickname', $user->nickname);
        }else{//得到code为止
            $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }
    }
    //兑换商城
    public function action_items() {
        $view = "weixin/ytb/items";
        // die('系统维护中...');

        if (!$this->uid) {
            $msg = '活动参与人数已经达到今日上限。<br />请明天继续参与。';
            return $this->action_msg($msg, 'noti');
            exit;
        }

        $userobj = ORM::factory('ytb_qrcode', $this->uid);
        //总积分
        $userobj->score = $result['score'] = $userobj->scores->select(array('SUM("score")', 'total_score'))->find()->total_score;
        if ($userobj->id) $userobj->save();
        $items = ORM::factory('ytb_item')->where('bid', '=', $this->bid)->where('show', '=', 1)->order_by('pri', 'DESC')->find_all();
        $this->template->title = $this->config['score_name'].'兑换中心';
        $this->template->content = View::factory($view)->bind('userobj', $userobj)->bind('result', $result)->bind('items',$items);
    }
    //商品详情
    public function action_item($iid){
        $this->template = 'tpl/blank';
        self::before();
        $view="weixin/ytb/xiangqing";
        $item = ORM::factory('ytb_item')->where('bid', '=', $this->bid)->where('id', '=', $iid)->find();
        $day_limit = ORM::factory('ytb_cfg')->where('bid','=',$this->bid)->where('key','=','day_limit')->find()->value;
        $qid = ORM::factory('ytb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->id;
        //$day = strtotime(date("Y-m-d"));
        $times = ORM::factory('ytb_score')->where('bid','=',$this->bid)->where('type','=',4)->where('qid','=',$qid)->count_all();
        if($times>=$day_limit&&$day_limit!=0){
            $dlimit = 1;
        }else{
            $dlimit = 2;
        }
        $user2 = ORM::factory('ytb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find()->as_array();
        // $this->template->title = '兑换中心';
        $this->template->content = View::factory($view)->bind('item', $item)->bind('dlimit',$dlimit)->bind('user2',$user2);
    }
    public function action_ticket($cardId) {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/ytb/ticket";
        $wx['appid'] = $this->config['appid'];
        $wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $we = new Wechat($wx);

        $jsapi = $we->getJsSign($callback_url);
        $ticket = $we->getJsCardTicket();
        $sign = $we->getTicketSignature(array($jsapi["timestamp"], $ticket, $cardId));

        $this->template->content = View::factory($view)
                ->bind('cardId', $cardId)
                ->bind('jsapi', $jsapi)
                ->bind('ticket', $ticket)
                ->bind('sign', $sign);
    }
    //兑换表单
    public function action_acount($openid,$bid){
        //echo $openid."<br>";
        $this->openid=$openid;
        $this->bid=$bid;
        $user = ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->as_array();
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$openid'")->execute()->as_array();
         $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
         $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；

          for($i=0;$firstchild[$i];$i++)
          {
            $tempid[$i]=$firstchild[$i]['openid'];
          }

        $customer=ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $openid)->order_by('all_score', 'DESC');
        $user['follows'] =$customer->count_all();
        $month = strtotime(date('Y-m-1'));
        $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$openid' and jointime>='$month'")->execute()->as_array();

        $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
        for($i=0;$firstchild[$i];$i++)
        {
            $tempid[$i]=$firstchild[$i]['openid'];
        }

        $user['follows_month']=ORM::factory('ytb_qrcode')->where('bid', '=', $bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid','IN',$tempiid)->or_where('fopenid', '=', $openid)->where('jointime','>=',$month)->count_all();
        //待结算
        $score_sum1=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','=',$user['id'])->select(array('SUM("score1")', 'score1s'))->find()->score1s;

        $firstchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $bid and fopenid='$openid'")->execute()->as_array();

        $tempid=array();
        $tempids=('!!!');
        if($firstchild[0]['openid']==null){
        $tempid=array('0' =>'!!!');
        $tempids=('!!!');//没有二级时 匹配一个不存在的；
        }else{
            for($i=0;$firstchild[$i];$i++){
              $tempid[$i]=$firstchild[$i]['id'];
              //$tempids[$i]=$firstchild[$i]['openid'];
              //$tempids=$tempids."'".$firstchild[$i]['openid']."',";
              $tempids=$tempids."','".$firstchild[$i]['openid'];
            }
        //$the_uname ="uname in(".$uname."'')";
        }
        //$the_uname ="fopenid in(".$tempids."'')";
        $the_uname ="fopenid in('$tempids')";

        $score_sum2=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid)->select(array('SUM("score2")', 'score2s'))->find()->score2s;

        $lastchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $bid and ".$the_uname." ")->execute()->as_array();

        $tempid1=array();
        if($lastchild[0]['openid']==null){
        $tempid1=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        }else{
        for($i=0;$lastchild[$i];$i++){
          $tempid1[$i]=$lastchild[$i]['id'];
        }
        }

        $score_sum3=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid1)->select(array('SUM("score3")', 'score3s'))->find()->score3s;

        $score_sum=number_format($score_sum1+$score_sum2+$score_sum3,2);
        //echo $score_sum."<br>";
        return $score_sum;
    }
    public function action_neworder($iid) {
        $view = "weixin/ytb/neworder";
        $config = $this->config;
        $bid = $this->bid;
        $this->access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }

        $item = ORM::factory('ytb_item', $iid);
        if (!$item->id || $item->bid != $this->bid) Request::instance()->redirect('/ytb/items');

        $this->template->content = View::factory($view)->bind('item', $item);

        //判断是否满足兑换条件
        //00.到期没？
        if ($item->endtime && strtotime($item->endtime) < time()) die('该奖品已截止兑换！');
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //0.有库存没？
        if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");

        //1.积分够不
        $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
        if ($item->score > $userobj->score) die("该奖品需要 {$item->score} {$this->scorename}，您只有 {$userobj->score} {$this->scorename}。");

        //2.是否限购
        if ($item->limit > 0) {
            $limit = ORM::factory('ytb_order')->where('qid', '=', $userobj->id)->where('iid', '=', $iid)->count_all();
            if ($limit >= $item->limit) die("您已经兑换了 {$limit} 件，超过了最大兑换数量");
        }

        $this->template->title = $item->name;
        if($_POST['data'] && Security::check($_POST['csrf']) !== true) die('不合法');

        //实物填地址 || 虚拟产品
        if ( ($_POST['data']['name'] && $_POST['data']['address'] && $_POST['data']['tel']) || ($_POST['url'] && $item->url&&$_POST['data']['type']!=5&&$_POST['data']['type']!=3) &&Security::check($_POST['csrf'])==1) {
            $order = ORM::factory('ytb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;

            //虚拟产品
            if ($item->url) {

                $order->status = 1;

                $url = substr($item->url , 0 , 4);
                if ($url == 'http'){
                    $order->url = $item->url;
                } else {
                    // $order->url = '/ytb/ticket/'.$item->url;
                }

            } else {
                //省份 城市
                $order->city = $_POST['s_province'].' '.$_POST['s_city'].' '.$_POST['s_dist'];
            }

            //成功
            if ($order->save()) {
                //减库存
                $item->stock--;
                $item->save();

                //扣积分
                $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                Kohana::$log->add("openid", print_r($userobj->openid, true));
                if($config['switch']==1){
                    $this->rsync($bid,$userobj->openid,$this->access_token,$config['switch'],$order->score);
                }
                $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/ytb/shorders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //有赞优惠券
        if ($_POST['data']['type']==3 ) {
            $order = ORM::factory('ytb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;
            //成功
            //发优惠券
            require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
            $access_token=ORM::factory('ytb_login')->where('id', '=', $bid)->find()->access_token;
            $client = new YZTokenClient($access_token);
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');
            $wx['appid'] = $this->config['appid'];
            $wx['appsecret'] = $this->config['appsecret'];
            $we = new Wechat($wx);
            
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$item->url,
                'weixin_openid'=>$userobj->openid,
             ];
            $results = $client->post($method, $this->methodVersion, $params, $files);

            if($results['response']){
                $text = '恭喜您，有赞优惠券已经下发到您的有赞账户，请到会员中心查收，在购买的时候可以使用哦~';
                $save = 1;
            }else{
                $text = $results['error_response']['code'].':'.$results['error_response']['msg'];
            }
            if($config['coupontpl']){
                $this->sendtplcoupon($userobj->openid,$config,$text,$we);
            }else{
                $msg['touser'] = $userobj->openid;
                $msg['msgtype'] = 'text';
                $msg['text']['content'] = $text;
                $we->sendCustomMessage($msg);
            }
            if($save == 1 ){
                //减库存
                $item->stock--;
                $item->save();
                $order->save();
                //扣积分
                $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                $userobj->scores->scoreOut($userobj, 4, $order->score);

                $goal_url = '/ytb/shorders';
                if ($order->url) $goal_url = $order->url;

                //成功后跳转
                Request::instance()->redirect($goal_url);
            }
        }

        //微信红包
        if ($_POST['data']['type']==4&&Security::check($_POST['csrf'])==1) {

            $order = ORM::factory('ytb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id;
            $order->score = $item->score;
            $order->status = 1;

            if($this->config['hb_check']==1){
               $order->status = 0;
               $order->save();
                //减库存
               $item->stock--;
               $item->save();
               $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

               $userobj->scores->scoreOut($userobj, 4, $order->score);
               $goal_url = '/ytb/orders';
                require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                $we = new Wechat($config);

                $msg['msgtype'] = 'text';
                $msg['touser'] = $userobj->openid;
                $msg['text']['content'] = '您的红包需要审核，审核通过后会自动下发，请耐心等待';
                $we->sendCustomMessage($msg);
                Request::instance()->redirect($goal_url);
                exit;
            }
                //发红包
                $tempname=ORM::factory("ytb_login")->where("id","=",$this->bid)->find()->user;
                $tempmoney=ORM::factory("ytb_item")->where("id","=",$iid)->find()->price;
                $tempmoney=$tempmoney*100;

                //读取 用户 请求红包
                $mem = Cache::instance('memcache');
                $cache = $mem->get($this->openid.Request::$client_ip);
                if($cache) die('请勿重复刷红包');

                $hbresult = $this->hongbao($this->config, $this->openid, '', $tempname, $tempmoney);
                if($hbresult['result_code']=='SUCCESS')
                {
                    //成功
                   $order->save();

                    //减库存
                   $item->stock--;
                   $item->save();
                    //扣积分
                   $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                    if($config['switch']==1){
                        $this->rsync($bid,$userobj->openid,$this->access_token,$config['switch'],$order->score);
                    }
                   $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                   $userobj->scores->scoreOut($userobj, 4, $order->score);
                   $goal_url = '/ytb/shorders';
                   if ($order->url) $goal_url = $order->url;

                    //成功后跳转
                    Request::instance()->redirect($goal_url);

                }else{
                    echo $hbresult['return_msg'];
                    exit();
                }

        }

        //赠品
        if ($_POST['data']['type']==5){
            $order = ORM::factory('ytb_order');
            $order->values($_POST['data']);

            $order->bid = $this->bid;
            $order->iid = $iid;
            $order->qid = $userobj->id; //? $userobj
            $order->score = $item->score; //? $item


            //gift
            //$wx['appid'] = ORM::factory('ytb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appid')->find()->value;
            //$wx['appsecret'] = ORM::factory('ytb_cfg')->where('bid', '=', $this->bid)->where('key','=','yz_appsecert')->find()->value;
            $oid = ORM::factory('ytb_item')->where('bid','=',$this->bid)->where('id','=',$iid)->find()->url; //? iid
            $client = new YZTokenClient($this->access_token);

            // echo '赠品列表:<br><br><br>';
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            //Kohana::$log->add('weixin:giftresult:$this->bid', print_r($results, true));//写入日志，可以删除
            for($i=0;$results['response']['presents'][$i];$i++){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$this->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'youzan.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $result1s = $client->post($method, $this->methodVersion, $params, $files);
                    Kohana::$log->add('weixin:oid', print_r($oid, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:fans_id', print_r($user_id, true));//写入日志，可以删除
                    Kohana::$log->add('weixin:gift', print_r($result1s, true));//写入日志，可以删除
                    if($result1s['response']['is_success']==true){
                        $order->status = 1;
                        $order->save();

                        //减库存
                       $item->stock--;
                       $item->save();
                        //扣积分
                       $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
                        if($config['switch']==1){
                            $this->rsync($bid,$userobj->openid,$this->access_token,$config['switch'],$order->score);
                        }
                       $userobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();

                       $userobj->scores->scoreOut($userobj, 4, $order->score);
                       //echo "<script>alert('领取成功，请回到公众号主页查看！')</script>";
                        Request::instance()->redirect($result1s["response"]["receive_address"]);
                       //echo $results["response"]["receive_address"];
                       // exit;
                    }else{
                        echo "您已经兑换过该赠品，每个人只能领一次哦～";
                        exit;
                    }

                }
            }

        }

        //自动填写旧地址
        $old_order = ORM::factory('ytb_order')->where('qid', '=', $userobj->id)->order_by('id', 'DESC')->find();
        if ($old_order) $_POST['data'] = $old_order->as_array();
    }
    //积分排行榜
    public function action_top() {
        $mem = Cache::instance('memcache');
        $view = "weixin/ytb/top";
        $top = $this->config['rank'] ? $this->config['rank'] : 50;

        $this->template->title = '积分排名';
        $this->template->content = View::factory($view)->bind('users', $users)->bind('user', $user)->bind('result', $result);

        //计算排名
        $user = ORM::factory('ytb_qrcode', $this->uid)->as_array();

        $rankkey = "ytb:rank3:{$this->bid}:{$this->openid}:$top";
        $result['rank'] = $mem->get($rankkey);
        if (!$result['rank']) {
            $result['rank'] = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('score', '>', $user['score'])->count_all()+1;
            $mem->set($rankkey, $result['rank'], 600);
        }

        $topkey = "ytb:top3:{$this->bid}:$top";
        $users = $mem->get($topkey);
        if (!$users) {
            $usersobj = ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->order_by('score', 'DESC')->limit($top)->find_all();
            foreach ($usersobj as $userobj) {
                $users[] = $userobj->as_array();
            }
            $mem->set($topkey, $users, 600);
        }
    }
    public function action_score($type=0) {
        $view = "weixin/ytb/scores";
        $userobj = ORM::factory('ytb_qrcode', $this->uid);

        $this->template->title = $this->config['score_name'].'明细';
        $this->template->content = View::factory($view)->bind('scores', $scores);

        $scores = $userobj->scores;

        $scores = $scores->order_by('lastupdate', 'DESC')->limit(500)->find_all();
    }
    public function action_shorders($type=0) {
        $view = "weixin/ytb/shorders";
        $userobj = ORM::factory('ytb_qrcode', $this->uid);
        $this->template->title = '我的'.$this->config['score_name'];
        $this->template->content = View::factory($view)->bind('orders', $orders)->bind('userobj', $userobj);
        $orders = ORM::factory('ytb_order')->where('bid', '=', $this->bid)->where('qid', '=', $this->uid)->order_by('id', 'DESC')->find_all();
    }
    //订单明细
    public function action_orders() {
        $view = "weixin/ytb/orders";
        $userobj = ORM::factory('ytb_qrcode', $this->uid);

        $this->template->title = '推广订单';
        $this->template->content = View::factory($view)->bind('trades', $trades);

        //只显示直接和间接推广订单，自购不显示
        $trades = $userobj->scores->where('type', 'IN', array(2,3));
        $trades = $trades->order_by('id', 'DESC')->find_all();
    }

    public function action_order($tid) {
        $view = "weixin/ytb/order";

        $order = ORM::factory('ytb_trade', $tid);
        if (!$order->id) die('无效订单');

        $this->template->title = '查看订单';
        $this->template->content = View::factory($view)->bind('order', $order);
    }
    public function action_wait_score(){
        $view = "weixin/ytb/wait_score";
        $openid=$this->openid;
        $user=ORM::factory('ytb_qrcode')->where('bid','=',$this->bid)->where('openid','=',$openid)->find();
        $order1 = ORM::factory('ytb_trade')->where('bid','=',$this->bid)->where('qid','=',$user->id)->where('status','!=','TRADE_BUYER_SIGNED')->where('score1','!=',0)->find_all();
        $firstchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $user->bid and `fopenid`='$user->openid' ")->execute()->as_array();
        $tempids=('!!!');
        $tempid=array();
        if($firstchild[0]['openid']==null){
            $tempid=array('0' =>'!!!');
            $tempids=('!!!');//没有二级时 匹配一个不存在的；
        }else{
            for($i=0;$firstchild[$i];$i++){
              $tempid[$i]=$firstchild[$i]['id'];
              //$tempids[$i]=$firstchild[$i]['openid'];
              $tempids=$tempids."','".$firstchild[$i]['openid'];
            }
        }
        $the_uname ="fopenid in('$tempids')";
        $order2=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('bid','=',$this->bid)->where('qid','in',$tempid)->where('score2','!=',0)->find_all();

        $lastchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $user->bid and ".$the_uname." ")->execute()->as_array();
                  $tempid1=array();
        if($lastchild[0]['openid']==null){
            $tempid1=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
        }else{
            for($i=0;$lastchild[$i];$i++){
              $tempid1[$i]=$lastchild[$i]['id'];
            }
        }
        $order3=ORM::factory('ytb_trade')->where('bid','=',$this->bid)->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid1)->where('score3','!=',0)->find_all();
        $this->template->title = '待结算';
        $this->template->content = View::factory($view)->bind('order1', $order1)->bind('order2', $order2)->bind('order3', $order3);
    }
    //查看自己客户(下线和二级 以及三级)
    public function action_customer($newadd='') {
        $view = 'weixin/ytb/customer';
        $this->template->title = '累计客户';
        $this->template->content = View::factory($view)
        ->bind('config',$this->config)
        ->bind('mycustomers',$totlecustomer)//绑定所有用户（1）级
        ->bind('result', $result)
        ->bind('totlenum',$totlenum)
        ->bind('page',$pages)
        ->bind('pagenum',$page)
        ->bind('newadd',$newadd);
        //$this->template->content = View::factory($view)->bind('result', $result);

        $user = ORM::factory('ytb_qrcode', $this->uid);

         if($newadd=='month')//查看本月新增
         {
            $month = strtotime(date('Y-m-1'));
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$user->openid' and jointime>='$month'")->execute()->as_array();
         }
         else
            $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$user->openid'")->execute()->as_array();
            $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
            $tempiid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；

               for($i=0;$firstchild[$i];$i++)
               {
                $tempid[$i]=$firstchild[$i]['openid'];
               }
           if($newadd=='month')
           {
            $customer=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', 'IN',$tempiid)->or_where('fopenid', '=', $user->openid)->where('jointime','>=',$month);
           }
           else
             $customer=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', 'IN',$tempiid)->or_where('fopenid', '=', $user->openid);


           $totlenum=$customer->count_all();

             //分页
            $page = max($_GET['page'], 1);
            $offset = (500 * ($page - 1));

            $pages = Pagination::factory(array(
                'total_items'   => $totlenum,
                'items_per_page'=>500,
            ))->render('weixin/ytb/admin/pages');


         if($newadd=='month')
           {
            $totlecustomer=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->or_where('fopenid', 'IN', $tempiid)->order_by('score', 'DESC')->limit(500)->offset($offset)->find_all();
           }
         else
           $totlecustomer=ORM::factory('ytb_qrcode')->where('bid', '=', $this->bid)->where('fopenid', 'IN',$tempid)->or_where('fopenid', '=', $user->openid)->or_where('fopenid', 'IN', $tempiid)->order_by('score', 'DESC')->limit(500)->offset($offset)->find_all();


    }

    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "ytb_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    private function sendtplcoupon($openid,$config,$text,$we) {
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $config['coupontpl'];

        $tplmsg['data']['keyword1']['value'] = '有赞优惠卷';
        $tplmsg['data']['keyword1']['color'] = '#999999';

        $tplmsg['data']['remark']['value'] = $text;
        $tplmsg['data']['remark']['color'] = '#999999';
        return $we->sendTemplateMessage($tplmsg);
    }
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }
    //提示页面
    public function action_msg($msg, $type='suc') {
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/ytb/msg";
        $this->template->content = View::factory($view)->bind('msg', $msg)->bind('type', $type);
    }
    public function sendtext($openid,$keyword){
        if($this->config['coupontpl']){
            $result=$this->sendTemplateMessage1($openid,$this->config['coupontpl'],'','',$keyword);
        }else{
            $result=$this->sendCustomMessage1($openid,$keyword);
        }
        return $result;
    }
    public function sendCustomMessage1($openid,$km_text){
        $msg['msgtype'] = 'text';
        $msg['touser'] = $openid;
        $msg['text']['content'] = $km_text;
        $result=$this->we->sendCustomMessage($msg);
        return $result;
    }
    public function sendTemplateMessage1($openid,$mgtpl,$keyword,$keyword1,$keyword2){
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $mgtpl;
        $tplmsg['url']=$url;
        $tplmsg['data']['first']['value']=urlencode($keyword);
        $tplmsg['data']['first']['color'] = '#FF0000';
        $tplmsg['data']['keyword1']['value'] = urlencode($keyword1);
        $tplmsg['data']['keyword1']['color'] = '#FF0000';
        $tplmsg['data']['remark']['value'] = urlencode($keyword2);
        $tplmsg['data']['remark']['color'] = '#FF0000';
        $result=$this->we->sendTemplateMessage1(urldecode(json_encode($tplmsg)));
        return $result;
    }
    private function hongbao($config, $openid, $we='', $bid=1, $money)
    {
        //记录 用户 请求红包
        $mem = Cache::instance('memcache');
        $cache = $mem->set($openid.Request::$client_ip, time(), 2);

        if (!$we) {
            require_once Kohana::find_file('vendor', 'weixin/wechat.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/inc');
            //require_once Kohana::find_file('vendor', "weixin/biz/$bid");//配置文件

            $we = new Wechat($config);
        }

        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $we->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['partnerid']; //支付商户号
        $data["wxappid"] = $config['appid'];//appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        // $data["min_value"] = $money; //最小金额
        // $data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        // $data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name']; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        // $data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $config['partnerkey']));//将签名转化为大写
        // var_dump($data);
        // echo $config['apikey'];
        $postXml = $we->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('weixin:hongbaopost', print_r($data, true));//写入日志，可以删除

        $resultXml = $this->curl_post_ssl($url, $postXml, 5, array(), $bid);//支付安全验证函数（核心函数）
        $response = simplexml_load_string($resultXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        //将xml格式数据转化为string

        $result['xml'] = $resultXml;
        $result['return_code'] = (string)$response->return_code;
        $result['return_msg'] = (string)$response->return_msg[0];
        $result['result_code'] = (string)$response->result_code[0];
        $result['re_openid'] = (string)$response->re_openid[0];
        $result['total_amount'] = (string)$response->total_amount[0];
        $result['err_code'] = (string)$response->err_code[0];

        return $result;//hash数组
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();

        $config = $this->config;
        $bid = $this->bid;

        $cert_file = DOCROOT."ytb/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."ytb/tmp/$bid/key.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('ytb_cfg')->where('bid', '=', $bid)->where('key', '=', 'ytb_file_cert')->find();
        $file_key = ORM::factory('ytb_cfg')->where('bid', '=', $bid)->where('key', '=', 'ytb_file_key')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

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
