

  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                充值明细
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>红包码下载及红包充值</li>
                <li class="am-active"><a href="/qwthbya/payment">红包码下载</a></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                        <div class="caption font-green bold">
                        红包码下载
                        </div>
                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                                    <?php if ($success['ok']!=null):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 配置保存成功! </p>
                </div>
            </div>
            </div>
                        <?php elseif($success['ok'] =='file'):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 文件更新成功! </p>
                </div>
            </div>
            </div>
                        <?php endif?>
                        <?php if($config==null):?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="download/<?=$bid?>" class="am-btn am-btn-primary">下载红包素材</a>
                </div>
                </div>
                </div>
                        <?php else:?>
                            <?php if($left==1):?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="pre_generate/<?=$bid?>" class="am-btn am-btn-primary">生成红包码</a>
                </div>
                </div>
                </div>
                            <?php else:?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <button class="am-btn am-btn-danger" disabled="">红包码已经获取</button><small>(7天后才能再次获取)</small>
                </div>
                </div>
                </div>

                            <?php endif;?>
                <?php if($result['cron']->id):?>
                <?php if($result['cron']->has_qr==0):?>
                        <div class="am-u-sm-12" style="margin-top:1.5rem;">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 红包码正在生成中，大概还需要<?=$result['time']?>分钟，请稍后下载！ </p>
                </div>
            </div>
            </div>
                <?php else:?>
                    <?php if($result['cron']->has_down==1):?>
                        <div class="am-u-sm-12" style="margin-top:1.5rem;">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <button class="am-btn am-btn-danger" disabled="">最新红包码已经下载了</button>
                        </div>
                        </div>
                        </div>
                    <?php else:?>
                        <div class="am-u-sm-12" style="margin-top:1.5rem;">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <a href="downzip" class="am-btn am-btn-primary">下载最新红包码</a>
                        </div>
                        </div>
                        </div>
                    <?php endif?>

                <?php endif?>
                <?php endif?>
                        <!-- <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="<?php echo URL::site('qwthbya/download/'.$buy_id)?>" class="am-btn am-btn-danger">下载红包素材</a>
                  </div>
                  </div>
                  </div> -->
              <?php endif?>

                    </div>
                </div>
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
        </div>
