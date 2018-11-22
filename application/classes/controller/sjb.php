<?php defined('SYSPATH') or die('No direct script access.');

class Controller_sjb extends Controller_Base{
    public $template = 'tpl/blank';
    public function before() {
        Database::$default = "wdy";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_test(){
      $openid='oCVQM0bqZgNX_5EFtIqpHRv7zz24';
      echo '<pre>';
      $qrcodes=DB::query(Database::SELECT,"SELECT * from `sjb_qrcodes` where `fopenid` = '$openid' or `openid` = '$openid' or openGId IN (SELECT openGId from sjb_qrcodes where `openid` = '$openid' and openGId IS NOT NULL) group by openid")->execute()->as_array();
      var_dump($qrcodes);
      echo '---------------------------<br>';
      kohana::$log->add('sjbqrcodes',print_r($qrcodes,true));
      $qrcodef = ORM::factory('sjb_qrcode')->where('openid','=',$openid)->where('fopenid','!=',NULL)->find();
      if($qrcodef->id){
        $fopenid=$qrcodef->fopenid;
        kohana::$log->add('sjbqrcode',print_r($fopenid,true));
        $qrcode1 =DB::query(Database::SELECT,"SELECT * from sjb_qrcodes where `openid` = '$fopenid' group by openid")->execute()->as_array();
        kohana::$log->add('sjbqrcode1',print_r($qrcode1,true));
        var_dump($qrcode1);
        echo '---------------------------<br>';
        $flag=0;
        foreach ($qrcodes as $key => $qrcodea) {
          if($fopenid==$qrcodea['openid']){
            $flag=1;
          }
        }
        if($flag==0){
          $qrcodes= array_merge($qrcodes,$qrcode1);
        }
      }
      foreach ($qrcodes as $k => $v) {
          $str = $qrcodes[$k]['model'];
          $str = preg_replace('/\(.*?\)/', '', $str);
          $qrcodes[$k]['model'] = preg_replace('/\<.*?\>/', '', $str);
      }
      var_dump($qrcodes);
      kohana::$log->add('sjbqrcodes',print_r($qrcodes,true));
      echo json_encode($qrcodes);
      kohana::$log->add('sjbqrcodes1',print_r($qrcodes,true));
      echo '</pre>';
      exit();
    }
    public function action_decode(){
      //ob_clean();
      $postStr = file_get_contents("php://input");
      kohana::$log->add('sjbpost1',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        kohana::$log->add('sjbresult1',print_r($postStr,true));
        $encryptedData=$result1['encryptedData'];
        $fopenid=$result1['fopenid'];
        $iv=$result1['iv'];
        $openid=$result1['openid'];
        $phone=$result1['phone'];
        $result1['model']=$phone['model'];
        $result1['brand']=$result1['phone']['brand'];
        $state=$result1['state'];
        $result1['nickName']=$result1['userInfo']['nickName'];
        $result1['gender']=$result1['userInfo']['gender'];
        $result1['avatarUrl']=$result1['userInfo']['avatarUrl'];
        $result1['language']=$result1['userInfo']['language'];
        $result1['city']=$result1['userInfo']['city'];
        $result1['province']=$result1['userInfo']['province'];
        $result1['country']=$result1['userInfo']['country'];
        // $result1['nickName']=$result1['userInfo']['nickName'];
        // $result1['nickName']=$result1['userInfo']['nickName'];
        // $nickName=$result1['nickName'];
        // $gender=$result1['gender'];
        // $avatarUrl=$result1['avatarUrl'];
        // $language=$result1['language'];
        // $city=$result1['city'];
        // $province=$result1['province'];
        // $country=$result1['country'];
        if($openid==$fopenid){
          $qrcode=ORM::factory('sjb_qrcode')->where('openid','=',$openid)->where('fopenid','=',NULL)->where('openGId','=',NULL)->find();
          $result2=$result1;
          $result2['fopenid']=NULL;
          $result2['openGId']=NULL;
          kohana::$log->add('sjbresult2',print_r($result2,true));
          $qrcode->values($result2);
          $qrcode->save();
        }else{
          $qrcode=ORM::factory('sjb_qrcode')->where('openid','=',$openid)->where('fopenid','=',$fopenid);
          if($state=='group'){
            require_once Kohana::find_file('vendor/lib', 'wxBizDataCrypt');
            $appid = 'wx8278c87fcb0d77ed';
            $men=Cache::instance('memcache');
            $sessionKey=$men->get($openid);
            // echo $appid.'<br>';
            // echo $sessionKey.'<br>';
            // echo $encryptedData.'<br>';
            // echo $iv.'<br>';
            kohana::$log->add('sjbresult3',print_r($sessionKey,true));
            kohana::$log->add('sjbresult3',print_r($encryptedData,true));
            kohana::$log->add('sjbresult3',print_r($iv,true));
            $pc = new WXBizDataCrypt($appid, $sessionKey);
            $errCode = $pc->decryptData($encryptedData, $iv, $data );
            //ob_flush();
            // echo '<pre>';
            // var_dump($errCode);
            // var_dump($data);
            // echo '</pre>';
            // exit();
            kohana::$log->add('sjbresult3',print_r($errCode,true));
            kohana::$log->add('sjbresult3',print_r($data,true));
            if ($errCode == 0) {
                $result=json_decode($data,true);
                $openGId=$result['openGId'];
                $qrcode=$qrcode->where('openGId','=',$openGId);
            } else {
              echo $errCode;
              exit();
            }
            //ob_flush();
          }
          $qrcode=$qrcode->find();
          $result4=$result1;
          $result4['openGId']=NULL;
          if($state=='group'){
            $fqrcode1=ORM::factory('sjb_qrcode')->where('openid','=',$fopenid)->find();
            $fqrcode=ORM::factory('sjb_qrcode')->where('openid','=',$fopenid)->where('fopenid','=',NULL)->where('openGId','=',$openGId)->find();
            $result4['openGId']=$openGId;
            $result3['openGId']=$openGId;
            $result3['openid']=$fopenid;
            $result3['nickName']=$fqrcode1->nickName;
            $result3['gender']=$fqrcode1->gender;
            $result3['avatarUrl']=$fqrcode1->avatarUrl;
            $result3['language']=$fqrcode1->language;
            $result3['city']=$fqrcode1->city;
            $result3['country']=$fqrcode1->country;
            $result3['province']=$fqrcode1->province;
            $result3['brand']=$fqrcode1->brand;
            $result3['model']=$fqrcode1->model;
            $result3['jointime']=time();
            $fqrcode->values($result3);
            kohana::$log->add('sjbresult3',print_r($result3,true));
            $fqrcode->save();
          }
          kohana::$log->add('sjbresult4',print_r($result4,true));
          $qrcode->values($result4);
          $qrcode->save();
        }
        $qrcodes=DB::query(Database::SELECT,"SELECT * from `sjb_qrcodes` where `fopenid` = '$openid' or `openid` = '$openid' or openGId IN (SELECT openGId from sjb_qrcodes where `openid` = '$openid' and openGId IS NOT NULL) group by openid")->execute()->as_array();
        kohana::$log->add('sjbqrcodes',print_r($qrcodes,true));
        $qrcodef = ORM::factory('sjb_qrcode')->where('openid','=',$openid)->where('fopenid','!=',NULL)->find();
        if($qrcodef->id){
          $fopenid=$qrcodef->fopenid;
          kohana::$log->add('sjbqrcode',print_r($fopenid,true));
          $qrcode1 =DB::query(Database::SELECT,"SELECT * from sjb_qrcodes where `openid` = '$fopenid' group by openid")->execute()->as_array();
          kohana::$log->add('sjbqrcode1',print_r($qrcode1,true));
          $flag=0;
          foreach ($qrcodes as $key => $qrcodea) {
            if($fopenid==$qrcodea['openid']){
              $flag=1;
            }
          }
          if($flag==0){
            $qrcodes= array_merge($qrcodes,$qrcode1);
          }
          //$qrcodes= array_merge($qrcodes,$qrcode1);
        }
        foreach ($qrcodes as $k => $v) {
            $str = $qrcodes[$k]['model'];
            $str = preg_replace('/\(.*?\)/', '', $str);
            $qrcodes[$k]['model'] = preg_replace('/\<.*?\>/', '', $str);
        }
        kohana::$log->add('sjbqrcodes',print_r($qrcodes,true));
        sort($qrcodes);
        $jsonqrocde =json_encode($qrcodes);
        // $a=json_decode($jsonqrocde,true);
        // kohana::$log->add('sjbqrcodes2',print_r($a,true));
        // $b=json_encode($a);
        // kohana::$log->add('sjbqrcodes3',print_r($b,true));
        // $addqrocde=addslashes($jsonqrocde);
        // kohana::$log->add('sjbqrcodes2',print_r($addqrocde,true));
        //ob_flush();
        //ob_clean();
        echo $jsonqrocde;
        kohana::$log->add('sjbqrcodes1',print_r($jsonqrocde,true));
        exit();
      }
    }
    public function action_onLogin(){
        kohana::$log->add('sjbresult112',print_r($_GET,true));
        if($_GET['code']){
          $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wx8278c87fcb0d77ed&secret=fca2f78642c77de8ba24da7e41e8e35c&js_code=".$_GET['code']."&grant_type=authorization_code";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $output = curl_exec($ch);
          curl_close($ch);
          $result = json_decode($output,true);
          kohana::$log->add('sjbresult111',print_r($result,true));
          //var_dump($result);
          // echo $output;
          $user = ORM::factory('sjb_qrcode')->where('openid','=',$result['openid'])->find();
          $men=Cache::instance('memcache');
          $men->set($result['openid'],$result['session_key'],7200);
          if($user->id){
            $result['hasin'] = 'yes';//存在表中
          }else{
            $result['hasin'] = 'no';//不存在该用户
          }
          $result['price'] = '99.00';

          $result['pic'][0] = 'http://jfb.smfyun.com/sjb/post1.png';
          $result['pic'][1] = 'http://jfb.smfyun.com/sjb/post2.png';
          $result['pic'][2] = 'http://jfb.smfyun.com/sjb/post3.png';
          $result['pic'][3] = 'http://jfb.smfyun.com/sjb/post4.png';
          $result['pic'][4] = 'http://jfb.smfyun.com/sjb/post5.png';
          $result['pic'][5] = 'http://jfb.smfyun.com/sjb/post6.png';
          $result['pic'][6] = 'http://jfb.smfyun.com/sjb/post7.png';
          $result['pic'][7] = 'http://jfb.smfyun.com/sjb/post8.png';
          $result['pic'][8] = 'http://jfb.smfyun.com/sjb/post9.png';

          echo json_encode($result);
          exit;
        }
    }
    public function action_payfee(){
      require_once Kohana::find_file('vendor/lib', 'WeixinPay');
      $appid='wx8278c87fcb0d77ed';
      $openid= $_GET['openid'];
      $mch_id='1279137701';
      $key='JZ3nHfIHdoD9ZKEuaGHX76H9a4dPMQx4';
      $out_trade_no = $mch_id. time();
      $total_fee = 99;
      if(empty($total_fee)) //押金
      {
          $body = "充值押金";
          $total_fee = floatval(5);
      }
       else {
           $body = "购买：打电话眼镜";
           $total_fee = floatval($total_fee*100);
       }
      $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
      $return=$weixinpay->pay();
      echo json_encode($return);
    }
    public function action_paydone(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('sjbpost1',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        $result1['qid']=ORM::factory('sjb_qrcode')->where('openid','=',$result1['openid'])->find()->id;
        $result1['address']=$result1['addr'];
        $result1['state']=1;
        $result1['money']=99;
        $result1['iid']=1;
        $tid=ORM::factory('sjb_tid');
        $tid->values($result1);
        if($tid->save()){
          $code['state']=1;
          $code['tid']=$tid->id;
        }else{
          $code['state']=0;
        }
        echo json_encode($code);
      }
    }
}
