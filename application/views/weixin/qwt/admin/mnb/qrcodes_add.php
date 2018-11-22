
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
                <li>代理列表</li>
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
                <div class="tpl-block" style="overflow:-webkit-paged-x">

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
                                    <label for="goal2" class="am-u-sm-12 am-form-label">手机号码</label>
                                    <div class="am-u-sm-12">
                    <input type="number" class="tpl-form-input titleinput" id="goal2" name="form[tel]" value="<?=$user->tel?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">姓名</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[name]" value="<?=$user->name?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">授权码</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[pcode]" value="<?=$user->pcode?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">密码</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[password]" value="<?=$user->password?$user->password:'123456'?>">
                                    </div>
                                </div>
                                <?php if ($user->wx_username):?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">微信号</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="form[wx_username]" value="<?=$user->wx_username?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">上级姓名</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="form[fname]" value="<?=$user->fname?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">上级授权码</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="form[fpcode]" value="<?=$user->fpcode?>">
                                    </div>
                                </div>
                                <?php endif?>
                                <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-3 am-form-label">设置代理等级</label>
                                    <div class="am-u-sm-9">
                                    <?php if ($lv[0]->id):?>
                                        <select data-am-selected="{searchBox: 1}" name="form[lv]" value="<?=$user->lid?>">
                                        <?php foreach ($lv as $k => $v):?>
                                            <option value="<?=$v->id?>" <?=$user->lid==$v->id?'selected':''?>><?=$v->lv?></option>
                                        <?php endforeach?>
</select>
<?php else:?>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p><span class="label label-danger">请先添加代理等级 </p>
                </div>
            </div>
        <?php endif?>
                                    </div>
                                </div>
                                <?php if($user->status==2):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否通过该用户的审核？</p>
                            </div>
                        </div>
                </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green green-on">通过</li>
                                    <li id="switch-off" class="red">不通过</li>
                        <input type="hidden" name="check[pass]" id="show0" value="0">
                                </ul>
                            </div>
                </div>
            <?php endif?>
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
    $('.titleinput').each(function(){
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
$('#switch-on').click(function(){
    $('#switch-off').removeClass('red-on');
    $('#switch-on').addClass('green-on');
    $('#show0').val(0);
})
$('#switch-off').click(function(){
    $('#switch-off').addClass('red-on');
    $('#switch-on').removeClass('green-on');
    $('#show0').val(1);
})
</script>
