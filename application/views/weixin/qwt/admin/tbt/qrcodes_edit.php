
<style type="text/css">
    label{
        text-align: left !important;
    }
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                会员详情
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">特别特</a></li>
                <li class="am-active">会员详情</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>基本信息</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">门头</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($qrcode->shop_pic):
                  ?>
                  <a href="/qwttbta/image1s/qrcode/<?=$qrcode->id?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img" style="width:100px;padding:2px;border-radius:2px;border:1px solid #dedede;">
                                                <img src="/qwttbta/image1s/qrcode/<?=$qrcode->id?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                    </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <label for="pic2" class="am-u-sm-12 am-form-label">身份证</label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($qrcode->ic_pic):
                ?><a href="/qwttbta/image2s/qrcode/<?=$qrcode->id?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img" style="width:100px;padding:2px;border-radius:2px;border:1px solid #dedede;">
                                                <img class="avator" src="/qwttbta/image2s/qrcode/<?=$qrcode->id?>.v<?=time()?>.jpg" alt="">
                                            </div>
                                            </a>
                                          <?php endif?>
                                    </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <label for="name" class="am-u-sm-12 am-form-label">真实姓名</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="name" name="text[name]" value="<?=$qrcode->name?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="tel" class="am-u-sm-12 am-form-label">手机</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" class="tpl-form-input" id="tel" name="text[tel]" value="<?=$qrcode->telphone?>" disabled>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="address" class="am-u-sm-12 am-form-label">地址</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="address" name="text[address]" value="<?=$qrcode->address?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">行业类型</label>
                                    <div class="am-u-sm-12">
                                        <select name="text[type]" data-am-selected="{searchBox: 1}">
                                        <option value="销售商" <?=$qrcode->type=='销售商'?"selected":""?>>销售商</option>
                                        <option value="车队" <?=$qrcode->type=='车队'?"selected":""?>>车队</option>
                                        <option value="修理厂" <?=$qrcode->type=='修理厂'?"selected":""?>>修理厂</option>
                                        <option value="服务站" <?=$qrcode->type=='服务站'?"selected":""?>>服务站</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">密码</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="password" name="text[password]" value="<?=$qrcode->password?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">状态</label>
                        <div class="am-u-sm-12 am-u-md-3" style="float:left">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$qrcode->flag == 1 ? 'green-on' : ''?>">已审核</li>
                                    <li id="switch-off" class="red <?=$qrcode->flag == 0 ? 'red-on' : ''?>">未审核</li>
                                    <input type="hidden" value="<?=$qrcode->flag?>" name="text[flag]" id="flock">
                                </ul>
                            </div>
                </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-danger tpl-btn-bg-color-danger ">提交</button>
                                        <a href="/qwttbta/qrcodes" class="am-btn am-btn-primary tpl-btn-bg-color-success ">返回列表</a>
                                    </div>
                                </div>
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
            </div>



        </div>
        <script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
        <script>

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('#flock').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('#flock').val(0);
})
        </script>
