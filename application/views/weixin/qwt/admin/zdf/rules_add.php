
<style type="text/css">
    label{
        text-align: left !important;
    }
    .loc .am-form-group select{
        width: 30% !important;
        display: inline-block !important;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['action']?>
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">公众号自动下发小程序卡片工具</a></li>
                <li>发送规则</li>
                <li>输入关键词后下发</li>
                <li class="am-active"><?=$result['action']?></li>
            </ol>
            <div class="tpl-portlet-components" style="overflow: -webkit-paged-x;">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            <?=$result['action']?>
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-3 am-form-label">关键词</label>
                                    <div class="am-u-sm-9">
                    <input type="text" class="tpl-form-input" id="goal2" name="rule[keyword]" value="<?=$rule->keyword?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-3 am-form-label">发送的小程序卡片</label>
                                    <div class="am-u-sm-9">
                                        <select data-am-selected="{searchBox: 1}" name="rule[content]" value="<?=$rule->mid?>">
                                        <?php foreach ($msg as $k => $v):?>
  <option <?=$v->id==$rule->mid?'selected':''?> value="<?=$v->id?>"><?=$v->name?></option>
<?php endforeach?>
</select>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-3 am-form-label">发送文案（在小程序卡片后发送，不填即不发送）</label>
                                    <div class="am-u-sm-9">
                    <input type="text" class="tpl-form-input" id="goal2" name="rule[text]" value="<?=$rule->text?>">
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>
    <script type="text/javascript">
<?php if($result['err3']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
</script>
