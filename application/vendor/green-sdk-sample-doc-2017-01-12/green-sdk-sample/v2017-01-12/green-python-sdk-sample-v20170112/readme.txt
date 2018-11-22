1. 依赖的库在本工程 aliyuncs目录下, 包括两个库:
   aliyun-python-sdk-core
   aliyun-python-sdk-green(v20170112)
安装:
sudo pip install aliyun-python-sdk-core

green-python-sdk安装：
请将/sdk/aliyun-python-sdk-green.zip 解压进行安装
python setup.py install


2.需要替换自己的ak到配置文件里面，见配置文件aliyun.ak.conf, 你也可以直接在程序里面替换

3.本样例提供一下接口的调用示例:
    a. 异步图片检测接口: ImageAsyncScanRequestSample.py
    b. 获取异步图片检测结果接口:ImageAsyncScanResultsSample.py
    c. 同步图片检测接口:ImageSyncScanRequestSample.py


4. 用户请参照样例里面的代码注释描述

5. 几点解释说明
    a. 异步图片检测不会实时返回服务的处理结果，每张图片将以任务的形式在服务端处理，所以会分配调用后图片的taskid，在一定时间内通过taskid来获取处理结果
    b. 同步图片检测将会实时返回服务的处理结果
