<?php defined('SYSPATH') or die('No direct access allowed.');
class Model_Smfyun extends Model {

    /**
     * 设置公众号授权选项
     *
     * @param string $int xxxx
     * @return array $response xxx
     **/
    public $token = 'smfyun';
    public $appId = 'wx4d981fffa8e917e7';
    public $appSecret = '49950b496b4dcccd3fa4ac67ad74ddaf';
    public $encodingAesKey = 'aCR3CJKZszCBi8DELhIPmJzjA6MFh8lqU5zOWdShQXQ';

    public function set_option($bid,$option_name,$option_value){

        $user = ORM::factory('qwt_login')->where('id','=',$bid)->find();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = $user->appid;

        $wx = new Wxoauth($bid,$options);
        $data['component_appid'] = $this->appId;
        $data['authorizer_appid'] = $options['appid'];
        $data['option_name'] = $option_name;
        $data['option_value'] = $option_value;
        // var_dump($data);
        $result = json_decode($wx->authorizer_option_set($data),true);
        return $result;
    }

}
