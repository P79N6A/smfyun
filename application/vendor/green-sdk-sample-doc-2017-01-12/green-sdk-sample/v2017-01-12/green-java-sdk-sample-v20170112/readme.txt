新增特性:
一. 文本检测支持关键词自定义: 关键词自定义请前往绿网控制台进行添加(https://yundun.console.aliyun.com/?p=cts#/greenWeb/config2), 添加的关键词默认自动生效
二. 新增图片广告识别


1.需要替换自己的ak到配置文件里面，见配置文件config.properties, 你也可以直接在程序里面替换, 支持主账号AK以及Ram子账号, Ram子账号请在ram控制台授权给系统策略：AliyunYundunGreenWebFullAccess

2.本样例提供一下接口的调用示例:
  a. 图片异步检测接口: ImageAsyncScanRequestSample
  b. 获取图片异步检测结果接口:ImageAsyncScanResultsSample
  c. 图片同步检测接口:ImageSyncScanRequestSample
  d. 文本检测接口:TextScanSample
  e. 视频异步检测接口:VideoAsyncScanRequestSample
  f. 获取视频异步检测结果接口:VideoAsyncScanResultsSample

3. 用户请参照test目录下个样例里面的代码注释描述


4. 几点解释说明
  a. 异步图片检测不会实时返回服务的处理结果，每张图片将以任务的形式在服务端处理，所以会分配调用后图片的taskid，在一定时间内通过taskid来获取处理结果
  b. 同步图片检测将会实时返回服务的处理结果
