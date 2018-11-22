<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtydd extends Controller_Base {
    // public $template = 'weixin/smfyun/ydd/tpl/fftpl';
    public $template = 'weixin/smfyun/hby/tpl/blank';
    public $access_token;
    public $config;
    public $openid;
    public $bid;
    public $uid;
    public $methodVersion='3.0.0';
    var $wx;
    var $client;
    public function before() {
        // die('10月15日 23:00 到 10月16日 6:00 服务器升级，请见谅。');
        Database::$default = "wdy";
        parent::before();
        $_SESSION =& Session::instance()->as_array();
        //if (Request::instance()->action == 'index_oauth') return;
        $this->openid = $_SESSION['qwtydd']['userinfo']['openid'];
        $this->userinfo = $_SESSION['qwtydd']['userinfo'];
        $this->bid = $_SESSION['qwtydd']['bid'];
        // $this->uid = $_SESSION['qwtydd']['uid'];
        //只能通过微信打开
    //     if (stripos($_SERVER["HTTP_USER_AGENT"], 'MicroMess') === false && !$_SESSION['qwtydds']['bid']) die('请通过微信访问。');
    }

    public function action_check_location(){
        if (isset($_GET['x'])){
          $x = $_GET['x'];
          $y = $_GET['y'];
          $get_location_url = 'https://apis.map.qq.com/ws/geocoder/v1?location=' . $x . ',' . $y . '&key=MV7BZ-QTDHF-XZVJE-JEEXC-HQWKS-QOBZ7';
          $ch = curl_init(); // 初始化一个 cURL 对象
          curl_setopt($ch, CURLOPT_URL, $get_location_url); // 设置你需要抓取的URL
          curl_setopt($ch, CURLOPT_HEADER, 0); // 设置header
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          $res = curl_exec($ch); // 运行cURL，请求网页
          curl_close($ch); // 关闭一个curl会话
          $json_obj = json_decode($res, true);
          //$nation = $json_obj['result']['address_component']['nation'];
          $province = $json_obj['result']['address_component']['province'];
          $city = $json_obj['result']['address_component']['city'];
          $disrict = $json_obj['result']['address_component']['district'];
          //$street = $json_obj['result']['address_component']['street'];
          $content = $province.$city.$disrict;
          $result['province'] = $province;
          $result['city'] = $city;
          $result['disrict'] = $disrict;
          echo json_encode($result);
          // $area = ORM::factory('qwt_yddqrcode')->where('bid', '=', $this->bid)->where('openid', '=', $this->openid)->find();
          // $area->area = $content;
          // $area->save();
          exit;
        }
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$this->bid)->find()->appid;
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');

        $callback_url = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        if ($_GET['url']) $callback_url = urldecode($_GET['url']);

        if(!$this->bid) Kohana::$log->add('qwtyddbid:', 'location');//写入日志，可以删除

        $wx = new Wxoauth($this->bid,$options);

        $this->template = 'tpl/blank';
        self::before();

        $view = "weixin/smfyun/ydd/ydd";

        require_once Kohana::find_file('vendor', 'weixin/ydd.inc');

        $jsapi = $wx->getJsSign($callback_url);
        // echo '<pre>';
        // var_dump($area);
        // var_dump($jsapi);
        // exit;
        $this->template->content = View::factory($view)
                ->bind('jsapi', $jsapi)
                ->bind('area', $area);
    }
}
