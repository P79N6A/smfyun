#coding=utf-8
# 异步图片检测服务接口, 需要根据该接口返回的taskId来轮询结果

from aliyunsdkcore import client
from aliyunsdkcore.profile import region_provider
from aliyunsdkgreen.request.v20170112 import ImageAsyncScanRequest
import json
import uuid
import datetime

import ConfigParser
cf = ConfigParser.ConfigParser()
cf.read("aliyun.ak.conf")
# 请替换成你自己的accessKeyId、accessKeySecret, 您可以类似的配置在配置文件里面，也可以直接明文替换
clt = client.AcsClient(cf.get("AK", "accessKeyId"), cf.get("AK", "accessKeySecret"),'cn-hangzhou')
region_provider.modify_point('Green', 'cn-hangzhou', 'green.cn-hangzhou.aliyuncs.com')
request = ImageAsyncScanRequest.ImageAsyncScanRequest()
request.set_accept_format('JSON')

# 异步现支持多张图片，最多50张，即50个task
task1 = {"dataId": str(uuid.uuid1()),
         "url":"http://xxxx.jpg",
         "time":datetime.datetime.now().microsecond
        }

request.set_content(bytearray(json.dumps({"tasks": [task1], "scenes": ["porn"]}), "utf-8"))

response = clt.do_action(request)
print response
result = json.loads(response)
if 200 == result["code"]:
    taskResults = result["data"]
    for taskResult in taskResults:
        if(200 == taskResult["code"]):
           taskId = taskResult["taskId"]
           print taskId
           #将taskId 保存下来，间隔一段时间来轮询结果, 参照ImageAsyncScanResultsRequest

