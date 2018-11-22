<style type="text/css">
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$title?>营销规则
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>营销规则</li>
                <li class="am-active"><?=$title?>营销规则</li>
            </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <?=$title?>营销规则
                    </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                <form method="post" class="am-form" onsubmit="return toValid()">
                <div class="am-form-group">
                    <label for="name" class="am-u-sm-12 am-form-label">名称</label>
                        <div class="am-u-sm-12">
                        <input id="name" name="name" type="text" class="form-control" value="<?=$account->name?>">
                        </div>
                        </div>
                <div class="am-form-group">
                <label for="menu" class="am-u-sm-12 am-form-label">选择营销类型</label>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$account->type==1||$account->type==0?'green-on':''?>">分享到朋友圈后发送微信红包</li>
                                    <li id="switch-off" class="red <?=$account->type==2?'red-on':''?>">分享到朋友圈后领取微信卡券</li>
                        <input type="hidden" name="type" id="show0" value="<?=$account->type==2?2:1?>">
                                </ul>
                            </div>
                </div>
                </div>

            <div class='hb' <?=$account->type==2?'style="display:none"':''?>>
                <div class="am-form-group">
                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包最小金额（单位：分，最少100分）</label>
                    <div class="am-u-sm-12">
                        <input type="number" class="form-control" id="moneyMin" name="cus[moneyMin]" placeholder="moneyMin" value="<?=$account->moneyMin?>">
                        </div>
                    </div>
                <div class="am-form-group">
                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包最大金额（单位：分，最大20000分）</label>
                    <div class="am-u-sm-12">
                        <input type="number" class="form-control" id="money" name="cus[money]" placeholder="money" value="<?=$account->money?>">
                    </div>
                </div>
            </div>
            <div class='coupon' <?=$account->type==2?'':'style="display:none"'?>>
                <div class="am-form-group">
                  <label for="doc-select-1" class="am-u-sm-12 am-form-label">选择用户领取的微信卡券</label>                    <div class="am-u-sm-12">
                  <select id="doc-select-1" name='coupon'>
                    <?php foreach($result['wxcards'] as $k=>$v):?>
                        <option value="<?=$v['id']?>" <?=$account->couponid==$v['id']?"selected":''?>><?=$v['title']?></option>
                    <?php endforeach?>
                  </select>
                  </div>
                </div>
            </div>
            <div class="am-form-group">
            <label for="menu" class="am-u-sm-12 am-form-label">分享朋友圈后是否需要关注公众号才能领取奖励。</label>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on-1" class="green <?=$account->issub==1||$account->type==0?'green-on':''?>">是</li>
                                    <li id="switch-off-1" class="red <?=$account->issub==2?'red-on':''?>">否</li>
                        <input type="hidden" name="issub" id="show1" value="<?=$account->issub==2?2:1?>">
                                </ul>
                            </div>
                </div>
            </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9  am-u-sm-push-3" style="margin-top:20px;">
                  <button class="am-btn am-btn-secondary" type="submit"><?=$title?></button>
                        <?php if($title=='修改'):?>
                  <a class="am-btn am-btn-danger" id="delete">取消此营销规则（不可恢复）</a>
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

    </div>
    </div>
    </div>
    </div></div>
    <script type="text/javascript">
    $('#switch-on').click(function(){
        $('#switch-on').addClass('green-on');
        $('#switch-off').removeClass('red-on');
        $('#show0').val(1);
        $('.hb').show();
        $('.coupon').hide();
    })
    $('#switch-off').click(function(){
        $('#switch-on').removeClass('green-on');
        $('#switch-off').addClass('red-on');
        $('#show0').val(2);
        $('.hb').hide();
        $('.coupon').show();
    })
    $('#switch-on-1').click(function(){
        $('#switch-on-1').addClass('green-on');
        $('#switch-off-1').removeClass('red-on');
        $('#show1').val(1);
        $('.hb').show();
        $('.coupon').hide();
    })
    $('#switch-off-1').click(function(){
        $('#switch-on-1').removeClass('green-on');
        $('#switch-off-1').addClass('red-on');
        $('#show1').val(2);
    })
    <?php if($result['error']):?>
    $(document).ready(function(){
        alert("<?=$result['error']?>");
    })
    <?php endif?>
    function toValid(){
        var flag = 0;
        $(":text").each(function(){
        　　if($(this).val() == "") {
            flag = 1;
            };
        });
        if(flag==1){
            alert('请填写完整');
            return false;
        }else{
            return true;
        }
    }
<?php if ($title=='修改'): ?>
    $('#delete').click(function(){
        var id = <?=$account->id?>;
        swal({
            title: "你确定吗？",
            text: "确认要删除此门店账号吗？删除后不可撤销",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: '确认',
            cancelButtonText: '取消',
            closeOnConfirm: false
        },
        function(){
            window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/ruledelete/"+id;
        });
    })
<?php endif?>

    </script>
