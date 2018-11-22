<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yhb extends Controller_Base{
    public $template = 'tpl/blank';
    public function before() {
        Database::$default = "yhb";
        parent::before();
        if (Request::instance()->action == 'test') return;
        if (Request::instance()->action == 'music') return;
    }
    public function action_check(){
        $mem = Cache::instance('memcache');
        $openid=$_GET['openid'];
        $hack_count = (int)$mem->get($openid);
        if($_GET['code']&&$hack_count<10){
            $kl=ORM::factory('yhb_kl')->where('code','=',$_GET['code'])->find();
            if($kl->id){
                $enddata = array('msg'=>'success');
                $rtjson =json_encode($enddata);
                echo $rtjson;
            }else{
                $hack_count++;
                $mem->set($openid,$hack_count,3600*48);
                $enddata = array('msg'=>'口令不存在');
                $rtjson =json_encode($enddata);
                echo $rtjson;
            }
        }else {
            $enddata = array('msg'=>'url不符合规范');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
    }
    public function action_recreate(){
      $bid=1;
      $code=$_GET['code'];
      $openid=$_GET['openid'];
      $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
      // echo $code_used->used.'<br>';
      $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find()->as_array();
      $result['detail']=$order;
      if(!$order['music']){
        $mid=$order['mid'];
        $bgm=ORM::factory('yhb_tpl')->where('id','=',$mid)->find()->music;
        $result['detail']['music']=$bgm;
      }
      // var_dump($order);
      // exit;
      $tpls=ORM::factory('yhb_tpl')->where('bid','=',$bid)->find_all();
      foreach ($tpls as $key => $tpl) {
          $result['tpls'][$key]['pic']=$tpl->pic;
          $result['tpls'][$key]['id']=$tpl->id;
          // $result[$key]['word']=$tpl->word;
          // $result[$key]['music']=$tpl->music;
          // $result[$key]['vedio']=$tpl->vedio;
          // $result[$key]['name']=$tpl->name;
      }
      $rtjson =json_encode($result);
      echo $rtjson;
    }
    public function action_list(){
      $bid=1;
      $openid=$_GET['openid'];
      $lists=ORM::factory('yhb_youzan')->where('bid','=',$bid)->where('openid','=',$openid)->find_all();
      foreach ($lists as $key => $list) {

        $result['tpls'][$key]['pic']=$list->tpl->pic;
        $result['tpls'][$key]['yid']=$list->id;
        $result['tpls'][$key]['code']=$list->code;
        if($list->type==1){
          $result['tpls'][$key]['type']='done';
        }
        if($list->type==2){
          $result['tpls'][$key]['type']='audiodone';
        }
        if($list->type==3){
          $result['tpls'][$key]['type']='vediodone';
        }
        if(time()-$list->creattime>3*24*3600||$list->code==88888){
          $result['tpls'][$key]['status']='look';
        }else{
          $result['tpls'][$key]['status']='check';
        }
        // $result[$key]['word']=$tpl->word;
        // $result[$key]['music']=$tpl->music;
        // $result[$key]['vedio']=$tpl->vedio;
        // $result[$key]['name']=$tpl->name;
      }
      $rtjson =json_encode($result);
      echo $rtjson;

    }
    public function action_done(){
      $yid=$_GET['yid'];
      $order=ORM::factory('yhb_youzan')->where('id','=',$yid)->find()->as_array();
      $mid=$order['mid'];
      $tpl=ORM::factory('yhb_tpl')->where('id','=',$mid)->find();
      $pic=$tpl->pic;
      $order['pic']=$pic;
      if(!$order['music']){
        $bgm=$tpl->music;
        $order['music']=$bgm;
      }
      $rtjson=json_encode($order);
      echo $rtjson;
    }
    public function action_checkpsd(){
      $bid=1;
      $code=$_GET['code'];
      $yid=$_GET['yid'];
      $lastcode=ORM::factory('yhb_youzan')->where('id','=',$yid)->find()->code;
      if($lastcode==$code){
        $result['flag']=1;
      }else{
        $result['flag']=0;
      }
      $rtjson=json_encode($result);
      echo $rtjson;
    }
    public function action_choose(){
        $bid=1;
        $openid=$_GET['openid'];
        $code=$_GET['code'];
        $share=$_GET['share'];
        if($_GET['code']){
            $kl=ORM::factory('yhb_kl')->where('code','=',$_GET['code'])->find();
            if($kl->used!=0){
              if($kl->order->word){
                $result['word']=$kl->order->word;
              }else{
                $result['word']=$kl->order->tpl->word;
              }
              if($kl->order->music){
                $result['record']=1;
              }else{
                $result['record']=0;
              }
              if($kl->order->word){
                $result['name']=$kl->order->name;
              }else{
                $result['name']=$kl->order->tpl->name;
              }
              $result['music'] = $kl->order->tpl->music;
              $result['pic'] = $kl->order->tpl->pic;
              $result['mid']=$kl->order->mid;
              $result['switch']=$kl->order->switch;
              $result['type']=$kl->order->type;
              $result['yid']=$kl->order->id;
              $result['date']=$kl->order->date;
              $result['time']=$kl->order->time;
              $time=$kl->order->creattime;
              $result['now']=date('Y-m-d',$time);
              $endtime=strtotime("+3days",$time);
              if($endtime>time()&&$kl->order->openid==$openid&&$share!=1){
                  $result['msg']='check';
              }else{
                $date=$kl->order->date;
                $time=$kl->order->time;
                $timestamp=strtotime($date.' '.$time);
                $result['wait_time'] = $date.$time;
                $result['wait_time2'] = $timestamp;
                $result['now'] = time();
                if($timestamp>time()){
                  $result['msg']= 'notime';
                  $result['flag']=1;
                  $result['wait_time'] = $date.$time;
                }else{
                  if($kl->order->switch==1){
                    $result['flag']=2;
                  }else{
                    $result['flag']=3;
                  }
                  $result['msg']='look';
                }
                if($kl->order->openid==$openid){
                  $result['flag']=3;
                  $result['msg']='look';
                }
              }
              $rtjson =json_encode($result);
              echo $rtjson;
            }else{
                $tpls=ORM::factory('yhb_tpl')->where('bid','=',$bid)->find_all();
                foreach ($tpls as $key => $tpl) {
                    $result['tpls'][$key]['pic']=$tpl->pic;
                    $result['tpls'][$key]['id']=$tpl->id;
                    // $result[$key]['word']=$tpl->word;
                    // $result[$key]['music']=$tpl->music;
                    // $result[$key]['vedio']=$tpl->vedio;
                    // $result[$key]['name']=$tpl->name;
                }
                $result['msg'] ='touse';
                $rtjson =json_encode($result);
                echo $rtjson;
            }
        }else {
            $enddata = array('msg'=>'没有code');
            $rtjson =json_encode($enddata);
            echo $rtjson;
        }
    }
    public function action_model(){
      $bid=1;
      $id=$_GET['id'];
      $tpl=ORM::factory('yhb_tpl')->where('id','=',$id)->find();
      $result['pic']=$tpl->pic;
      $result['id']=$tpl->id;
      $result['word']=$tpl->word;
      $result['music']=$tpl->music;
      $result['vedio']=$tpl->vedio;
      $result['name']=$tpl->name;
      $result['time']=date('Y-m-d',time());
      $rtjson =json_encode($result);
      echo $rtjson;
    }
    public function action_music($yid){
        $bid=1;
        $music = ORM::factory('yhb_youzan')->where('bid','=',$bid)->where('id','=',$yid)->find()->music;

        // if (!$pic) die('404 Not Found!');
        header("Content-Type: audio/mpeg");
        header("Content-Length: ".strlen($music));
        echo $music;
        exit;
    }
    public function action_video($yid){
        $bid=1;
        $video_file = DOCROOT."yhb/tmp/$yid.mp4";
        umask(0002);
        @mkdir(dirname($video_file),0777,true);

        $video = ORM::factory('yhb_youzan')->where('bid','=',$bid)->where('id','=',$yid)->find()->video;
        file_put_contents($video_file, $video);
        $this->action_buffer($video_file);
    }
    public function action_buffer($tmpname){
        $file = $tmpname;
        $fp = @fopen($file, 'rb');

        $size   = filesize($file); // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte

        header('Content-type: video/mp4');
        header("Accept-Ranges: 0-$length");
        if (isset($_SERVER['HTTP_RANGE'])) {

            $c_start = $start;
            $c_end   = $end;

            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            }else{
                $range  = explode('-', $range);
                $c_start = $range[0];
                $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start  = $c_start;
            $end    = $c_end;
            $length = $end - $start + 1;
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: ".$length);


        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end) {

            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            flush();
        }

        fclose($fp);
        @unlink($tmpname);
        exit();
    }
    public function action_set(){
      $bid=1;
      $yid=$_GET['yid'];
      $order=ORM::factory('yhb_youzan')->where('id','=',$yid)->find();
      $postStr = file_get_contents("php://input");
      if($postStr){
        $result=json_decode($postStr,true);
        $date=$result['data']['date'];
        $switch=$result['data']['switch'];
        $time=$result['data']['time'];
        $order->date=$date;
        $order->switch=$switch;
        $order->time=$time;
        $order->save();
      }
      $type=$order->type;
      $result['type']=$type;
      $result['date']=$order->date;
      $result['switch']=$order->switch;
      $result['time']=$order->time;
      $rtjson=json_encode($result);
      echo $rtjson;
    }
    public function action_save(){
      $bid=1;
      $postStr = file_get_contents("php://input");
      if($postStr){
        $result=json_decode($postStr,true);
        Kohana::$log->add('yhbbbb2', print_r($result, true));//写入日志，可以删除
        Kohana::$log->add('yhbbbb1', print_r($_FILES['file'], true));//写入日志，可以删除
        $code=$result['data']['code'];
        Kohana::$log->add('yhbbbb3', print_r($code, true));//写入日志，可以删除
        $openid=$result['data']['openid'];
        $nickname=$result['data']['userInfo']['nickName'];
        $headimgurl=$result['data']['userInfo']['avatarUrl'];
        Kohana::$log->add('yhbbbb4', print_r($openid, true));//写入日志，可以删除
        $id=$result['data']['edit']['id'];
        $type=$result['data']['edit']['type'];
        Kohana::$log->add('yhbbbb5', print_r($id, true));//写入日志，可以删除
        $word=$result['data']['edit']['word'];
        $name=$result['data']['edit']['name'];
        $switch=$result['data']['set']['switch'];
        $date=$result['data']['set']['date'];
        $time=$result['data']['set']['time'];
        Kohana::$log->add('yhbbbb6', print_r($word, true));//写入日志，可以删除
        $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
        Kohana::$log->add('yhbbbb10', print_r($code_used->used, true));//写入日志，可以删除
        $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find();
        $order->bid=$bid;
        $order->openid=$openid;
        $order->nickname=$nickname;
        $order->headimgurl=$headimgurl;
        $order->name=$name;
        $order->code=$code;
        $order->mid=$id;
        $order->word=$word;
        $order->type=$type;
        $order->save();
        if($code_used->used==0){
          $used=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
          $code_used->used=$used;
          $code_used->save();
        }
      }
      $order1=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find();
      $result['used']=$order1->id;
      $result['type']=$order1->type;
      $rtjson=json_encode($result);
      echo $rtjson;
    }
    public function action_saveMusic(){
      $bid=1;
      $postStr = file_get_contents("php://input");
      if($postStr){
        $result=json_decode($postStr,true);
        Kohana::$log->add('yhbbbb2', print_r($result, true));//写入日志，可以删除
        Kohana::$log->add('yhbbbb1', print_r($_FILES['file'], true));//写入日志，可以删除
        $code=$result['data']['code'];
        Kohana::$log->add('yhbbbb3', print_r($code, true));//写入日志，可以删除
        $openid=$result['data']['openid'];
        $nickname=$result['data']['userInfo']['nickName'];
        $headimgurl=$result['data']['userInfo']['avatarUrl'];
        Kohana::$log->add('yhbbbb4', print_r($openid, true));//写入日志，可以删除
        $id=$result['data']['edit']['id'];
        $type=$result['data']['edit']['type'];
        Kohana::$log->add('yhbbbb5', print_r($id, true));//写入日志，可以删除
        $word=$result['data']['edit']['word'];
        $name=$result['data']['edit']['name'];
        $switch=$result['data']['set']['switch'];
        $date=$result['data']['set']['date'];
        $time=$result['data']['set']['time'];
        Kohana::$log->add('yhbbbb6', print_r($word, true));//写入日志，可以删除
        $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
        Kohana::$log->add('yhbbbb10', print_r($code_used->used, true));//写入日志，可以删除
        $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find();
        $order->bid=$bid;
        $order->openid=$openid;
        $order->nickname=$nickname;
        $order->headimgurl=$headimgurl;
        $order->name=$name;
        $order->code=$code;
        $order->mid=$id;
        $order->type=$type;
        $order->save();
        if($code_used->used==0){
          $used=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
          $code_used->used=$used;
          $code_used->save();
        }
      }
      if($_FILES['file']){
          $openid=$_GET['openid'];
          Kohana::$log->add('yhbbbb7', print_r($openid, true));//写入日志，可以删除
          $code=$_GET['code'];
          Kohana::$log->add('yhbbbb8', print_r($code, true));//写入日志，可以删除
          $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
          Kohana::$log->add('yhbbbb9', print_r($code_used->used, true));//写入日志，可以删除
          $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find();
          $order->code=$code;
          $order->openid=$openid;
          $order->music=file_get_contents(($_FILES['file']['tmp_name']));
          $order->save();
          if($code_used->used==0){
            $used=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
            $code_used->used=$used;
            $code_used->save();
          }
      }
      $order1=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find();
      $result['used']=$order1->id;
      $result['type']=$order1->type;
      $rtjson=json_encode($result);
      echo $rtjson;
    }
    public function action_saveVideo(){
      $bid=1;
      $postStr = file_get_contents("php://input");
      if($postStr){
        $result=json_decode($postStr,true);
        Kohana::$log->add('yhbbbb2', print_r($result, true));//写入日志，可以删除
        Kohana::$log->add('yhbbbb1', print_r($_FILES['file'], true));//写入日志，可以删除
        $code=$result['data']['code'];
        Kohana::$log->add('yhbbbb3', print_r($code, true));//写入日志，可以删除
        $openid=$result['data']['openid'];
        $nickname=$result['data']['userInfo']['nickName'];
        $headimgurl=$result['data']['userInfo']['avatarUrl'];
        Kohana::$log->add('yhbbbb4', print_r($openid, true));//写入日志，可以删除
        $id=$result['data']['edit']['id'];
        $type=$result['data']['edit']['type'];
        Kohana::$log->add('yhbbbb5', print_r($id, true));//写入日志，可以删除
        $word=$result['data']['edit']['word'];
        $name=$result['data']['edit']['name'];
        $switch=$result['data']['set']['switch'];
        $date=$result['data']['set']['date'];
        $time=$result['data']['set']['time'];
        Kohana::$log->add('yhbbbb6', print_r($word, true));//写入日志，可以删除
        $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
        Kohana::$log->add('yhbbbb10', print_r($code_used->used, true));//写入日志，可以删除
        $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find();
        $order->bid=$bid;
        $order->openid=$openid;
        $order->nickname=$nickname;
        $order->headimgurl=$headimgurl;
        $order->name=$name;
        $order->code=$code;
        $order->mid=$id;
        $order->type=$type;
        $order->save();
        if($code_used->used==0){
          $used=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
          $code_used->used=$used;
          $code_used->save();
        }
      }
      if($_FILES['file']){
          $openid=$_GET['openid'];
          Kohana::$log->add('yhbbbb7', print_r($openid, true));//写入日志，可以删除
          $code=$_GET['code'];
          Kohana::$log->add('yhbbbb8', print_r($code, true));//写入日志，可以删除
          $code_used=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$code)->find();
          Kohana::$log->add('yhbbbb9', print_r($code_used->used, true));//写入日志，可以删除
          $order=ORM::factory('yhb_youzan')->where('id','=',$code_used->used)->find();
          $order->code=$code;
          $order->openid=$openid;
          $order->video=file_get_contents(($_FILES['file']['tmp_name']));
          $order->save();
          if($code_used->used==0){
            $used=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find()->id;
            $code_used->used=$used;
            $code_used->save();
          }
      }
      $order1=ORM::factory('yhb_youzan')->where('code','=',$code)->where('bid','=',$bid)->where('openid','=',$openid)->find();
      $result['used']=$order1->id;
      $result['type']=$order1->type;
      $rtjson=json_encode($result);
      echo $rtjson;
    }
    public function action_onLogin(){
        if($_GET['code']){
          $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wxfd77b6e69fb10894&secret=cbe8f979862d1a205a502aca15886021&js_code=".$_GET['code']."&grant_type=authorization_code";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $output = curl_exec($ch);
          curl_close($ch);
          $result = json_decode($output,true);
          // var_dump($result);
          require_once Kohana::find_file('vendor', 'xcx_aes/wxBizDataCrypt');
          $appid = 'wxfd77b6e69fb10894';
          $sessionKey = $result['session_key'];
          $postStr = file_get_contents("php://input");
          $data=json_decode($postStr,true);
          // echo 'sessionKey'.$sessionKey;
          $encryptedData= $data['data']['encryptedData'];
          // echo 'encryptedData'.$encryptedData;
          $iv = $data['data']['iv'];
          // echo 'iv'.$iv;
          $pc = new WXBizDataCrypt($appid, $sessionKey);
          $errCode = $pc->decryptData($encryptedData, $iv, $resdata );
          ob_clean();
          if ($errCode == 0) {
              $result = json_decode($resdata,true);
              print($result['openId']);
          } else {
              print($errCode . "\n");
          }
        }
    }
}
