

    <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                应用开关
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">应用中心</a></li>
                <li class="am-active">应用开关</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>应用开关</span>
                            </div>

                        </div>
                        <?php if ($result['ok'] > 0):?>
                        <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 保存成功!</p>
                            </div>
                        </div>
                      <?php endif?>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12">
                            <?php if($buys):?>
                            <form method="post" class="am-form am-form-horizontal">
                <?php foreach ($buys as $k=>$v):?>
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label"><?=$v->item->name?></label>
                                    <div class="am-u-sm-1">
                                        <div class="tpl-switch">
                                            <input name="item[<?=$v->iid?>]" data-name="<?=$v->iid?>"<?=($v->switch==1)?"checked='checked'":"";?> type="checkbox" value="<?=$v->switch?>" class="ios-switch bigswitch tpl-switch-btn"/>
                                            <div class="tpl-switch-btn-view">
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="am-u-sm-8">
                                        <?=date('Y-m-d',$v->expiretime)?>到期
                                    </div>
                                </div>
                        <?php endforeach ?>
                                <input type="hidden" name='switch' value="1">
                                <!-- <div class="am-form-group">
                                    <div class="am-u-sm-3 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">修改用户</button>
                                    </div>
                                </div> -->
                            </form>
                            <?php else:?>
                            <div class="am-form am-form-horizontal">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 您还没有购买任何应用，或应用均以到期，可以前往【产品中心】购买应用。 </p>
                </div>
            </div>
                            </div>
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
            </div>



        </div>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $('.tpl-switch-btn-view,.ios-switch').click(function(){
        var iid = $(this).parent().children('input').data('name');
        var value = $(this).parent().children('input').val();

        $.ajax({
            url: '/qwta/switch',
            type: 'post',
            async: false,
            dataType: 'text',
            data: {iid:iid,value:value},
            beforsend:function(){
                swal({
                  title: "请稍等",
                  text: "应用开关切换中",
                  showConfirmButton: false
                });
            },
            success:function(){
                swal({
                  type: "success",
                  title: "完成",
                  text: "应用状态已更新",
                  timer: 1000,
                  showConfirmButton: true
                });
            }
        })
    });
</script>

