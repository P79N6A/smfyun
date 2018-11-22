#coding=utf-8
# 同步图片检测服务接口, 会实时返回检测的结果

from aliyunsdkcore import client
from aliyunsdkcore.profile import region_provider
from aliyunsdkgreen.request.v20170112 import ImageAsyncScanResultsRequest
import json
import uuid
import datetime

import ConfigParser
cf = ConfigParser.ConfigParser()
cf.read("aliyun.ak.conf")
# 请替换成你自己的accessKeyId、accessKeySecret, 您可以类似的配置在配置文件里面，也可以直接明文替换
clt = client.AcsClient(cf.get("AK", "accessKeyId"), cf.get("AK", "accessKeySecret"),'cn-hangzhou')
region_provider.modify_point('Green', 'cn-hangzhou', 'green.cn-hangzhou.aliyuncs.com')
request = ImageAsyncScanResultsRequest.ImageAsyncScanResultsRequest()
request.set_accept_format('JSON')

# 同步现支持单张图片，即一个task
taskIds = ["e4c72f02-8120-41b6-a7ee-66684a6936f8-1492678445660"]

request.set_content(bytearray(json.dumps(taskIds), "utf-8"))

response = clt.do_action(request)
print response
result = json.loads(response)
if 200 == result["code"]:
    taskResults = result["data"]
    for taskResult in taskResults:
        if (200 == taskResult["code"]):
            sceneResults = taskResult["results"]

            for sceneResult in sceneResults:
                scene = sceneResult["scene"]
                suggestion = sceneResult["suggestion"]
                print suggestion
                print scene
                #根据scene和suggetion做相关的处理
                #do some
