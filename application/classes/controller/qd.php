<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qd extends Controller_Base {
    public $template = 'tpl/blank';
    var $appid = 'wxd3a678cfeb03e3a3';
    var $appsecret = '661fb2647a804e14ded1f65fad682695';
    var $baseurl = 'http://game.smfyun.com/qd/';
    var $we;
    var $qid;
    var $endtime= "2016-12-25 00:00:00";
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');

        Database::$default = "qd";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        $this->qid=$_SESSION['qid'];
        if (Request::instance()->action == 'storefuop') return;
        if (Request::instance()->action == 'getopenid') return;
        // if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false ) die('请通过微信访问。');
    }
    public function action_storefuop($day){// 静默授权
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/qd/getopenid/';
        $wx['appid']=$this->appid;
        $wx['appsecret']=$this->appsecret;
        $this->we = $we = new Wechat($wx);
        $auth_url = $we->getOauthRedirect($callback_url, '', 'snsapi_base');
        header("Location:$auth_url");
        exit;
    }
    public function action_getopenid(){//通过code获取openid
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $wx['appid']=$this->appid;
        $wx['appsecret']=$this->appsecret;
        $this->we = $we = new Wechat($wx);
        $token = $this->we->getOauthAccessToken();
        $openid=$token['openid'];
        //echo $openid.'<br>';
        $user = ORM::factory('qd_qrcode')->where('openid','=',$openid)->find();
        $user->openid = $openid;
        $user->save();
        Request::instance()->redirect('/qd/commends/'.$openid);
    }
    public function action_commends($openid){
        $wx['appid']=$this->appid;
        $wx['appsecret']=$this->appsecret;
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = $we = new Wechat($wx);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $ƒ = $we->getJsSign($callback_url);
        $this->qid=$qid=ORM::factory('qd_qrcode')->where('openid','=',$openid)->find()->id;
        $_SESSION['qid'] = $qid;
        $num_time=ORM::factory('qd_score')->where('qid','=',$qid)->where('status','=',1)->count_all();
        $time_name=$num_time+1;
        $order=ORM::factory('qd_order')->where('qid','=',$qid)->find();
        $flag=0;
        if(time()>strtotime($this->endtime)){
            $flag=6;//活动时间已结束
            die('活动已结束');
        }else{
            if($num_time==15){
                $flag=5;//任务已完成
                $time=ORM::factory('qd_score')->where('qid','=',$qid)->where('status','=',1)->order_by('time', 'DESC')->find()->time;
                if(date('Ymd',$time)==date('Ymd',time())){
                   $time_name=15;
                }else{
                    $results= DB::query(Database::UPDATE,"UPDATE  qd_scores set `status` = 0  where `qid` = $qid")->execute();
                   $time_name=1;
                }
            }else{
                $socres=ORM::factory('qd_score')->where('qid','=',$qid)->order_by('time', 'DESC')->find();
                if($socres->id){
                    //echo $socres->id."<br>";
                    $time=ORM::factory('qd_score')->where('qid','=',$qid)->where('status','=',1)->order_by('time', 'DESC')->find()->time;
                    if(date('Ymd',$time)==date('Ymd',strtotime("-1days"))){
                        $flag=1;//正常持续签到
                    }elseif(date('Ymd',$time)==date('Ymd',time())){
                        $flag=2;//今天已签到

                        $time_name=$time_name-1;
                        //echo $time_name."<br>";
                    }else{
                        $results= DB::query(Database::UPDATE,"UPDATE  qd_scores set `status` = 0  where `qid` = $qid")->execute();
                        $flag=3;//签到已经中断，任务结束
                        $time_name=1;
                    }
                }else{
                    $flag=4;//第一次签到
                }
            }

        }
        $view = "weixin/qd/start";
        $this->template->content = View::factory($view)
            ->bind('day',$time_name)
            ->bind('signPackage',$signPackage)
            ->bind('flag',$flag);
        if($_POST['name']){
            $time1=ORM::factory('qd_score')->where('qid','=',$qid)->order_by('time', 'DESC')->find()->time;
            if(!$time1||date('Ymd',$time1)!=date('Ymd',time())){
                $scores=ORM::factory('qd_score');
                $scores->qid=$qid;
                $scores->status=1;
                $scores->time=time();
                $nums=ORM::factory('qd_score')->where('qid','=',$qid)->where('status','=',1)->count_all();
                $score=ORM::factory('qd_qrcode')->where('id','=',$qid)->find()->score;
                $qrcode=ORM::factory('qd_qrcode')->where('id','=',$qid)->find();
                if($nums==4){
                    $qrcode->score =$score+50;
                    $scores->score=50;
                }elseif ($nums==9) {
                    $qrcode->score =$score+100;
                    $scores->score=100;
                }elseif ($nums==14) {
                    $qrcode->score =$score+150;
                    $scores->score=150;
                }
                $qrcode->save();
                $scores->save();
            }
            $result['name']=$_POST['name'];
            $result['time_name']=$time_name;
            $result['qid']=$qid;
            $view = "weixin/qd/end";
            $this->template->content = View::factory($view)
                ->bind('result',$result)
                ->bind('signPackage',$signPackage);
        }

    }
    public function action_test($num){
        $wx['appid']=$this->appid;
        $wx['appsecret']=$this->appsecret;
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = $we = new Wechat($wx);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_url);
        $this->qid=$qid=1;
        $_SESSION['qid'] = $qid;
        $time_name=$num;
        $order=ORM::factory('qd_order')->where('qid','=',$qid)->find();
        $flag=1;
        $view = "weixin/qd/start";
        $this->template->content = View::factory($view)
            ->bind('day',$time_name)
            ->bind('signPackage',$signPackage)
            ->bind('flag',$flag);
        if($_POST['name']){
            $time1=ORM::factory('qd_score')->where('qid','=',$qid)->order_by('time', 'DESC')->find()->time;
            $result['name']=$_POST['name'];
            $result['time_name']=$time_name;
            $result['qid']=$qid;
            $view = "weixin/qd/end";
            $this->template->content = View::factory($view)
                ->bind('result',$result)
                ->bind('signPackage',$signPackage);
        }
    }
    public function action_himage($name,$time_name){
        $src = DOCROOT."qd/bg/bg{$time_name}_2.jpg";
        $text=$name;
        $size =95;
        $top =1860;
        $left =1550;
        if($time_name==1){
            $top =1750;
            $left =1625;
        }elseif($time_name==2){
            $top =1840;
            $left =1615;
        }elseif($time_name==3){
            $top =1965;
            $left =1615;
        }elseif($time_name==4){
            $top =1760;
            $left =1600;
        }elseif($time_name==5){
            $top =1760;
            $left =1600;
        }elseif($time_name==6){
            $top =1860;
            $left =1550;
        }elseif($time_name==7){
            $top =1775;
            $left =1595;
        }elseif($time_name==8){
            $top =1775;
            $left =1595;
        }elseif($time_name==9){
            $top =1800;
            $left =1600;
        }elseif($time_name==10){
            $top =1880;
            $left =1600;
        }elseif($time_name==11){
            $top =1775;
            $left =1550;
        }elseif($time_name==12){
            $top =1775;
            $left =1615;
        }elseif($time_name==13){
            $top =1975;
            $left =1615;
        }elseif($time_name==14){
            $top =1860;
            $left =1600;
        }elseif($time_name==15){
            $top =2000;
            $left =1600;
        }
        $im = imagecreatefromjpeg($src);//选择图片
        $font = DOCROOT."qd/dist/msyh.ttf";//选择字体
        $color = @imagecolorallocate($im, 0,0,0);
        header('Content-Type: image/jpeg');
        imagettftext($im, $size, 0, $left, $top, $color, $font , $text);//将文字和图片合成
        imagettftext($im, $size, 0, $left+1, $top+1, $color, $font , $text);//将文字加粗
        imagejpeg($im, NULL , 50);//缓存到文件
        imagedestroy($im);
    }
    public function action_days(){
        $qid=$this->qid;
        $days=ORM::factory('qd_score')->where('qid','=',$qid)->order_by('time','ASC')->find_all();
        // echo $qid."<br>";
        // echo "<pre>";
        // var_dump($days);
        // echo "</pre>";
        $day=ORM::factory('qd_score')->where('qid','=',$qid)->count_all();
        $user = ORM::factory('qd_qrcode')->where('id','=',$qid)->find();
        $view = "weixin/qd/days";
        $this->template->content = View::factory($view)->bind('days',$days)->bind('day',$day)->bind('user',$user);
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qd_$type";

        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_prize(){
        $wx['appid']=$this->appid;
        $wx['appsecret']=$this->appsecret;
        require Kohana::find_file('vendor', 'weixin/wechat.class');
        $this->we = $we = new Wechat($wx);
        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if (isset($_GET['url'])) $callback_url = urldecode($_GET['url']);
        $signPackage = $we->getJsSign($callback_url);
        $qid=$this->qid;
        //echo $qid."<br>";
        $has=0;
        $day=ORM::factory('qd_score')->where('qid','=',$qid)->count_all();
        $order=ORM::factory('qd_order');
        $items=ORM::factory('qd_item');
        // $items-> $items->reset(FALSE);
        $user = ORM::factory('qd_qrcode')->where('id','=',$qid)->find();
        if($_POST['form']['name']&&$_POST['form']['tel']&&$_POST['form']['address']){
            $num=ORM::factory('qd_order')->where('iid','=',$_POST['form']['type'])->count_all();
            $item=ORM::factory('qd_item')->where('id','=',$_POST['form']['type'])->find();
            if($num<$item->stock){
                $order->qid=$qid;
                $order->iid=$_POST['form']['type'];
                $order->name=$_POST['form']['name'];
                $order->tel=$_POST['form']['tel'];
                $order->address=$_POST['form']['address'];
                $order->memo=$_POST['form']['memo'];
                $order->score=$item->score;
                $order->save();
                $user->score = $user->score-$item->score;
                $user->save();
            }
        }
        $result['items'] = $items->order_by('id', 'ASC')->find_all();

        $view = "weixin/qd/prize";
        $this->template->content = View::factory($view)
            ->bind('user',$user)
            ->bind('result',$result)
            ->bind('day',$day)
            ->bind('signPackage',$signPackage);
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
}
