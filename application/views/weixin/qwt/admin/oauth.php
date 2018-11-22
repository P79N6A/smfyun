<section>
<?php
$service_type_info[0] = '订阅号';
$service_type_info[1]='由历史老帐号升级后的订阅号';
$service_type_info[2]='服务号';

function verify($id){
  switch ($id) {
    case -1:
      return '未认证';
      break;
    case 0:
      return '微信认证';
      break;
    case 1:
      return '新浪微博认证';
      break;
    case 2:
      return '腾讯微博认证';
      break;
    case 3:
      return '已资质认证通过但还未通过名称认证';
      break;
    case 4:
      return '已资质认证通过、还未通过名称认证，但通过了新浪微博认证';
      break;
    case 5:
      return '已资质认证通过、还未通过名称认证，但通过了腾讯微博认证';
      break;
    default:
      return '未知';
      break;
  }
}
// 1、消息管理权限
// 2、用户管理权限
// 3、帐号服务权限
// 4、网页服务权限
// 5、微信小店权限
// 6、微信多客服权限
// 7、群发与通知权限
// 8、微信卡券权限
// 9、微信扫一扫权限
// 10、微信连WIFI权限
// 11、素材管理权限
// 12、微信摇周边权限
// 13、微信门店权限
// 14、微信支付权限
// 15、自定义菜单权限
// 16、获取认证状态及信息
// 17、帐号管理权限（小程序）
// 18、开发管理与数据分析权限（小程序）
// 19、客服消息管理权限（小程序）
// 20、微信登录权限（小程序）
// 21、数据分析权限（小程序）
// 22、城市服务接口权限
// 23、广告管理权限
// 24、开放平台帐号管理权限
// 25、开放平台帐号管理权限（小程序）
// 26、微信电子发票权限
$auth[1] = '消息管理权限';
$auth[2] = '用户管理权限';
$auth[3] = '帐号服务权限';
$auth[4] = '网页服务权限';
$auth[5] = '微信小店权限';
$auth[6] = '微信多客服权限';
$auth[7] = '群发与通知权限';
$auth[8] = '微信卡券权限';
$auth[9] = '微信扫一扫权限';
$auth[10] = '微信连WIFI权限';
$auth[11] = '素材管理权限';
$auth[12] = '微信摇周边权限';
$auth[13] = '微信门店权限';
$auth[14] = '微信支付权限';
$auth[15] = '自定义菜单权限';
$auth[16] = '获取认证状态及信息';
$auth[17] = '帐号管理权限（小程序）';
$auth[18] = '开发管理与数据分析权限（小程序）';
$auth[19] = '客服消息管理权限（小程序）';
$auth[20] = '微信登录权限（小程序）';
$auth[21] = '数据分析权限（小程序）';
$auth[22] = '城市服务接口权限';
$auth[23] = '广告管理权限';
$auth[24] = '开放平台帐号管理权限';
$auth[25] = '开放平台帐号管理权限（小程序）';
$auth[26] = '微信电子发票权限';

$has[0] = '<label class="unconfirmed">未授权</label>';
$has[1] = '<label class="confirmed">已授权</label>';
?>
<style type="text/css">
  .quanxian{
    font-size: 14px;
    color: #666;
    display: none;
  }
  .unconfirmed{
    font-weight: normal;
    color: #999;
    margin-left: 5px;
    padding: 2px;
    border-radius: 2px;
    border: 1px solid #efefef;
  }
  .confirmed{
    font-weight: normal;
    color: #29c10d;
    margin-left: 5px;
    padding: 2px;
    border-radius: 2px;
    border: 1px solid #29c10d;
  }
  .subscr{
    font-size: 16px;
    color: #444;
    line-height: 32px;
  }
</style>
  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
        微信一键授权
      </div>
      <ol class="am-breadcrumb">
        <li><a href="#" class="am-icon-home">绑定我们</a></li>
        <li class="am-active">微信一键授权</li>
      </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
          <div class="tpl-portlet-title">
            <div class="tpl-caption font-green ">
              <span>微信一键授权</span>
            </div>
          </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 subscr">
                            <?php if($arr):?>
                              微信昵称：<?=$arr['authorizer_info']['nick_name']?><br>
                              <!-- 头像：<img src="<?=$arr['authorizer_info']['head_img']?>"><br> -->
                              公众号类型：<?=$service_type_info[$arr['authorizer_info']['service_type_info']['id']]?><br>
                              认证类型：<?=verify($arr['authorizer_info']['verify_type_info']['id'])?><br>
                              微信号：<?=$arr['authorizer_info']['alias']?><br>
                              <!-- 二维码：<img src="<?=$arr['authorizer_info']['qrcode_url']?>"><br> -->
                              是否开通微信支付功能：<?=$arr['authorizer_info']['business_info']['open_pay']==1?'是':'否'?><br>
                              是否开通微信卡券功能：<?=$arr['authorizer_info']['business_info']['open_card']==1?'是':'否'?><br>
                              <!-- 看权限改这里 -->
                              <div class="quanxian">
                              <?php foreach ($auth as $key => $value){
                                $flag = 0;
                                for($i=0;$arr['authorization_info']['func_info'][$i];$i++){
                                    if($arr['authorization_info']['func_info'][$i]['funcscope_category']['id']==$key){
                                      $flag = 1;
                                    }
                                }
                                echo $value.$has[$flag].'<br>';
                              }?>
                              </div>
                            <?php endif?>
                            </div>
                            <?php if($arr):?>
                            <div class="am-u-sm-3"><img src="http://<?=$_SERVER['HTTP_HOST']?>/qwta/images/<?=$bid?>/wx_qr_img" style="max-width:100%;"></div>
                          <?php endif?>
                          </div>
                      </div>
                      <div class="am-u-sm-6 am-u-sm-push-3" style="float:left;">
                <?php if($user->refresh_token):?>
                  <!-- <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx4d981fffa8e917e7&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/qwta/oauth'> -->
                              <button type="button" class="am-btn am-btn-warning">您已经授权成功，如果遇到接口异常问题，请联系我们</button>
                              <!-- </a> -->
                <?php else:?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx4d981fffa8e917e7&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/qwta/oauth'>
                              <button type="button" class="am-btn am-btn-primary">点击一键授权</button></a>
                <?php endif?>
                </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
