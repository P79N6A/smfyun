<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                用户概况
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">预约宝</a></li>
                <li><a>模板消息群发管理</a></li>
                <li class="active"><a>用户概况</a></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>用户概况</span>
                            </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtyyba/qrcode?refresh=1" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-refresh"></span> 点击刷新公众号粉丝</a>
                        </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">
            <?php if (isset($config['qr_count'])&&$qrcron==1):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                <p>公众号粉丝拉取完毕</p>
                </div>
                </div>
            <?php endif?>
            <?php if (!$login->appid):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                <p>请先微信一键授权</p>
                </div>
                </div>
            <?php endif?>
            <?php if ($login->appid&&!isset($config['qr_count'])&&$qrcron==0):?>
            <div class="tpl-content-scope">
            <div class="note note-info">
            <p>队列中，请稍后</p>
            </div>
            </div>
            <?php endif?>
        <?php if ($config['qr_count']!=0&&$qrcron==0):?>
        <?php
          $number=$config['qr_total']-$number2;
          $time=ceil($number/10000);
          $text='预计还需要'.$time.'分钟公众号粉丝拉取完毕';
          ?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                <p><?=$text?></p>
                </div>
                </div>
              <?php endif?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                <p>特别说明：系统会首次自动拉取公众号的粉丝数据，拉取完成后需要点击刷新按钮才能刷新公众号的新增粉丝，取消关注的粉丝数据不会更新；</p>
                </div>
                </div>

          <div class="tab-pane active">
                    <div class="am-g">
                        <div class="am-u-sm-12">
            <form method="post" class="am-form">
                                <table class="inline-block am-scrollable-horizontal am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                            <th class="table-id">预约活动用户数</th>
                                            <th class="table-title">公众号粉丝数</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                <tr>
                  <td nowrap=""><?=$number1?></td>
                  <td nowrap=""><?=$number2?></td>
                </tr>
                                    </tbody>
                                </table>
                                <hr>

                            </form>
                        </div>

                    </div>
                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>



          </div><!-- tab-pane -->
          </div>
