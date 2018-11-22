<?php defined('SYSPATH') or die('No direct script access.');
Class Controller_wdy2 {
    public $Keyword;
    public $cdnurl ;
    public $baseurl ;
    public $methodVersion='3.0.0';
    public $wx;
    public $config;
    public function action_aaaaa(){
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        require_once Kohana::find_file('vendor', 'api/rwb');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $appid;//商户appid
        $wx = new Wxoauth(6,$options);
        $qwt_rwb=new rwb(111,$wx,6,1223,313,32,321,312);
        $txtReply = $qwt_rwb->end();
    }
}
    

