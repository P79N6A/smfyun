<?php
/**
 * PHP SDK for wechat (using OAuth2)
 *
 * @author Darx Wu <dddeee6@qq.com>
 * @link http://www.jqueryplugin.cn/?p=325
 */


/**
 * OAuth 认证类(OAuth2)
 */
class OAuthV2 {
    /**
     * @ignore
     */
    public $client_id;
    /**
     * @ignore
     */
    public $client_secret;
    /**
     * @ignore
     */
    public $access_token;
    /**
     * @ignore
     */
    public $refresh_token;
    /**
     * Contains the last HTTP status code returned.
     *
     * @ignore
     */
    public $http_code;
    /**
     * Contains the last API call.
     *
     * @ignore
     */
    public $url;
    /**
     * Set timeout default.
     *
     * @ignore
     */
    public $timeout = 30;
    /**
     * Set connect timeout.
     *
     * @ignore
     */
    public $connecttimeout = 30;
    /**
     * Verify SSL Cert.
     *
     * @ignore
     */
    public $ssl_verifypeer = FALSE;
    /**
     * Contains the last HTTP headers returned.
     *
     * @ignore
     */
    public $http_info;

    /**
     * print the debug info
     *
     * @ignore
     */
    public $debug = FALSE;

    /**
     * boundary of multipart
     * @ignore
     */
    public static $boundary = '';

    /**
     * construct WeiboOAuth object
     */
    function __construct($client_id, $client_secret, $access_token = NULL, $refresh_token = NULL) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }

    /**
     * authorize接口
     *
     * @link https://open.weixin.qq.com/connect/qrconnect?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
     *
     * @param string $url 授权后的回调地址,站外应用需与回调地址一致,站内应用需要填写canvas page的地址
     * @param string $response_type 支持的值包括 code 和token 默认值为code
     * @param string $state 用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
     * @return array
     */
    function getAuthorizeURL( $url, $response_type = 'code', $scope = 'snsapi_login', $state = NULL) {
        $params = array();
        $params['appid'] = $this->client_id;
        $params['redirect_uri'] = $url;
        $params['response_type'] = $response_type;
        $params['scope'] = $scope;
        $params['state'] = $state;
        return "https://open.weixin.qq.com/connect/qrconnect?" . http_build_query($params);
    }

    /**
     * access_token接口
     *
     * 对应API：{@link https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code}
     *
     * @param string $type 请求的类型,可以为:code, password, token
     * @param array $keys 其他参数：
     *  - 当$type为code时： array('code'=>..., 'redirect_uri'=>...)
     *  - 当$type为password时： array('username'=>..., 'password'=>...)
     *  - 当$type为token时： array('refresh_token'=>...)
     * @return array
     */
    function getAccessToken( $type = 'code', $keys ) {
        $params = [];
        $params['appid'] = $this->client_id;
        $params['secret'] = $this->client_secret;
        if ( $type === 'token' ) {
            $params['grant_type'] = 'refresh_token';
            $params['refresh_token'] = $keys['refresh_token'];
        } elseif ( $type === 'code' ) {
            $params['grant_type'] = 'authorization_code';
            $params['code'] = $keys['code'];
//      } elseif ( $type === 'password' ) {
//          $params['grant_type'] = 'password';
//          $params['username'] = $keys['username'];
//          $params['password'] = $keys['password'];
        } else {
            throw new \Exception("wrong auth type");
        }

        $response = $this->oAuthRequest('https://api.weixin.qq.com/sns/oauth2/access_token', 'GET', $params);
        $token = json_decode($response, true);
        if ( is_array($token) && !isset($token['errcode']) ) {
            $this->access_token = $token['access_token'];
            $this->refresh_token = $token['refresh_token'];
        } else {
            throw new \Exception("get access token failed." . $token['errmsg']);
        }
        return $token;
    }

    /**
     * 取得用户数据
     * @link https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
     * @param string $type
     * @param unknown $keys
     * @throws \Exception
     * @return mixed
     */
    function getUserInfo($openId) {
        $params = [];
        $params['access_token'] = $this->access_token;
        $params['openid'] = $openId;

        $response = $this->oAuthRequest('https://api.weixin.qq.com/sns/userinfo', 'GET', $params);
        $data = json_decode($response, true);
        if (is_array($data) && !isset($data['errcode'])) {
        } else {
            throw new \Exception("get access token failed." . $data['errmsg']);
        }
        return $data;
    }

    /**
     * Format and sign an OAuth / API request
     *
     * @return string
     * @ignore
     */
    function oAuthRequest($url, $method, $parameters, $multi = false) {
        switch ($method) {
            case 'GET':
                $url = $url . '?' . http_build_query($parameters);
                return $this->http($url, 'GET');
            default:
                $headers = array();
                if (!$multi && (is_array($parameters) || is_object($parameters)) ) {
                    $body = http_build_query($parameters);
                } else {
                    $body = self::build_http_query_multi($parameters);
                    $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
                }
                return $this->http($url, $method, $body, $headers);
        }
    }

    /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
    function http($url, $method, $postfields = NULL, $headers = array()) {
        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
//      curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                    $this->postdata = $postfields;
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($postfields)) {
                    $url = "{$url}?{$postfields}";
                }
        }

        if ( isset($this->access_token) && $this->access_token )
            $headers[] = "Authorization: OAuth2 ".$this->access_token;

        if ( !empty($this->remote_ip) ) {
            if ( defined('SAE_ACCESSKEY') ) {
                $headers[] = "SaeRemoteIP: " . $this->remote_ip;
            } else {
                $headers[] = "API-RemoteIP: " . $this->remote_ip;
            }
        } else {
            if ( !defined('SAE_ACCESSKEY') ) {
                $headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
            }
        }
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;

        if ($this->debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);

            echo "=====headers======\r\n";
            print_r($headers);

            echo '=====request info====='."\r\n";
            print_r( curl_getinfo($ci) );

            echo '=====response====='."\r\n";
            print_r( $response );
        }
        curl_close ($ci);
        return $response;
    }

    /**
     * Get the header info to store.
     *
     * @return int
     * @ignore
     */
    function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * @ignore
     */
    public static function build_http_query_multi($params) {
        if (!$params) return '';

        uksort($params, 'strcmp');

        $pairs = array();

        self::$boundary = $boundary = uniqid('------------------');
        $MPboundary = '--'.$boundary;
        $endMPboundary = $MPboundary. '--';
        $multipartbody = '';

        foreach ($params as $parameter => $value) {

            if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
                $url = ltrim( $value, '@' );
                $content = file_get_contents( $url );
                $array = explode( '?', basename( $url ) );
                $filename = $array[0];

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
                $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
                $multipartbody .= $content. "\r\n";
            } else {
                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
                $multipartbody .= $value."\r\n";
            }

        }

        $multipartbody .= $endMPboundary;
        return $multipartbody;
    }
}