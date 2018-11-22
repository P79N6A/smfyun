<?php
// namespace Jd;

// use Jd\Api\IApi;
// use Jd\Net\Requests;

class JClient {

    /**
     * @var null
     */
    protected $requests = null;

    /**
     * @var null
     */
    protected $requestUrl = null;


    public $appKey;
    public $appSecret;
    public $accessToken;
    protected $apiUrlHost = 'https://api.jd.com/routerjson';
    protected $jsonParamKey = '360buy_param_json' ;
    protected $version = '2.0';
    protected $charSet = 'UTF8';
    protected $connTimeout = 60;
    protected $readTimeout = 200;

    protected $urlParams = array();

    protected static $_instance;


    // public function __construct()
    // {
    //     //$config = JRegister::get('config');

    //     $this->appKey       = $config->get('appKey');
    //     $this->appSecret    = $config->get('appSecret');
    //     $this->accessToken  = $config->get('accessToken');
    //     $this->version      = $config->get('version') ;
    //     $this->requestUrl   = $config->get('requestUrl');
    //     $this->jsonParamKey = $config->get('jsonParamKey');
    //     $this->charSet      = $config->get('charSet');
    //     $this->connTimeout  = $config->get('connTimeout');
    //     $this->readTimeout  = $config->get('readTimeout');

    //     $this->urlParams['v']            = $this->version;
    //     $this->urlParams['app_key']      = $this->appKey;
    //     $this->urlParams['access_token'] = $this->accessToken;
    // }


    /**
     * Get JClient object
     *
     * @return JClient
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * @param IApi $api
     * @return JResult|void
     */
    public function execute($api)
    {
        $this->urlParams['v']                   = $this->version;
        $this->urlParams['app_key']             = $this->appKey;
        $this->urlParams['access_token']        = $this->accessToken;
        $this->urlParams['method']            = $api->getMethod();
        $this->urlParams[$this->jsonParamKey] = $api->getJsonParams();
        $this->urlParams['timestamp']         = date('Y-m-d H:i:s');
        $this->urlParams['sign']              = $this->createRequestUrlSign($this->urlParams);

        // Create request url
        $this->requestUrl = $this->createRequestUrl($this->urlParams);

        // Send post request
        //$this->requests = ($this->requestUrl);
        try {
            $resp = $this->sendHttpRequest($this->requestUrl);
        } catch (Exception $e) {
            //todo  要处理异常，记录日志
            print_r($e->getMessage());
            return false;
        }
        $respObject = json_decode($resp);
        return $respObject;
        // Http request success
        // if (200 === $this->requests->httpCode)
        // {
        //     $respJsonStr = $this->requests->response;
        //     $respJsonObj = json_decode($respJsonStr);

        //     /**
        //      * error response template
        //      * {"error_response": {"code":"21","zh_desc":"key=0 信息无效","en_desc":"Invalid app_key"}}
        //      */
        //     if (isset($respJsonObj->error_response))
        //     {
        //         return $result = new JResult(false, $this->urlParams['method'], $this->requestUrl,
        //             $respJsonObj->error_response->code,
        //             $respJsonObj->error_response->zh_desc
        //         );
        //     }

        //     return new JResult(true,
        //         $this->urlParams['method'],
        //         $this->requestUrl, '0', '', $respJsonStr);
        // }

        // return new JResult(false, $this->urlParams['method'], $this->requestUrl,
        //     $this->requests->httpCode,
        //     $this->requests->response);
    }
     public function sendHttpRequest($url, $apiParams = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (is_array($apiParams) && 0 < count($apiParams)) {
            $postBodyString = "";
            foreach ($apiParams as $k => $v) {
                $postBodyString .= "$k=" . urlencode($v) . "&";
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
        }

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new Exception($response, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $response;
    }

    /**
     * Get request obj
     *
     * @return null
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Get request url
     *
     * @return null
     */
    public function getRequestUrl()
    {
        return htmlentities($this->requestUrl);
    }

    /**
     * Create request url
     *
     * @param $params
     * @return string
     */
    private function createRequestUrl($params)
    {
        $url = $this->apiUrlHost . '?';
        $i = 0;
        foreach ($params as $k => $v)
        {
            $url .= "$k=$v";
            ++$i === count($this->urlParams) ?: $url .= '&';
        }

        return $url;
    }

    /**
     * Create sign
     *
     * @param $params
     * @return string
     */
    private function createRequestUrlSign($params)
    {
        if (is_array($params) && 0 < count($params))
        {
            ksort($params);
            $signStr = $this->appSecret;
            foreach ($params as $k => $v)
            {
                $signStr .= "$k$v";
            }
            $signStr .= $this->appSecret;
        }

        $result = isset($signStr) ?: $this->appSecret.$this->appSecret;
        return strtoupper(md5($result));
    }

}
