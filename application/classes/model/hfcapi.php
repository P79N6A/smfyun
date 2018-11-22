<?php defined('SYSPATH') or die('No direct access allowed.');
class Model_hfcapi extends Model {
    // http://agent.weixin.gdsjll.com/agent/login

    // 账号：15271936959
    // 密码：15271936959
    public $appId = 'yll5ad5acbfb06cc';
    public $key = 'd61686185f0b331031bacca28ffea7d8';
    public $base_api = 'http://agent.weixin.gdsjll.com/api/';

    //api总入口
    public function todoapi($arr,$api){
        $arr['appid'] = $this->appId;
        $arr['timestamp'] = time();
        $arr['nonce_str'] = $this->createNoncestr();

        $arr['sign'] = $this->getSign($arr);
        $res = $this->http_post($arr,$this->base_api.$api);
        return $res;
    }
    private function http_post($arr,$url){
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        $post_data = $arr;
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }
    //作用：产生随机字符串，不长于32位
    private function createNoncestr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    //作用：生成签名
    private function getSign($Obj) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, true);
        // echo "按字典序排序参数：".$String.'<br>';
        //签名步骤二：在string后加入KEY
        $String = $String . "&appsecret=" . $this->key;
        // echo "在string后加入KEY：".$String.'<br>';
        //签名步骤三：MD5加密
        $String = md5($String);
        // echo "MD5加密：".$String.'<br>';
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        // echo "所有字符转为大写：".$result_.'<br>';
        return $result_;
    }


    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
}
