#coding=utf-8
# 视频异步检测接口

from aliyunsdkcore import client
from aliyunsdkcore.profile import region_provider
from aliyunsdkgreen.request.v20170112 import VideoAsyncScanRequest
import json
import uuid
import datetime

import ConfigParser
cf = ConfigParser.ConfigParser()
cf.read("aliyun.ak.conf")
# 请替换成你自己的accessKeyId、accessKeySecret, 您可以类似的配置在配置文件里面，也可以直接明文替换
clt = client.AcsClient(cf.get("AK", "accessKeyId"), cf.get("AK", "accessKeySecret"),'cn-hangzhou')
region_provider.modify_point('Green', 'cn-hangzhou', 'green.cn-hangzhou.aliyuncs.com')
request = VideoAsyncScanRequest.VideoAsyncScanRequest()
request.set_accept_format('JSON')


frame1 = {
          "offset":0,
          "url": "http://pic12.nipic.com/20110221/6727421_210944911000_2.jpg"
         }

frame2 = {
          "offset":5,
          "url": "http://pic12.nipic.com/20110221/6727421_210944911000_3.jpg"
         }
frame3 = {
          "offset":10,
          "url": "http://pic12.nipic.com/20110221/6727421_210944911000_4.jpg"
         }

task1 = {"dataId": str(uuid.uuid1()),
         "interval":5,
         "length":3600,
         "url": "http://cloud.video.taobao.com/play/u/228898015/p/1/e/1/t/1/***.swf",
         "frames": [frame1, frame2, frame3]
        }


request.set_content(bytearray(json.dumps({"tasks": [task1], "scenes": ["porn"]}), "utf-8"))

response = clt.do_action(request)
print response
