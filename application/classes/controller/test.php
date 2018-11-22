<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Controller_Test extends Controller_Base {
    public $template = 'tpl/blank';
    public function action_index() {
        $result=$this->sendcouponmsg('oDt2QjjkFaJbjoBKPO4zVpHj0Qgs','www.smfyun.com','2YdXsXpvoyM6Ei6vMi499A4A9OruM-3eupDmzdMHBfo','aaa','bbb');
        echo '<pre>';
        var_dump($result);
    }
    private function sendcouponmsg($openid,$url,$tpl,$name,$remark){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] ='wx72fba01d7f49d2a9';
        $wx = new Wxoauth(6,$options);
        $tplmsg['touser'] = $openid;
        $tplmsg['template_id'] = $tpl;
        $tplmsg['url'] = $url;
        $tplmsg['data']['first']['value'] = '恭喜您';
        $tplmsg['data']['first']['color'] = '#999999';
        $tplmsg['data']['name']['value'] = $name;
        $tplmsg['data']['name']['color'] = '#999999';
        $tplmsg['data']['remark']['value'] = $remark;
        $tplmsg['data']['remark']['color'] = '#999999';
        Kohana::$log->add('couponmsgtpl', print_r($tplmsg, true));
        $result=$wx->sendTemplateMessage($tplmsg);
        Kohana::$log->add('couponmsgresult', print_r($result, true));
        return $result;
    }
}
