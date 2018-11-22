<?php defined('SYSPATH') or die('No direct script access.');

class Controller_yjb extends Controller_Base{
    public $template = 'tpl/blank';
    public $charge;
    public function before() {
        Database::$default = "wdy";
        parent::before();
        $this->charge = 2;
        if (Request::instance()->action == 'test') return;
    }
    public function action_qrcode(){
        $postStr = file_get_contents("php://input");
        kohana::$log->add('qrpost1',print_r($postStr,true));
        // if($_POST){
        //   kohana::$log->add('qrpost2s',print_r($_POST,true));
        // }
        if($postStr){
          $result1=json_decode($postStr,true);
          kohana::$log->add('qrpost',print_r($result1,true));
          $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$result1['openid'])->find();
          $qrcode->values($result1);
          if($qrcode->save()){
            $result['code'] = 'success';
          }else{
            $result['code'] = 'fail';
          }
          echo json_encode($result);
          exit;
        }
    }
    public function action_party(){
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
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$_POST['openid'])->find();
        if($qrcode->id){
          if($_POST['pid']){
            $party=ORM::factory('yjb_party')->where('id','=',$_POST['pid'])->find();
            $_POST['qid']=$qrcode->id;
            $_POST['inttime']=strtotime($_POST['date'].' '.$_POST['time']);
            if($_FILES){
                kohana::$log->add('filepost',print_r($_FILES,true));
                $_POST['pic']=file_get_contents($_FILES['addimg']['tmp_name']);
                $party->pic=$_POST['pic'];
            }
            $party->qid=$_POST['qid'];
            $party->theme=$_POST['theme'];
            $party->date=$_POST['date'];
            $party->memo=$_POST['memo'];
            $party->time=$_POST['time'];
            $party->inttime=$_POST['inttime'];
            $party->pos=$_POST['pos'];
            $party->money=$_POST['money'];
            $party->num=$_POST['num'];
            $party->type=$_POST['type'];
            $party->yuetype=$_POST['yuetype'];
            $party->code=$_POST['code'];
            $party->latitude=$_POST['latitude'];
            $party->longitude=$_POST['longitude'];
            if($party->save()){
              $result['code'] = 'success';
            }else{
              $result['code'] = 'fail';
            }
          }else{
            $party=ORM::factory('yjb_party');
            $_POST['qid']=$qrcode->id;
            $_POST['inttime']=strtotime($_POST['date'].' '.$_POST['time']);
            if($_FILES){
                kohana::$log->add('filepost',print_r($_FILES,true));
                $_POST['pic']=file_get_contents($_FILES['addimg']['tmp_name']);
            }
            $party->values($_POST);
            if($party->save()){
              $result['code'] = $party->id;
            }else{
              $result['code'] = 'fail';
            }
          }
        }else{
          $result['code'] = 'nopeople';
        }
        echo $result['code'];
        exit;
      }
    }
    public function action_showjoin($pid){
      $postStr = file_get_contents("php://input");
      if($postStr){
        $result1=json_decode($postStr,true);
        $openid=$result1['openid'];
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $party=ORM::factory('yjb_party')->where('id','=',$pid)->find();
        $result['isme']=0;
        if($qrcode->id==$party->qid) $result['isme']=1;
        $records=ORM::factory('yjb_record')->where('pid','=',$pid)->find_all();
        foreach ($records as $k => $record) {
          // $score=ORM::factory('yjb_score')->where('type','=',0)->where('pid','=',$pid)->where()
          $result['qrcode'][$k]['avatarUrl']=$record->qrcode->avatarUrl;
          $result['qrcode'][$k]['name']=$record->qrcode->name;
          $result['qrcode'][$k]['jointime']=date('Y-m-d H:i:s',$record->jointime);
          $result['qrcode'][$k]['pos']=$record->qrcode->pos;
          $result['qrcode'][$k]['company']=$record->qrcode->company;
          $result['qrcode'][$k]['tel']=$record->qrcode->tel;
          $result['qrcode'][$k]['wx']=$record->qrcode->wx;
          $result['qrcode'][$k]['nickName']=$record->qrcode->nickName;
          $result['qrcode'][$k]['money']=$record->sid?number_format($record->score->score,2):0.00;
        }
        echo json_encode($result);
        exit;
      }
    }
    public function action_images($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "yjb_$type";
        $pic = ORM::factory($table, $id)->pic;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    public function action_person(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      //$qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$result1['openid'])->find();
      if($postStr){
        $result['type']='edit';
        $result1=json_decode($postStr,true);
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$result1['openid'])->find();
        kohana::$log->add('papost',print_r($result1,true));
        if($result1['type']=='edit'){
          if($qrcode->id){
            $qrcode->values($result1);
            if($qrcode->save()){
              $result['qid'] = $qrcode->id;
            }else{
              $result['code'] = 'fail';
            }
          }else{
            $result['code'] = 'nopeople';
          }
          echo json_encode($result);
          exit;
        }
      }
      $result=$qrcode->as_array();
      $result['type']='show';
      echo json_encode($result);
      exit;
    }
    public function action_checkuserinfo($openid){
      $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
      if($qrcode->id){
        if($qrcode->name&&$qrcode->tel&&$qrcode->pos&&$qrcode->wx){
          $result['state']=1;
        }else{
          $result['state']=0;
        }
      }else{
        $result['state']=2;
      }
      echo json_encode($result);
      exit;
    }
    public function action_test(){
      $partys=DB::query(Database::SELECT,"SELECT * from  yjb_parties ")->execute()->as_array();
      echo "<pre>";
      var_dump($partys);
      echo "</pre>";
      exit();
    }
    public function action_showparty($id){
      $pagsize=6;
      $offset=$id*$pagsize;
      $time=time();
      $partys=DB::query(Database::SELECT,"SELECT * from  yjb_parties where inttime >= $time order by jointime desc limit $pagsize offset $offset ")->execute()->as_array();
      $num=0;
      foreach ($partys as $k => $value) {
        //$join_date=date('Y-m-d H:i:s',$value['jointime']);
        $qrcode=ORM::factory('yjb_qrcode')->where('id','=',$value['qid'])->find();
        $partys[$k]['join_date']=date('Y-m-d H:i:s',$value['jointime']);
        $partys[$k]['avatarUrl']=$qrcode->avatarUrl;
        if($value['pic']);{
          $partys[$k]['pic']='http://'.$_SERVER["HTTP_HOST"].'/yjb/images/party/'.$value['id'];
        }
        $partys[$k]['state']='进行中';
        if($value['inttime']<time()){
          $partys[$k]['state']='已完成';
        }
        $partys[$k]['name']=$qrcode->name;
        $partys[$k]['nickName']=$qrcode->nickName;
        $partys[$k]['position']=$qrcode->pos;
        $partys[$k]['company']=$qrcode->company;
        $record_num=ORM::factory('yjb_record')->where('pid','=',$value['id'])->count_all();
        $partys[$k]['record_num']=$record_num;
        $partys[$k]['residue']=$value['num']-$record_num;
        if($value['num']-$record_num<=0){
          $partys[$k]['state']='约满';
        }
        if($record_num>0){
          $records=ORM::factory('yjb_record')->where('pid','=',$value['id'])->find_all();
          foreach ($records as $key => $value) {
            $partys[$k]['qrcodes'][$key]['avatarUrl']=$value->qrcode->avatarUrl;
            $partys[$k]['qrcodes'][$key]['name']=$value->qrcode->name;
            if($key>=4) break;
          }
        }
        $num++;
      }
      if($num>=$pagsize){
        $result['flag']=1;
      }else{
        $result['flag']=0;
      }
      $result['id']=$id+1;
      $result['partys']=$partys;
      echo json_encode($result);
      exit;
    }
    public function action_preparty($id){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      $result1=json_decode($postStr,true);
      $openid=$result1['openid'];
      $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
      $record=ORM::factory('yjb_record')->where('pid','=',$id)->where('qid','=',$qrcode->id)->find();
      $party=DB::query(Database::SELECT,"SELECT * from  yjb_parties where id = $id ")->execute()->as_array();
      $party=$party[0];
      $qrcode1=ORM::factory('yjb_qrcode')->where('id','=',$party['qid'])->find();
      $party['join_date']=date('Y-m-d H:i:s',$party['jointime']);
      $party['avatarUrl']=$qrcode1->avatarUrl;
      if($party['pic']){
        $party['pic']='http://'.$_SERVER["HTTP_HOST"].'/yjb/images/party/'.$party['id'];
      }
      $party['name']=$qrcode1->name;
      $party['nickName']=$qrcode1->nickName;
      $party['position']=$qrcode1->pos;
      $party['company']=$qrcode1->company;
      if($party['qid']==$qrcode->id){
        $party['own']=1;
      }else{
        $party['own']=0;
      }
      if($party['inttime']>=time()){
        $party['state']=1;
      }else{
        $party['state']=0;
      }
      if($record->id){
        $party['ifjoin']=1;
      }else{
        $party['ifjoin']=0;
      }
      $scoretype[0]=1;
      $scoretype[1]=2;
      $awardnum=ORM::factory('yjb_score')->where('pid','=',$party['id'])->where('type','IN',$scoretype)->count_all();
      $nameaward=ORM::factory('yjb_score')->where('pid','=',$party['id'])->where('type','=',1)->count_all();
      if($nameaward>0){
        $nameawards=ORM::factory('yjb_score')->where('pid','=',$party['id'])->where('type','=',1)->find_all();
        foreach ($nameawards as $k => $nameaward) {
          $qrcodea=ORM::factory('yjb_qrcode')->where('id','=',$nameaward->rid)->find();
          $party['nameaward'][$k]['avatarUrl']=$qrcodea->avatarUrl;
          $party['nameaward'][$k]['name']=$qrcodea->name;
          if($k>=4) break;
        }
      }
      $party['awardnum']=$awardnum;
      $record_num=ORM::factory('yjb_record')->where('pid','=',$party['id'])->count_all();
      $party['residue']=$party['num']-$record_num;
      $party['record_num']=$record_num;
      //$partys[$k]['residue']=$value['num']-$record_num;
      if($record_num>0){
        $records=ORM::factory('yjb_record')->where('pid','=',$party['id'])->find_all();
        foreach ($records as $key => $value) {
          $party['qrcodes'][$key]['avatarUrl']=$value->qrcode->avatarUrl;
          $party['qrcodes'][$key]['name']=$value->qrcode->name;
          if($key>=4) break;
        }
      }
      echo json_encode($party);
      exit;
    }
    public function action_reward(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        $pid=$result1['pid'];
        $openid=$result1['openid'];
        $money=$result1['money'];
        if($result1['type']==0){
          $type=1;
        }else{
          $type=2;
        }
        $party=ORM::factory('yjb_party')->where('id','=',$pid)->find();
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $fqrcode=$party->user;
        $qid=$qrcode->id;
        $sid=ORM::factory('yjb_score')->scoreIn($fqrcode,$type,$money,$qid,$pid);
        // ORM::factory('yjb_score')->scoreOut($qrcode,4,$fee);
        echo $sid;
      }
    }
    public function action_scores($openid){
      $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
      $qid=$qrcode->id;
      $time=time();
      //$own_money=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from yjb_scores where qid = $qid and paydate <= $time")->execute()->as_array();
      //$result['balance']=number_format($own_money[0]['ownmoney'],2);
      //$result['own_money']
      $scores=ORM::factory('yjb_score')->where('qid','=',$qrcode->id)->order_by('lastupdate','DESC')->find_all();
      foreach ($scores as $key => $score) {
        $dqrcode=ORM::factory('yjb_qrcode')->where('id','=',$score->rid)->find();
        $result[$key]['partyname']=$score->party->theme;
        if($score->type==0){
          $result[$key]['type']='收钱';
        }elseif($score->type==1||$score->type==2){
          $result[$key]['type']='被打赏';
        }elseif($score->type==3){
          $result[$key]['type']='提现';
        }elseif($score->type==4){
          $result[$key]['type']='扣服务费';
        }
        if($score->type==2){
          $result[$key]['name']='匿名';
        }elseif($score->type==0||$score->type==1){
          $result[$key]['avatarUrl']=$dqrcode->avatarUrl;
          $result[$key]['name']=$dqrcode->name;
        }elseif($score->type==3){
          $result[$key]['avatarUrl']=$qrcode->avatarUrl;
          $result[$key]['name']=$qrcode->name;
        }elseif($score->type==4){
          $result[$key]['avatarUrl']='http://xcx.smfyun.com/yjb/img/sxf.png';
          $result[$key]['name']='服务费';
        }
        $result[$key]['balance']=number_format($score->allmoney,2);
        $result[$key]['money']=number_format($score->score,2);
        $result[$key]['time']=date('Y-m-d H:i:s',$score->lastupdate);
      }
      echo json_encode($result);
      exit();
    }
    public function action_joinparty(){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        $pid=$result1['pid'];
        $openid=$result1['openid'];
        $party=ORM::factory('yjb_party')->where('id','=',$pid)->find();
        // $money=$result1['money'];
        // $type=$result1['type'];
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $fqrcode=$party->user;
        $qid=$qrcode->id;
        kohana::$log->add('papost',print_r($result1,true));
        $record=ORM::factory('yjb_record')->where('pid','=',$pid)->where('qid','=',$qid)->find();
        if($record->id){
          echo "您已经加入过了该活动";
        }else{
          if($qid){
            $fee=0.1;
            if($ownmoney>=5) $fee=$ownmoney*$this->charge/100;
            ORM::factory('yjb_score')->scoreIn($fqrcode,4,-$fee,$qid,$pid);
            $sid=ORM::factory('yjb_score')->scoreIn($fqrcode,0,$party->money,$qid,$pid);
            $record->pid=$pid;
            $record->qid=$qid;
            $record->sid=$sid;
            if($record->save()){
              echo "加入成功";
            }else{
              echo "加入失败";
            }
          }else{
            echo "用户不存在";
          }
        }
      }
    }
    //我发起的活动
    public function action_personparty($id){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$result1['openid'])->find();
        $pagsize=6;
        $offset=$id*$pagsize;
        $time=time();
        $qid=$qrcode->id;
        $partys=DB::query(Database::SELECT,"SELECT * from  yjb_parties where qid = $qid order by jointime desc limit $pagsize offset $offset ")->execute()->as_array();
        $num=0;
        foreach ($partys as $k => $value) {
          //$join_date=date('Y-m-d H:i:s',$value['jointime']);
          $qrcode=ORM::factory('yjb_qrcode')->where('id','=',$value['qid'])->find();
          $partys[$k]['join_date']=date('Y-m-d H:i:s',$value['jointime']);
          $partys[$k]['avatarUrl']=$qrcode->avatarUrl;
          if($value['pic']){
            $partys[$k]['pic']='http://'.$_SERVER["HTTP_HOST"].'/yjb/images/party/'.$value['id'];
          }
          $partys[$k]['name']=$qrcode->name;
          $partys[$k]['nickName']=$qrcode->nickName;
          $partys[$k]['position']=$qrcode->pos;
          $partys[$k]['company']=$qrcode->company;
          if($value['inttime']>=time()){
            $partys[$k]['state']=1;
          }else{
            $partys[$k]['state']=0;
          }
          $record_num=ORM::factory('yjb_record')->where('pid','=',$value['id'])->count_all();
          $partys[$k]['joinnum']=$record_num;
          $partys[$k]['record_num']=$record_num;
          $partys[$k]['residue']=$value['num']-$record_num;
          if($record_num>0){
            $records=ORM::factory('yjb_record')->where('pid','=',$value['id'])->find_all();
            foreach ($records as $key => $value) {
              $partys[$k]['qrcodes'][$key]['avatarUrl']=$value->qrcode->avatarUrl;
              $partys[$k]['qrcodes'][$key]['name']=$value->qrcode->name;
              if($key>=4) break;
            }
          }
          $num++;
        }
        if($num>=$pagsize){
          $result['flag']=1;
        }else{
          $result['flag']=0;
        }
        $result['id']=$id+1;
        $result['partys']=$partys;
        echo json_encode($result);
        exit;
      }

    }
    //我参加过的活动
    public function action_doneparty($id){
      $postStr = file_get_contents("php://input");
      kohana::$log->add('partypost',print_r($postStr,true));
      if($postStr){
        $result1=json_decode($postStr,true);
        $openid=$result1['openid'];
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $qid=$qrcode->id;
        $pagsize=6;
        $offset=$id*$pagsize;
        $time=time();
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $qid=$qrcode->id;
        $partys=DB::query(Database::SELECT,"SELECT * from  yjb_parties where id in (SELECT pid from yjb_records where qid =$qid) order by jointime desc limit $pagsize offset $offset ")->execute()->as_array();
        $num=0;
        foreach ($partys as $k => $value) {
          //$join_date=date('Y-m-d H:i:s',$value['jointime']);
          $qrcode=ORM::factory('yjb_qrcode')->where('id','=',$value['qid'])->find();
          $partys[$k]['join_date']=date('Y-m-d H:i:s',$value['jointime']);
          $partys[$k]['avatarUrl']=$qrcode->avatarUrl;
          if($value['pic']){
            $partys[$k]['pic']='http://'.$_SERVER["HTTP_HOST"].'/yjb/images/party/'.$value['id'];
          }
          $partys[$k]['name']=$qrcode->name;
          $partys[$k]['wx']=$qrcode->wx;
          $partys[$k]['tel']=$qrcode->tel;
          $partys[$k]['nickName']=$qrcode->nickName;
          $partys[$k]['position']=$qrcode->pos;
          $partys[$k]['company']=$qrcode->company;
          if($value['inttime']>=time()){
            $partys[$k]['state']=1;
          }else{
            $partys[$k]['state']=0;
          }
          // $partys[$k]['joinnum']=ORM::factory('yjb_record')->where('pid','=',$value['id'])->count_all();
          $record_num=ORM::factory('yjb_record')->where('pid','=',$value['id'])->count_all();
          $partys[$k]['joinnum']=$record_num;
          $partys[$k]['record_num']=$record_num;
          $partys[$k]['residue']=$value['num']-$record_num;
          if($record_num>0){
            $records=ORM::factory('yjb_record')->where('pid','=',$value['id'])->find_all();
            foreach ($records as $key => $value) {
              $partys[$k]['qrcodes'][$key]['avatarUrl']=$value->qrcode->avatarUrl;
              $partys[$k]['qrcodes'][$key]['name']=$value->qrcode->name;
              if($key>=4) break;
            }
          }
          $num++;
        }
        if($num>=$pagsize){
          $result['flag']=1;
        }else{
          $result['flag']=0;
        }
        $result['id']=$id+1;
        $result['partys']=$partys;
        echo json_encode($result);
        exit;
      }
    }
    public function action_upload(){
        $postStr = file_get_contents("php://input");
        kohana::$log->add('loadpost',print_r($postStr,true));
        if($postStr){
          $result1=json_decode($postStr,true);
          kohana::$log->add('lopost',print_r($result1,true));
          $qrcode=ORM::factory('yjb_party')->where('id','=',$result1['pid'])->find();
          if($_Files){
            $party=ORM::factory('yjb_party');
            $result1['qid']=$qrcode->id;
            $party->values($result1);
            if($party->save()){
              $result['pid'] = $party->id;
            }else{
              $result['code'] = 'fail';
            }
          }else{
            $result['code'] = 'nopeople';
          }
          echo json_encode($result);
          exit;
        }
    }
    public function action_onLogin(){
        if($_GET['code']){
          $charge = $this->charge;
          $url = "https://api.weixin.qq.com/sns/jscode2session?appid=wxfd77b6e69fb10894&secret=cbe8f979862d1a205a502aca15886021&js_code=".$_GET['code']."&grant_type=authorization_code";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $output = curl_exec($ch);
          curl_close($ch);
          $result = json_decode($output,true);
          kohana::$log->add('yjbonLogin',print_r($result,true));
          //kohana::$log->add('lopost',print_r($result1,true));
          // var_dump($result);
          // echo $output;
          $user = ORM::factory('yjb_qrcode')->where('openid','=',$result['openid'])->find();
          if($user->id){
            $result['hasin'] = 'yes';//存在表中
          }else{
            $result['hasin'] = 'no';//不存在该用户
          }
          $result['charge'] = $charge;
          echo json_encode($result);
          exit;
          // exit;
          // require_once Kohana::find_file('vendor', 'xcx_aes/wxBizDataCrypt');
          // $appid = 'wx963d1a274a56374f';
          // $sessionKey = $result['session_key'];
          // $postStr = file_get_contents("php://input");
          // $data=json_decode($postStr,true);
          // // echo 'sessionKey'.$sessionKey;
          // $encryptedData= $data['data']['encryptedData'];
          // // echo 'encryptedData'.$encryptedData;
          // $iv = $data['data']['iv'];
          // // echo 'iv'.$iv;
          // $pc = new WXBizDataCrypt($appid, $sessionKey);
          // $errCode = $pc->decryptData($encryptedData, $iv, $resdata );
          // // ob_clean();
          // if ($errCode == 0) {
          //     $result = json_decode($resdata,true);
          //     print($result['openId']);
          // } else {
          //     print($errCode . "\n");
          // }
        }
    }
    public function action_notify(){
      $postXml = $GLOBALS["HTTP_RAW_POST_DATA"]; //接收微信参数
      if (empty($postXml)) {
          return false;
      }
      //将xml格式转换成数组
      function xmlToArray($xml) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        Kohana::$log->add('notify_end', print_r($val, true));
        return $val;
      }
      $attr = xmlToArray($postXml);
      $total_fee = $attr[total_fee];
      $open_id = $attr[openid];
      $out_trade_no = $attr[out_trade_no];
      $time = $attr[time_end];
    }
    public function action_payfee(){
      require_once Kohana::find_file('vendor/lib', 'WeixinPay');
      $appid='wxfd77b6e69fb10894';
      $openid= $_GET['openid'];
      $mch_id='1494097542';
      $key='RdbXTQeFMety5rw8wuYB3rxd5TWN5tAa';
      $out_trade_no = $mch_id. time();
      $total_fee = $_GET['fee'];
      if(empty($total_fee)) //押金
      {
          $body = "充值押金";
          $total_fee = floatval(5);
      }
       else {
           $body = "充值余额";
           $total_fee = floatval($total_fee*100);
       }
      $weixinpay = new WeixinPay($appid,$openid,$mch_id,$key,$out_trade_no,$body,$total_fee);
      $return=$weixinpay->pay();
      echo json_encode($return);
    }
    public function action_sendMoney() {
        // $config = $this->config;
        // $openid = $userobj->openid;
        // if (!$this->we) {
        // }
        $openid= $_GET['openid'];
        //$money1= $_GET['money'];
        $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
        $qid=$qrcode->id;
        $time=time();
        $ownmoney=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from yjb_scores where qid = $qid and paydate <= $time")->execute()->as_array();
        $ownmoney=$ownmoney[0]['ownmoney'];
        // $fee=0.1;
        // if($ownmoney>=5) $fee=$ownmoney*$this->charge/100;
        $money1=$ownmoney;
        if($money1<=1){
          $result1['state']='FAIL';
          $result1['code']='可提现金额不足1元';
          echo json_encode($result1);
          exit();
        }
        $money=100*$money1;
        //$openid='oOkHq0HqPpbn_xS6tzssaGW8TQpI';
        $wx['appid']='wxfd77b6e69fb10894';
        $wx['appsecret']='cbe8f979862d1a205a502aca15886021';
        $key='RdbXTQeFMety5rw8wuYB3rxd5TWN5tAa';
        require_once Kohana::find_file('vendor', 'weixin/inc');
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $we = new Wechat($wx);
        $mch_id='1494097542';
        $mch_billno = $mch_id. date('YmdHis').rand(1000, 9999); //订单号
        $data["mch_appid"] = $wx['appid'];
        $data["mchid"] = $mch_id; //商户号
        $data["nonce_str"] = $we->generateNonceStr(32);
        $data["partner_trade_no"] = $mch_billno; //订单号
        $data["openid"] = $openid;
        $data["re_user_name"] = $qrcode->name;
        $data["check_name"] = 'NO_CHECK'; //校验用户姓名选项
        // $data["re_user_name"] = $name; //收款用户姓名
        $data["amount"] = $money;
        $data["desc"] = '个人提现';
        $data["spbill_create_ip"] = $_SERVER['SERVER_ADDR'] ?: '127.0.0.1'; //调用接口的机器 Ip 地址
        $data["sign"] = strtoupper(md5($we->getSignature($data, 'trim')."&key=" . $key));
        // echo "<pre>";
        // var_dump($data);
        // echo "</pre>";
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
          ORM::factory('yjb_score')->scoreOut($qrcode,3,$money1);
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
      $qrcode=ORM::factory('yjb_qrcode')->where('openid','=',$openid)->find();
      $qid=$qrcode->id;
      $time=time();
      $result['all_money']=DB::query(Database::SELECT,"SELECT SUM(score) as allmoney from yjb_scores where qid = $qid")->execute()->as_array();
      $result['all_money']=number_format($result['all_money'][0]['allmoney'],2);
      $result['dai_money']=DB::query(Database::SELECT,"SELECT SUM(score) as daimoney from yjb_scores where qid = $qid and paydate > $time")->execute()->as_array();
      $result['dai_money']=number_format($result['dai_money'][0]['daimoney'],2);
      $own_money=DB::query(Database::SELECT,"SELECT SUM(score) as ownmoney from yjb_scores where qid = $qid and paydate <= $time")->execute()->as_array();
      $ownmoney=$ownmoney[0]['ownmoney'];
      // $fee=0.1;
      // if($ownmoney>=5) $fee=$ownmoney*$this->charge/100;
      $result['own_money']=number_format($ownmoney,2);
      echo json_encode($result);
      exit();
    }
    private function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=0) {
        $ch = curl_init();
        // $config = $this->config;
        // $bid = $this->bid;
        $cert_file = DOCROOT."yjb/tmp/apiclient_cert.pem";
        $key_file = DOCROOT."yjb/tmp/apiclient_key.pem";
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
