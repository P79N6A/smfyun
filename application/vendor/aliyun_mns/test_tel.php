<?php
require_once('mns-autoloader.php');
use AliyunMNS\Client;
use AliyunMNS\Topic;
use AliyunMNS\Constants;
use AliyunMNS\Model\MailAttributes;
use AliyunMNS\Model\SmsAttributes;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;
class PublishBatchSMSMessageDemo
{
    public function run($tel,$code)
    {
        /**
         * Step 1. 初始化Client
         */
        $this->endPoint = "http://1279612644787835.mns.cn-hangzhou.aliyuncs.com/"; // eg. http://1234567890123456.mns.cn-shenzhen.aliyuncs.com
        $this->accessId = "LTAIvJvaLwxAKeLd";
        $this->accessKey = "F6dGikS2Ovz4Vyi3VjwvzIzSTywsYt";
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
        /**
         * Step 2. 获取主题引用
         */
        $topicName = "sms.topic-cn-hangzhou";
        $topic = $this->client->getTopicRef($topicName);
        /**
         * Step 3. 生成SMS消息属性
         */
        // 3.1 设置发送短信的签名（SMSSignName）和模板（SMSTemplateCode）
        $batchSmsAttributes = new BatchSmsAttributes("神码浮云", "SMS_96275008");
        // 3.2 （如果在短信模板中定义了参数）指定短信模板中对应参数的值
        $batchSmsAttributes->addReceiver($tel, array("code" => $code));
        // $batchSmsAttributes->addReceiver("YourReceiverPhoneNumber2", array("YourSMSTemplateParamKey1" => "value1"));
        $messageAttributes = new MessageAttributes(array($batchSmsAttributes));
        /**
         * Step 4. 设置SMS消息体（必须）
         *
         * 注：目前暂时不支持消息内容为空，需要指定消息内容，不为空即可。
         */
         $messageBody = "smsmessage";
        /**
         * Step 5. 发布SMS消息
         */
        $request = new PublishMessageRequest($messageBody, $messageAttributes);
        try
        {
            $res = $topic->publishMessage($request);
            $result['status'] = $res->isSucceed();
            $result['MessageId'] = $res->getMessageId();
            return $result;
            // echo $res->isSucceed();
            // echo "\n";
            // echo $res->getMessageId();
            // echo "\n";
        }
        catch (MnsException $e)
        {
            $result['error'] = $e;
            return $result;
            // echo $e;
            // echo "\n";
        }
    }
}
// $instance = new PublishBatchSMSMessageDemo();
// $instance->run();
?>
