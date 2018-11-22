<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtyyhb extends Controller_Base {
    public $template = 'weixin/sqb/tpl/blank';
    public $config;
    public $pagesize = 10;
    public $openid;
    public $bid;
    public $uid;
    public $scorename;
    public $access_token;
    public $methodVersion = '3.0.0';
    public function before() {
        Database::$default = "qwt";
        parent::before();
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action == 'notify') return;
        if (Request::instance()->action == 'yycheck') return;
        if (Request::instance()->action == 'die') return;
        // if (Request::instance()->action == 'shiwu') return;
        if (Request::instance()->action == 'audio') return;
        if (Request::instance()->action == 'savemedia') return;
        $_SESSION =& Session::instance()->as_array();
        if (!$_SESSION['qwtyyhb']['bid']) die('页面已过期。请重新点击相应菜单');
        $this->config = $_SESSION['qwtyyhb']['config'];
        $this->openid = $_SESSION['qwtyyhb']['openid'];
        $this->bid = $_SESSION['qwtyyhb']['bid'];
        $this->uid = $_SESSION['qwtyyhb']['uid'];
    }

    public function after() {
        $this->template->user = $user;
        parent::after();
    }
    public function action_shiwu($oid){
        // $this->template = 'tpl/blank';
        // self::before();
        // $oid = $_GET['oid'];
        $order = ORM::factory('qwt_yyhborder')->where('id','=',$oid)->find();
        $iid = $order->iid;
        $item1 = ORM::factory('qwt_yyhbitem')->where('id','=',$iid)->find();
        if (!$order) die('您的url有误');

        $bid=$order->bid;
        $qid=$order->qid;
        // $tid=$_GET['tid'];
        // $kid=$_GET['kid'];
        // $iid=$_GET['iid'];
        // $item1=ORM::factory('qwt_yyhbitem')->where('id','=',$iid)->find();
        // $order=ORM::factory('qwt_yyhborder')->where('bid','=',$bid)->where('qid','=',$qid)->where('tid','=',$tid)->where('kid','=',$kid)->where('iid','=',$iid)->find();
        $item['need_money']=($item1->need_money)/100;
        // if(!$order->id) die('您的url有误');
        if($order->id&&$_POST){
            $receive_name=$_POST['data']['name'];
            $tel=$_POST['data']['tel'];
            $address=$_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['data']['address'];
            $order->receive_name=$receive_name;
            $order->tel=$tel;
            $order->address=$address;
            $order->pay_money=$item['need_money'];
            $order->save();
        }
        if($order->tel&&$order->address&&($order->order_state==1||$order->pay_money==0)){
            $result['status']=1;
            $neirong='';
            if($order->status==0){
                $result['neirong']='请耐心等待管理员发货';
            }else{
                $result['neirong']='您的奖品已发货，请注意查收';
            }
        }else{
            $result['status']=0;
        }
        $item['pic']='http://'.$_SERVER['HTTP_HOST'].'/qwtyyhb/images/item/'.$item1->id.'v'.$item1->lastupdate.'.jpg';
        $item['km_content']=$item1->km_content;
        $item['id']=$item1->id;

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        $view = "weixin/smfyun/yyhb/gerenxinxi";
        $this->template->content = View::factory($view)
            ->bind('jsapi',$jsapi)
            ->bind('item',$item)
            ->bind('bid',$bid)
            ->bind('qid',$qid)
            ->bind('result', $result)
            ->bind('order', $order);
    }
    public function action_die(){

        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        $wx = new Wxoauth($bid,$options);
        $jsapi = $wx->getJsSign($callback_url);

        $this->template->content = View::factory("weixin/smfyun/yyhb/die")
            ->bind('jsapi',$jsapi);
    }
    public function action_yyhb(){
        $app = 'yyhb';
        $check = Model::factory('select_experience')->fenzai($bid,$app);
        if ($check == false) {
            $joinqr = ORM::factory('qwt_yyhbrecord')->where('bid','=',$this->bid)->count_all();
            if (!$joinqr<50) {
                Request::instance()->redirect('/qwtyyhb/die');
            }
        }
        if($_POST['oid']){
            $oid = $_POST['oid'];
            // $m = new Memcached();
            // $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
            $order = ORM::factory('qwt_yyhborder')->where('id','=',$oid)->find();
            $sku = ORM::factory('qwt_yyhbsku')->where('id','=',$order->kid)->find();
            // $stock = $sku->stock;
            // $keyname="qwfb_itemnum:{$bid}:{$item->id}";
            //   //将库存存入memcache
            // do {
            //     $item_num = $m->get($keyname, null, $cas);
            //     if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
            //         $m->add($keyname, $stock);
            //     } else {
            //         $m->cas($cas, $keyname, $stock);
            //     }
            // } while ($m->getResultCode() != Memcached::RES_SUCCESS);
            // //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
            // //通过memcache队列判断库存
            // do {
            //     $item_num = $m->get($keyname, null, $cas1);
            //     $item_num-=1;
            //     $m->cas($cas1, $keyname, $item_num);
            // } while ($m->getResultCode() != Memcached::RES_SUCCESS);

            // if($item_num<0){
            //     $item_num+=1;
            //     $order->log = '该奖品库存为 '.$item_num.'，暂时不能兑换，请稍后再试！';
            //     $order->save();
            //     $result['status'] = 0;
            //     $result['error'] = '该奖品库存为 '.$item_num.'，暂时不能兑换，请稍后再试！';
            //     // die("该奖品库存为 {$item_num}，暂时不能兑换，请稍后再试！");
            // }else{
            //     $order->receive_name = $_POST['receive_name'];
            //     $order->tel = $_POST['tel'];
            //     $order->address = $_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['address'];
            //     $order->save();

            //     $sku->stock = $sku->stock - 1;
            //     $sku->save();
            //     $result['status'] = 1;
            // }
            $order->receive_name = $_POST['receive_name'];
            $order->tel = $_POST['tel'];
            $order->address = $_POST['s_province'].$_POST['s_city'].$_POST['s_dist'].$_POST['address'];
            $order->save();
            $result['status'] = 1;
        }
        if($_POST['zan']){
            $record = ORM::factory('qwt_yyhbrecord')->where('id','=',$_POST['rid'])->find();
            $record->zan = $record->zan + 1;
            $record->save();
            $result['state'] = 1;
            echo json_encode($result);
            exit;
        }
        $order_by = 'jointime';
        if($_GET['order_by']){
            $order_by = $_GET['order_by'];
        }
        $user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('openid','=',$this->openid)->find();
        $time = time();
        $result['task'] = ORM::factory('qwt_yyhbtask')->where('bid','=',$this->bid)->where('begintime','<',$time)->where('endtime','>',$time)->where('flag','=',1)->find();

        $result['recodes'] = ORM::factory('qwt_yyhbrecord')->where('bid','=',$this->bid)->where('tid','=',$result['task']->id)->where('flag','=',1)->order_by($order_by,'desc')->limit($this->pagesize)->offset(0)->find_all();
        if($_POST['page']){
            $page = max($_POST['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $result['recodes'] = ORM::factory('qwt_yyhbrecord')->where('bid','=',$this->bid)->where('tid','=',$result['task']->id)->where('flag','=',1)->order_by($order_by,'desc')->limit($this->pagesize)->offset($offset)->find_all();
            $result['page'] = $_POST['page'] + 1;
            foreach ($result['recodes'] as $k => $v) {
                $arr[$k]['nickname'] = $v->user->nickname;
                $arr[$k]['headimgurl'] = $v->user->headimgurl;
                $arr[$k]['jointime'] = date('Y-m-d h:i',$v->jointime);
                $arr[$k]['audioTime'] = $v->audioTime;
                $arr[$k]['prize_name'] = $v->prize_name;
                $arr[$k]['zan'] = $v->zan;
                $arr[$k]['mp3'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwtyyhb/audio/record/'.$v->id;
                $arr[$k]['id'] = $v->id;
            }
            // var_dump($arr);
            echo json_encode($arr);
            exit;
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        //UV，PV
        if ($result['task']->id) {
            $uv = ORM::factory('qwt_yyhbuv');
            $uv->bid = $this->bid;
            $qid = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$_SESSION['qwtyyhb']['openid'])->where('bid','=',$this->bid)->find()->id;
            $uv->qid = $qid;
            $uv->tid = $result['task']->id;
            $uv->save();
        }
        //店铺头像昵称活动说明
        $weixin_name = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->weixin_name;
        $headimg = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->headimg;

        //语音红包QID
        $userqid = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$_SESSION['qwtyyhb']['openid'])->where('bid','=',$this->bid)->find()->id;

        //设了关注开关但没关注的情况
        if ($result['task']->state == 1) {
            $subqid = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$_SESSION['qwtyyhb']['openid'])->where('bid','=',$this->bid)->find()->qid;
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($this->bid))->where('bid','=',$this->bid)->where('id','=',$subqid)->find();
            if ($qr_user->subscribe == 0) {
                $result['notsub'] = 1;
                $suser = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$_SESSION['qwtyyhb']['openid'])->where('bid','=',$this->bid)->find();
                $suser->need_subscribe = 1;
                $suser->save();
            }
        }

        //中讲过的情况
        $prized = ORM::factory('qwt_yyhborder')->where('bid','=',$this->bid)->where('qid','=',$userqid)->where('tid','=',$result['task']->id)->find();
        //说对没中奖的情况
        $yihan = ORM::factory('qwt_yyhbrecord')->where('bid','=',$this->bid)->where('qid','=',$userqid)->where('tid','=',$result['task']->id)->where('flag','=',0)->find();

        //个人中奖纪录
        $prize = ORM::factory('qwt_yyhborder')->where('bid','=',$this->bid)->where('qid','=',$userqid)->find_all();


        $wx = new Wxoauth($this->bid,$options);
        $jsapi = $wx->getJsSign($callback_url);
        $view = 'weixin/smfyun/yyhb/yyhb';
        $this->template->content = View::factory($view)
            ->bind('yihan', $yihan)
            ->bind('prize', $prize)
            ->bind('prized', $prized)
            ->bind('weixin_name', $weixin_name)
            ->bind('headimg', $headimg)
            ->bind('jsapi', $jsapi)
            ->bind('result', $result)
            ->bind('user',$user)
            ->bind('config',$this->config);
    }
    public function action_ajax(){
        if($_POST['request_id']){
            $record = ORM::factory('qwt_yyhbrecord')->where('request_id','=',$_POST['request_id'])->find();
            if($record->text==''&&$record->audioTime==''){
                $result['status'] = 'no';
                echo json_encode($result);
                exit;
            }
            if($record->flag == 1){
                $order = ORM::factory('qwt_yyhborder')->where('bid','=',$this->bid)->where('rid','=',$record->id)->find();
                if($order->state==0){
                    $result['error'] = $order->log;
                }else{
                    $result['content'] = '恭喜您，中奖了：'.$order->item_name;
                    $result['oid'] = $order->id;
                    $result['type'] = $order->item->type;
                    $result['log'] = $order->log;
                }
            }else{
                // flag
                // 0 未中奖
                // 1 语音达标
                switch ($record->flag) {
                    case 0:
                $result['error'] = '很遗憾，没有中奖';
                $result['yihan'] = 1;
                        break;
                    case 3:
                $result['error'] = '您已中过奖，请勿重复参与';
                        break;
                    case 4:
                $result['error'] = '奖品无库存';
                        break;
                    case 5:
                $result['error'] = '语音不达标，再接再厉';
                        break;
                    case 6:
                $result['error'] = '活动不合法';
                        break;
                    case 7:
                $result['error'] = '与已有句子重复，再试试吧';
                        break;
                    case 8:
                $result['error'] = '来晚了一步';
                        break;
                    default:
                $result['error'] = '很遗憾，未中奖！';
                        break;
                }
                // 2 参与次数过多
                // 3 已经语音达标过了
                // 4 没库存
                // 5 语音不达标
                // 6 活动不合法
                // 7 有重复录音
                // 8 并发库存不足。
                // $record->flag
            }
            echo json_encode($result);
        }
        exit;
    }
    public function action_wodeshiwu(){
        $qid = ORM::factory('qwt_yyhbqrcode')->where('openid','=',$_SESSION['qwtyyhb']['openid'])->find()->id;
        $prize = ORM::factory('qwt_yyhborder')->where('qid','=',$qid)->find_all();
        // echo $qid;
        // var_dump($prize);
        // exit;
        $view = "weixin/smfyun/yyhb/wodeshiwu";
        $this->template->content = View::factory($view)
            ->bind('qid',$qid)
            ->bind('prize',$prize);
    }
    public function http_get($url){
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo;
    }
    public function action_savemedia($bid,$serverid){
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $wx = new Wxoauth($bid,$options);

        $token = $wx->get_accesstoken(); //微信请求素材的Token
        echo $token.'<br>';
        $mediaid = $serverid;            //语音素材的mediaid
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$mediaid;
        echo $url.'<br>';
        $content = $this->http_get($url);      //get请求
        echo '<pre>';
        var_dump($content);
        if ($content){
            // $prefix = explode("/", $content['headers']["Content-Type"]);
            $filename = DOCROOT.'qwt/yyhb/tmp/'.$mediaid.".amr";
            umask(0002);
            @mkdir(dirname($filename),0777,true);
            $fp = fopen($filename, 'w');
            $state=fwrite($fp, $content);  //写入数据
            fclose($fp);

            $amr = $filename;
            $mp3 = DOCROOT.'qwt/yyhb/tmp/'.$mediaid.".mp3";
            $record = ORM::factory('qwt_yyhbrecord')->where('bid','=',$bid)->where('media_id','=',$serverid)->find();
            if(file_exists($mp3) == true){
                // exit('无需转换');
            }else{
                $command = "/usr/local/bin/ffmpeg -i $amr $mp3";
                exec($command,$error);
                $record->mp3 = file_get_contents($mp3);
                $record->save();
            }
        }
        exit;
    }
    public function action_wxpay(){
        if($_POST['iid']){
            $item = ORM::factory('qwt_yyhbitem')->where('bid','=',$_POST['bid'])->where('id','=',$_POST['iid'])->find();
            $order=ORM::factory('qwt_yyhborder')->where('id','=',$_POST['oid'])->find();
            if($order->id){
                $receive_name = $_POST['name'];
                $tel = $_POST['tel'];
                $address = $_POST['address'];
                $order->receive_name = $receive_name;
                $order->tel = $tel;
                $order->address = $address;
                $order->pay_money = $item->need_money/100;
                $order->save();
            }
            if(!$item->id) {
                $result['error'] = '未找到奖品';
            }else{
                $config = ORM::factory('qwt_cfg')->getCfg($_POST['bid']);
                require_once Kohana::find_file('vendor/wx_pay', 'WeixinPay');
                $biz = ORM::factory('qwt_login')->where('id','=',$_POST['bid'])->find();
                $appid = $biz->appid;
                $qiz = ORM::factory('qwt_yyhbqrcode')->where('id','=',$_POST['qid'])->find();
                $openid = $qiz->openid;
                $mch_id = $config['mchid'];
                $key = $config['apikey'];
                $out_trade_no = $mch_id.time();
                $total_fee = floor($item->need_money);
                $body = $item->km_content.'费用';
                $attach = base64_encode('qwt_yyhborder:'.$_POST['oid'].':order_state');//表名 oid  字段状态
                $notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/smfyun/notify_url';
                $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee,$attach,$notify_url);
                $result=$weixinpay->pay();
            }
            echo json_encode($result);
        }
        exit;
    }
    public function action_yydown(){
        if($_POST['serverId']){
            Kohana::$log->add('yyhb_serverId', print_r($_POST['serverId'], true));
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;

            $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
            if ($_GET['url']) $callback_url = urldecode($_GET['url']);

            $wx = new Wxoauth($this->bid,$options);
            $media_id = $_POST['serverId'];
            $img = $wx->downMedia($media_id);

            $record = ORM::factory('qwt_yyhbrecord');

            $record->bid = $this->bid;
            $record->tid = $_POST['tid'];
            $record->qid = $this->uid;
            $record->media_id = $media_id;
            $record->voice = $img;
            $record->save();

            $requestId = $this->action_yycheck($record->id);
            $record = ORM::factory('qwt_yyhbrecord')->where('id','=',$record->id)->find();
            $record->request_id = $requestId;
            $record->save();

            $result['request_id'] = $requestId;
            echo json_encode($result);
            exit;
        }
        // var_dump($img);
        // TIi2bkszLE97gGpRVSzSaanqNbiRXKVVlZzZLAkJMGvP7ARSojHuviK80U1nQ9nm
    }
    public function action_notify(){
        $postStr = file_get_contents("php://input");
        Kohana::$log->add('yyhb_notify:post', print_r($postStr, true));
        // POST http://xx.yy.com/code=code&message=message&requestId=requestId&appid=appid&projectid=projectid&audioUrl=audioUrl&text=text&audioTime=audioTime
        parse_str($postStr,$arr);
        Kohana::$log->add('yyhb_notify:arr', print_r($arr['text'],true));


        $record = ORM::factory('qwt_yyhbrecord')->where('request_id','=',$arr['requestId'])->find();
        if($record->text && $record->mp3){
            $result['code'] = 0;
            $result['message'] = '成功';
            ob_clean();
            ob_flush();
            echo json_encode($result);
            exit;
        }
        $pos = strpos($arr['text'],']',0);
        $str = trim(substr($arr['text'], $pos+1));
        $record->text = $str;
        $record->audioTime = $arr['audioTime'];
        // $record->save();

        $task = ORM::factory('qwt_yyhbtask')->where('id','=',$record->tid)->find();
        if($task->flag!=1||time()>$task->endtime||time()<$task->begintime){
            $record->flag = 6;//活动不合法
        }
        Kohana::$log->add('yyhb_notify:$str', print_r($str,true));
        Kohana::$log->add('yyhb_notify:keyword', print_r($task->keyword,true));
        $pos  =  strpos ( $str ,  $task->keyword );
        // flag
        // 0 未中奖
        // 1 语音达标
        // 2 参与次数过多
        // 3 已经语音达标过了
        // 4 没库存
        // 5 语音不达标
        // 6 活动不合法
        // 7 有重复录音
        // 8 并发库存不足。
        if($pos  ===  false ) {
          $record->flag = 5;//语音不达标
         }else{
          $record->flag = 1;//语音达标
         }
        //统计当前活动当前用户参与次数
        $task_join = ORM::factory('qwt_yyhbrecord')->where('tid','=',$record->tid)->count_all();
        $task_win = ORM::factory('qwt_yyhbrecord')->where('tid','=',$record->tid)->where('qid','=',$record->qid)->where('flag','=',1)->count_all();

        // if($task->join_num<=$task_join){//参与次数过多
        //     $record->flag = 2;
        // }
        if(1<=$task_win){//已经语音达标过了
            $record->flag = 3;
        }
        if($str!=''){
            $has_record = ORM::factory('qwt_yyhbrecord')->where('tid','=',$record->tid)->where('text','=',$str)->find();
            if($has_record->id){//有重复录音
                $record->flag = 7;
            }
            Kohana::$log->add('yyhb_notify:has_record', print_r($has_record->id,true));
        }
        $rand = rand(1,100);
        $arr = array();
        if($rand<=$task->probability&&$record->flag==1){//中奖了
            $record->flag = 1;
            $skus = ORM::factory('qwt_yyhbsku')->where('bid','=',$record->bid)->where('tid','=',$task->id)->where('stock','>',0)->find_all();
            foreach ($skus as $k => $v) {
                $arr[$k] = $v->id;
            }
            if(!$arr[0]){
                $record->flag = 4;//完全没有库存了
            }else{
                Kohana::$log->add('yyhb_notify:arr', print_r($arr,true));
                $sku = ORM::factory('qwt_yyhbsku')->where('id','=',$arr[array_rand($arr,1)])->find();

                $m = new Memcached();
                $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
                $stock = $sku->stock;
                $keyname="qwtyyhb_itemnum:{$bid}:{$item->id}";
                  //将库存存入memcache
                do {
                    $item_num = $m->get($keyname, null, $cas);
                    if ($m->getResultCode() == Memcached::RES_NOTFOUND) {
                        $m->add($keyname, $stock);
                    } else {
                        $m->cas($cas, $keyname, $stock);
                    }
                } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                //if ($item->stock <= 0) die("该奖品库存为 {$item->stock}，暂时不能兑换！");
                //通过memcache队列判断库存
                do {
                    $item_num = $m->get($keyname, null, $cas1);
                    $item_num-=1;
                    $m->cas($cas1, $keyname, $item_num);
                } while ($m->getResultCode() != Memcached::RES_SUCCESS);

                if($item_num<0){
                    // $item_num+=1;
                    $record->flag = 8;
                    // die("该奖品库存为 {$item_num}，暂时不能兑换，请稍后再试！");
                }

                $prize = ORM::factory('qwt_yyhbitem')->where('id','=',$sku->iid)->find();
                $record->prize_name = $prize->km_content;
                Kohana::$log->add('yyhb_notify:skuid', print_r($sku->id,true));
                Kohana::$log->add('yyhb_notify:prizetype', print_r($prize->type,true));
            }
        }else{//未中奖
            if($record->flag==1){
                $record->flag = 0;
            }
        }

        //音频转mp3
        if ($record->voice){
            // $prefix = explode("/", $content['headers']["Content-Type"]);
            $filename = DOCROOT.'qwt/yyhb/tmp/'.$record->media_id.".amr";
            umask(0002);
            @mkdir(dirname($filename),0777,true);
            $fp = fopen($filename, 'w');
            $state = fwrite($fp, $record->voice);  //写入数据
            fclose($fp);

            $amr = $filename;
            $mp3 = DOCROOT.'qwt/yyhb/tmp/'.$record->media_id.".mp3";
            // $record = ORM::factory('qwt_yyhbrecord')->where('bid','=',$bid)->where('media_id','=',$media_id)->find();
            if(file_exists($mp3) == true){
                // exit('无需转换');
            }else{
                $command = "/usr/local/bin/ffmpeg -i $amr $mp3";
                exec($command,$error);
                $record->mp3 = file_get_contents($mp3);
                $record->save();
            }
            @unlink($filename);
            @unlink($mp3);
        }

        //实物填地址
        $this->config = $config = ORM::factory('qwt_cfg')->getCfg($record->bid);
        $this->bid = $bid = $record->bid;
        $user = ORM::factory('qwt_yyhbqrcode')->where('id','=',$record->qid)->find();

        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        $yzaccess_token=$admin->yzaccess_token;
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($admin->yzaccess_token){
            $client = new YZTokenClient($admin->yzaccess_token);
        }else{
            Kohana::$log->add("yyhb:$bid:bname", print_r('有赞参数未填', true));
        }

        if ($prize->type==1) {
            $order = ORM::factory('qwt_yyhborder');

            $order->bid = $record->bid;
            $order->rid = $record->id;
            $order->tid = $task->id;
            $order->qid = $record->qid;
            $order->kid = $sku->id;
            $order->iid = $sku->iid;
            $order->name = $user->nickname;
            $order->task_name = $task->name;
            $order->item_name = $prize->km_content;
            $order->pay_money = $prize->need_money;
            $order->state = 1;
            $order->save();

            //减库存
            $sku->stock--;
            $sku->save();
        }
        //微信红包
        if ($prize->type==4) {

            $order = ORM::factory('qwt_yyhborder');

            $order->bid = $record->bid;
            $order->rid = $record->id;
            $order->tid = $task->id;
            $order->qid = $record->qid;
            $order->kid = $sku->id;
            $order->iid = $sku->iid;
            $order->name = $user->nickname;
            $order->task_name = $task->name;
            $order->item_name = $prize->km_content;

            //发红包
            $tempmoney=$prize->value*100;
            $hbresult = $this->hongbao($this->config, $user->openid, '', $bid, $tempmoney);
            if($hbresult['result_code']=='SUCCESS')
            {
                //成功
               $order->status = 1;//是否处理
               $order->state = 1;//是否下发
                //减库存
               $sku->stock--;
               $sku->save();
            }else{
               $order->log =  $hbresult['return_msg'];
               $order->status = 1;//是否处理
               $order->state = 0;//是否下发
            }
            $order->save();
        }
        //赠品
        if ($prize->type==6){
            $order = ORM::factory('qwt_yyhborder');

            $order->bid = $record->bid;
            $order->rid = $record->id;
            $order->tid = $task->id;
            $order->qid = $record->qid;
            $order->kid = $sku->id;
            $order->iid = $sku->iid;
            $order->name = $user->nickname;
            $order->task_name = $task->name;
            $order->item_name = $prize->km_content;

            $oid = $prize->value; //? iid
            // $client = new YZTokenClient($this->access_token);
            //echo $oid.'<br>';
            // echo '赠品列表:<br><br><br>';
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [

            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            for($i=0;$results['response']['presents'][$i];$i++){
                $res = $results['response']['presents'][$i];
                $present_id=$res['present_id'];
                //echo 'present_id:'.$present_id.'<br>';
                // echo $this->openid."<br>";
                // echo $present_id."<br>";
                if($present_id==$oid){//找到指定赠品
                    //根据openid获取userid
                    $method = 'youzan.users.weixin.follower.get';
                    $params = [
                       'weixin_openid'=>$user->openid,
                       'fields'=>'user_id',
                    ];
                    $results = $client->post($method, $this->methodVersion, $params, $files);
                    $user_id = $results['response']['user']['user_id'];
                    var_dump($results);
                    //echo 'user_id:'.$user_id;
                    //根据openid发送奖品
                    $method = 'youzan.ump.present.give';
                    $params = [
                     'activity_id'=>$oid,
                     'fans_id'=>$user_id,
                    ];
                    $result1s = $client->post($method, $this->methodVersion, $params, $files);
                    // echo '<pre>';
                    var_dump($result1s);
                    // echo  '</pre>';
                    // exit();
                    Kohana::$log->add('yyhb:oid', print_r($oid, true));//写入日志，可以删除
                    Kohana::$log->add('yyhb:fans_id', print_r($user_id, true));//写入日志，可以删除
                    Kohana::$log->add('yyhb:gift', print_r($result1s, true));//写入日志，可以删除
                    if($result1s['response']['is_success']==true){
                        $order->status = 1;
                        $order->state = 1;
                        $order->log = $result1s["response"]["receive_address"];

                        //减库存
                        $sku->stock--;
                        $sku->save();

                    }else{
                        //var_dump()
                        $order->log = $result1s['error_response']['code'].$result1s['error_response']['msg'];
                        $order->status = 1;//是否处理
                        $order->state = 0;//是否下发
                        // echo $result1s['error_response']['code'].$result1s['error_response']['msg'];
                        //echo "您已经兑换过该赠品，每个人只能领一次哦～";
                        // exit;
                    }

                }
            }
            $order->save();
        }
        //优惠券
        if ($prize->type==5) {
            $order = ORM::factory('qwt_yyhborder');

            $order->bid = $record->bid;
            $order->rid = $record->id;
            $order->tid = $task->id;
            $order->qid = $record->qid;
            $order->kid = $sku->id;
            $order->iid = $sku->iid;
            $order->name = $user->nickname;
            $order->task_name = $task->name;
            $order->item_name = $prize->km_content;

            $oid = $prize->value; //? iid
            $method = 'youzan.ump.coupon.take';
            $params = [
                'coupon_group_id'=>$oid,
                'weixin_openid'=>$user->openid,
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            //成功
            if ($results['response']) {
                //减库存
                $order->status = 1;
                $order->state = 1;

                $sku->stock--;
                $sku->save();
                // require_once Kohana::find_file('vendor', 'weixin/wechat.class');
                // $wx = new Wechat($config);
                require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
                $options['token'] = $this->token;
                $options['encodingaeskey'] = $this->encodingAesKey;
                $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
                $wx = new Wxoauth($bid,$options);
                $msg['msgtype'] = 'text';
                $msg['touser'] = $user->openid;
                $msg['text']['content'] = '您的有赞优惠券优惠码已下发，请在会员中心查看！';
                $wx->sendCustomMessage($msg);

            }else{
                // echo $results['error_response']['code'].$results['error_response']['msg'];
                $order->log = $results['error_response']['code'].$results['error_response']['msg'];
                $order->status = 1;//是否处理
                $order->state = 0;//是否下发
            }
            $order->save();
        }
        //特权商品 打标签
        if ($prize->type==7){
            $order = ORM::factory('qwt_yyhborder');

            $order->bid = $record->bid;
            $order->rid = $record->id;
            $order->tid = $task->id;
            $order->qid = $record->qid;
            $order->kid = $sku->id;
            $order->iid = $sku->iid;
            $order->name = $user->nickname;
            $order->task_name = $task->name;
            $order->item_name = $prize->km_content;

            $hello = explode('&',$prize->value);

            $method = 'youzan.users.weixin.follower.tags.add';
            $params = [
                'tags'=> $hello[0],
                'weixin_openid'=>$user->openid,
            ];
            $aa = $client->post($method, $this->methodVersion, $params, $files);

            $order->status = 1;
            $order->state = 1;
            $order->log = $hello[1];
            $sku->stock--;
            $order->save();
            $sku->save();
        }
        $record->save();
        $result['code'] = 0;
        $result['message'] = '成功';
        ob_clean();
        ob_flush();
        echo json_encode($result);
        exit;
    }
    public function action_yycheck($id){

        $appid = '1256052569';
        $secretid = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
        $secretkey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';

        $req_url = 'aai.qcloud.com/asr/v1/'.$appid;

        $args = array(
            'channel_num' => 1,
            'secretid' => $secretid,
            'engine_model_type' => 1,
            'timestamp' => time(),
            'expired' => time() + 3600,
            'nonce' => rand(100000, 200000),
            'projectid' => 0,
            'callback_url' => "http://jfb.dev.smfyun.com/qwtyyhb/notify",
            'res_text_format' => 0,
            'res_type' => 1,
            'source_type' => 1,
            'sub_service_type' => 0,
            // 'url' => "http://aai.qcloud.com/test.mp3",
        );

        // 参数按照 Key 的字母序排序
        ksort($args);

        $arg_str = "";
        foreach($args as $k => $v) {
            $arg_str = $arg_str . "$k=$v&";
        }
        $arg_str = trim($arg_str, "&");

        // 拼接签名串
        $sig_str = "POST$req_url?$arg_str";
        // echo "sig_str: $sig_str\n";

        // 计算签名
        $signature = base64_encode(hash_hmac("sha1", $sig_str, $secretkey, TRUE));
        // echo "signature: $signature\n";

        $req_url = "https://$req_url?$arg_str";
        // echo "curl -sv -H 'Authorization:$signature' '$req_url' -d ''\n";

        $header = array(
            'Content-Type: application/octet-stream',
            'Authorization:'.$signature
        );
        // $body = [
        //     'img' => new CURLFile('imagepath', 'octet-stream', 'file_name')
        // ];
        // $body = [
        //     'img' => new CurlFile($_FILES['Filedata']['tmp_name'],'octet-stream',$RealTitleID);
        // ];
        // $body = file_get_contents($path);
        $body = ORM::factory('qwt_yyhbrecord')->where('id','=',$id)->find()->voice;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $req_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        if(!is_null($header)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        // 4. 调用API，获取响应结果
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_get = curl_exec($curl);
        // echo '<pre>';
        // var_dump($curl_get);
        return json_decode($curl_get,true)['requestId'];
        exit;
    }

    //音频
    public function action_audio($type='record', $id=1, $cksum='') {
        $table = "qwt_yyhb$type";

        $pic = ORM::factory($table, $id)->mp3;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: audio/mp3");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //产品图片
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_yyhb$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
     //积分兑换微信红包
    //判断个人用户所选奖品是否为微信红包，然后调用此函数
      private function hongbao($config, $openid, $wx='', $bid=1, $money)
    {
        if (!$wx) {
            require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');//配置文件
            require_once Kohana::find_file('vendor', 'weixin/qwt.inc');
            //require_once Kohana::find_file('vendor', "weixin/smfyun/biz/$bid");//配置文件

            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
            if(!$this->bid) Kohana::$log->add('qwtyyhbbid:', 'hongbao');//写入日志，可以删除
            $wx = new Wxoauth($this->bid,$options);
        }
        $config['name'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->name;
        $mch_billno = $config['mchid']. date('YmdHis').rand(1000, 9999); //订单号
        $data["nonce_str"] = $wx->generateNonceStr(32);//随机字符串
        $data["mch_billno"] = $mch_billno; //订单号
        $data["mch_id"] = $config['mchid']; //支付商户号
        $data["wxappid"] = $options['appid'];//三方appid
        $data["re_openid"] =$openid;//用户openid
        $data["total_amount"] = $money;//红包金额
        //$data["min_value"] = $money; //最小金额
        //$data["max_value"] = $money; //最大金额
        $data["total_num"] = 1; //总人数

        $data["act_name"] = "本次活动"; //活动名称
        //$data["nick_name"] = $config['name'].""; //提供方名称
        $data["send_name"] = $config['name'].""; //红包发送者名称
        $data["wishing"] = $config['name'].'恭喜发财！'; //红包祝福
        $data["remark"] = '告诉你的朋友一起来抢红包吧'; //备注信息
        //$data["share_content"] = '一起来'. $config['name'] .'抢红包吧'; //分享文案

        $data["client_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($wx->getSignature($data, 'trim')."&key=" . $config['apikey']));//将签名转化为大写

        $postXml = $wx->xml_encode($data);//将数据转化为xml数据,接口只能识别xml数据
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';//请求地址

        if ($bid === $this->debugbid) Kohana::$log->add('yyhb:hongbaopost', print_r($data, true));//写入日志，可以删除

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

        $cert_file = DOCROOT."qwt/tmp/$bid/cert.pem";
        $key_file = DOCROOT."qwt/tmp/$bid/key.pem";
        //$rootca_file=DOCROOT."yyhb/tmp/$bid/rootca.{$config['appsecret']}.pem";

        //证书分布式异步更新
        $file_cert = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_cert')->find();
        $file_key = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_file_key')->find();
        //$file_rootca = ORM::factory('qwt_yyhbcfg')->where('bid', '=', $bid)->where('key', '=', 'qwt_yyhbfile_rootca')->find();

        if (file_exists($cert_file) && $file_cert->lastupdate > filemtime($cert_file)) unlink($cert_file);
        if (file_exists($key_file) && $file_key->lastupdate > filemtime($key_file)) unlink($key_file);
        if (file_exists($rootca_file) && $file_rootca->lastupdate > filemtime($rootca_file)) unlink($rootca_file);

        if (!file_exists($cert_file)) {
            @mkdir(dirname($cert_file));
            @file_put_contents($cert_file, $file_cert->pic);
        }

        if (!file_exists($key_file)) {
            @mkdir(dirname($key_file));
            @file_put_contents($key_file, $file_key->pic);
        }

        // if (!file_exists(rootca_file)) {
        //     @mkdir(dirname($rootca_file));
        //     @file_put_contents($rootca_file, $file_rootca->pic);
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

        curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $rootca_file);// CA根证书（用来验证的网站证书是否是CA颁布）

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
