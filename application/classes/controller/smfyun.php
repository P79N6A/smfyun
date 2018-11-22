<?php defined('SYSPATH') or die('No direct script access.');

class Controller_smfyun extends Controller_Base {
    public $template = 'tpl/blank';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
    public $token = 'smfyun';
    public $access_token;
    public function before() {
        parent::before();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $_SESSION =& Session::instance()->as_array();
    }

    public function after() {
        parent::after();
    }
    public function action_stand(){
        $view = 'weixin/stand/index';
        if($_POST['enterprisea100']){
            $res[0]['a100'] = 'acc1';
            $res[1]['a100'] = 'acc2';
            $res[2]['a100'] = 'acc3';
            $res[3]['a100'] = 'acc4';
            echo json_encode($res);
            exit;
        }
        if($_POST['company_name']){
            $res[0]['enterpriseName'] = 'name1';
            $res[0]['location'] = 'address1';
            $res[1]['enterpriseName'] = 'name2';
            $res[1]['location'] = 'address2';
            $res[2]['enterpriseName'] = 'name3';
            $res[2]['location'] = 'address3';
            $res[3]['enterpriseName'] = 'name4';
            $res[3]['location'] = 'address4';
            echo json_encode($res);
            exit;
        }
        if($_POST['item_name_post']){
            $res[0]['item_name'] = 'name1';
            $res[1]['item_name'] = 'name2';
            $res[2]['item_name'] = 'name3';
            $res[3]['item_name'] = 'name4';
            echo json_encode($res);
            exit;
        }
        if($_POST['pic_item_name']){
            $res['pic1'] = 'http://jfb.dev.smfyun.com/qwt/wfb/news_follow.png';
            $res['pic2'] = 'http://jfb.dev.smfyun.com/qwt/wfb/news_order.jpg';
            echo json_encode($res);
            exit;
        }
        if($_POST){
            echo '<pre>';
            var_dump($_POST);
            exit;
        }
        $this->template->content = View::factory($view);
    }
    public function action_getalltpl($bid){
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;

        $wx = new Wxoauth($bid,$options);
        $res = $wx->getalltpl();
        echo '<pre>';
        var_dump($res);
    }
    public function action_editor(){
        $view = 'weixin/mnb';
        $mnb = ORM::factory('qwt_mnb')->where('id','=',1)->find();
        if($_POST['text']){
            $mnb->comment = $_POST['text'];
            $mnb->save();
        }
        $this->template->content = View::factory($view)
                                    ->bind('text',$mnb->comment);
    }
    // public function action_csv(){
    //     set_time_limit(0);
    //     $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
    //     header( 'Content-Type: text/csv' );
    //     header( 'Content-Disposition: attachment;filename='.$filename);
    //     $fp = fopen('php://output', 'w');
    //     $logins = ORM::factory('qwt_login')->find_all();
    //     $title = array('ID','商户名称', '商户微信公众号','微信头像','有赞店铺', '手机号');
    //     if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
    //     fputcsv($fp, $title);
    //     foreach ($logins as $k=>$v) {
    //         $array = array($k+1,$v->name,$v->weixin_name,$v->headimg,$v->shopname, $v->user);

    //         if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
    //             //非 Mac 转 gbk
    //             foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
    //         }

    //         fputcsv($fp, $array);
    //     }
    //     exit;
    // }
    public function arr2xml($data, $root = true){
      $str="";
      if($root)$str .= "<xml>";
      foreach($data as $key => $val){
        if(is_array($val)){
          $child = arr2xml($val, false);
          $str .= "<$key>$child</$key>";
        }else{
          $str.= "<$key><![CDATA[$val]]></$key>";
        }
      }
      if($root)$str .= "</xml>";
      return $str;
    }
    //api里面发送的地址判断 跳到这个url
    public function action_check_location($bid,$openid,$app){
        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/check_location";
        //$wx['appid'] = $this->config['appid'];
        //$wx['appsecret'] = $this->config['appsecret'];

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        //$we = new Wechat($wx);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $wx = new Wxoauth($bid,$options);
        $msg['touser'] = $openid;
        $msg['msgtype'] = 'text';
        $cfg = ORM::factory('qwt_cfg')->getCfg(0,1);
        if (isset($_GET['x'])){
            $locationx = $_GET['x'];
            $locationy = $_GET['y'];
            if($cfg['map_qq']==1){
                $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $locationx. ',' . $locationy . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
                $ch = curl_init(); // 初始化一个 cURL 对象
                curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
                curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置curl参数，要求结果保存到字符串中还是输出到屏幕上
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $res = curl_exec($ch); // 运行cURL，请求网页
                curl_close($ch); // 关闭一个curl会话
                $json_obj = json_decode($res, true);
                $pro = $json_obj['result']['address_component']['province'];//省份
                $city = $json_obj['result']['address_component']['city'];//城市
                $dis = $json_obj['result']['address_component']['district'];//区
                if($pro == $city){
                    $city = $dis;
                    $dis = '';
                }
            }else{
                $host = "http://jisujwddz.market.alicloudapi.com";
                $path = "/geoconvert/coord2addr";
                $method = "GET";
                $appcode = "5ee84130544445bf875bbb1d3a017a71";
                $headers = array();
                array_push($headers, "Authorization:APPCODE " . $appcode);
                $querys = "lat=".$locationx."&lng=".$locationy."&type=baidu";
                $bodys = "";
                $url = $host . $path . "?" . $querys;

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_FAILONERROR, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                if (1 == strpos("$".$host, "https://"))
                {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                }
                $res = curl_exec($curl);
                curl_close($curl); // 关闭一个curl会话
                $json_obj = json_decode($res, true);
                $pro = $json_obj['result']['province'];//省份
                $city = $json_obj['result']['city'];//城市
                $dis = $json_obj['result']['district'];//区
                if($pro == $city){
                    $city = $dis;
                    $dis = '';
                }
            }
            $content = $pro.$city.$dis;
            $result['area'] = $content;
            $area = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $area->area = $content;
            $area->save();
            if($app == 'rwb'){
                $rwbqrcode = ORM::factory('qwt_rwbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($rwbqrcode->joinarea==1){
                    require_once Kohana::find_file('vendor', 'area/rwb');
                    $rwbarea = new rwb($bid,$wx,$openid,$rwbqrcode);
                    $result['content'] = $txtReply = $rwbarea->end();
                }
            }
            if($app == 'wfb'){
                $wfbqrcode = ORM::factory('qwt_wfbqrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
                if($wfbqrcode->joinarea==1){
                    require_once Kohana::find_file('vendor', 'area/wfb');
                    $wfbarea = new wfb($bid,$wx,$openid,$wfbqrcode);
                    $result['content'] = $wfbarea->end();
                }
            }
            if ($result['content']) {
                $msg['text']['content'] = $result['content'];
                $wx->sendCustomMessage($msg);
            }
            echo json_encode($result);
            exit;
        }
        $count = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'count')->find()->value;
        for ($i=1; $i <=$count ; $i++) {
            $pro[$i] = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'pro'.$i)->find()->value;
            $city[$i] = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'city'.$i)->find()->value;
            $dis[$i] = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'dis'.$i)->find()->value;
            $p_location[$i]= $pro[$i].$city[$i].$dis[$i];
        }
        $info = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'info')->find()->value;//活动说明
        $reply = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'reply')->find()->value;//不在活动范围内的活动回复
        $isreply = ORM::factory('qwt_'.$app.'cfg')->where('bid', '=', $bid)->where('key', '=', 'isreply')->find()->value;//在活动范围内的活动回复
        $area = array("pro" =>$pro, "city"=>$city,"dis" =>$dis,"info"=>$info,"reply"=>$reply,"isreply"=>$isreply);
        $jsapi = $wx->getJsSign($callback_url);
        $this->template->content = View::factory($view)
                ->bind('bid', $bid)
                ->bind('openid', $openid)
                ->bind('app', $app)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area)
                ->bind('p_location', $p_location);
                //->bind('fuopenid', $fuopenid2);
    }
    public function action_notify_url(){//qwt 微信公众号支付订单
        $postStr = file_get_contents("php://input");
        Kohana::$log->add("qwt_notify_xml", print_r($postStr,true));
        $arr = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        Kohana::$log->add("qwt_notify_arr", print_r($arr,true));
        //先验证签名
        $appid = $arr['appid'];
        $biz = ORM::factory('qwt_login')->where('appid','=',$appid)->find();
        $apikey = ORM::factory('qwt_cfg')->where('bid','=',$biz->id)->where('key','=','apikey')->find()->value;
        $sign = $arr['sign'];
        unset($arr['sign']);
        $new_sign = $this->getSign($arr,'key',$apikey);
        Kohana::$log->add("qwt_notify_oauth:1111", print_r($new_sign,true));
        if($sign == $new_sign){
            //改变订单状态
            Kohana::$log->add("qwt_notify_oauth:2222", print_r($sign,true));
            $decode = base64_decode($arr['attach']);
            $table = explode(':',$decode)[0];
            $oid = explode(':',$decode)[1];
            $key = explode(':',$decode)[2];
            $app = explode(':',$decode)[3];
            $wait_order_id = explode(':',$decode)[4];//$wait_order->id
            $order = ORM::factory($table)->where('id','=',$oid)->find();
            if($app == 'wfb'){
                //针对积分宝服务号 积分兑换的奖品扣积分
                //扣积分
                $userobj = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$biz->id)->where('openid','=',$arr['openid'])->find();
                $sid = $userobj->scores->scoreOut($userobj, 4, $order->score);
                $config = ORM::factory('qwt_'.$app.'cfg')->getCfg($biz->id,1);
                if($config['switch']==1){
                    require_once Kohana::find_file('vendor', 'qwt/SmfyQwt');
                    $smfy = new SmfyQwt();
                    $result = $smfy->wfbrsync($biz->id,$userobj->openid,$biz->yzaccess_token,-$order->score,$sid,'兑换奖品1');
                }
            }
            //避免脚本使库存恢复
            if($wait_order_id>0){
                $wait_order = ORM::factory('qwt_waitorder')->where('id','=',$wait_order_id)->find();
                $wait_order->delete();
            }
            // Kohana::$log->add("qwt_notify_oauth:table", print_r($table,true));
            // Kohana::$log->add("qwt_notify_oauth:oid", print_r($oid,true));
            // Kohana::$log->add("qwt_notify_oauth:key", print_r($key,true));
            if($order->$key!=1){
                $order->$key = 1;
                $order->save();
            }
        }else{
            Kohana::$log->add("qwt_notify_fail:签名验证失败", print_r($new_sign,true));
        }
        //返回给微信
        $res['return_code'] = 'SUCCESS';
        $res['return_msg'] = 'OK';
        echo $this->arr2xml($res,true);
        exit;
    }

    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    //作用：生成签名
    private function getSign($Obj,$keyname,$key) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        // echo "按字典序排序参数：".$String.'<br>';
        //签名步骤二：在string后加入KEY
        $String = $String . "&".$keyname."=".$key;
        // echo "在string后加入KEY：".$String.'<br>';
        //签名步骤三：MD5加密
        $String = md5($String);
        // echo "MD5加密：".$String.'<br>';
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        // echo "所有字符转为大写：".$result_.'<br>';
        return $result_;
    }


    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    public function action_miniprogrampage(){
        $newfile =  DOCROOT."asus/bg.jpg";
        $bid = 6;
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $result = $wx->uploadForever(array('media'=>"@$newfile"), 'image');
        // var_dump($result);
        // exit;
        // wxbc550991f98c2c7b
        $msg['touser'] = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
        $msg['msgtype'] = 'miniprogrampage';
        $msg['miniprogrampage']['title'] = '小程序页面title';
        $msg['miniprogrampage']['appid'] = 'wxbc550991f98c2c7b';
        // $msg['miniprogrampage']['pagepath'] = 'pages/new/index';
        $msg['miniprogrampage']['thumb_media_id'] = '_pcz_YKmYqC1QGwr5nkaFyiqFgprIAIbRGunYUi41_4';
        // $wx_result = $wx->sendCustomMessage($msg);
        // var_dump($wx_result);
        echo '<pre>';
        $data['type'] = 'image';
        $data['offset'] = 0;
        $data['count'] = 20;
        $result = $wx->getallforever($data);
        var_dump($result);
        exit;
    }
    public function action_downcert($id){
        $cfg = ORM::factory('qwt_cfg')->where('id','=',$id)->find();
        header("Content-Type: application/octet-stream");
        header("Accept-Ranges: bytes");
        // header("Accept-Length: ".filesize('文件地址'));
        // header("Content-Disposition: attachment; filename=文件名称");
        echo $cfg->pic;
        exit;
    }
    public function action_aes(){
        $privateKey = "sjdksldkwospaisk";
        $iv = "wsldnsjwisqweskl";
        $data = "aaamlzkyxwxeocxd";

        //AES-CBC 加密方案
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
        $data = base64_encode($encrypted);
        echo $data;
        echo '<br/>';
        //AES 解密
        $encryptedData = base64_decode($data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        echo($decrypted);
    }
    public function action_wsd_trade(){
        $bid = 4;
        $s1 = 36176894;
        $s2 = 36176893;
        $trade_details = ORM::factory('wsd_tradedetail')->where('id','<',4170)->where('bid','=',$bid)->and_where_open()->where('skuid','=',$s1)->or_where('skuid','=',$s2)->and_where_close()->find_all();
        foreach ($trade_details as $k => $v) {
            $tid = $v->tid;
            $order = ORM::factory('wsd_trade')->where('bid','=',$bid)->where('tid','=',$tid)->find();
            $openid = $order->openid;
            $user = ORM::factory('wsd_qrcode')->where('bid','=',$bid)->where('openid','=',$openid)->find();
            if($user->group_id==0){
                $goodidcof=ORM::factory('wsd_goodsku')->where('sku_id','=',$v->skuid)->where('bid','=',$bid)->find();
            }else{
                $goodidcof=ORM::factory('wsd_smoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('sku_id','=',$v->skuid)->find();
            }
            $now = $v->total_fee-($v->num*$goodidcof->money);
            echo $v->tid.'<br>';
            echo '现在利润：'.$v->money.'<br>';
            echo '真实利润：'.$now.'<br>';
            $v->money = $now;
            $v->save();
            echo 'trade表<br>';
            echo $order->money1.'<br>'.$v->num.'<br>'.$goodidcof->money.'<br>';
            $order->money1 = $order->money1-($v->num*$goodidcof->money).'<br>';
            echo 'trade_money:'.$order->money1.'<br>';
            $order->save();
            echo 'score表<br>';
            $score = ORM::factory('wsd_score')->where('bid','=',$bid)->where('tid','=',$order->id)->find();
            $score->score = $score->score-($v->num*$goodidcof->money);
            echo 'score_money:'.$score->score.'<br>';
            $score->save();
        }
    }
    public function action_ticket($appid,$bid){
        //2443   wxcd0322935406b3b3
        $options['token'] = 'weifubao';
        $options['encodingaeskey'] = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
        $options['appid'] = $appid;//商户appid
        require_once Kohana::find_file('vendor', 'wx_oauth/wxoauth.class');
        $qrcode_type = 0;
        $ticket_lifetime = 3600*24*7;
        $wx = new Wxoauth($bid,'wfb','wxc520face24b8f175',$options);
        $res = $wx->getQRCode($bid, $qrcode_type, $ticket_lifetime);
        var_dump($res);
        exit;
    }
    public function make_password($length = 16){
        // 密码字符集，可任意添加你需要的字符
        $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $password = '';
        for($i = 0; $i < $length; $i++){
            // 将 $length 个数组元素连接成字符串
            $password .= $chars[rand(0,25)];
        }
        return $password;
    }
    public function action_qwt_wdb($bid){
        $config = ORM::factory('qwt_wdbcfg')->getCfg($bid,1);
        $openid = $_POST['openid'];
        if(!$openid||!$bid) die('no openid or no bid');
        $appid = ORM::factory('qwt_login')->where('id','=',$bid)->appid;
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $appid;//商户appid
        $wx = new Wxoauth($bid,$options);
        $msg['touser'] = $openid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        Kohana::$log->add("qwtwdb:$bid", '2222');
        $model_q = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        $ticket_lifetime = 3600*24*7;
        if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

        if ( ($result['ticket'] = $model_q->ticket) &&  (time()<explode('|',$model_q->ticket)[2]) ) {
            Kohana::$log->add("qwtwdb:$bid:2", $model_q->ticket);
            $msg['text']['content'] = $config['text_send'];

        } else {
            Kohana::$log->add("qwtwdb:$bid:3", $model_q->ticket);
            $time = time()+$ticket_lifetime;

            $result['ticket'] = $model_q->openid.'|'.$bid.'|'.$time;// ticket直接用openid和bid加密
            $model_q->lastupdate = time();

            $msg['text']['content'] = $config['text_send'];

            //生成海报并保存
            // $model_q->values($userinfo);
            // $model_q->bid = $bid;
            // $model_q->ticket = $result['ticket'];
            // $model_q->save();
            $fuopenid = ORM::factory('qwt_wdbqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->fuopenid;
            //Kohana::$log->add("openid:$bid:url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
            if(!$fuopenid){
                $cksum = md5($openid.$config['appsecret'].date('Y-m-d'));
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '点击验证生成海报';
                $msg['news']['articles'][0]['url'] = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtwdb/index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid);
                $msg['news']['articles'][0]['description'] = '特别注意：点击验证后才能生成海报';
                $msg['news']['articles'][0]['picurl'] = 'http://cdn.jfb.smfyun.com/qwt/wdb/subscribe.png';
                $wx_result = $wx->sendCustomMessage($msg);
                exit;
            }
        }

        $md5 = md5($result['ticket'].time().rand(1,100000));
        $model_q->ticket = $result['ticket'];
        $model_q->save();
        //图片合成
        //模板
        $imgtpl = DOCROOT."qwt/wdb/tmp/tpl.$bid.jpg";
        $tmpdir = '/dev/shm/';

        //判断模板文件是否需要从数据库更新
        $tpl = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
        if (!$tpl->pic) {
            $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
            $wx_result = $wx->sendCustomMessage($msg);
            exit;
        }

        if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
        //exit;
        if (!file_exists($imgtpl)) {
            @mkdir(dirname($imgtpl));
            @file_put_contents($imgtpl, $tpl->pic);
        }

        //默认头像
        $tplhead = ORM::factory('qwt_wdbcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
        $default_head_file = DOCROOT."qwt/wdb/tmp/head.$bid.jpg";

        if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
        if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);


            //获取二维码
            require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
            $qrurl =  'http://'.$_SERVER["HTTP_HOST"].'/qwtwdb/qrscan/'.$result['ticket'];
            $localfile = "{$tmpdir}$md5.jpg";
            QRcode::png($qrurl,$localfile,'L','6','2');
            $remote_qrcode = $localfile;
            //if (!$remote_qrcode) $remote_qrcode = QRcode::png($qrurl, false, 'L', '4');
            //if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);
            //获取头像

            $headfile = "{$tmpdir}$md5.head.jpg";

            $remote_head_url = str_replace('wx.qlogo.cn', '182.254.104.16', $model_q->headimgurl);
            $remote_head = curls($remote_head_url);

            if (!$remote_head) {
                $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
                $remote_head = curls($remote_head_url);
            }

            //retry... 96px
            if (!$remote_head) {
                $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                $remote_head = curls($remote_head_url);
            }

            //获取失败用默认头像
            if (!$remote_head && $default_head) $remote_head = file_get_contents($default_head_file);

            //写入临时头像文件
            if ($remote_head) file_put_contents($headfile, $remote_head);

            if (!$remote_head || !$remote_qrcode) {
                $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                $wx_result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("qwtwdb:$bid:file:remote_head_url get ERROR!", $remote_head_url);

                @unlink($headfile);
                @unlink($localfile);
                exit;
            }

            //合成
            $dest = imagecreatefromjpeg($imgtpl);
            $src_qrcode = imagecreatefrompng($localfile);
            $src_head = imagecreatefromjpeg($headfile);

            if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
            if($src_head) imagecopyresampled($dest, $src_head, $config['px_head_left'], $config['px_head_top'], 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

            $newfile = "{$tmpdir}$md5.new.jpg";
            imagejpeg($dest, $newfile);
            if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
            if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);


            if (file_exists($newfile)) {
                $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                if (!$uploadresult['media_id']) {
                    Kohana::$log->add("qwtwdb:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                    if ($wx->errCode == 45009) {
                        $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                        $wx_result = $wx->sendCustomMessage($msg);
                        exit;
                    }
                } else {
                    //上传成功 pass
                    if ($bid == $debug_bid) Kohana::$log->add("qwtwdb:$bid:$newfile upload OK!", print_r($uploadresult, true));
                }

            } else {
                Kohana::$log->add("qwtwdb:$bid:newfile $newfile gen ERROR!");
                Kohana::$log->add("qwtwdb:$bid:imgtplfile", file_exists($imgtpl));
                Kohana::$log->add("qwtwdb:$bid:qrcodefile", file_exists($localfile));
                Kohana::$log->add("qwtwdb:$bid:headfile", file_exists($headfile));
            }

            unlink($localfile);
            unlink($headfile);
            unlink($newfile);
        //海报发送前提醒消息


        $txtReply2 = '海报有效期到 '. date('Y-m-d H:i', explode('|',$model_q->ticket)[2]) .' 过期后请点击「生成海报」菜单重新获取哦！';

        $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;
        //exit;
        // if ($txtReply2) {
        //     Kohana::$log->add('$wdb', 'wwwww');
        //     $textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>";
        //     Kohana::$log->add('$wdb001', 'aaa');
        //     $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);
        //     $result2 = sprintf($textTpl, $fromUsername, $toUsername, $timeStamp, 'text',$txtReply2);
        //     Kohana::$log->add('$wdb00', print_r($result2, true));
        //     $encryptMsg = '';
        //     $errCode = $pc->encryptMsg($result2, $timeStamp, $nonce, $encryptMsg);
        //     if ($errCode == 0) {
        //         if ($bid == 1)Kohana::$log->add('$wdb', print_r($encryptMsg, true));
        //         Kohana::$log->add('$wdb11', print_r($encryptMsg, true));
        //     } else {
        //         Kohana::$log->add('$wdb22', print_r($errCode, true));
        //     }
        //     ob_flush()
        //     echo $encryptMsg;
        //     exit;
        // }
        //echo $wx->text($txtReply2)->reply(array(), true);
        //Kohana::$log->add('$wdb11', print_r($result, true));
        Kohana::$log->add("msg:$bid:qwtwdb", print_r($msg,true));
        $msg['msgtype'] = 'text';
        $wx_result = $wx->sendCustomMessage($msg);
        Kohana::$log->add("qwtmsg:$bid:txt", print_r($wx_result,true));
        $msg['msgtype'] = 'image';
        $msg['image']['media_id'] = $uploadresult['media_id'];
        unset($msg['text']);
        $wx_result = $wx->sendCustomMessage($msg);
        Kohana::$log->add("qwtmsg:$bid:tplimg", print_r($wx_result,true));

        exit;
    }
    public function action_qwt_rwd($bid){
        $config = ORM::factory('qwt_rwdcfg')->getCfg($bid,1);
        $openid = $_POST['openid'];
        if(!$openid||!$bid) die('no openid or no bid');
        $appid = ORM::factory('qwt_login')->where('id','=',$bid)->appid;
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $appid;//商户appid
        $wx = new Wxoauth($bid,$options);
        $msg['touser'] = $openid;
        require_once Kohana::find_file('vendor', 'weixin/inc');
        Kohana::$log->add("qwtrwd:$bid", '2222');
        $model_q = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
        $ticket_lifetime = 3600*24*7;
        if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];

        if ( ($result['ticket'] = $model_q->ticket) &&  (time()<explode('|',$model_q->ticket)[2]) ) {
            Kohana::$log->add("qwtrwd:$bid:2", $model_q->ticket);
            $msg['text']['content'] = $config['text_send'];

        } else {
            Kohana::$log->add("qwtrwd:$bid:3", $model_q->ticket);
            $time = time()+$ticket_lifetime;

            $result['ticket'] = $model_q->openid.'|'.$bid.'|'.$time;// ticket直接用openid和bid加密
            $model_q->lastupdate = time();

            $msg['text']['content'] = $config['text_send'];

            //生成海报并保存
            // $model_q->values($userinfo);
            // $model_q->bid = $bid;
            // $model_q->ticket = $result['ticket'];
            // $model_q->save();
            $fuopenid = ORM::factory('qwt_rwdqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->fuopenid;
            //Kohana::$log->add("openid:$bid:url", $this->baseurl.'index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid));
            if(!$fuopenid){
                $cksum = md5($openid.$config['appsecret'].date('Y-m-d'));
                $msg['touser'] = $openid;
                $msg['msgtype'] = 'news';
                $msg['news']['articles'][0]['title'] = '点击验证生成海报';
                $msg['news']['articles'][0]['url'] = 'http://'. $_SERVER['HTTP_HOST'] .'/qwtrwd/index/'. $bid .'?url=storefuop&cksum='. $cksum .'&openid='. base64_encode($openid);
                $msg['news']['articles'][0]['description'] = '特别注意：点击验证后才能生成海报';
                $msg['news']['articles'][0]['picurl'] = 'http://cdn.jfb.smfyun.com/qwt/rwd/subscribe.png';
                $wx_result = $wx->sendCustomMessage($msg);
                exit;
            }
        }

        $md5 = md5($result['ticket'].time().rand(1,100000));
        $model_q->ticket = $result['ticket'];
        $model_q->save();
        //图片合成
        //模板
        $imgtpl = DOCROOT."qwt/rwd/tmp/tpl.$bid.jpg";
        $tmpdir = '/dev/shm/';

        //判断模板文件是否需要从数据库更新
        $tpl = ORM::factory('qwt_rwdcfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
        if (!$tpl->pic) {
            $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
            $wx_result = $wx->sendCustomMessage($msg);
            exit;
        }

        if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);
        //exit;
        if (!file_exists($imgtpl)) {
            @mkdir(dirname($imgtpl));
            @file_put_contents($imgtpl, $tpl->pic);
        }

        //默认头像
        $tplhead = ORM::factory('qwt_rwdcfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
        $default_head_file = DOCROOT."qwt/rwd/tmp/head.$bid.jpg";

        if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
        if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);


            //获取二维码
            require_once Kohana::find_file("vendor/phpqrcode","phpqrcode");
            $qrurl =  'http://'.$_SERVER["HTTP_HOST"].'/qwtrwd/qrscan/'.$result['ticket'];
            $localfile = "{$tmpdir}$md5.jpg";
            QRcode::png($qrurl,$localfile,'L','6','2');
            $remote_qrcode = $localfile;
            //if (!$remote_qrcode) $remote_qrcode = QRcode::png($qrurl, false, 'L', '4');
            //if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);
            //获取头像

            $headfile = "{$tmpdir}$md5.head.jpg";

            $remote_head_url = str_replace('wx.qlogo.cn', '182.254.104.16', $model_q->headimgurl);
            $remote_head = curls($remote_head_url);

            if (!$remote_head) {
                $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
                $remote_head = curls($remote_head_url);
            }

            //retry... 96px
            if (!$remote_head) {
                $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                $remote_head = curls($remote_head_url);
            }

            //获取失败用默认头像
            if (!$remote_head && $default_head) $remote_head = file_get_contents($default_head_file);

            //写入临时头像文件
            if ($remote_head) file_put_contents($headfile, $remote_head);

            if (!$remote_head || !$remote_qrcode) {
                $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                $wx_result = $wx->sendCustomMessage($msg);
                Kohana::$log->add("qwtrwd:$bid:file:remote_head_url get ERROR!", $remote_head_url);

                @unlink($headfile);
                @unlink($localfile);
                exit;
            }

            //合成
            $dest = imagecreatefromjpeg($imgtpl);
            $src_qrcode = imagecreatefrompng($localfile);
            $src_head = imagecreatefromjpeg($headfile);

            if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
            if($src_head) imagecopyresampled($dest, $src_head, $config['px_head_left'], $config['px_head_top'], 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

            $newfile = "{$tmpdir}$md5.new.jpg";
            imagejpeg($dest, $newfile);
            if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
            if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);


            if (file_exists($newfile)) {
                $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                if (!$uploadresult['media_id']) {
                    Kohana::$log->add("qwtrwd:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                    if ($wx->errCode == 45009) {
                        $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                        $wx_result = $wx->sendCustomMessage($msg);
                        exit;
                    }
                } else {
                    //上传成功 pass
                    if ($bid == $debug_bid) Kohana::$log->add("qwtrwd:$bid:$newfile upload OK!", print_r($uploadresult, true));
                }

            } else {
                Kohana::$log->add("qwtrwd:$bid:newfile $newfile gen ERROR!");
                Kohana::$log->add("qwtrwd:$bid:imgtplfile", file_exists($imgtpl));
                Kohana::$log->add("qwtrwd:$bid:qrcodefile", file_exists($localfile));
                Kohana::$log->add("qwtrwd:$bid:headfile", file_exists($headfile));
            }

            unlink($localfile);
            unlink($headfile);
            unlink($newfile);
        //海报发送前提醒消息


        $txtReply2 = '海报有效期到 '. date('Y-m-d H:i', explode('|',$model_q->ticket)[2]) .' 过期后请点击「生成海报」菜单重新获取哦！';

        $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;

        Kohana::$log->add("msg:$bid:qwtrwd", print_r($msg,true));
        $msg['msgtype'] = 'text';
        $wx_result = $wx->sendCustomMessage($msg);
        Kohana::$log->add("qwtmsg:$bid:txt", print_r($wx_result,true));
        $msg['msgtype'] = 'image';
        $msg['image']['media_id'] = $uploadresult['media_id'];
        unset($msg['text']);
        $wx_result = $wx->sendCustomMessage($msg);
        Kohana::$log->add("qwtmsg:$bid:tplimg", print_r($wx_result,true));

        exit;
    }
    public function action_qwt_dka($bid){
        if(isset($_POST['tplcheck'])){//发布模板消息

            die();
        }
        if($_GET['poster']==1){
            require_once Kohana::find_file('vendor', 'weixin/inc');
            $mem = Cache::instance('memcache');
            $openid = $_GET['openid'];
            $config = ORM::factory('qwt_dkacfg')->getCfg($bid,1);
            $model_q = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find();
            $appid = ORM::factory('qwt_login')->where('id','=',$bid)->appid;
            $options['token'] = $this->token;
            $options['encodingaeskey'] = $this->encodingAesKey;
            $options['appid'] = $appid;//商户appid
            $wx = new Wxoauth($bid,$options);
            $ticket_lifetime = 3600*24*7;
            $msg['touser'] = $openid;
            $msg['msgtype'] = 'text';
            if(Model::factory('select_experience')->dopinion($bid,'dka')){
                if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];
                if (($model_q->id2!=0)&&($result['ticket'] = $model_q->ticket) && (time() - $model_q->lastupdate < $ticket_lifetime) ) {
                    // 非新用户&&该ticket存在并且直接赋值给$result['ticket']&&未过期
                    //有 ticket 并且没有超过 7 天 就不用重新生成
                    //Kohana::$log->add('weixin:ticket1', print_r($result['ticket'], true));//
                    $msg['text']['content'] = $config['text_send'];
                }else{
                    $ticket_lifetime = 3600*24*7;
                    //自定义过期时间
                    if ($config['ticket_lifetime']) $ticket_lifetime = 3600*24*$config['ticket_lifetime'];
                    $result = $wx->getQRCode('dka'.$bid, 0, $ticket_lifetime);
                    //Kohana::$log->add('weixin:result', print_r($result, true));//
                    $msg['text']['content'] = $config['text_send'];
                    //生成海报并保存
                    $model_q->bid = $bid;
                    $model_q->ticket = $result['ticket'];
                    // $msg['touser'] = $model_q->openid;
                    // $msg['msgtype'] = 'text';
                    // $msg['text']['content'] = 'ticket:'.$result['ticket'];

                    // $we->sendCustomMessage($msg);
                    // exit;
                    $model_q->lastupdate = time();
                    $model_q->save();

                    $newticket = true;
                }

                // 3 条客服消息限制，这里不发了
                //$we_result = $wx->sendCustomMessage($msg);

                $md5 = md5($result['ticket'].time().rand(1,100000));

                //图片合成
                //模板
                $imgtpl = DOCROOT."qwt/dka/tmp/tpl.$bid.jpg";
                $tmpdir = '/dev/shm/';
                //判断模板文件是否需要从数据库更新
                $tpl = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tpl')->find();
                if (!$tpl->pic) {
                    $msg['text']['content'] = '二维码模板未配置，请登录商户后台配置后再生成';
                    $we_result = $wx->sendCustomMessage($msg);
                    exit;
                }

                if (file_exists($imgtpl) && $tpl->lastupdate > filemtime($imgtpl)) unlink($imgtpl);

                if (!file_exists($imgtpl)) {
                    @mkdir(dirname($imgtpl));
                    @file_put_contents($imgtpl, $tpl->pic);
                }

                //默认头像
                $tplhead = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find();
                $default_head_file = DOCROOT."qwt/dka/tmp/head.$bid.jpg";

                if (file_exists($default_head_file) && $tplhead->lastupdate > filemtime($default_head_file)) unlink($default_head_file);
                if ($tplhead->pic && !file_exists($default_head_file)) file_put_contents($default_head_file, $tplhead->pic);

                //有海报缓存直接发送
                $tpl_key = 'qwtdka:tpl:'.$openid.':'.$tpl->lastupdate;
                $uploadresult['media_id'] = $mem->get($tpl_key);

                if ($bid == $debug_bid) $newticket = true;


                    //获取参数二维码
                    $qrurl = $wx->getQRUrl($result['ticket']);

                    $localfile = "{$tmpdir}$md5.jpg";
                    $remote_qrcode = curls($qrurl);
                    if (!$remote_qrcode) $remote_qrcode = curls($qrurl);
                    if ($remote_qrcode) file_put_contents($localfile, $remote_qrcode);

                    //获取头像
                    $headfile = "{$tmpdir}$md5.head.jpg";

                    //IP 获取
                    //http://182.254.104.16/mmopen/ajNVdqHZLLB1WVibay1icL4QZ4VWrLZriblYa9yBu7hia3AAERIvI4ysT3MhwoKpCbgC1WF7mBuHxhRHLhRbI7scUg/0
                    //http://wx.qlogo.cn/mmopen/ajNVdqHZLLAwad4e2M5lW5vNg6iaMSIkeNnt3oNfw84BWrg657rfeoLSico8eyyOV8mLXuSsx723UJntfZJLu4vA/132
                    $remote_head_url = str_replace('wx.qlogo.cn', '182.254.104.16', $model_q->headimgurl);
                    $remote_head = curls($remote_head_url);
                    if (!$remote_head) {
                        $remote_head_url = str_replace('/0', '/132', $model_q->headimgurl);
                        $remote_head = curls($remote_head_url);
                    }

                    //retry... 96px
                    if (!$remote_head) {
                        $remote_head_url = str_replace('/132', '/96', $remote_head_url);
                        $remote_head = curls($remote_head_url);
                    }

                    //获取失败用默认头像
                    if (!$remote_head && $default_head_file) {
                        $remote_head = file_get_contents($default_head_file);
                        Kohana::$log->add("qwtdka:$bid:head4:", $remote_head);
                    }

                    //写入临时头像文件
                    if ($remote_head) file_put_contents($headfile, $remote_head);

                    if (!$remote_head || !$remote_qrcode) {
                        $msg['text']['content'] = '非常抱歉，系统正忙，请过 5 分钟后再试...';
                        $we_result = $wx->sendCustomMessage($msg);
                        Kohana::$log->add("qwtdka:$bid:file:remote_head_url get ERROR!", $remote_head_url);

                        @unlink($headfile);
                        @unlink($localfile);
                        exit;
                    }

                    //合成
                    $dest = imagecreatefromjpeg($imgtpl);
                    $src_qrcode = imagecreatefromjpeg($localfile);
                    $src_head = imagecreatefromjpeg($headfile);
                    $config['px_qrcode_left'] = 170;
                    $config['px_qrcode_top'] = 450;
                    $config['px_qrcode_width'] = 300;

                    if($src_qrcode) imagecopyresampled($dest, $src_qrcode, $config['px_qrcode_left'], $config['px_qrcode_top'], 0, 0, $config['px_qrcode_width'], $config['px_qrcode_width'], imagesx($src_qrcode), imagesy($src_qrcode));
                    if($src_head) imagecopyresampled($dest, $src_head, 90, 315, 0, 0, $config['px_head_width'], $config['px_head_width'], imagesx($src_head), imagesy($src_head));

                    $newfile = "{$tmpdir}$md5.new.jpg";
                    imagejpeg($dest, $newfile);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 95);
                    if (!file_exists($newfile)) imagejpeg($dest, $newfile, 85);

                    $qid = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->id;
                    $today = date('y-m-d',time());
                    function countday($userconday,$bid,$qid){
                        $frontday = date("Y-m-d",strtotime('-'.$userconday.'day'));
                        $continue = ORM::factory('qwt_dkascore')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$frontday)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date;
                        if($continue){
                            $userconday++;
                            return countday($userconday,$bid,$qid);
                        }else{
                            return $userconday;
                        }
                    }
                    function hex2rgb($hexColor) {
                        $color = str_replace('#', '', $hexColor);
                        if (strlen($color) > 3) {
                            $rgb = array(
                                'r' => hexdec(substr($color, 0, 2)),
                                'g' => hexdec(substr($color, 2, 2)),
                                'b' => hexdec(substr($color, 4, 2))
                            );
                        } else {
                            $color = $hexColor;
                            $r = substr($color, 0, 1) . substr($color, 0, 1);
                            $g = substr($color, 1, 1) . substr($color, 1, 1);
                            $b = substr($color, 2, 1) . substr($color, 2, 1);
                            $rgb = array(
                                'r' => hexdec($r),
                                'g' => hexdec($g),
                                'b' => hexdec($b)
                                );
                        }
                        return $rgb;
                    }
                    if(ORM::factory('qwt_dkascore')->where('bid', '=', $bid)->where('qid', '=', $qid)->where('date','=',$today)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->date){
                        $flag = 1;
                    }else{
                        $flag = 0;
                    }//判断今日签到
                    $countday = countday(1,$bid,$qid)-1+$flag;//计算连续打卡天数
                    $num = ORM::factory('qwt_dkascore')->where('bid', '=', $bid)->where('qid', '=', $qid)->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->count_all();
                    //$score=ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('openid', '=', $openid)->find()->score;
                    $add = ORM::factory('qwt_dkascore')->where('bid', '=', $bid)->where('qid', '=' , $qid)->where('date','=',date('Y-m-d',time()))->where_open()->where('type', '=', 5)->or_where('type', '=', 6)->or_where('type', '=', 7)->where_close()->find()->score;
                    //第一次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    $text = "$num";//文字

                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =52;
                    //$top = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top =360;
                    //$left = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =425;
                    //$color = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#EC6941";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件


                    //第二次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    $text = "$countday";//总天数

                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =32;
                    //$top = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top = 405;
                    //$left = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =322;
                    //$color = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#86CC00";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件


                    //第三次合成
                    $im = imagecreatefromjpeg($newfile);//选择图片

                    $newfile =  "{$tmpdir}$md5.new.jpg";//缓存地址文件
                    if(!$add) $add=0;
                    $text = "$add";//连续打卡奖励积分
                    $font = DOCROOT."dka/dist/msyh.ttf";//选择字体


                    //$size = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_size')->find()->value;
                    $size =32;
                    //$top = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_top')->find()->value;
                    $top =405;
                    //$left = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_left')->find()->value;
                    $left =460;
                    //$color = ORM::factory('qwt_dkacfg')->where('bid', '=', $bid)->where('key', '=', 'font_color')->find()->value;//选择颜色
                    $color = "#86CC00";
                    $rgb = hex2rgb($color);

                    $color = @imagecolorallocate($im, $rgb['r'],$rgb['g'],$rgb['b']);//选择颜色

                    imagettftext($im, $size, 0, $left, $top, $color, $font, $text);//将文字和图片合成

                    imagejpeg($im, $newfile,100);//缓存到文件



                    if (file_exists($newfile)) {
                        $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) $uploadresult = $wx->uploadMedia(array('media'=>"@$newfile"), 'image');
                        if (!$uploadresult['media_id']) {
                            Kohana::$log->add("qwtdka:$bid:$newfile upload ERROR!", $wx->errCode.':'.$wx->errMsg);
                            if ($wx->errCode == 45009) {
                                $msg['text']['content'] = '亲，十分抱歉，本活动今天参与人数已经达到微信规定的上限，请明天再来参与哦~ 有疑问请直接发送消息给公众号。';
                                $we_result = $wx->sendCustomMessage($msg);
                                exit;
                            }
                        } else {
                            //上传成功 pass
                            if ($bid == $debug_bid) Kohana::$log->add("qwtdka:$bid:$newfile upload OK!", print_r($uploadresult, true));
                        }

                    } else {
                        Kohana::$log->add("qwtdka:$bid:newfile $newfile gen ERROR!");
                        Kohana::$log->add("qwtdka:$bid:imgtplfile", file_exists($imgtpl));
                        Kohana::$log->add("qwtdka:$bid:qrcodefile", file_exists($localfile));
                        Kohana::$log->add("qwtdka:$bid:headfile", file_exists($headfile));
                    }

                    unlink($localfile);
                    unlink($headfile);


                    //Cache
                    if ($uploadresult['media_id'] && $remote_head) $mem->set($tpl_key, $uploadresult['media_id'], 3600*24);
                //}

                //海报发送前提醒消息
                $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「生成海报」菜单重新获取哦！';
                if ($bid == 64) $txtReply2 = $msg['text']['content'] = '海报有效期到 '. date('Y-m-d H:i', $model_q->lastupdate+$ticket_lifetime) .' 过期后请点击「我要参加」菜单重新获取哦！';
                $msg['text']['content'] = $config['text_send']. "\n\n" .$txtReply2;

                $we_result = $wx->sendCustomMessage($msg);

                $msg['msgtype'] = 'image';
                $msg['image']['media_id'] = $uploadresult['media_id'];
                unset($msg['text']);

                if(!$model_q->id2){
                    $fx_count = ORM::factory('qwt_dkaqrcode')->where('bid', '=', $bid)->where('ticket', '<>', '')->count_all();
                    $model_q->id2 = $fx_count;
                    if ($model_q->id) $model_q->save();
                }

                $we_result = $wx->sendCustomMessage($msg);

                if ($bid == $debug_bid) Kohana::$log->add("qwtdka:$bid:img_msg", var_export($msg, true));
                if ($bid == $debug_bid) Kohana::$log->add("qwtdka:$bid:we_result_img", var_export($we_result, true).$wx->errCode.':'.$wx->errMsg);
                exit;
            }else{
                $msg['text']['content'] = '体验海报已用完，需要续费后才能正常使用，谢谢！';
                $wx->sendCustomMessage($msg);
                exit();
            }

        }
    }
    public function action_location(){
        $locationx = 39.92;
        $locationy = 116.46;
        $host = "http://jisujwddz.market.alicloudapi.com";
        $path = "/geoconvert/coord2addr";
        $method = "GET";
        $appcode = "5ee84130544445bf875bbb1d3a017a71";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "lat=".$locationx."&lng=".$locationy."&type=baidu";
        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $res = curl_exec($curl);
        curl_close($curl); // 关闭一个curl会话
        // var_dump($res);
        $json_obj = json_decode($res, true);
        // var_dump($json_obj);
        $pro = $json_obj['result']['province'];//省份
        $city = $json_obj['result']['city'];//城市
        $dis = $json_obj['result']['district'];//区
        echo $pro.','.$city.','.$dis;
    }
    public function action_yyb_insert(){
        $bid = 1;
        $time = time();
        for ($a=1; $a <= 200000; $a++) {

            $str = $this->make_password();
            $values[] = "($bid, '".$str."', $time)";
        }
        //统一插入
        $SQL = 'INSERT IGNORE INTO yyb_tests (`bid`,`openid`,`lastupdate`) VALUES '. join(',', $values);
        $now_count = $colum = DB::query(NULL,$SQL)->execute();
    }
    public function action_all_top(){
        //今日
        $shops = ORM::factory('smfyun_shop')->where('date','=',date('Ymd',strtotime("-1 day")))->order_by('score','desc')->limit(10)->find_all();
        $shop = array();
        foreach ($shops as $k => $v) {
            $shop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->count_all();//上榜天数
            $shop[$k]['shopName'] = $v->shopName;
            $shop[$k]['shopIcon'] = $v->shopIcon;
            $shop[$k]['shopId'] = $v->shopId;
            $shop[$k]['url'] = $v->url;
            $shop[$k]['wxId'] = $v->wxId;
            $shop[$k]['score'] = $v->score;
        }
        if($shop[0]){
            foreach ($shop as $k => $v){
                $num[$k]  = $v['num'];
                $shopName[$k]  = $v['shopName'];
                $score[$k]  = $v['score'];
            }
            //先按照积分排序后按照上榜天数和名字
            array_multisort($score,SORT_DESC,$num, SORT_DESC, $shopName, SORT_ASC,$shop);
        }
        //周排名
        $shops = ORM::factory('smfyun_shop')->find_all();
        // $weekshop = array();
        // $weekstart = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w')+1-7-1,date('Y')));//上周开始
        // $weekend = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w')+7-7+1,date('Y')));//上周结束
        // $timestamp = time();
        // $firstday = date('Ym01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
        // $monthstart = date('Ymd',strtotime("$firstday -1 day"));
        // $monthend = date('Ymd',strtotime("$firstday +1 month"));

        $weekshops = ORM::factory('smfyun_weektop')->where('date','=',date('Ymd',strtotime("-1 day")))->order_by('score','desc')->limit(10)->find_all();
        $monthshops = ORM::factory('smfyun_monthtop')->where('date','=',date('Ymd',strtotime("-1 day")))->order_by('score','desc')->limit(10)->find_all();
        $weekshops_num = ORM::factory('smfyun_weektop')->where('date','=',date('Ymd',strtotime("-1 day")))->order_by('score','desc')->count_all();
        $monthshops_num = ORM::factory('smfyun_monthtop')->where('date','=',date('Ymd',strtotime("-1 day")))->order_by('score','desc')->count_all();
        //总排名
        foreach ($shops as $k => $v) {
            $allshop[$k]['shopName'] = $v->shopName;
            $allshop[$k]['shopIcon'] = $v->shopIcon;
            $allshop[$k]['shopId'] = $v->shopId;
            $allshop[$k]['url'] = $v->url;
            $allshop[$k]['wxId'] = $v->wxId;
            $allshop[$k]['score'] = $v->score;
        }
        foreach ($allshop as $k => $v){
            $shopName[$k]  = $v['shopName'];
            $score[$k]  = $v['score'];
        }
        //先按照上榜天数排名 后按照名字排名
        array_multisort($score,SORT_DESC, $shopName, SORT_ASC,$allshop);
        $view = 'weixin/smfyun/all_top';
        $this->template->content = View::factory($view)
                    ->bind('weekshops_num',$weekshops_num)
                    ->bind('monthshops_num',$monthshops_num)
                    ->bind('shop',$shop)//今日
                    ->bind('weekshop',$weekshops)//周榜
                    ->bind('monthshop',$monthshops)//月榜
                    ->bind('allshop',$allshop);//总榜
    }
    public function action_week_top(){
        $weekshops = ORM::factory('smfyun_weektop')->where('date','=',date('Ymd',strtotime("-1 day")))->where('num','!=',0)->order_by('score','desc')->find_all();
        $weekshops_num = ORM::factory('smfyun_weektop')->where('date','=',date('Ymd',strtotime("-1 day")))->where('num','!=',0)->order_by('score','desc')->count_all();
        $view = 'weixin/smfyun/week_top';
        $this->template->content = View::factory($view)->bind('weekshop',$weekshops)->bind('weekshops_num',$weekshops_num);
    }
    public function action_month_top(){
        $monthshops = ORM::factory('smfyun_monthtop')->where('date','=',date('Ymd',strtotime("-1 day")))->where('num','!=',0)->order_by('score','desc')->find_all();
        $monthshops_num = ORM::factory('smfyun_monthtop')->where('date','=',date('Ymd',strtotime("-1 day")))->where('num','!=',0)->order_by('score','desc')->count_all();
        $view = 'weixin/smfyun/month_top';
        $this->template->content = View::factory($view)->bind('monthshop',$monthshops)->bind('monthshops_num',$monthshops_num);
    }
    public function action_sum_top(){
        $shops = ORM::factory('smfyun_shop')->find_all();
        $weekshop = array();
        $weekstart = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w')+1-7-1,date('Y')));//上周开始
        $weekend = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w')+7-7+1,date('Y')));//上周结束
        $timestamp = time();
        $firstday = date('Ym01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
        $monthstart = date('Ymd',strtotime("$firstday -1 day"));
        $monthend = date('Ymd',strtotime("$firstday +1 month"));
        foreach ($shops as $k => $v) {
            $allshop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->count_all();//上榜天数
            $allshop[$k]['shopName'] = $v->shopName;
            $allshop[$k]['shopIcon'] = $v->shopIcon;
            $allshop[$k]['shopId'] = $v->shopId;
            $allshop[$k]['url'] = $v->url;
            $allshop[$k]['wxId'] = $v->wxId;
            $allshop[$k]['score'] = $v->score;
        }
        foreach ($allshop as $k => $v){
            $shopName[$k]  = $v['shopName'];
            $score[$k]  = $v['score'];
        }
        //先按照上榜天数排名 后按照名字排名
        array_multisort($score,SORT_DESC, $shopName, SORT_ASC,$allshop);

        $view = 'weixin/smfyun/sum_top';
        $this->template->content = View::factory($view)->bind('allshop',$allshop);
    }
    public function action_now_top(){
        $shops = ORM::factory('smfyun_shop')->where('date','=',date('Ymd',strtotime("-1 day")))->find_all();
        $shop = array();
        foreach ($shops as $k => $v) {
            $shop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->count_all();//上榜天数
            $shop[$k]['shopName'] = $v->shopName;
            $shop[$k]['shopIcon'] = $v->shopIcon;
            $shop[$k]['shopId'] = $v->shopId;
            $shop[$k]['url'] = $v->url;
            $shop[$k]['wxId'] = $v->wxId;
            $shop[$k]['score'] = $v->score;
        }
        foreach ($shop as $k => $v){
            $num[$k]  = $v['num'];
            $shopName[$k]  = $v['shopName'];
            $score[$k]  = $v['score'];
        }
        //先按照上榜天数排名 后按照名字排名
        @array_multisort($score,SORT_DESC,$num, SORT_DESC, $shopName, SORT_ASC,$shop);
        $view = 'weixin/smfyun/now_top';
        $this->template->content = View::factory($view)->bind('shop',$shop);
    }
    //有赞小报
    public function action_paper(){
        $view = 'weixin/smfyun/paper';
        $this->template->content = View::factory($view);
    }
    //神码浮云营销应用平台
    public function action_smplatform(){
        $view = 'weixin/smfyun/smplatform';
        $this->template->content = View::factory($view);
    }
    public function action_smtp_jsons(){
        if($_POST['json']){
            $arr = json_decode($_POST['json']);
            echo '<pre>';
            foreach ($arr as $k => $v) {
                //保证店铺id唯一的情况
                $shop = ORM::factory('smfyun_shop')->where('shopId','=',$v->shopId)->find();
                $shop->date = $v->date;
                $shop->shopName = $v->shopName;
                $shop->shopIcon = $v->shopIcon;
                $shop->shopId = $v->shopId;
                $shop->url = $v->url;
                $shop->wxId = $v->wxId;
                $shop->save();
                //插入当天积分
                $today_score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',$v->date)->find();
                if($today_score->id){
                    echo '已录入，跳过';
                }else{
                    $i = 0;
                    ORM::factory('smfyun_score')->scoreIn($shop,1,0,$v->date);
                    while ($score->id||$i==0) {
                        if($i==1){
                            ORM::factory('smfyun_score')->scoreIn($shop,0.50,1,$v->date);
                        }
                        if($i==2){
                            ORM::factory('smfyun_score')->scoreIn($shop,0.30,2,$v->date);
                        }
                        if($i==3){
                            break;
                        }
                        $i++;
                        $score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',date('Ymd',strtotime('-'.$i.'day')))->find();
                    }
                    echo $k.'--------------------------<br>';
                    echo 'shopIcon：'.$v->shopIcon.'<br>';
                    echo 'shopName：'.$v->shopName.'<br>';
                    echo 'shopId：'.$v->shopId.'<br>';
                    echo 'url：'.$v->url.'<br>';
                    echo 'wxId：'.$v->wxId.'<br>';
                    echo 'date：'.$v->date.'<br>';
                }
            }
            echo '保存成功';
            exit;
        }
        $view = "weixin/jsons";
        $this->template->content = View::factory($view);
    }
    public function action_smtp_json_again(){
        $_POST['json']=1;
        if($_POST['json']){
            // $arr = json_decode($_POST['json']);
            // echo '<pre>';
            // var_dump($arr);
            // foreach ($arr as $k => $v) {
            //     //保证店铺id唯一的情况
            //     $shop = ORM::factory('smfyun_shop')->where('shopId','=',$v->shopId)->find();
            //     $shop->date = $v->date;
            //     $shop->shopName = $v->shopName;
            //     $shop->shopIcon = $v->shopIcon;
            //     $shop->shopId = $v->shopId;
            //     $shop->url = $v->url;
            //     $shop->wxId = $v->wxId;
            //     $shop->save();
            //     //插入当天积分
            //     $today_score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',$v->date)->find();
            //     if($today_score->id) die('已录入，跳过');
            //     $i = 0;
            //     ORM::factory('smfyun_score')->scoreIn($shop,1,0,$v->date);
            //     while ($score->id||$i==0) {
            //         if($i==1){
            //             ORM::factory('smfyun_score')->scoreIn($shop,0.50,1,$v->date);
            //         }
            //         if($i==2){
            //             ORM::factory('smfyun_score')->scoreIn($shop,0.30,2,$v->date);
            //         }
            //         if($i==3){
            //             break;
            //         }
            //         $i++;
            //         $score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',date('Ymd',strtotime('-'.$i.'day')))->find();
            //     }
            //     echo $k.'--------------------------<br>';
            //     echo 'shopIcon：'.$v->shopIcon.'<br>';
            //     echo 'shopName：'.$v->shopName.'<br>';
            //     echo 'shopId：'.$v->shopId.'<br>';
            //     echo 'url：'.$v->url.'<br>';
            //     echo 'wxId：'.$v->wxId.'<br>';
            //     echo 'date：'.$v->date.'<br>';
            //     $all_date = $v->date;
            // }
            // 周排名
            $all_date = date('Ymd',strtotime("-1 day"));//前一天
            echo '前一天:'.$all_date;
            $shops = ORM::factory('smfyun_shop')->find_all();
            $weekshop = array();
            $weekstart = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w'),date('Y')));//这周开始
            $weekend = date('Ymd');//今天
            $timestamp = time();
            $firstday = date('Ym01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)).'-01'));
            $monthstart = date('Ymd',strtotime("$firstday -2 day"));
            $monthend = date('Ymd');//今天
            echo $weekstart.'<br>';
            echo $weekend.'<br>';
            echo $monthstart.'<br>';
            echo $monthend.'<br>';
            // exit;
            foreach ($shops as $k => $v) {
                // $weekshop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','=',0)->where('type','=',0)->count_all();//上榜天数
                $num=DB::query(Database::SELECT,"SELECT SUM(score) as score from smfyun_score where sid=$v->id and `date`< $weekend and `date`>$weekstart")->execute()->as_array();
                // var_dump($num);
                // if($num[0]['score']!=0){
                $num_day = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','<',$weekend)->where('date','>',$weekstart)->count_all();//上榜天数
                $week_top_tb = ORM::factory('smfyun_weektop')->where('date','=',$all_date)->where('sid','=',$v->id)->find();
                if($week_top_tb->id){
                    echo '已经导入week，跳过<br>';
                }else{
                    $week_top_tb->sid = $v->id;
                    $week_top_tb->date = $all_date;
                    $week_top_tb->num = $num_day;
                    $week_top_tb->score = $num[0]['score']?$num[0]['score']:0;
                    $week_top_tb->save();
                }
            }
            //月排名
            foreach ($shops as $k => $v) {
                // $weekshop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','=',0)->where('type','=',0)->count_all();//上榜天数
                $num=DB::query(Database::SELECT,"SELECT SUM(score) as score from smfyun_score where sid=$v->id and `date`< $monthend and `date`>$monthstart")->execute()->as_array();
                $num_day = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','<',$monthend)->where('date','>',$monthstart)->count_all();//上榜天数
                $month_top_tb = ORM::factory('smfyun_monthtop')->where('date','=',$all_date)->where('sid','=',$v->id)->find();
                if($month_top_tb->id){
                    echo '已经导入month，跳过<br>';
                }else{
                    $month_top_tb->sid = $v->id;
                    $month_top_tb->date = $all_date;
                    $month_top_tb->num = $num_day;
                    $month_top_tb->score = $num[0]['score']?$num[0]['score']:0;
                    $month_top_tb->save();
                }
            }
            echo '保存成功';
            exit;
        }
        $view = "weixin/json";
        $this->template->content = View::factory($view);
    }
    public function action_smtp_json(){
        // $_POST['json']=1;
        if($_POST['json']){
            $arr = json_decode($_POST['json']);
            echo '<pre>';
            var_dump($arr);
            foreach ($arr as $k => $v) {
                //保证店铺id唯一的情况
                $shop = ORM::factory('smfyun_shop')->where('shopId','=',$v->shopId)->find();
                $shop->date = $v->date;
                $shop->shopName = $v->shopName;
                $shop->shopIcon = $v->shopIcon;
                $shop->shopId = $v->shopId;
                $shop->url = $v->url;
                $shop->wxId = $v->wxId;
                $shop->save();
                //插入当天积分
                $today_score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',$v->date)->find();
                if($today_score->id) die('已录入，跳过');
                $i = 0;
                ORM::factory('smfyun_score')->scoreIn($shop,1,0,$v->date);
                while ($score->id||$i==0) {
                    if($i==1){
                        ORM::factory('smfyun_score')->scoreIn($shop,0.50,1,$v->date);
                    }
                    if($i==2){
                        ORM::factory('smfyun_score')->scoreIn($shop,0.30,2,$v->date);
                    }
                    if($i==3){
                        break;
                    }
                    $i++;
                    $score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',date('Ymd',strtotime('-'.$i.'day')))->find();
                }
                echo $k.'--------------------------<br>';
                echo 'shopIcon：'.$v->shopIcon.'<br>';
                echo 'shopName：'.$v->shopName.'<br>';
                echo 'shopId：'.$v->shopId.'<br>';
                echo 'url：'.$v->url.'<br>';
                echo 'wxId：'.$v->wxId.'<br>';
                echo 'date：'.$v->date.'<br>';
                $all_date = $v->date;
            }
            // 周排名
            // $all_date = '20171105';//前一天
            $shops = ORM::factory('smfyun_shop')->find_all();
            $weekshop = array();
            $weekstart = date('Ymd',mktime(0,0,0,date('m'),date('d')-date('w'),date('Y')));//这周开始
            $weekend = date('Ymd');//今天
            $timestamp = time();
            $firstday = date('Ym01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)).'-01'));
            $monthstart = date('Ymd',strtotime("$firstday -2 day"));
            $monthend = date('Ymd');//今天
            echo $weekstart.'<br>';
            echo $weekend.'<br>';
            echo $monthstart.'<br>';
            echo $monthend.'<br>';
            // exit;
            foreach ($shops as $k => $v) {
                // $weekshop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','=',0)->where('type','=',0)->count_all();//上榜天数
                $num=DB::query(Database::SELECT,"SELECT SUM(score) as score from smfyun_score where sid=$v->id and `date`< $weekend and `date`>$weekstart")->execute()->as_array();
                // var_dump($num);
                // if($num[0]['score']!=0){
                $num_day = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','<',$weekend)->where('date','>',$weekstart)->count_all();//上榜天数
                $week_top_tb = ORM::factory('smfyun_weektop')->where('date','=',$all_date)->where('sid','=',$v->id)->find();
                if($week_top_tb->id){
                    echo '已经导入week，跳过<br>';
                }else{
                    $week_top_tb->sid = $v->id;
                    $week_top_tb->date = $all_date;
                    $week_top_tb->num = $num_day;
                    $week_top_tb->score = $num[0]['score']?$num[0]['score']:0;
                    $week_top_tb->save();
                }
            }
            //月排名
            foreach ($shops as $k => $v) {
                // $weekshop[$k]['num'] = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','=',0)->where('type','=',0)->count_all();//上榜天数
                $num=DB::query(Database::SELECT,"SELECT SUM(score) as score from smfyun_score where sid=$v->id and `date`< $monthend and `date`>$monthstart")->execute()->as_array();
                $num_day = ORM::factory('smfyun_score')->where('sid','=',$v->id)->where('type','=',0)->where('date','<',$monthend)->where('date','>',$monthstart)->count_all();//上榜天数
                $month_top_tb = ORM::factory('smfyun_monthtop')->where('date','=',$all_date)->where('sid','=',$v->id)->find();
                if($month_top_tb->id){
                    echo '已经导入month，跳过<br>';
                }else{
                    $month_top_tb->sid = $v->id;
                    $month_top_tb->date = $all_date;
                    $month_top_tb->num = $num_day;
                    $month_top_tb->score = $num[0]['score']?$num[0]['score']:0;
                    $month_top_tb->save();
                }
            }
            echo '保存成功';
            exit;
        }
        $view = "weixin/json";
        $this->template->content = View::factory($view);
    }
    public function action_smtp_json_back(){
        if($_POST['json']){
            $arr = json_decode($_POST['json']);
            echo '<pre>';
            // var_dump($arr);
            foreach ($arr as $k => $v) {
                //保证店铺id唯一的情况
                $shop = ORM::factory('smfyun_shop')->where('shopId','=',$v->shopId)->find();
                $shop->date = $v->date;
                $shop->shopName = $v->shopName;
                $shop->shopIcon = $v->shopIcon;
                $shop->shopId = $v->shopId;
                $shop->url = $v->url;
                $shop->wxId = $v->wxId;
                $shop->save();
                //插入当天积分
                $today_score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',$v->date)->find();
                if($today_score->id) die('已录入，跳过');
                $i = 0;
                ORM::factory('smfyun_score')->scoreIn($shop,1,0,$v->date);
                while ($score->id||$i==0) {
                    if($i==1){
                        ORM::factory('smfyun_score')->scoreIn($shop,0.50,1,$v->date);
                    }
                    if($i==2){
                        ORM::factory('smfyun_score')->scoreIn($shop,0.30,2,$v->date);
                    }
                    if($i==3){
                        break;
                    }
                    $i++;
                    $score = ORM::factory('smfyun_score')->where('sid','=',$shop->id)->where('type','=',0)->where('date','=',date('Ymd',strtotime('-'.$i.'day')))->find();
                }
                echo $k.'--------------------------<br>';
                echo 'shopIcon：'.$v->shopIcon.'<br>';
                echo 'shopName：'.$v->shopName.'<br>';
                echo 'shopId：'.$v->shopId.'<br>';
                echo 'url：'.$v->url.'<br>';
                echo 'wxId：'.$v->wxId.'<br>';
                echo 'date：'.$v->date.'<br>';
            }
            echo '保存成功';
            exit;
        }
        $view = "weixin/json";
        $this->template->content = View::factory($view);
    }
    public function action_user_snsapi_base($bid,$app,$url){
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_base');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapibase_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getUserInfo($token['openid']);
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$qr_user->id){
                $qr_user->bid = $bid;
                $qr_user->values($userinfo);
            }else{//更新头像  跑路的不更新
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $qr_user->subscribe_time = $userinfo['subscribe_time'];
                    $qr_user->jointime = time();
                    $qr_user->nickname = $userinfo['nickname'];
                    $qr_user->headimgurl = $userinfo['headimgurl'];
                }
                $qr_user->subscribe = $userinfo['subscribe'];
            }
            $qr_user->save();

            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$user->id){
                $user->qid = $qr_user->id;
                $user->bid = $bid;
                $user->values($userinfo);
            }else{//更新头像
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $user->subscribe_time = $userinfo['subscribe_time'];
                    $user->jointime = time();
                    $user->nickname = $userinfo['nickname'];
                    $user->headimgurl = $userinfo['headimgurl'];
                }
                $user->subscribe = $userinfo['subscribe'];
            }
            $user->save();

            $_SESSION['qwt'.$app]['config'] = ORM::factory('qwt_'.$app.'cfg')->getCfg($bid,1);
            $_SESSION['qwt'.$app]['openid'] = $user->openid;
            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['uid'] = $user->id;
            $_SESSION['qwt'.$app]['sid'] = $shop->shopid;
            if($app == 'wzb' && !$shop->shopid){
                $_SESSION['qwt'.$app]['sid'] = 936565;
            }

            if($app=='hby'){
                if($_GET['hb_code']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'?hb_code='.urlencode($_GET['hb_code']));
                }
            }
            if($app=='ywm'){
                if($_GET['hb_code']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'?hb_code='.urlencode($_GET['hb_code']));
                }
            }
            Request::instance()->redirect('/qwt'.$app.'/'.$url);
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_user_snsapi_userinfo($bid,$app,$url){
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        if($app == 'tbt') die('链接错误！');
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapiuserinfo_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
            // echo '<pre>';
            // var_dump($userinfo);
            // var_dump($token);
            // echo '</pre>';
            // exit();
            $userinfo2 = $wx->getUserInfo($token['openid']);
            $userinfo['subscribe'] = $userinfo2['subscribe'];
            $userinfo['subscribe_time'] = $userinfo2['subscribe_time']?$userinfo2['subscribe_time']:0;
            // var_dump($userinfo);
            // exit;
            $qr_user = ORM::factory('qwt_qrcode','',Model::factory('select_qwtorm')->selectorm($bid))->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$qr_user->id){
                $qr_user->bid = $bid;
                $qr_user->values($userinfo);
            }else{//更新头像  跑路的不更新
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $qr_user->subscribe_time = $userinfo['subscribe_time'];
                    $qr_user->jointime = time();
                    $qr_user->nickname = $userinfo['nickname'];
                    $qr_user->headimgurl = $userinfo['headimgurl'];
                }
                $qr_user->subscribe = $userinfo['subscribe'];
            }
            $qr_user->save();

            $user = ORM::factory('qwt_'.$app.'qrcode')->where('bid','=',$bid)->where('openid','=',$token['openid'])->find();
            if(!$user->id){
                $user->qid = $qr_user->id;
                $user->bid = $bid;
                $user->values($userinfo);
            }else{//更新头像
                if($userinfo['nickname']&&$userinfo['headimgurl']){
                    $user->subscribe_time = $userinfo['subscribe_time'];
                    $user->jointime = time();
                    $user->nickname = $userinfo['nickname'];
                    $user->headimgurl = $userinfo['headimgurl'];
                }
                $user->subscribe = $userinfo['subscribe'];
            }
            $user->save();

            if($app == 'tbt'){

            }else{
                $_SESSION['qwt'.$app]['config'] = ORM::factory('qwt_'.$app.'cfg')->getCfg($bid,1);
            }
            $_SESSION['qwt'.$app]['openid'] = $user->openid;
            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['uid'] = $user->id;
            $_SESSION['qwt'.$app]['sid'] = $shop->shopid;
            if($app == 'wzb' && !$shop->shopid){
                $_SESSION['qwt'.$app]['sid'] = 936565;
            }
            if($_GET['wzbdebug']=='start'){
                Request::instance()->redirect('/qwt'.$app.'/'.$url.'?wzbdebug=start');
            }
            if($app=='hby'){
                if($_GET['hb_code']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'?hb_code='.urlencode($_GET['hb_code']));
                }
            }
            if($app=='kjb'){
                if($_GET['eid']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'/'.urlencode($_GET['eid']));
                }
                if($_GET['iid']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'/'.urlencode($_GET['iid']));
                }
                if($_GET['oid']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'/'.urlencode($_GET['oid']));
                }
            }
            if($app=='ywm'){
                if($_GET['hb_code']){
                    Request::instance()->redirect('/qwt'.$app.'/'.$url.'?hb_code='.urlencode($_GET['hb_code']));
                }
            }
            Request::instance()->redirect('/qwt'.$app.'/'.$url);
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_user_snsapi_tbt($bid,$app,$url){
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();

            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapiuserinfo_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
            Kohana::$log->add("qwt_tbt:sns_userinfo:{$bid}:{$token['openid']}", print_r($userinfo,true));
            Kohana::$log->add("qwt_tbt:sns_token:{$bid}:{$token['openid']}", print_r($token,true));
            // echo '<pre>';
            // var_dump($userinfo);
            // var_dump($token);
            // echo '</pre>';
            // exit();
            // $userinfo2 = $wx->getUserInfo($token['openid']);
            // $userinfo['subscribe'] = $userinfo2['subscribe'];
            // $userinfo['subscribe_time'] = $userinfo2['subscribe_time']?$userinfo2['subscribe_time']:0;
            // var_dump($userinfo);
            // exit;

            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['openid'] = $token['openid'];
            $_SESSION['qwt'.$app]['userinfo'] = $userinfo;


            Request::instance()->redirect('/qwt'.$app.'/'.$url);
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_user_snsapi_mnb($bid,$app,$url){
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();

            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapiuserinfo_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
            Kohana::$log->add("qwt_mnb:sns_userinfo:{$bid}:{$token['openid']}", print_r($userinfo,true));
            Kohana::$log->add("qwt_mnb:sns_token:{$bid}:{$token['openid']}", print_r($token,true));
            // echo '<pre>';
            // var_dump($userinfo);
            // var_dump($token);
            // echo '</pre>';
            // exit();
            // $userinfo2 = $wx->getUserInfo($token['openid']);
            // $userinfo['subscribe'] = $userinfo2['subscribe'];
            // $userinfo['subscribe_time'] = $userinfo2['subscribe_time']?$userinfo2['subscribe_time']:0;
            // var_dump($userinfo);
            // exit;

            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['openid'] = $token['openid'];
            $_SESSION['qwt'.$app]['userinfo'] = $userinfo;


            Request::instance()->redirect('/qwt'.$app.'/'.$url);
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_user_snsapi($bid,$app,$url){
        // echo $_GET['hb_code'].'<br>';
        // exit;
        //统一获取用户信息 一个bid只有一个微信公众号
        $shop = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if(!$shop->id) die('不合法');
        $item = ORM::factory('qwt_item')->where('alias','=',$app)->find();
        if(!$item->id) die('不合法');
        $buy = ORM::factory('qwt_buy')->where('status','=',1)->where('bid','=',$bid)->where('iid','=',$item->id)->find();

        if($buy->id&&$buy->expiretime>time()){

        }else{
            // die('未购买'.$item->name.'应用或关闭');
            die('您的'.$item->name.'应用已到期，请前往yingyong.smfyun.com登陆管理中心：在应用中心-应用开关，关闭对应的应用即可取消本提示；如需继续使用，自行续费即可。');
        }
        if($buy->switch==0){
            die('您的'.$item->name.'应用开关已经关闭');
        }
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $config = ORM::factory('qwt_cfg')->getCfg($bid,1);
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $shop->appid;
        $wx = new Wxoauth($bid,$options);
        $callback = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $split = (strpos($_SERVER['REQUEST_URI'], '?') === false) ? '?' : '&';
        if (!$_GET['callback']) {
            $callback .= $split."callback=1";
            $auth_url = $wx->sns_getOauthRedirect($callback, '', 'snsapi_userinfo');
            header("Location:$auth_url");exit;
        }else{
            $token = $wx->sns_getOauthAccessToken();
            if(!$token['openid']) {
                Kohana::$log->add("qwt_smfyun_snsapiuserinfo_openid:$bid", print_r($token,true).'openid未获取到');
                die('openid未获取到！');
            }
            $userinfo = $wx->getOauthUserinfo($token['access_token'], $token['openid']);
            // echo '<pre>';
            // var_dump($userinfo);
            // var_dump($token);
            // echo '</pre>';
            // exit();
            $userinfo2 = $wx->getUserInfo($token['openid']);
            $userinfo['subscribe'] = $userinfo2['subscribe'];
            $userinfo['subscribe_time'] = $userinfo2['subscribe_time']?$userinfo2['subscribe_time']:0;
            // var_dump($userinfo);
            // exit;

            $_SESSION['qwt'.$app]['bid'] = $bid;
            $_SESSION['qwt'.$app]['userinfo'] = $userinfo;


            Request::instance()->redirect('/qwt'.$app.'/'.$url);
            // echo '<pre>';
            // var_dump($userinfo);
        }
        exit;
    }
    public function action_getjson(){
        $json = file_get_contents("https://www.youzan.com/v2/statcenter/dashboard/data.json");
        echo $json;
    }
    public function action_setmemcache($key,$value){

    }

    public function action_getmemcache($key){
        $mem = Cache::instance('memcache');
        $memcache = $mem->get($key);
        var_dump($memcache);
        exit;
    }
    public function action_delmemcache($key){
        $mem = Cache::instance('memcache');
        $memcache = $mem->delete($key);
        var_dump($memcache);
        $memcache = $mem->get($key);
        var_dump($memcache);
        exit;
    }
    public function action_userinfo($bid,$openid){ //用户信息
        $this->template = 'tpl/blank';
        self::before();
        $wx = new Wxoauth($bid);
        $userinfo = $wx->getUserInfo($openid);
        echo '<pre>';
        echo $userinfo['nickname'];
        echo $userinfo['headimgurl'];
        var_dump($userinfo);
        exit;
    }
    public function action_sendcustommsg($openid){// 客服接口
        $this->template = 'tpl/blank';
        self::before();
        $wx = new Wxoauth(6);
        $url = 'http://jfb.dev.smfyun.com/qwthby/api_ticket/pDt2QjmTpVfGrFc5gCOKYjfCzCLk/6';
        $msg['touser'] = $openid;
        $msg['msgtype'] = 'text';
        $msg['text']['content'] = "<a href=\"".$url."\">点击领取微信卡券</a>";
        var_dump($wx->sendCustomMessage($msg));
        exit;
    }
    public function action_sendtpl(){
        $wx = new Wxoauth(6);
        $tplmsg['touser'] = 'oDt2QjtTeio8l0dBl28SQGhcHSH4';
        $tplmsg['template_id'] = '21wdwa21wafhkawjndkjwf';
        $tplmsg['url'] = 'http://www.baidu.com';
        // echo $tplmsg['url'].'<br>';

        $tplmsg['data']['first']['value'] = '模板消息测试';
        $tplmsg['data']['first']['color'] = '#999999';

        $tplmsg['data']['keyword1']['value'] = '1nnovator';
        $tplmsg['data']['keyword1']['color'] = '#999999';
        $tplmsg['data']['keyword2']['value'] = ''.date('Y-m-d H:i');
        $tplmsg['data']['keyword2']['color'] = '#999999';
        $tplmsg['data']['keyword3']['value'] = '点击打卡';
        $tplmsg['data']['keyword3']['color'] = '#999999';

        $tplmsg['data']['work']['value'] = '亲，快来打卡哦~';
        $tplmsg['data']['work']['color'] = '#999999';

        $tplmsg['data']['remark']['value'] = '连续坚持打卡会有额外的积分奖励，兑换超值奖品~';
        $tplmsg['data']['remark']['color'] = '#999999';
        $wx->sendTemplateMessage1($tplmsg);
    }
    public function action_dcomponent_access_token($appid){// 删除三方平台 ctoken
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$appid;
        $ctoken = $mem->delete($cachename1);
        var_dump($ctoken);
        $cachename2 ='expiretime'.$appid;
        $ctime = $mem->delete($cachename2);
        var_dump($ctime);
        exit;
    }
    public function action_component_access_token($appid){//读取 三方平台 ctoken
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='component_access_token'.$appid;
        $ctoken = $mem->get($cachename1);
        var_dump($ctoken);
        $cachename2 ='expiretime'.$appid;
        $ctime = $mem->get($cachename2);
        echo date('y-m-d H:i:s',$ctime);
        exit;
    }
    public function action_access_token($bid){//读取商户 token
        $wx = new Wxoauth(1);
        $access_token = $wx->get_accesstoken();
        if($access_token){
            echo 'accesstoken:'.$access_token;
        }else{
            echo 'access_token已过期或不存在';
        }
        exit;
    }
    public function action_daccess_token($bid){//删除商户 token
        $this->template = 'tpl/blank';
        self::before();
        $mem = Cache::instance('memcache');
        $cachename1 ='qwt.access_token'.$bid;
        $ctoken = $mem->delete($cachename1);
        var_dump($ctoken);
        exit;
    }
}
