#coding=utf-8
# 视频异步检测结果获取接口

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

taskIds = ["ali3ozhT@HV4E$5e@MMpDXQU9-1mNXEp"]

request.set_content(bytearray(json.dumps(taskIds), "utf-8"))

response = clt.do_action(request)
print response
