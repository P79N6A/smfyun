<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtazb extends Controller_Base{
  public $template = 'tpl/blank';
  public function before() {
    kohana::$log->add('qwtwzbdebug:0',time());
      Database::$default = "wdy";
      parent::before();
  }

  public function action_login(){
      kohana::$log->add('qwtwzbdebug:1',time());
      if($_GET){
        kohana::$log->add('get',print_r($_GET,true));
      }
      if($_GET['phoneNumber']&&$_GET['passWord']){
          kohana::$log->add('qwtwzbdebug:2',time());
          $biz=ORM::factory('qwt_login')->where('user','=',$_GET['phoneNumber'])->find();
          kohana::$log->add('qwtwzbdebug:3',time());
          if($biz->id){
            if($biz->pass==$_GET['passWord']){
              kohana::$log->add('qwtwzbdebug:4',time());
              $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$biz->id ");
              $num = $sql->execute()->as_array();
              $use =  $num[0]['CT']/(1024*1024*1024);
              $all = $biz->stream_data;
              kohana::$log->add('qwtwzbdebug:4_2',time());
              // //开播检测
              // $post_data = array(
              //   'sid' =>$biz->shopid
              // );
              // $url = 'http://'.$_SERVER["HTTP_HOST"].'/qwtwzb/aliyun?online=1';
              // $res = $this->request_post($url, $post_data);
              $aliyun = Model::factory('aliyun');
              $res = json_encode($aliyun->getAcsResponse());
              $result['onlines'] = json_decode($res,true);
              // var_dump($result);
              kohana::$log->add('qwtwzbdebug:5',time());
              if($result['onlines']['OnlineInfo']['LiveStreamOnlineInfo']){
                  $onliens = $result['onlines']['OnlineInfo']['LiveStreamOnlineInfo'];
                  for ($i=0; $onliens[$i]['StreamName'] ; $i++) {
                      if($onliens[$i]['StreamName']==$biz->id){
                          $enddata = array('msg'=>'您的账户正在直播中！');
                          kohana::$log->add('get1',print_r($enddata,true));
                          $rtjson =json_encode($enddata);
                          echo $rtjson;
                          exit;
                      }
                  }
              }
              kohana::$log->add('qwtwzbdebug:6',time());
              //流量检测
              if($use>=$all){
                $enddata = array('msg'=>'流量已经用完！请及时购买流量');
                kohana::$log->add('get1',print_r($enddata,true));
                $rtjson =json_encode($enddata);
                echo $rtjson;
                exit;
              }else{
                if($use>=$all*0.9){
                  $residue=$all-$use;
                  $url='rtmp://video-center.alivecdn.com/AppName/'.$biz->id.'?vhost=live.smfyun.com';
                  $bid=$biz->id;
                  $shopname=$biz->name;
                  $username=$biz->user;
                  $sid=$biz->shopid;
                  $logo = $biz->shoplogo;
                  $echomsg='您的流量还剩下不到10%，仅剩余'.$residue.'G,请及时充值流量避免直播时候出现异常！';
                  $enddata = array('msg'=>'OK','url'=>$url,'bid'=>$bid,'username'=>$username,'shopname'=>$shopname,'sid'=>$sid,'logo'=>$logo,'bili'=>number_format($use/$all,2),'usedata'=>number_format($use,2),'alldata'=>number_format($all,2),'echomsg'=>$echomsg);
                  kohana::$log->add('get1',print_r($enddata,true));
                  $rtjson =json_encode($enddata);
                  echo $rtjson;
                  exit;
                }
              }
              kohana::$log->add('qwtwzbdebug:7',time());
              $buy = ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',11)->find();
              if($buy->expiretime && (strtotime($buy->expiretime)+86400) < time()){
                $enddata = array('msg'=>'账号已过期，请续费');
                kohana::$log->add('get1',print_r($enddata,true));
                $rtjson =json_encode($enddata);
                echo $rtjson;
              }else{
                kohana::$log->add('qwtwzbdebug:8',time());
                $url='rtmp://video-center.alivecdn.com/AppName/'.$biz->id.'?vhost=live.smfyun.com';
                $bid=$biz->id;
                $shopname=$biz->name;
                $username=$biz->user;
                $sid=$biz->shopid;
                $logo = $biz->shoplogo;
                $enddata = array('msg'=>'OK','url'=>$url,'bid'=>$bid,'username'=>$username,'shopname'=>$shopname,'sid'=>$sid,'logo'=>$logo,'bili'=>number_format($use/$all,2),'usedata'=>number_format($use,2),'alldata'=>number_format($all,2),'echomsg'=>'');
                kohana::$log->add('get1',print_r($enddata,true));
                $rtjson =json_encode($enddata);
                echo $rtjson;
              }
            }else{
              $enddata = array('msg'=>'密码错误');
              kohana::$log->add('get1',print_r($enddata,true));
              $rtjson =json_encode($enddata);
              echo $rtjson;
            }
          }else{
            $enddata = array('msg'=>'该用户不存在');
            kohana::$log->add('get1',print_r($enddata,true));
            $rtjson =json_encode($enddata);
            echo $rtjson;
          }
      }else {
          $enddata = array('msg'=>'账号密码请填写完整');
          kohana::$log->add('get1',print_r($enddata,true));
          $rtjson =json_encode($enddata);
          echo $rtjson;
      }

  }
  public function action_geturl(){
      if($_GET){
        kohana::$log->add('url',print_r($_GET,true));
      }
      if($_GET['phoneNumber']&&$_GET['passWord']){
          $biz=ORM::factory('qwt_login')->where('user','=',$_GET['phoneNumber'])->find();
          if($biz->id){
              echo $biz->url;
          }else{
            echo '未查询到推流地址';
          }
      }else {
          echo '推流地址获取失败';
      }

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
  public function action_sendmessage(){
    if($_GET){
      kohana::$log->add('sendmessage',print_r($_GET,true));
    }
    if($_GET['bid']){
      $biz=ORM::factory('qwt_login')->where('id','=',$_GET['bid'])->find();
      kohana::$log->add('bid',print_r($_GET['bid'],true));
    }
  }
}
