<?php
// namespace Jd\Api\Seller;

// use Jd\Api\IApi;
// use Jd\JFunc;

class SellerVenderInfoGetApi {

    /**
     * @var string
     * @must false
     * @desc 预留的参数为，json格式
     */
    public $extJsonParam;

    /**
     * @var string
     */
    public $method = 'jingdong.seller.vender.info.get';

    /**
     * @var array
     */
    protected $params = array();

    /**
     * SellerVenderInfoGetApi constructor.
     */
    public function __construct() {}

    /**
     * Get api method name
     *
     * @return string
     */
    public function getApiMethod()
    {
        return $this->method;
    }

    /**
     * Get api params
     *
     * @return string
     */

    public function getAppJsonParams()
    {
        $this->params['ext_json_param'] = $this->extJsonParam;
        if (!is_array($params) || !count($params))
        {
            $params = "";
            return ;
        }

        foreach ($params as $key => $val) {
            $params[$key] = is_null($val) ? "" : strval($val);
        }

        //ksort($this->params);
        return json_encode($this->params);
    }

    /**
     * Open api document
     */
    public static function openApiDocument()
    {
        header('Location:http://jos.jd.com/api/detail.htm?apiName=jingdong.seller.vender.info.get&id=493');
    }
}

