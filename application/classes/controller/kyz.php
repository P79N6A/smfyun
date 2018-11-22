<?php defined('SYSPATH') or die('No direct script access.');
require_once DOCROOT.'../application/vendor/wanxiang_api_v2.0/index.php';
// require_once DOCROOT.'../application/vendor/aliyun-oss-php-sdk/autoload.php';
use QcloudImage\CIClient;
// 1nnovator  10:19:08
// xiaochengxu@smfyun.com Www19910523
// wxbc550991f98c2c7b
// f5fb50132783f898ceb3aaef37c5bc2d
class Controller_kyz extends Controller_Base{
  public $template = 'tpl/blank';
  public $appkey = '710ZbEVK0yS5ioMP';//腾讯ai加速器
  public $app_id = '1106636107';
  public $api;
  public $charge = 0.02;
  public function before() {
      Database::$default = "wdy";
      parent::before();
      $this->api = Model::factory('bnk_api');
  }
  public function action_face_compare($path,$oid){
      // 图片base64编码
      $path   = $path1;
      $data   = file_get_contents($path);
      $base64a = base64_encode($data);

      $path   = $path2;
      $data   = file_get_contents($path);
      $base64b = base64_encode($data);
      // 设置请求数据
      $appkey = $this->appkey;
      $params = array(
          'app_id'     => $this->app_id,
          'image_a'    => $base64a,
          'image_b'    => $base64b,
          'time_stamp' => strval(time()),
          'nonce_str'  => strval(rand()),
          'sign'       => '',
      );
      $params['sign'] = $this->api->getReqSign($params, $appkey);

      // 执行API调用
      $url = 'https://api.ai.qq.com/fcgi-bin/face/face_facecompare';
      $response = $this->api->doHttpPost($url, $params);
      $arr = json_decode($response,true);
      echo $arr['data']['similarity'];
  }
  public function action_porn_test(){

      $path   = DOCROOT.'bnk/img/11.jpg';
      $data   = file_get_contents($path);
      $base64 = base64_encode($data);

      // 设置请求数据
      $appkey = $this->appkey;
      $params = array(
          'app_id'     => $this->app_id,
          'image'      => $base64,
          'time_stamp' => strval(time()),
          'nonce_str'  => strval(rand()),
          'sign'       => '',
      );
      $params['sign'] = $this->api->getReqSign($params, $appkey);

      // 执行API调用
      $url = 'https://api.ai.qq.com/fcgi-bin/vision/vision_porn';
      $response = $this->api->doHttpPost($url, $params);
      $arr = json_decode($response,true);
      echo '<pre>';
      var_dump($arr);
  }
  public function action_image($type='item', $id=1, $cksum='') {
      $field = 'image';
      $table = "bnk_$type";
      $pic = ORM::factory($table, $id)->image;
      if (!$pic) die('404 Not Found!');
      header("Content-Type: image/jpeg");
      header("Content-Length: ".strlen($pic));
      echo $pic;
      exit;
  }
  public function action_report(){
     // $_GET['openid'];
     // $_GET['oid'];
     // $_GET['tel'];
     // $_GET['type'];
     $user = ORM::factory('bnk_qrcode')->where('openid','=',$_GET['openid'])->find();
     $report = ORM::factory('bnk_report');
     $report->qid = $user->id;
     $report->tel = $_GET['tel'];
     $report->oid = $_GET['oid'];
     $report->type = $_GET['type'];
     $report->save();
     $count_all = ORM::factory('bnk_report')->where('oid','=',$_GET['oid'])->count_all();
     $order = ORM::factory('bnk_order')->where('id','=',$_GET['oid'])->find();
     $order->ts_num = $count_all;
     $order->save();
     $result['conent'] = '提交成功！';
     echo json_encode($result);
  }
  public function action_hb_get_list($openid){
    $user = ORM::factory('bnk_qrcode')->where('openid','=',$openid)->find();
    $scores = ORM::factory('bnk_score')->where('qid','=',$user->id)->where('type','=',3)->order_by('id','desc')->find_all();
    foreach ($scores as $k => $v) {
      $result['hb_get'][$k]['avatarUrl'] = $v->order->qrcode->avatarUrl;
      $result['hb_get'][$k]['nickName'] = $v->order->qrcode->nickName;
      $result['hb_get'][$k]['money'] = $v->score;
      $result['hb_get'][$k]['time'] = date('Y-m-d H:i:s',$v->createdtime);
      $result['hb_get'][$k]['id'] = $v->oid;
      $result['hb_get'][$k]['image'] = 'http://'.$_SERVER['HTTP_HOST'].'/bnk/image/order/'.$v->order->id.'?v='.time();
    }
    $own_money = DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from bnk_scores where qid = $user->id and type=3")->execute()->as_array();
    $result['num'] = ORM::factory('bnk_score')->where('qid','=',$user->id)->where('type','=',3)->count_all();
    $result['money'] = number_format($own_money[0]['ownmoney'],2);
    echo json_encode($result);
    exit;
  }
  public function action_hb_send_list($openid){
    $user = ORM::factory('bnk_qrcode')->where('openid','=',$openid)->find();

    $orders = ORM::factory('bnk_order')->where('qid','=',$user->id)->where('status','in',array(1,2,3,4))->order_by('id','desc')->find_all();
    foreach ($orders as $k => $v) {
      // $result['hb_send'][$k]['avatarUrl'] = $v->qrcode->avatarUrl;
      // $result['hb_send'][$k]['nickName'] = $v->qrcode->nickName;
      $result['hb_send'][$k]['used_num'] = $v->used_num;
      $result['hb_send'][$k]['num'] = $v->num;
      $result['hb_send'][$k]['money'] = $v->money;
      $result['hb_send'][$k]['time'] = date('Y-m-d H:i:s',$v->createdtime);
      $result['hb_send'][$k]['id'] = $v->id;
      $result['hb_send'][$k]['image'] = 'http://'.$_SERVER['HTTP_HOST'].'/bnk/image/order/'.$v->id.'?v='.time();
    }
    $own_money = DB::query(Database::SELECT,"SELECT SUM(money) as ownmoney from bnk_orders where qid = $user->id ")->execute()->as_array();
    $result['avatarUrl'] = $user->avatarUrl;
    $result['nickName'] = $user->nickName;
    $result['num'] = ORM::factory('bnk_order')->where('qid','=',$user->id)->where('status','in',array(1,2,3,4))->count_all();
    $result['money'] = number_format($own_money[0]['ownmoney'],2);
    echo json_encode($result);
    exit;
  }
  public function action_comment($tid){
      $postStr = file_get_contents("php://input");
      // kohana::$log->add('partypost',print_r($postStr,true));
      kohana::$log->add('papost',print_r($postStr,true));
      $_POST=json_decode($postStr,true);
      $trade = ORM::factory('bnk_trade')->where('id','=',$tid)->find();
      $trade->comment = $_POST['comment'];
      $trade->save();
  }
  public function action_hb_detail($oid,$openid){
      $order = ORM::factory('bnk_order')->where('id','=',$oid)->find();
      if($order->id){
        $result['order'] = $order->as_array();
        $result['order']['nickName'] = $order->qrcode->nickName;
        $result['order']['avatarUrl'] = $order->qrcode->avatarUrl;
        $result['order']['image'] = 'http://'.$_SERVER['HTTP_HOST'].'/bnk/image/order/'.$order->id.'?v='.time();
        $result['order']['hasin'] = 0;//
        if($order->qrcode->openid == $openid){
          $result['order']['isone'] = 1;//是一个人
        }else{
          $result['order']['isone'] = 0;//不是一个人
        }
        $trades = ORM::factory('bnk_trade')->where('oid','=',$oid)->order_by('id','desc')->find_all()->as_array();
        foreach ($trades as $k => $v) {
            $result['trades'][$k]['image'] = 'http://'.$_SERVER['HTTP_HOST'].'/bnk/image/trade/'.$v->id.'?v='.time();
            $result['trades'][$k]['nickName'] = $v->qrcode->nickName;
            $result['trades'][$k]['avatarUrl'] = $v->qrcode->avatarUrl;
            $result['trades'][$k]['sex'] = $v->qrcode->sex;
            $result['trades'][$k]['money'] = $v->money;
            $result['trades'][$k]['openid'] = $v->qrcode->openid;
            $result['trades'][$k]['id'] = $v->id;
            $result['trades'][$k]['comment'] = $v->comment;
            $result['trades'][$k]['time'] = date('H:i',$v->createdtime);
            if($v->qrcode->openid == $openid){//已经参与过了。
              $result['order']['hasin'] = 1;//已经参与过了
              $result['order']['inmoney'] = $v->money;//已经参与过了
            }
        }
      }else{
        $result['error'] = 'id错误！';
      }
      echo json_encode($result);
  }
  public function action_api_face_detect($pic){
    // //单个文件file
    $appid = '1256052569';
    $secretId = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
    $secretKey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';
    // $bucket = 'YOUR_BUCKET';
    $client = new CIClient($appid, $secretId, $secretKey, $bucket);
    $client->setTimeout(30);
    // var_dump ($client->faceCompare(array('file'=>DOCROOT."bnk/img/a_2.jpg"), array('file'=>DOCROOT."bnk/img/b.jpg")));
    echo '<pre>';
    var_dump ($client->faceDetect(array('file'=>DOCROOT."bnk/img/".$pic.".jpg"),0));
    // //单个文件内容
    // var_dump ($client->faceIdentify('group11', array('buffer'=>file_get_contents('F:\pic\yang3.jpg'))));
  }
  public function action_api_face($pic1,$pic2){
    // //单个文件file
    $appid = '1256052569';
    $secretId = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
    $secretKey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';
    // $bucket = 'YOUR_BUCKET';
    $client = new CIClient($appid, $secretId, $secretKey, $bucket);
    $client->setTimeout(30);
    var_dump ($client->faceCompare(array('file'=>DOCROOT."bnk/img/".$pic1.".jpg"), array('file'=>DOCROOT."bnk/img/".$pic2.".jpg")));
    // var_dump ($client->faceDetect(array('file'=>DOCROOT."bnk/img/mutil.jpg"),0));
    // //单个文件内容
    // var_dump ($client->faceIdentify('group11', array('buffer'=>file_get_contents('F:\pic\yang3.jpg'))));
  }
  public function action_api_face_compare(){
      $appid = '1256052569';
      $secretId = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
      $secretKey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';
      // $bucket = 'YOUR_BUCKET';
      $client = new CIClient($appid, $secretId, $secretKey, $bucket);

      $client->setTimeout(30);
      $order = ORM::factory('bnk_order')->where('id','=',$_POST['oid'])->find();
      if($order->used_num>=$order->num) $result['error'] = '该口令已经被抢完啦！';
      if($order->money - $order->used_money<=0) $result['error'] = '该口令已经没有赏金了，下次再试试！';
      if($order->status!=2){
          switch ($order->status) {
            case 0:
              $result['error'] = '该红包状态异常！';
              break;
            case 1:
              $result['error'] = '该红包已经抢完了';
              break;
            case 3:
              $result['error'] = '该活动时间已经结束';
              break;
            case 4:
              $result['error'] = '该红包异常，人为结束';
              break;
            case 5:
              $result['error'] = '该红包还没有充入金额';
              break;
            default:
              # code...
              break;
          }
      }
      if($result['error']){
        echo json_encode($result);
        exit;
      }
      if($_POST['openid']){
        if($_FILES){
            kohana::$log->add('filepost',print_r($_FILES,true));
            $_POST['image']=file_get_contents($_FILES['addimg']['tmp_name']);
            $img = DOCROOT."bnk/img/".$_POST['openid'].'v'.time().".jpg";
            umask(0002);
            @mkdir(dirname($img),0777,true);
            @file_put_contents($img,$_POST['image']);
            $porn_json = $client->pornDetect(array('files'=>array($img)));
            $porn_arr = json_decode($porn_json,true);

            @unlink($img);
            $face_json = $client->faceDetect(array('buffer'=>$_POST['image']),0);
            $face_arr = json_decode($face_json,true);
            if(!$face_arr['data']['face'][0]){
                $result['error'] = '图片中没有包含人像，请重新上传单个人像的照片';
                echo json_encode($result);
                exit;
            }
            if($face_arr['data']['face'][1]){
                $result['error'] = '图片中包含多个人像，请重新上传单个人像的照片';
                echo json_encode($result);
                exit;
            }
            if($porn_arr['result_list'][0]['data']['porn_score']>60){
                $result['error'] = '图片不健康，请重新上传。';
            }else{
                //比较图片相似度
                // $order = ORM::factory('bnk_order')->where('id','=',$_POST['oid'])->find();
                //跟以前图片对比
                $trades = ORM::factory('bnk_trade')->where('oid','=',$order->id)->find_all();
                foreach ($trades as $k => $v) {
                  $compare_json = $client->faceCompare( array('buffer'=>$_POST['image']), array('buffer'=>$v->image));
                  $compare_arr = json_decode($compare_json,true);
                  if($compare_arr['data']['similarity'] == 100){
                    $a = 0;
                    //角度 容错率 剔除 美颜造成的偏差
                    if(abs($face_arr['data']['face'][0]['pitch']-$v->pitch)>=3){
                      $a++;
                    }
                    if(abs($face_arr['data']['face'][0]['yaw']-$v->yaw)>=3){
                      $a++;
                    }
                    if(abs($face_arr['data']['face'][0]['roll']-$v->roll)>=3){
                      $a++;
                    }
                    kohana::$log->add('face_arr'.$order->id,print_r($face_arr,true));


                    if($a>0){

                    }else{
                      if($face_arr['data']['face'][0]['beauty']==$v->beauty){//魅力值相等 肯定是同一张 剔除裁剪
                          $result['error'] = '和已上传的照片重复，请重新上传！!';
                          echo json_encode($result);
                          exit;
                      }
                      $result['error'] = '和已上传的照片重复，请重新上传！';
                      echo json_encode($result);
                      exit;
                    }
                  }
                }

                $compare_json = $client->faceCompare( array('buffer'=>$_POST['image']), array('buffer'=>$order->image));
                $compare_arr = json_decode($compare_json,true);
                if($compare_arr['data']['similarity']==100){
                  $a = 0;
                  //角度 容错率 剔除 美颜造成的偏差
                  if(abs($face_arr['data']['face'][0]['pitch']-$order->pitch)>=3){
                    $a++;
                  }
                  if(abs($face_arr['data']['face'][0]['yaw']-$order->yaw)>=3){
                    $a++;
                  }
                  if(abs($face_arr['data']['face'][0]['roll']-$order->roll)>=3){
                    $a++;
                  }
                  kohana::$log->add('face_arr2'.$order->id,print_r($face_arr,true));

                  if($a>0){

                  }else{
                      if($face_arr['data']['face'][0]['beauty']==$order->beauty){//魅力值相等 肯定是同一张 剔除裁剪
                          $result['error'] = '和照片口令重复，请重新上传！!';
                          echo json_encode($result);
                          exit;
                      }
                      $result['error'] = '和照片口令重复，请重新上传！';
                      echo json_encode($result);
                      exit;
                  }
                }
                if($compare_arr['data']['similarity']<80){
                  $result['error'] = '照片匹配不成功,请重新上传！';
                }
                if($compare_arr['data']['similarity']<=100&&$compare_arr['data']['similarity']>=80){
                  $lessmoney = ($order->money - $order->used_money)*100;
                  $lessnum = $order->num - $order->used_num;
                  $money = $this->action_rand_hb($lessmoney,$lessnum);

                  $user = ORM::factory('bnk_qrcode')->where('openid','=',$_POST['openid'])->find();

                  $trade = ORM::factory('bnk_trade');
                  $trade->qid = $user->id;
                  $trade->oid = $order->id;
                  $trade->money = $money/100;
                  $trade->image = $_POST['image'];

                  $trade->pitch = $face_arr['data']['face'][0]['pitch'];
                  $trade->yaw = $face_arr['data']['face'][0]['yaw'];
                  $trade->roll = $face_arr['data']['face'][0]['roll'];
                  $trade->beauty = $face_arr['data']['face'][0]['beauty'];

                  $trade->save();

                  $order->used_num = $order->used_num+1;
                  if($order->used_num == $order->num){
                      $order->status = 1;
                  }
                  $order->used_money = $order->used_money+$money/100;
                  $order->used_fee = $order->used_money*$this->charge;
                  $order->save();

                  $score = ORM::factory('bnk_score');
                  $score->scoreIn($user,3,$money/100,$order->id,$trade->id);

                  $result['success'] = '图片识别成功，恭喜，获得'.($money/100).'元';
                  $result['code'] = 'success';
                }
            }
        }
      }
      $result['similarity'] = $compare_arr;
      echo json_encode($result);
      exit;
  }
  public function action_api_porn_test(){
      $appid = '1256052569';
      $secretId = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
      $secretKey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';
      // $bucket = 'YOUR_BUCKET';

      $client = new CIClient($appid, $secretId, $secretKey, $bucket);
      $client->setTimeout(30);
      var_dump ($client->pornDetect(array('files'=>array(DOCROOT.'bnk/img/11.jpg',DOCROOT.'bnk/img/a1.jpg'))));
  }
  public function action_imgurl(){
      $pic = file_get_contents(DOCROOT.'bnk/img/11.jpg');
      header("Content-Type: image/jpeg");
      echo $pic;
      exit;
  }
  //剩余金额，剩余数量 保证每个都大于100分。
  public function action_rand_hb($lessmoney,$lessnum){
      if($lessnum==1){
          $money = $lessmoney;
      }else{
          $can_rand = floor(($lessmoney - $lessnum*100)*2/$lessnum);
          $money = rand(100,100+$can_rand);
      }
      return $money;
  }
  public function action_new($openid){
      $user = ORM::factory('bnk_qrcode')->where('openid','=',$openid)->find();
      $ownmoney=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from bnk_scores where qid = $user->id ")->execute()->as_array();
      $ownmoney=$ownmoney[0]['ownmoney'];
      $result['ownmoney'] = $ownmoney?$ownmoney:0.00;
      echo json_encode($result);
      exit;
  }
  public function action_pay_check(){
      $postStr = file_get_contents("php://input");
      $_POST=json_decode($postStr,true);
      $true_money = $_POST['true_money'];
      $_POST['all_money'] = $_POST['allmoney'];
      $_POST['fee'] = $_POST['allmoney']-$_POST['money'];
      $order = ORM::factory('bnk_order')->where('id','=',$_POST['pid'])->find();
      $order->all_money = $_POST['allmoney'];
      $order->fee = $_POST['fee'];
      $order->money = $_POST['money'];
      $order->status = 2;
      $order->save();
      $score = ORM::factory('bnk_score');

      $qrcode = ORM::factory('bnk_qrcode')->where('id','=',$order->qid)->find();

      $result['content'] = '图片口令生成成功';
      $result['pid'] = $order->id;

      if($true_money==0){//实付金额为0 全部走余额

      }else{
        $score->scoreIn($qrcode,1,$_POST['true_money']/100,$order->id);
        $score = ORM::factory('bnk_score');
      }
      $score->scoreOut($qrcode,2,$_POST['allmoney'],$order->id);

      echo json_encode($result);
      exit;
  }
  public function action_party(){
      $appid = '1256052569';
      $secretId = 'AKIDGTtCLsQs9JI5lGbzAD6871n8LNEumkCb';
      $secretKey = 'QfjbHp7JMH1bGl8KK8lDZq5A5pNqtsiL';
      // $bucket = 'YOUR_BUCKET';
      $client = new CIClient($appid, $secretId, $secretKey, $bucket);
      $client->setTimeout(30);

      $postStr = file_get_contents("php://input");
      // kohana::$log->add('partypost',print_r($postStr,true));
      kohana::$log->add('papost',print_r($_POST,true));
      if($_POST||$postStr){
        //$result['type'] = 'hasimg';
        if(!$_POST){
          $_POST=json_decode($postStr,true);
          //$result['type'] = 'noimg';
        }
        ///$result1=json_decode($postStr,true);
        kohana::$log->add('papost',print_r($_POST,true));
        $qrcode=ORM::factory('bnk_qrcode')->where('openid','=',$_POST['openid'])->find();
        if($qrcode->id){
            $order=ORM::factory('bnk_order');
            $_POST['qid']=$qrcode->id;
            if($_FILES){
                kohana::$log->add('filepost',print_r($_FILES,true));
                $_POST['image']=file_get_contents($_FILES['addimg']['tmp_name']);
                $img = DOCROOT."bnk/img/".$_POST['openid'].'v'.time().".jpg";
                umask(0002);
                @mkdir(dirname($img),0777,true);
                @file_put_contents($img,$_POST['image']);
                $pron_json = $client->pornDetect(array('files'=>array($img)));
                $arr = json_decode($pron_json,true);
                @unlink($localfile);
                if($arr['result_list'][0]['data']['porn_score']>60){
                    $result['error'] = '图片不健康，请重新上传。';
                    // echo json_encode($result);
                    // exit;
                }
            }

            $face_json = $client->faceDetect(array('buffer'=>$_POST['image']),0);
            $face_arr = json_decode($face_json,true);
            if($face_arr['data']['face'][1]){
                $result['error'] = '图片中包含多个人像，请重新上传单个人像的照片';
                // echo json_encode($result);
            }
            if(!$face_arr['data']['face'][0]){
                $result['error'] = '图片中没有包含人像，请重新上传单个人像的照片';
                // echo json_encode($result);
            }
            $_POST['pitch'] = $face_arr['data']['face'][0]['pitch'];
            $_POST['yaw'] = $face_arr['data']['face'][0]['yaw'];
            $_POST['roll'] = $face_arr['data']['face'][0]['roll'];
            $_POST['beauty'] = $face_arr['data']['face'][0]['beauty'];

            if(!$result['error']){
                $order->values($_POST);
                $order->save();
                $result['code'] = 'success';
                $result['pid'] = $order->id;
            }

        }else{
          $result['error'] = 'nopeople';
        }
        echo json_encode($result);
        exit;
      }
  }
  public function action_onLogin(){
      if($_GET['code']){
        $charge = $this->charge;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wxbc550991f98c2c7b&secret=f5fb50132783f898ceb3aaef37c5bc2d&js_code=".$_GET['code']."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output,true);
        kohana::$log->add('bnkonLogin',print_r($result,true));
        //kohana::$log->add('lopost',print_r($result1,true));
        // var_dump($result);
        // echo $output;
        $user = ORM::factory('bnk_qrcode')->where('openid','=',$result['openid'])->find();
        if($user->id){
          $result['hasin'] = 'yes';//存在表中
        }else{
          $result['hasin'] = 'no';//不存在该用户
        }
        $result['charge'] = $charge;
        echo json_encode($result);
        exit;
      }
  }
  public function action_qrcode(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('bnk_qrpost1',print_r($postStr,true));
      // if($_POST){
      //   kohana::$log->add('qrpost2s',print_r($_POST,true));
      // }
      if($postStr){
        $result1=json_decode($postStr,true);
        kohana::$log->add('bnk_qrpost',print_r($result1,true));
        $qrcode=ORM::factory('bnk_qrcode')->where('openid','=',$result1['openid'])->find();
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
      require_once Kohana::find_file('vendor/lib', 'WeixinPay');
      $appid='wxbc550991f98c2c7b';
      $openid= $_GET['openid'];
      $user = ORM::factory('bnk_qrcode')->where('openid','=',$_GET['openid'])->find();
      $mch_id='1229635702';
      $key='vdY25BlR1U58kBDiuJ1DFHPgldXnOkD6';
      $out_trade_no = $mch_id. time();
      $total_fee = $_GET['fee'];
      if($user->score>=$_GET['fee']){
          $result['status'] = 'success';
          $result['true_money'] = 0;
          echo json_encode($result);
      }else{
          $body = "充值余额";
          $total_fee = (($total_fee-$user->score)*100);
          $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
          $return=$weixinpay->pay();
          $return['true_money'] = $total_fee;//实付金额。分
          $return['status'] = 'fail';
          echo json_encode($return);
      }
      exit;
  }
  public function action_qr($oid){
      $order = ORM::factory('bnk_order')->where('id','=',$oid)->find();
      $user = ORM::factory('bnk_qrcode')->where('id','=',$order->qid)->find();
      // $order->image;

      $wx['appid']='wxbc550991f98c2c7b';
      $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d';
      require_once Kohana::find_file('vendor', 'weixin/wechat.class');
      $we = new wechat($wx);
      // $color['r'] = '42';
      // $color['g'] = '163';
      // $color['b'] = '68';
      // $color_ob = (object)$color;
      // var_dump($color_ob);
      $qr = $we->getxcx_qr('pid='.$order->id,'pages/home/dashboard/index');//最小280
      // 图片1
      $path_1 = 'http://'.$_SERVER['HTTP_HOST'].'/bnk/img/postertpl.png';
      $tmpdir = '/dev/shm/';
      $md5 = md5(time().rand(1,100000));
      $path_2 = "{$tmpdir}$md5.jpg";
      file_put_contents($path_2, $qr);
      // 图片二
      // 创建图片对象
      $image_1 = imagecreatefrompng($path_1);
      $image_2 = imagecreatefromjpeg($path_2);
      // 合成图片
      imagecopyresampled($image_1, $image_2, 380, 720, 0, 0, 200,200,imagesx($image_2), imagesy($image_2));

      $tmpdir = '/dev/shm/';
      $md5 = md5(time().rand(1,100000));
      $tpl = "{$tmpdir}$md5.jpg";
      file_put_contents($tpl, $order->image);

      $tmpdir = '/dev/shm/';
      $md5 = md5(time().rand(1,100000));
      $head = "{$tmpdir}$md5.jpg";
      file_put_contents($head, $this->curls($user->avatarUrl));

      $image_head = imagecreatefromjpeg($head);
      $image_tpl = imagecreatefromjpeg($tpl);

      imagecopyresampled($image_1, $image_head, 400, 10, 0, 0, 150,150,imagesx($image_head),imagesy($image_head));
      imagecopyresampled($image_1, $image_tpl, 325, 240, 0, 0, 320,450,imagesx($image_tpl), imagesy($image_tpl));
      // echo $image_1;
      header("Content-Type: image/png");
      imagepng($image_1);
      @unlink($path_2);
      @unlink($image_head);
      @unlink($image_tpl);
      exit;
  }
  public function action_sendMoney() {

        $openid= $_GET['openid'];
        $money1= $_GET['money'];
        $qrcode=ORM::factory('bnk_qrcode')->where('openid','=',$openid)->find();
        $qid=$qrcode->id;
        $time=time();
        $ownmoney=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from bnk_scores where qid = $qid ")->execute()->as_array();
        $ownmoney=$ownmoney[0]['ownmoney'];
        // $fee=0.1;
        // if($ownmoney>=5) $fee=$ownmoney*$this->charge/100;
        // $money1=$ownmoney;
        $count = ORM::factory('bnk_score')->where('type','=',5)->where('qid','=',$qid)->where('createdtime','>',strtotime(date("Y-m-d"),time()))->count_all();

        if($count>=3){
          $result1['state']='FAIL';
          $result1['code']='当日提现次数已达到最大次数三次，请明日再提现。';
          echo json_encode($result1);
          exit();
        }

        if($money1>$ownmoney){
          $result1['state']='FAIL';
          $result1['code']='提现金额大于余额';
          echo json_encode($result1);
          exit();
        }
        if($money1<1){
          $result1['state']='FAIL';
          $result1['code']='提现金额不能少于一元';
          echo json_encode($result1);
          exit();
        }
        $config = ORM::factory('bnk_cfg')->getCfg(1,1);
        if($config['send_self']==2){//1 自主提现 2申请提现
          ORM::factory('bnk_score')->scoreOut($qrcode,5,$money1,0,0,2);//审核中
          $result1['state']='WAITING';
          $result1['code']='提现申请成功，请耐心等候提现结果。';
          echo json_encode($result1);
          exit();
        }
        $money=100*$money1;
        //$openid='oOkHq0HqPpbn_xS6tzssaGW8TQpI';
        $wx['appid']='wxbc550991f98c2c7b';
        $wx['appsecret']='f5fb50132783f898ceb3aaef37c5bc2d';
        $key='wsYukM8knEzFeMtquv4L2jbO5g8fhFMo';
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($wx);
        $mch_id='1425138702';
        $mch_billno = $mch_id. date('YmdHis').rand(1000, 9999); //订单号
        $data["mch_appid"] = $wx['appid'];
        $data["mchid"] = $mch_id; //商户号
        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号
        $data["openid"] = $openid;
        $data["re_user_name"] = $qrcode->nickName;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名
        $data["amount"] = $money;
        $data["desc"] = '【看样子小程序】账户余额提现';
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
        // echo "<pre>";
        // var_dump($result);
        // echo "</pre>";
        // exit();
        if($result['result_code']=='SUCCESS'){
          ORM::factory('bnk_score')->scoreOut($qrcode,5,$money1,0,0,1);
          // ORM::factory('yjb_score')->scoreOut($qrcode,4,$fee);
          $result1['state']='SUCCESS';
          $result1['code']=$money1;
          echo json_encode($result1);
        }else{
          $result1['state']='FAIL';
          $result1['code']=$result['err_code_des'];
          echo json_encode($result1);
        }
        exit();
        //return $result;
  }
  public function action_money($openid){
      $qrcode=ORM::factory('bnk_qrcode')->where('openid','=',$openid)->find();
      $qid=$qrcode->id;

      $own_money=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from bnk_scores where qid = $qid ")->execute()->as_array();
      $ownmoney=$own_money[0]['ownmoney'];
      // $fee=0.1;
      $qrcode->score = $ownmoney;
      $qrcode->save();
      $scores = ORM::factory('bnk_score')->where('type','=',5)->where('qid','=',$qid)->order_by('id','desc')->find_all();
      foreach ($scores as $k => $v) {
          $result['score'][$k]['money'] = number_format(abs($v->score),2);
          $result['score'][$k]['time'] = date('Y-m-d h:i:s',$v->createdtime);
          $result['score'][$k]['state'] = $v->flag==1?'提现成功':'申请中';
      }
      // if($ownmoney>=5) $fee=$ownmoney*$this->charge/100;
      $result['own_money']=number_format($ownmoney,2);
      echo json_encode($result);
      exit();
  }
  public function curls($url, $timeout=5){
      // 1. 初始化
      $ch = curl_init();

      // 2. 设置选项，包括URL
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

      // 3. 执行并获取HTML文档内容
      $info = curl_exec($ch);
      // 4. 释放curl句柄
      curl_close($ch);
      return $info;
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
