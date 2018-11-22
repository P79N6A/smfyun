
<style type="text/css">
    label{
        text-align: left !important;
    }
    .content{
    padding: 10px;
    border-radius: 10px;
    border: 2px solid rgba(129,180,0,.5);
}
    .am-badge{
        background-color: green;
    }
.title{
    font-size: 16px;
    line-height: 16px;
    color: orange;
    margin-top: -18px;
    background: #fff;
    padding: 0 5px;
    width: 60px;
    text-align: center;
}
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['action']?>
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">蒙牛数据开发</a></li>
                <li>代理等级</li>
                <li class="am-active"><?=$result['action']?></li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><?=$result['action']?></span>
                            </div>
                        </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit="return toValid()">
                                    <?php if ($result['err3']):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p><span class="label label-danger">注意:</span> <?=$result['err3']?> </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">代理等级名称</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[name]" value="<?=$group->name?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success "><?=$result['action']?></button>
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

<script type="text/javascript">
function toValid () {
    var pass = 1;
    $('input').each(function(){
        if ($(this).val()=='') {
            pass = 0;
        }
    });
    if (pass==0) {
        alert('请填写完整');
        return false;
    }else{
        return true;
    }
}
</script>
