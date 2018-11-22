<?php defined('SYSPATH') or die('No direct access allowed.');
require_once Kohana::find_file('vendor/aliyun_sdk', 'aliyun-php-sdk-core/Config');
class Model_Aliyun extends Model {

    /**
     * 获取播放流
     *
     * @param string $int xxxx
     * @return array $response xxx
     **/
    public function getAcsResponse()
    {

        // include_once '../aliyun-php-sdk-core/Config.php';

        $iClientProfile = DefaultProfile::getProfile("cn-shanghai", "LTAIvJvaLwxAKeLd", "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt");
        $client = new DefaultAcsClient($iClientProfile);
        //获取当前推送的流
        $request = new live\Request\V20161101\DescribeLiveStreamsOnlineListRequest();
        $request->setDomainName("live.smfyun.com");
        $request->setAppName("AppName");
        // $request->setStreamName($sid);
        $response = $client->getAcsResponse($request);

        return $response;
    }

}
