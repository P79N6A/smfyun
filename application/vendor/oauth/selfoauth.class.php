<?php
//基于微信开放平台的 全网通专用
class Wxoauth
{
  const MSGTYPE_TEXT = 'text';
  const MSGTYPE_IMAGE = 'image';
  const MSGTYPE_LOCATION = 'location';
  const MSGTYPE_LINK = 'link';
  const MSGTYPE_EVENT = 'event';
  const MSGTYPE_MUSIC = 'music';
  const MSGTYPE_NEWS = 'news';
  const MSGTYPE_VOICE = 'voice';
  const MSGTYPE_VIDEO = 'video';
  const EVENT_SUBSCRIBE = 'subscribe';       //订阅
  const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
  const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
  const EVENT_LOCATION = 'LOCATION';         //上报地理位置
  const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
  const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
  const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
  const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
  const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
  const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
  const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
  const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
  const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
  const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
  const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
  const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
  const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
  const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
  const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
  const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
  const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券
  const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
  const AUTH_URL = '/token?grant_type=client_credential&';
  const MENU_CREATE_URL = '/menu/create?';
  const MENU_GET_URL = '/menu/get?';
  const MENU_DELETE_URL = '/menu/delete?';
  const GET_TICKET_URL = '/ticket/getticket?';
  const CALLBACKSERVER_GET_URL = '/getcallbackip?';
  const QRCODE_CREATE_URL='/qrcode/create?';
  const QR_SCENE = 0;
  const QR_LIMIT_SCENE = 1;
  const QRCODE_IMG_URL='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
  const SHORT_URL='/shorturl?';
  const USER_GET_URL='/user/get?';
  const USER_INFO_URL='/user/info?';
  const USER_UPDATEREMARK_URL='/user/info/updateremark?';
  const GROUP_GET_URL='/groups/get?';
  const USER_GROUP_URL='/groups/getid?';
  const GROUP_CREATE_URL='/groups/create?';
  const GROUP_UPDATE_URL='/groups/update?';
  const GROUP_MEMBER_UPDATE_URL='/groups/members/update?';
  const GROUP_MEMBER_BATCHUPDATE_URL='/groups/members/batchupdate?';
  const CUSTOM_SEND_URL='/message/custom/send?';
  const MEDIA_UPLOADNEWS_URL = '/media/uploadnews?';
  const MASS_SEND_URL = '/message/mass/send?';
  const TEMPLATE_SET_INDUSTRY_URL = '/message/template/api_set_industry?';
  const TEMPLATE_ADD_TPL_URL = '/message/template/api_add_template?';
  const TEMPLATE_SEND_URL = '/message/template/send?';
  const MASS_SEND_GROUP_URL = '/message/mass/sendall?';
  const MASS_DELETE_URL = '/message/mass/delete?';
  const MASS_PREVIEW_URL = '/message/mass/preview?';
  const MASS_QUERY_URL = '/message/mass/get?';
  const UPLOAD_MEDIA_URL = 'http://file.api.weixin.qq.com/cgi-bin';
  const MEDIA_UPLOAD_URL = '/media/upload?';
  const MEDIA_UPLOADIMG_URL = '/media/uploadimg?';//图片上传接口
  const MEDIA_GET_URL = '/media/get?';
  const MEDIA_VIDEO_UPLOAD = '/media/uploadvideo?';
    const MEDIA_FOREVER_UPLOAD_URL = '/material/add_material?';
    const MEDIA_FOREVER_NEWS_UPLOAD_URL = '/material/add_news?';
    const MEDIA_FOREVER_NEWS_UPDATE_URL = '/material/update_news?';
    const MEDIA_FOREVER_GET_URL = '/material/get_material?';
    const MEDIA_FOREVER_DEL_URL = '/material/del_material?';
    const MEDIA_FOREVER_COUNT_URL = '/material/get_materialcount?';
    const MEDIA_FOREVER_BATCHGET_URL = '/material/batchget_material?';
  const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
  const OAUTH_AUTHORIZE_URL = '/authorize?';
  ///多客服相关地址
  const CUSTOM_SERVICE_GET_RECORD = '/customservice/getrecord?';
  const CUSTOM_SERVICE_GET_KFLIST = '/customservice/getkflist?';
  const CUSTOM_SERVICE_GET_ONLINEKFLIST = '/customservice/getonlinekflist?';
  const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com'; //以下API接口URL需要使用此前缀
  const OAUTH_TOKEN_URL = '/sns/oauth2/access_token?';
  const OAUTH_REFRESH_URL = '/sns/oauth2/refresh_token?';
  const OAUTH_USERINFO_URL = '/sns/userinfo?';
  const OAUTH_AUTH_URL = '/sns/auth?';
  ///多客服相关地址
  const CUSTOM_SESSION_CREATE = '/customservice/kfsession/create?';
  const CUSTOM_SESSION_CLOSE = '/customservice/kfsession/close?';
  const CUSTOM_SESSION_SWITCH = '/customservice/kfsession/switch?';
  const CUSTOM_SESSION_GET = '/customservice/kfsession/getsession?';
  const CUSTOM_SESSION_GET_LIST = '/customservice/kfsession/getsessionlist?';
  const CUSTOM_SESSION_GET_WAIT = '/customservice/kfsession/getwaitcase?';
  const CS_KF_ACCOUNT_ADD_URL = '/customservice/kfaccount/add?';
  const CS_KF_ACCOUNT_UPDATE_URL = '/customservice/kfaccount/update?';
  const CS_KF_ACCOUNT_DEL_URL = '/customservice/kfaccount/del?';
  const CS_KF_ACCOUNT_UPLOAD_HEADIMG_URL = '/customservice/kfaccount/uploadheadimg?';
  ///卡券相关地址
  const CARD_CREATE                     = '/card/create?';
  const CARD_DELETE                     = '/card/delete?';
  const CARD_UPDATE                     = '/card/update?';
  const CARD_GET                        = '/card/get?';
  const CARD_BATCHGET                   = '/card/batchget?';
  const CARD_MODIFY_STOCK               = '/card/modifystock?';
  const CARD_LOCATION_BATCHADD          = '/card/location/batchadd?';
  const CARD_LOCATION_BATCHGET          = '/card/location/batchget?';
  const CARD_GETCOLORS                  = '/card/getcolors?';
  const CARD_QRCODE_CREATE              = '/card/qrcode/create?';
  const CARD_CODE_CONSUME               = '/card/code/consume?';
  const CARD_CODE_DECRYPT               = '/card/code/decrypt?';
  const CARD_CODE_GET                   = '/card/code/get?';
  const CARD_CODE_UPDATE                = '/card/code/update?';
  const CARD_CODE_UNAVAILABLE           = '/card/code/unavailable?';
  const CARD_TESTWHILELIST_SET          = '/card/testwhitelist/set?';
  const CARD_MEETINGCARD_UPDATEUSER      = '/card/meetingticket/updateuser?';    //更新会议门票
  const CARD_MEMBERCARD_ACTIVATE        = '/card/membercard/activate?';      //激活会员卡
  const CARD_MEMBERCARD_UPDATEUSER      = '/card/membercard/updateuser?';    //更新会员卡
  const CARD_MOVIETICKET_UPDATEUSER     = '/card/movieticket/updateuser?';   //更新电影票(未加方法)
  const CARD_BOARDINGPASS_CHECKIN       = '/card/boardingpass/checkin?';     //飞机票-在线选座(未加方法)
  const CARD_LUCKYMONEY_UPDATE          = '/card/luckymoney/updateuserbalance?';     //更新红包金额
  const SEMANTIC_API_URL = '/semantic/semproxy/search?'; //语义理解
  ///数据分析接口
  static $DATACUBE_URL_ARR = array(        //用户分析
          'user' => array(
                  'summary' => '/datacube/getusersummary?',   //获取用户增减数据（getusersummary）
                  'cumulate' => '/datacube/getusercumulate?',   //获取累计用户数据（getusercumulate）
          ),
          'article' => array(            //图文分析
                  'summary' => '/datacube/getarticlesummary?',    //获取图文群发每日数据（getarticlesummary）
                  'total' => '/datacube/getarticletotal?',    //获取图文群发总数据（getarticletotal）
                  'read' => '/datacube/getuserread?',     //获取图文统计数据（getuserread）
                  'readhour' => '/datacube/getuserreadhour?',   //获取图文统计分时数据（getuserreadhour）
                  'share' => '/datacube/getusershare?',     //获取图文分享转发数据（getusershare）
                  'sharehour' => '/datacube/getusersharehour?',   //获取图文分享转发分时数据（getusersharehour）
          ),
          'upstreammsg' => array(        //消息分析
                  'summary' => '/datacube/getupstreammsg?',   //获取消息发送概况数据（getupstreammsg）
          'hour' => '/datacube/getupstreammsghour?',  //获取消息分送分时数据（getupstreammsghour）
                  'week' => '/datacube/getupstreammsgweek?',  //获取消息发送周数据（getupstreammsgweek）
                  'month' => '/datacube/getupstreammsgmonth?',  //获取消息发送月数据（getupstreammsgmonth）
                  'dist' => '/datacube/getupstreammsgdist?',  //获取消息发送分布数据（getupstreammsgdist）
                  'distweek' => '/datacube/getupstreammsgdistweek?',  //获取消息发送分布周数据（getupstreammsgdistweek）
                  'distmonth' => '/datacube/getupstreammsgdistmonth?',  //获取消息发送分布月数据（getupstreammsgdistmonth）
          ),
          'interface' => array(        //接口分析
                  'summary' => '/datacube/getinterfacesummary?',  //获取接口分析数据（getinterfacesummary）
                  'summaryhour' => '/datacube/getinterfacesummaryhour?',  //获取接口分析分时数据（getinterfacesummaryhour）
          )
  );
  ///微信摇一摇周边
  const SHAKEAROUND_DEVICE_APPLYID = '/shakearound/device/applyid?';//申请设备ID
    const SHAKEAROUND_DEVICE_UPDATE = '/shakearound/device/update?';//编辑设备信息
  const SHAKEAROUND_DEVICE_SEARCH = '/shakearound/device/search?';//查询设备列表
  const SHAKEAROUND_DEVICE_BINDLOCATION = '/shakearound/device/bindlocation?';//配置设备与门店ID的关系
  const SHAKEAROUND_DEVICE_BINDPAGE = '/shakearound/device/bindpage?';//配置设备与页面的绑定关系
    const SHAKEAROUND_MATERIAL_ADD = '/shakearound/material/add?';//上传摇一摇图片素材
  const SHAKEAROUND_PAGE_ADD = '/shakearound/page/add?';//增加页面
  const SHAKEAROUND_PAGE_UPDATE = '/shakearound/page/update?';//编辑页面
  const SHAKEAROUND_PAGE_SEARCH = '/shakearound/page/search?';//查询页面列表
  const SHAKEAROUND_PAGE_DELETE = '/shakearound/page/delete?';//删除页面
  const SHAKEAROUND_USER_GETSHAKEINFO = '/shakearound/user/getshakeinfo?';//获取摇周边的设备及用户信息
  const SHAKEAROUND_STATISTICS_DEVICE = '/shakearound/statistics/device?';//以设备为维度的数据统计接口
    const SHAKEAROUND_STATISTICS_PAGE = '/shakearound/statistics/page?';//以页面为维度的数据统计接口

  private $token;
  private $encodingAesKey;
  private $encrypt_type;
  private $appid;// 商户
  private $appId;//第三方
  private $appsecret;
  private $access_token;
  private $jsapi_ticket;
  private $api_ticket;
  private $user_token;
  private $partnerid;
  private $partnerkey;
  private $paysignkey;
  private $postxml;
  private $_msg;
  private $_funcflag = false;
  private $_receive;
  private $_text_filter = true;
  public $debug =  false;
  public $errCode = 40001;
  public $errMsg = "no access";
  public $logcallback;

  public function __construct($bid,$options=array()){
      //必要参数 传入
      $this->bid = $bid;
      $this->type = 'oauth';//插件类型
      $this->token = 'smfyun';
      $this->encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';
      $this->appid = isset($options['appid'])?$options['appid']:'';//商户 appid
      $this->appId = 'wx4d981fffa8e917e7';//三方 appId
      //$this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
      $this->debug = isset($options['debug'])?$options['debug']:false;
      $this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;
      $this->access_token = $this->get_memcache('qwt.self.access_token'.$bid);
      $this->component_access_token = $component_access_token = $this->get_memcache('component_access_token'.$this->appId);//三方appid
      if($this->access_token){
        //Kohana::$log->add($this->type.'access_token', print_r($this->access_token, true));
      }else{//不存在 就请求一次接口 刷新 accesstoken
         Database::$default = 'qwt';
         // $component_access_token = $this->get_memcache('component_access_token'.$this->appId);//三方appid
         $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$component_access_token;
         $user = ORM::factory('qwt_oauth')->where('id','=',$bid)->find();
         $post_data = array(
              'component_appid' =>$this->appId,
              'authorizer_appid' =>$user->appid,
              "authorizer_refresh_token"=>$user->refresh_token
          );
         $post_data = json_encode($post_data);
         $res = $this->request_post($url, $post_data);
         $res = json_decode($res,true);
         if(substr($res['authorizer_refresh_token'],15)){
            $user->refresh_token = substr($res['authorizer_refresh_token'],15);//截取 refreshtoken
         }
         Kohana::$log->add('self:qwtrefresh'.$bid, print_r(substr($res['authorizer_refresh_token'],15), true));
         Kohana::$log->add('self:qwtaccesstoken2'.$bid, print_r($res['authorizer_access_token'], true));
         $user->expires_in = time()+7200;
         if($bid){
            $user->save();
         }
         $this->set_memcache($res['authorizer_access_token'],'qwt.access_token'.$bid,5400);
         $this->access_token = $res['authorizer_access_token'];
         //return $res['authorizer_access_token'].'<br>'.$res['authorizer_refresh_token'];
      }
   }

    /**
   * 创建菜单(认证后的订阅号可用)
   * @param array $data 菜单数组数据
   * example:
     *  array (
     *      'button' => array (
     *        0 => array (
     *          'name' => '扫码',
     *          'sub_button' => array (
     *              0 => array (
     *                'type' => 'scancode_waitmsg',
     *                'name' => '扫码带提示',
     *                'key' => 'rselfmenu_0_0',
     *              ),
     *              1 => array (
     *                'type' => 'scancode_push',
     *                'name' => '扫码推事件',
     *                'key' => 'rselfmenu_0_1',
     *              ),
     *          ),
     *        ),
     *        1 => array (
     *          'name' => '发图',
     *          'sub_button' => array (
     *              0 => array (
     *                'type' => 'pic_sysphoto',
     *                'name' => '系统拍照发图',
     *                'key' => 'rselfmenu_1_0',
     *              ),
     *              1 => array (
     *                'type' => 'pic_photo_or_album',
     *                'name' => '拍照或者相册发图',
     *                'key' => 'rselfmenu_1_1',
     *              )
     *          ),
     *        ),
     *        2 => array (
     *          'type' => 'location_select',
     *          'name' => '发送位置',
     *          'key' => 'rselfmenu_2_0'
     *        ),
     *      ),
     *  )
     * type可以选择为以下几种，其中5-8除了收到菜单事件以外，还会单独收到对应类型的信息。
     * 1、click：点击推事件
     * 2、view：跳转URL
     * 3、scancode_push：扫码推事件
     * 4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框
     * 5、pic_sysphoto：弹出系统拍照发图
     * 6、pic_photo_or_album：弹出拍照或者相册发图
     * 7、pic_weixin：弹出微信相册发图器
     * 8、location_select：弹出地理位置选择器
   */
  public function createMenu($data){
    if (!$this->access_token ) return false;
    $result = $this->http_post(self::API_URL_PREFIX.self::MENU_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      return true;
    }
    return false;
  }
  public function authorizer_user_get($data){
    // if (!$this->access_token ) return false;
    $result = $this->http_post("https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=".$this->component_access_token,self::json_encode($data));
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return $json;
      }
      return $result;
    }
    return false;
  }
  public function getCardIdList($offset=0,$count=50) {
        if ($count>50)
            $count = 50;
        $data = array(
            'offset' => $offset,
            'count'  => $count,
            "status_list"=> ["CARD_STATUS_VERIFY_OK","CARD_STATUS_NOT_VERIFY","CARD_STATUS_DISPATCH"],
        );
        if (!$this->access_token ) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_BATCHGET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return $json;
            }
            return $json;
        }
        return false;
  }
  public function getCardInfo($card_id) {
        $data = array(
            'card_id' => $card_id,
        );
        if (!$this->access_token ) return false;
        $result = $this->http_post(self::API_BASE_URL_PREFIX . self::CARD_GET . 'access_token=' . $this->access_token, self::json_encode($data));
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg  = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
  }
  public function get_accesstoken(){
    return $this->access_token;
  }
  /**
     * 日志记录，可被重载。
     * @param mixed $log 输入日志
     * @return mixed
     */
    protected function log($log){
        if ($this->debug && function_exists($this->logcallback)) {
          if (is_array($log)) $log = print_r($log,true);
          return call_user_func($this->logcallback,$log);
        }
    }
  /**
     * 获取微信服务器发来的信息
     */
  public function getRev()
  {
      $postStr = file_get_contents("php://input");
        //Kohana::$log->add($this->type.'$poststr', print_r($postStr, true));
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        // Kohana::$log->add($this->type, print_r($postObj, true));
        $timeStamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
         // Kohana::$log->add('$WFB:外面', print_r($this->encodingAesKey, true));
        $pc = new WXBizMsgCrypt($this->token, $this->encodingAesKey, $this->appId);//三方appid
        $Encrypt=$postObj->Encrypt;

        $msg_sign = $_GET["msg_signature"];
        // Kohana::$log->add('$WFB', print_r($msg_sign, true));
        // Kohana::$log->add('$WFB', print_r($timeStamp, true));
        // Kohana::$log->add('$WFB', print_r($nonce, true));

        $format = "<xml><AppId><![CDATA[toUser]]></AppId><Encrypt><![CDATA[%s]]></Encrypt></xml>";
        $from_xml = sprintf($format, $Encrypt);
        // 第三方收到公众号平台发送的消息
        // Kohana::$log->add('$WFB', print_r($from_xml, true));
        $errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $encryptMsg);
        if ($errCode == 0) {
            $this->_receive = (array)simplexml_load_string($encryptMsg, 'SimpleXMLElement', LIBXML_NOCDATA);
            if(strpos($this->_receive['Content'], 'QUERY_AUTH_CODE') !== false){
                $auth_code = str_replace('QUERY_AUTH_CODE:queryauthcode@@@', '', $this->_receive['Content']);
                //Kohana::$log->add($this->type.'$_code', print_r($auth_code, true));
                $ctoken = $this->get_memcache('component_access_token'.$this->appId);//三方appid
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$ctoken;
                $post_data = array(
                  'component_appid' =>$this->appId,
                  'authorization_code' =>$auth_code
                );
                $post_data = json_encode($post_data);
                $res = json_decode($this->request_post($url, $post_data),true);
                //Kohana::$log->add($this->type.'$_res', print_r($res, true));
                //$appid = $res['authorization_info']['authorizer_appid'];
                $this->access_token = $res['authorization_info']['authorizer_access_token'];

                $cachename1 =$this->type.'.access_token'.$this->bid;
                $this->set_memcache($this->access_token, $cachename1 , 5400);//有效期两小时
            }
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"]: '';
            Kohana::$log->add($this->type.'_receive', print_r($this->_receive, true));
        }else{
            Kohana::$log->add($this->type.':错误码', print_r($errCode, true));
        }
    return $this;
  }

  /**
   * 设置发送消息
   * @param array $msg 消息数组
   * @param bool $append 是否在原消息数组追加
   */
    public function Message($msg = '',$append = false){
        if (is_null($msg)) {
          $this->_msg =array();
        }elseif (is_array($msg)) {
          if ($append)
            $this->_msg = array_merge($this->_msg,$msg);
          else
            $this->_msg = $msg;
          return $this->_msg;
        } else {
          return $this->_msg;
        }
    }
  /**
   * 获取微信服务器发来的信息
   */
  public function getRevData()
  {
    return $this->_receive;
  }

  /**
   * 获取消息发送者
   */
  public function getRevFrom() {
    if (isset($this->_receive['FromUserName']))
      return $this->_receive['FromUserName'];
    else
      return false;
  }

  /**
   * 获取消息接受者
   */
  public function getRevTo() {
    if (isset($this->_receive['ToUserName']))
      return $this->_receive['ToUserName'];
    else
      return false;
  }
  /**
   * 获取接收消息内容正文
   */
  public function getRevContent(){
    if (isset($this->_receive['Content']))
      return $this->_receive['Content'];
    else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
      return $this->_receive['Recognition'];
    else
      return false;
  }
  /**
   * 获取上报地理位置事件
   */
  public function getRevEventGeo(){
          if (isset($this->_receive['Latitude'])){
             return array(
        'x'=>$this->_receive['Latitude'],
        'y'=>$this->_receive['Longitude'],
        'precision'=>$this->_receive['Precision'],
      );
    } else
      return false;
  }
  /**
   * 获取接收事件推送
   */
  public function getRevEvent(){
    if (isset($this->_receive['Event'])){
      $array['event'] = $this->_receive['Event'];
    }
    if (isset($this->_receive['EventKey'])){
      $array['key'] = $this->_receive['EventKey'];
    }
    if (isset($array) && count($array) > 0) {
      return $array;
    } else {
      return false;
    }
  }
    /**
   * 获取关注者详细信息
   * @param string $openid
   * @return array {subscribe,openid,nickname,sex,city,province,country,language,headimgurl,subscribe_time,[unionid]}
   * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
   */
  public function getUserInfo($openid){
    if (!$this->access_token ) return false;
    $result = $this->http_get(self::API_URL_PREFIX.self::USER_INFO_URL.'access_token='.$this->access_token.'&openid='.$openid);
    if ($result)
    {
      $json = json_decode($result,true);
      if (isset($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        //echo 1;
      }
      return $json;
    }
    return false;
  }
  public function getUserInfo_list($data){
    if (!$this->access_token ) return false;
    $result = $this->http_post('https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$this->access_token,self::json_encode($data));
    if ($result)
    {
     $json = json_decode($result,true);
     if (isset($json['errcode'])) {
      $this->errCode = $json['errcode'];
      $this->errMsg = $json['errmsg'];
      var_dump($json);
      return 1;
     }
     return $json;
    }
    return 2;
   }
  /**
   * 获取接收TICKET
   */
  public function getRevTicket(){
    if (isset($this->_receive['Ticket'])){
      return $this->_receive['Ticket'];
    } else
      return false;
  }

  /**
  * 获取二维码的场景值
  */
  public function getRevSceneId (){
    if (isset($this->_receive['EventKey'])){
      return str_replace('qrscene_','',$this->_receive['EventKey']);
    } else{
      return false;
    }
  }
   /**
   * 发送客服消息
   * @param array $data 消息结构{"touser":"OPENID","msgtype":"news","news":{...}}
   * @return boolean|array
   */
  public function sendCustomMessage($data){
    if (!$this->access_token ) return false;
    $result = $this->http_post(self::API_URL_PREFIX.self::CUSTOM_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return $json;
      }
      return $json;
    }
    return false;
  }
  public function getUserList($next_openid=''){
    if (!$this->access_token ) return false;
    $result = $this->http_get(self::API_URL_PREFIX.self::USER_GET_URL.'access_token='.$this->access_token.'&next_openid='.$next_openid);
    if ($result)
    {
      $json = json_decode($result,true);
      if (isset($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return $json;
      }
      return $json;
    }
    return false;
  }
  public function sendTemplateMessage($data){

    if (!$this->access_token) return 2;
    $result = $this->http_post(self::API_URL_PREFIX.self::TEMPLATE_SEND_URL.'access_token='.$this->access_token,self::json_encode($data));

    if($result){
      $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return $json;
      }
      return $json;
    }
    return 1;
  }
  public function sendTemplateMessage1($data){
        //Kohana::$log->add("data",print_r($data,true));
        if (!$this->access_token) return false;
        $result = $this->http_post1(self::API_URL_PREFIX.self::TEMPLATE_SEND_URL.'access_token='.$this->access_token,$data);
        //Kohana::$log->add("result",print_r($result,true));
        if($result){
            $json = json_decode($result,true);
            //Kohana::$log->add("json",print_r($json,true));
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return $json;
            }
            return $json;
        }
        return false;
    }
    private function http_post1($url,$data=null){
        //Kohana::$log->add("url", print_r($url,true));
        //Kohana::$log->add("data2", print_r($data,true));
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);

        if(!empty($data)){
            curl_setopt($oCurl, CURLOPT_POST,1);
            curl_setopt($oCurl, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        //$aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        return $sContent;
    }
    /**
   * oauth 授权跳转接口
   * @param string $callback 回调URI
   * @return string
   */
  public function getOauthRedirect($appid,$callback,$state='',$scope='snsapi_userinfo'){
    return self::OAUTH_PREFIX.self::OAUTH_AUTHORIZE_URL.'appid='.$appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
  }

  /**
   * 通过code获取Access Token
   * @return array {access_token,expires_in,refresh_token,openid,scope}
   */
  public function getOauthAccessToken($appid,$appsecret){
    $code = isset($_GET['code'])?$_GET['code']:'';
    if (!$code) return false;
    $result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_TOKEN_URL.'appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code');
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      $this->user_token = $json['access_token'];
      return $json;
    }
    return false;
  }
  public function sns_getOauthRedirect($callback,$state='',$scope='snsapi_userinfo'){
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?'.'appid='.$this->appid.'&redirect_uri='.urlencode($callback).'&response_type=code&scope='.$scope.'&state='.$state.'&component_appid='.$this->appId;
    return $url;
  }
  /**
   * 通过code获取Access Token
   * @return array {access_token,expires_in,refresh_token,openid,scope}
   */
  public function sns_getOauthAccessToken(){
    $code = isset($_GET['code'])?$_GET['code']:'';
    if (!$code) return 1;
    $result = $this->http_get('https://api.weixin.qq.com/sns/oauth2/component/access_token?appid='.$this->appid.'&code='.$code.'&grant_type=authorization_code&component_appid='.$this->appId.'&component_access_token='.$this->component_access_token);
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        echo $code.'<br>';
        echo 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid='.$this->appid.'&code='.$code.'&grant_type=authorization_code&component_appid='.$this->appId.'&component_access_token='.$this->component_access_token.'<br>';
        return $this->errCode.$this->errMsg;
      }
      $this->user_token = $json['access_token'];
      return $json;
    }
    return 3;
  }
  /**
   * 刷新access token并续期
   * @param string $refresh_token
   * @return boolean|mixed
   */
  public function getOauthRefreshToken($refresh_token){
    $result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_REFRESH_URL.'appid='.$this->appid.'&grant_type=refresh_token&refresh_token='.$refresh_token);
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      $this->user_token = $json['access_token'];
      return $json;
    }
    return false;
  }

  /**
   * 获取授权后的用户资料
   * @param string $access_token
   * @param string $openid
   * @return array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
   * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
   */
  public function getOauthUserinfo($access_token,$openid){
    $result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_USERINFO_URL.'access_token='.$access_token.'&openid='.$openid);
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      return $json;
    }
    return false;
  }

  /**
   * 检验授权凭证是否有效
   * @param string $access_token
   * @param string $openid
   * @return boolean 是否有效
   */
  public function getOauthAuth($access_token,$openid){
      $result = $this->http_get(self::API_BASE_URL_PREFIX.self::OAUTH_AUTH_URL.'access_token='.$access_token.'&openid='.$openid);
      if ($result)
      {
          $json = json_decode($result,true);
          if (!$json || !empty($json['errcode'])) {
              $this->errCode = $json['errcode'];
              $this->errMsg = $json['errmsg'];
              return false;
          } else
            if ($json['errcode']==0) return true;
      }
      return false;
  }
  /**
   * 创建二维码ticket
   * @param int|string $scene_id 自定义追踪id,临时二维码只能用数值型
   * @param int $type 0:临时二维码；1:永久二维码(此时expire参数无效)；2:永久二维码(此时expire参数无效)
   * @param int $expire 临时二维码有效期，最大为604800秒
   * @return array('ticket'=>'qrcode字串','expire_seconds'=>604800,'url'=>'二维码图片解析后的地址')
   */
  public function getQRCode($scene_id,$type=0,$expire=604800){
    if (!$this->access_token ) return false;
    $type = ($type && is_string($scene_id))?2:$type;
    $data = array(
      // 'action_name'=>$type?($type == 2?"QR_LIMIT_STR_SCENE":"QR_LIMIT_SCENE"):"QR_SCENE",
      'action_name'=>$type?($type == 2?"QR_LIMIT_STR_SCENE":"QR_LIMIT_SCENE"):"QR_STR_SCENE",
      'expire_seconds'=>$expire,
      // 'action_info'=>array('scene'=>($type == 2?array('scene_str'=>$scene_id):array('scene_id'=>$scene_id)))
      'action_info'=>array('scene'=>$type?($type == 2?array('scene_str'=>$scene_id):array('scene_id'=>$scene_id)):array('scene_str'=>$scene_id))
    );
    if ($type == 1) {
      unset($data['expire_seconds']);
    }
    $result = $this->http_post(self::API_URL_PREFIX.self::QRCODE_CREATE_URL.'access_token='.$this->access_token,self::json_encode($data));
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return $json;
      }
      return $json;
    }
    return 3;
  }

  /**
   * 获取二维码图片
   * @param string $ticket 传入由getQRCode方法生成的ticket参数
   * @return string url 返回http地址
   */
  public function getQRUrl($ticket) {
    return self::QRCODE_IMG_URL.urlencode($ticket);
  }
  /**
   * 上传临时素材，有效期为3天(认证后的订阅号可用)
   * 注意：上传大文件时可能需要先调用 set_time_limit(0) 避免超时
   * 注意：数组的键值任意，但文件名前必须加@，使用单引号以避免本地路径斜杠被转义
     * 注意：临时素材的media_id是可复用的！
   * @param array $data {"media":'@Path\filename.jpg'}
   * @param type 类型：图片:image 语音:voice 视频:video 缩略图:thumb
   * @return boolean|array
   */
  public function uploadMedia($data, $type){
    if (!$this->access_token ) return false;
    //原先的上传多媒体文件接口使用 self::UPLOAD_MEDIA_URL 前缀
    $result = $this->http_post(self::API_URL_PREFIX.self::MEDIA_UPLOAD_URL.'access_token='.$this->access_token.'&type='.$type,$data,true);
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      return $json;
    }
    return false;
  }
    /**
   * 过滤文字回复\r\n换行符
   * @param string $text
   * @return string|mixed
   */
  private function _auto_text_filter($text) {
    if (!$this->_text_filter) return $text;
    return str_replace("\r\n", "\n", $text);
  }
  /**
   * 设置回复消息
   * Example: $obj->text('hello')->reply();
   * @param string $text
   */
  public function text($text='')
  {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'MsgType'=>self::MSGTYPE_TEXT,
      'Content'=>$this->_auto_text_filter($text),
      'CreateTime'=>time(),
      'FuncFlag'=>$FuncFlag
    );
    $this->Message($msg);
    return $this;
  }
  /**
   * 设置回复消息
   * Example: $obj->image('media_id')->reply();
   * @param string $mediaid
   */
  public function image($mediaid='')
  {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'MsgType'=>self::MSGTYPE_IMAGE,
      'Image'=>array('MediaId'=>$mediaid),
      'CreateTime'=>time(),
      'FuncFlag'=>$FuncFlag
    );
    $this->Message($msg);
    return $this;
  }

  /**
   * 设置回复消息
   * Example: $obj->voice('media_id')->reply();
   * @param string $mediaid
   */
  public function voice($mediaid='')
  {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'MsgType'=>self::MSGTYPE_VOICE,
      'Voice'=>array('MediaId'=>$mediaid),
      'CreateTime'=>time(),
      'FuncFlag'=>$FuncFlag
    );
    $this->Message($msg);
    return $this;
  }

  /**
   * 设置回复消息
   * Example: $obj->video('media_id','title','description')->reply();
   * @param string $mediaid
   */
  public function video($mediaid='',$title='',$description='')
  {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'MsgType'=>self::MSGTYPE_VIDEO,
      'Video'=>array(
              'MediaId'=>$mediaid,
              'Title'=>$title,
              'Description'=>$description
      ),
      'CreateTime'=>time(),
      'FuncFlag'=>$FuncFlag
    );
    $this->Message($msg);
    return $this;
  }

  /**
   * 设置回复音乐
   * @param string $title
   * @param string $desc
   * @param string $musicurl
   * @param string $hgmusicurl
   * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
   */
  public function music($title,$desc,$musicurl,$hgmusicurl='',$thumbmediaid='') {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'CreateTime'=>time(),
      'MsgType'=>self::MSGTYPE_MUSIC,
      'Music'=>array(
        'Title'=>$title,
        'Description'=>$desc,
        'MusicUrl'=>$musicurl,
        'HQMusicUrl'=>$hgmusicurl
      ),
      'FuncFlag'=>$FuncFlag
    );
    if ($thumbmediaid) {
      $msg['Music']['ThumbMediaId'] = $thumbmediaid;
    }
    $this->Message($msg);
    return $this;
  }

  /**
   * 设置回复图文
   * @param array $newsData
   * 数组结构:
   *  array(
   *    "0"=>array(
   *      'Title'=>'msg title',
   *      'Description'=>'summary text',
   *      'PicUrl'=>'http://www.domain.com/1.jpg',
   *      'Url'=>'http://www.domain.com/1.html'
   *    ),
   *    "1"=>....
   *  )
   */
  /**
   * 设置回复图文
   * @param array $newsData
   * 数组结构:
   *  array(
   *    "0"=>array(
   *      'Title'=>'msg title',
   *      'Description'=>'summary text',
   *      'PicUrl'=>'http://www.domain.com/1.jpg',
   *      'Url'=>'http://www.domain.com/1.html'
   *    ),
   *    "1"=>....
   *  )
   */
  public function news($newsData=array())
  {
    $FuncFlag = $this->_funcflag ? 1 : 0;
    $count = count($newsData);

    $msg = array(
      'ToUserName' => $this->getRevFrom(),
      'FromUserName'=>$this->getRevTo(),
      'MsgType'=>self::MSGTYPE_NEWS,
      'CreateTime'=>time(),
      'ArticleCount'=>$count,
      'Articles'=>$newsData,
      'FuncFlag'=>$FuncFlag
    );
    $this->Message($msg);
    return $this;
  }

  /**
   *
   * 回复微信服务器, 此函数支持链式操作
   * Example: $this->text('msg tips')->reply();
   * @param string $msg 要发送的信息, 默认取$this->_msg
   * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
   */
  public function reply($msg=array(),$return = false)
  {
    if (empty($msg)) {
        if (empty($this->_msg))   //防止不先设置回复内容，直接调用reply方法导致异常
            return false;
      $msg = $this->_msg;
    }
    $xmldata=  $this->xml_encode($msg);
    $this->log($xmldata);
    if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
        $pc = new Prpcrypt($this->encodingAesKey);
        $array = $pc->encrypt($xmldata, $this->appId);
        $ret = $array[0];
        if ($ret != 0) {
            $this->log('encrypt err!');
            return false;
        }
        $timestamp = time();
        $nonce = rand(77,999)*rand(605,888)*rand(11,99);
        $encrypt = $array[1];
        $tmpArr = array($this->token, $timestamp, $nonce,$encrypt);//比普通公众平台多了一个加密的密文
        sort($tmpArr, SORT_STRING);
        $signature = implode($tmpArr);
        $signature = sha1($signature);
        $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
        $this->log($xmldata);
    }
    Kohana::$log->add($this->type.'_xml',print_r($xmldata,true));
    if ($return)
      return $xmldata;
    else
      echo $xmldata;
  }

  /**
   * 获取JSAPI授权TICKET
   * @param string $appid 用于多个appid时使用,可空
   * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
   */
  public function getJsTicket($appid='',$jsapi_ticket=''){
    if (!$this->access_token ) return false;
    if (!$appid) $appid = $this->appid;
    if ($jsapi_ticket) { //手动指定token，优先使用
        $this->jsapi_ticket = $jsapi_ticket;
        return $this->jsapi_ticket;
    }
    $authname = 'wechat_jsapi_ticket'.$appid;
    if ($rs = $this->get_memcache($authname))  {
      $this->jsapi_ticket = $rs;
      return $rs;
    }
    $result = $this->http_get(self::API_URL_PREFIX.self::GET_TICKET_URL.'access_token='.$this->access_token.'&type=jsapi');
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      $this->jsapi_ticket = $json['ticket'];
      $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
      $this->set_memcache($authname,$this->jsapi_ticket,$expire);
      return $this->jsapi_ticket;
    }
    return false;
  }
  /**
   * 获取JsApi使用签名
   * @param string $url 网页的URL，自动处理#及其后面部分
   * @param string $timestamp 当前时间戳 (为空则自动生成)
   * @param string $noncestr 随机串 (为空则自动生成)
   * @param string $appid 用于多个appid时使用,可空
   * @return array|bool 返回签名字串
   */
  public function getJsSign($url, $timestamp=0, $noncestr='', $appid=''){
      if (!$this->jsapi_ticket && !$this->getJsTicket($appid) || !$url) return false;
      if (!$timestamp)
          $timestamp = time();
      if (!$noncestr)
          $noncestr = $this->generateNonceStr();
      $ret = strpos($url,'#');
      if ($ret)
          $url = substr($url,0,$ret);
      $url = trim($url);
      if (empty($url))
          return false;
      $arrdata = array("timestamp" => $timestamp, "noncestr" => $noncestr, "url" => $url, "jsapi_ticket" => $this->jsapi_ticket);
      $sign = $this->getSignature($arrdata);
      if (!$sign)
          return false;
      $signPackage = array(
              "appId"     => $this->appid,
              "nonceStr"  => $noncestr,
              "timestamp" => $timestamp,
              "url"       => $url,
              "signature" => $sign
      );
      return $signPackage;
  }
    /**
   * 获取签名
   * @param array $arrdata 签名数组
   * @param string $method 签名方法
   * @return boolean|string 签名值
   */
  public function getSignature($arrdata,$method="sha1") {
    if (!function_exists($method)) return false;
    ksort($arrdata);
    $paramstring = "";
    foreach($arrdata as $key => $value)
    {
      if(strlen($paramstring) == 0)
        $paramstring .= $key . "=" . $value;
      else
        $paramstring .= "&" . $key . "=" . $value;
    }
    $Sign = $method($paramstring);
    return $Sign;
  }
  /**
   * 获取微信卡券api_ticket
   * @param string $appid 用于多个appid时使用,可空
   * @param string $api_ticket 手动指定api_ticket，非必要情况不建议用
   */
  public function getJsCardTicket($appid='',$api_ticket=''){
    if (!$this->access_token ) return false;
    if (!$appid) $appid = $this->appid;
    if ($api_ticket) { //手动指定token，优先使用
        $this->api_ticket = $api_ticket;
        return $this->api_ticket;
    }
    $authname = 'wechat_api_ticket_wxcard'.$appid;
    if ($rs = $this->get_memcache($authname))  {
      $this->api_ticket = $rs;
      return $rs;
    }
    $result = $this->http_get(self::API_URL_PREFIX.self::GET_TICKET_URL.'access_token='.$this->access_token.'&type=wx_card');
    if ($result)
    {
      $json = json_decode($result,true);
      if (!$json || !empty($json['errcode'])) {
        $this->errCode = $json['errcode'];
        $this->errMsg = $json['errmsg'];
        return false;
      }
      $this->api_ticket = $json['ticket'];
      $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
      $this->set_memcache($authname,$this->api_ticket,$expire);
      return $this->api_ticket;
    }
    return false;
  }

  /**
   * 获取微信卡券签名
   * @param array $arrdata 签名数组
   * @param string $method 签名方法
   * @return boolean|string 签名值
   */
  public function getTicketSignature($arrdata,$method="sha1") {
    if (!function_exists($method)) return false;
    $newArray = array();
    foreach($arrdata as $key => $value)
    {
      array_push($newArray,(string)$value);
    }
    sort($newArray,SORT_STRING);
    return $method(implode($newArray));
  }

  /**
   * 生成随机字串
   * @param number $length 长度，默认为16，最长为32字节
   * @return string
   */
  public function generateNonceStr($length=16){
    // 密码字符集，可任意添加你需要的字符
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for($i = 0; $i < $length; $i++)
    {
      $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;
  }

    /**
   * XML编码
   * @param mixed $data 数据
   * @param string $root 根节点名
   * @param string $item 数字索引的子节点名
   * @param string $attr 根节点属性
   * @param string $id   数字索引子节点key转换的属性名
   * @param string $encoding 数据编码
   * @return string
  */
  public function xml_encode($data, $root='xml', $item='item', $attr='', $id='id', $encoding='utf-8') {
      if(is_array($attr)){
          $_attr = array();
          foreach ($attr as $key => $value) {
              $_attr[] = "{$key}=\"{$value}\"";
          }
          $attr = implode(' ', $_attr);
      }
      $attr   = trim($attr);
      $attr   = empty($attr) ? '' : " {$attr}";
      $xml   = "<{$root}{$attr}>";
      $xml   .= self::data_to_xml($data, $item, $id);
      $xml   .= "</{$root}>";
      return $xml;
  }
  private function generate($encrypt, $signature, $timestamp, $nonce)
  {
      //格式化加密信息
      $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
      return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
  }
  /**
   * GET 请求
   * @param string $url
   */
  private function http_get($url){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
      return $sContent;
    }else{
      return false;
    }
  }
  /**
   * POST 请求
   * @param string $url
   * @param array $param
   * @param boolean $post_file 是否文件上传
   * @return string content
   */
  private function http_post($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (is_string($param) || $post_file) {
      $strPOST = $param;
    } else {
      $aPOST = array();
      foreach($param as $key=>$val){
        $aPOST[] = $key."=".urlencode($val);
      }
      $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
      return $sContent;
    }else{
      return false;
    }
  }
   private function get_memcache($name){
      $mem = Cache::instance('memcache');
      if ($result = $mem->get($name)) return $result;
      return false;
   }

   private function set_memcache($value,$name,$time){
      $mem = Cache::instance('memcache');
      $cache = $mem->set($name, $value, $time);
      return $cache;
   }

   private function request_post($url = '', $param = '') {
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

  public static function data_to_xml($data) {
      $xml = '';
      foreach ($data as $key => $val) {
          is_numeric($key) && $key = "item id=\"$key\"";
          $xml    .=  "<$key>";
          $xml    .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
          list($key, ) = explode(' ', $key);
          $xml    .=  "</$key>";
      }
      return $xml;
  }
  public static function xmlSafeStr($str){
    return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
  }

    /**
   * 微信api不支持中文转义的json结构
   * @param array $arr
   */
  static function json_encode($arr) {
    $parts = array ();
    $is_list = false;
    //Find out if the given array is a numerical array
    $keys = array_keys ( $arr );
    $max_length = count ( $arr ) - 1;
    if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
      $is_list = true;
      for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
        if ($i != $keys [$i]) { //A key fails at position check.
          $is_list = false; //It is an associative array.
          break;
        }
      }
    }
    foreach ( $arr as $key => $value ) {
      if (is_array ( $value )) { //Custom handling for arrays
        if ($is_list)
          $parts [] = self::json_encode ( $value ); /* :RECURSION: */
        else
          $parts [] = '"' . $key . '":' . self::json_encode ( $value ); /* :RECURSION: */
      } else {
        $str = '';
        if (! $is_list)
          $str = '"' . $key . '":';
        //Custom handling for multiple data types
        if (!is_string ( $value ) && is_numeric ( $value ) && $value<2000000000)
          $str .= $value; //Numbers
        elseif ($value === false)
        $str .= 'false'; //The booleans
        elseif ($value === true)
        $str .= 'true';
        else
          $str .= '"' . addslashes ( $value ) . '"'; //All other things
        // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
        $parts [] = $str;
      }
    }
    $json = implode ( ',', $parts );
    if ($is_list)
      return '[' . $json . ']'; //Return numerical JSON
    return '{' . $json . '}'; //Return associative JSON
  }
}


/**
 * 1.第三方回复加密消息给公众平台；
 * 2.第三方收到公众平台发送的消息，验证消息的安全性，并对消息进行解密。
 */
class WXBizMsgCrypt
{
  private $token;
  private $encodingAesKey;
  private $appId;

  /**
   * 构造函数
   * @param $token string 公众平台上，开发者设置的token
   * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
   * @param $appId string 公众平台的appId
   */
  public function WXBizMsgCrypt($token, $encodingAesKey, $appId)
  {
    $this->token = $token;
    $this->encodingAesKey = $encodingAesKey;
    $this->appId = $appId;
  }

  /**
   * 将公众平台回复用户的消息加密打包.
   * <ol>
   *    <li>对要发送的消息进行AES-CBC加密</li>
   *    <li>生成安全签名</li>
   *    <li>将消息密文和安全签名打包成xml格式</li>
   * </ol>
   *
   * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
   * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
   * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
   * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
   *                      当return返回0时有效
   *
   * @return int 成功0，失败返回对应的错误码
   */
  public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg)
  {
    $pc = new Prpcrypt($this->encodingAesKey);

    //加密
    $array = $pc->encrypt($replyMsg, $this->appId);
    $ret = $array[0];
    if ($ret != 0) {
      return $ret;
    }

    if ($timeStamp == null) {
      $timeStamp = time();
    }
    $encrypt = $array[1];

    //生成安全签名
    $sha1 = new SHA1;
    $array = $sha1->getSHA1($this->token, $timeStamp, $nonce, $encrypt);
    $ret = $array[0];
    if ($ret != 0) {
      return $ret;
    }
    $signature = $array[1];

    //生成发送的xml
    $xmlparse = new XMLParse;
    $encryptMsg = $xmlparse->generate($encrypt, $signature, $timeStamp, $nonce);
    return ErrorCode::$OK;
  }


  /**
   * 检验消息的真实性，并且获取解密后的明文.
   * <ol>
   *    <li>利用收到的密文生成安全签名，进行签名验证</li>
   *    <li>若验证通过，则提取xml中的加密消息</li>
   *    <li>对消息进行解密</li>
   * </ol>
   *
   * @param $msgSignature string 签名串，对应URL参数的msg_signature
   * @param $timestamp string 时间戳 对应URL参数的timestamp
   * @param $nonce string 随机串，对应URL参数的nonce
   * @param $postData string 密文，对应POST请求的数据
   * @param &$msg string 解密后的原文，当return返回0时有效
   *
   * @return int 成功0，失败返回对应的错误码
   */
  public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg)
  {
    if (strlen($this->encodingAesKey) != 43) {
      return ErrorCode::$IllegalAesKey;
    }

    $pc = new Prpcrypt($this->encodingAesKey);

    //提取密文
    $xmlparse = new XMLParse;
    $array = $xmlparse->extract($postData);
    $ret = $array[0];

    if ($ret != 0) {
      return $ret;
    }

    if ($timestamp == null) {
      $timestamp = time();
    }

    $encrypt = $array[1];
    $touser_name = $array[2];

    //验证安全签名
    $sha1 = new SHA1;
    $array = $sha1->getSHA1($this->token, $timestamp, $nonce, $encrypt);
    $ret = $array[0];

    if ($ret != 0) {
      return $ret;
    }

    $signature = $array[1];
    if ($signature != $msgSignature) {
      return ErrorCode::$ValidateSignatureError;
    }

    $result = $pc->decrypt($encrypt, $this->appId);
    if ($result[0] != 0) {
      return $result[0];
    }
    $msg = $result[1];

    return ErrorCode::$OK;
  }

}
/**
 * SHA1 class
 *
 * 计算公众平台的消息签名接口.
 */
class SHA1
{
  /**
   * 用SHA1算法生成安全签名
   * @param string $token 票据
   * @param string $timestamp 时间戳
   * @param string $nonce 随机字符串
   * @param string $encrypt 密文消息
   */
  public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
  {
    //排序
    try {
      $array = array($encrypt_msg, $token, $timestamp, $nonce);
      sort($array, SORT_STRING);
      $str = implode($array);
      return array(ErrorCode::$OK, sha1($str));
    } catch (Exception $e) {
      //print $e . "\n";
      return array(ErrorCode::$ComputeSignatureError, null);
    }
  }

}
class PKCS7Encoder
{
  public static $block_size = 32;

  /**
   * 对需要加密的明文进行填充补位
   * @param $text 需要进行填充补位操作的明文
   * @return 补齐明文字符串
   */
  function encode($text)
  {
    $block_size = PKCS7Encoder::$block_size;
    $text_length = strlen($text);
    //计算需要填充的位数
    $amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
    if ($amount_to_pad == 0) {
      $amount_to_pad = PKCS7Encoder::block_size;
    }
    //获得补位所用的字符
    $pad_chr = chr($amount_to_pad);
    $tmp = "";
    for ($index = 0; $index < $amount_to_pad; $index++) {
      $tmp .= $pad_chr;
    }
    return $text . $tmp;
  }

  /**
   * 对解密后的明文进行补位删除
   * @param decrypted 解密后的明文
   * @return 删除填充补位后的明文
   */
  function decode($text)
  {

    $pad = ord(substr($text, -1));
    if ($pad < 1 || $pad > 32) {
      $pad = 0;
    }
    return substr($text, 0, (strlen($text) - $pad));
  }

}



/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
  public $key;

  function Prpcrypt($k)
  {
    $this->key = base64_decode($k . "=");
  }

  /**
   * 对明文进行加密
   * @param string $text 需要加密的明文
   * @return string 加密后的密文
   */
  public function encrypt($text, $appid)
  {

    try {
      //获得16位随机字符串，填充到明文之前
      $random = $this->getRandomStr();
      $text = $random . pack("N", strlen($text)) . $text . $appid;
      // 网络字节序
      $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
      $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
      $iv = substr($this->key, 0, 16);
      //使用自定义的填充方式对明文进行补位填充
      $pkc_encoder = new PKCS7Encoder;
      $text = $pkc_encoder->encode($text);
      mcrypt_generic_init($module, $this->key, $iv);
      //加密
      $encrypted = mcrypt_generic($module, $text);
      mcrypt_generic_deinit($module);
      mcrypt_module_close($module);

      //print(base64_encode($encrypted));
      //使用BASE64对加密后的字符串进行编码
      return array(ErrorCode::$OK, base64_encode($encrypted));
    } catch (Exception $e) {
      //print $e;
      return array(ErrorCode::$EncryptAESError, null);
    }
  }

  /**
   * 对密文进行解密
   * @param string $encrypted 需要解密的密文
   * @return string 解密得到的明文
   */
  public function decrypt($encrypted, $appid)
  {

    try {
      //使用BASE64对需要解密的字符串进行解码
      $ciphertext_dec = base64_decode($encrypted);
      $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
      $iv = substr($this->key, 0, 16);
      mcrypt_generic_init($module, $this->key, $iv);

      //解密
      $decrypted = mdecrypt_generic($module, $ciphertext_dec);
      mcrypt_generic_deinit($module);
      mcrypt_module_close($module);
    } catch (Exception $e) {
      return array(ErrorCode::$DecryptAESError, null);
    }


    try {
      //去除补位字符
      $pkc_encoder = new PKCS7Encoder;
      $result = $pkc_encoder->decode($decrypted);
      //去除16位随机字符串,网络字节序和AppId
      if (strlen($result) < 16)
        return "";
      $content = substr($result, 16, strlen($result));
      $len_list = unpack("N", substr($content, 0, 4));
      $xml_len = $len_list[1];
      $xml_content = substr($content, 4, $xml_len);
      $from_appid = substr($content, $xml_len + 4);
    } catch (Exception $e) {
      //print $e;
      return array(ErrorCode::$IllegalBuffer, null);
    }
    if ($from_appid != $appid)
      return array(ErrorCode::$ValidateAppidError, null);
    return array(0, $xml_content);

  }


  /**
   * 随机生成16位字符串
   * @return string 生成的字符串
   */
  function getRandomStr()
  {

    $str = "";
    $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($str_pol) - 1;
    for ($i = 0; $i < 16; $i++) {
      $str .= $str_pol[mt_rand(0, $max)];
    }
    return $str;
  }

}
/**
 * XMLParse class
 *
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 */
class XMLParse
{

  /**
   * 提取出xml数据包中的加密消息
   * @param string $xmltext 待提取的xml字符串
   * @return string 提取出的加密消息字符串
   */
  public function extract($xmltext)
  {
    try {
      $xml = new DOMDocument();
      $xml->loadXML($xmltext);
      $array_e = $xml->getElementsByTagName('Encrypt');
      $array_a = $xml->getElementsByTagName('ToUserName');
      $encrypt = $array_e->item(0)->nodeValue;
      $tousername = $array_a->item(0)->nodeValue;
      return array(0, $encrypt, $tousername);
    } catch (Exception $e) {
      //print $e . "\n";
      return array(ErrorCode::$ParseXmlError, null, null);
    }
  }

  /**
   * 生成xml消息
   * @param string $encrypt 加密后的消息密文
   * @param string $signature 安全签名
   * @param string $timestamp 时间戳
   * @param string $nonce 随机字符串
   */
  public function generate($encrypt, $signature, $timestamp, $nonce)
  {
    $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
    return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
  }

}
/**
 * error code
 * 仅用作类内部使用，不用于官方API接口的errCode码
 */
class ErrorCode
{
  public static $OK = 0;
  public static $ValidateSignatureError = -40001;
  public static $ParseXmlError = -40002;
  public static $ComputeSignatureError = -40003;
  public static $IllegalAesKey = -40004;
  public static $ValidateAppidError = -40005;
  public static $EncryptAESError = -40006;
  public static $DecryptAESError = -40007;
  public static $IllegalBuffer = -40008;
  public static $EncodeBase64Error = -40009;
  public static $DecodeBase64Error = -40010;
  public static $GenReturnXmlError = -40011;
}
?>
