
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    .datepicker{
        min-width: 200px;
    }
    label{
        text-align: left !important;
    }
    input[type=checkbox]{
        width: 0;
    }
    .hide{display: none}
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                账号管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">账号管理</a></li>
                <li class="am-active">修改用户</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>修改用户</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12">
                            <form method="post" class="am-form am-form-horizontal">
                    <?php if ($result['error']):?>
            <div class="tpl-content-scope">
                <div class="note note-info" style="color:red;">
                    <p> <?=$result['error']?></p>
                </div>
            </div>
                    <?php endif?>
                                <div class="am-form-group">
                                    <button data-bid="<?=$bid?>" type="button" class="am-btn am-btn-primary clearoauth">清除微信授权</button>
                                </div>

                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">用户名 / User Name</label>
                                    <div class="am-u-sm-12">
                                <input type="text" maxlength="20" class="form-control" id="exampleInputPassword1" name="data[user]" placeholder="用户名 / User Name" value="<?=htmlspecialchars($_POST['data']['user'])?>">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-12 am-form-label">密码 / Password</label>
                                    <div class="am-u-sm-12">
                                <input type="password" maxlength="20" name="pass" class="form-control" id="exampleInputPassword1" placeholder="为空就不做修改">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">专属邀请码 / Invite Code</label>
                                    <div class="am-u-sm-12">
                                <input type="text" maxlength="20" name="data[code]" placeholder="" class="form-control" id="exampleInputPassword1" placeholder="为空就不做修改" value="<?=$_POST['data']['code']?trim($_POST['data']['code']):$lastcode.rand(100,1000)?>">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="user-QQ" class="am-u-sm-12 am-form-label">备注 / Remarks</label>
                                    <div class="am-u-sm-12">
                                <input type="text" maxlength="20" class="form-control" id="exampleInputPassword1" placeholder="备注 / Remarks" name="data[memo]" value="<?=trim($_POST['data']['memo'])?>">
                                    </div>
                                </div>
                        <?php
                        function exist($id,$bid){
                            $end = ORM::factory('qwt_buy')->where('bid', '=', $bid)->where('status', '=', 1)->where('iid', '=', $id)->find();
                            return $end;
                        }
                        function pro($id,$bid){
                            $buyitem = ORM::factory('qwt_buy')->where('bid', '=', $bid)->where('iid', '=', $id)->find();
                            if($buyitem->iid==1||$buyitem->iid==14){
                                $pro = $buyitem->hbnum;
                            }else{
                                $pro = $buyitem->expiretime;
                            }
                            return $pro;
                        }
                        function zhibo($id,$bid){
                            $user = ORM::factory('qwt_login')->where('id', '=', $bid)->find();
                            return $user->stream_data;
                        }
                        function existvpn($id,$bid){
                            $res = ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1)->where('sid','=',$id)->find();
                            return $res;
                        }
                        function vpnpri($id,$bid){
                            $res = ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1)->where('sid','=',$id)->find()->price;
                            return $res;
                        }
                        ?>

                            <?php if ($result['action'] == 'edit'):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 修改产品购买权限</p>
                </div>
            </div>
                                <?php foreach ($items as $item):?>
            <div class="am-form-group">
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label"><?=$item->name?></label>
                                    <div class="am-u-sm-9">
                                        <div class="tpl-switch">
                                            <input name="item[<?=$item->id?>]" <?=exist($item->id,$bid)->id?"checked='checked'":"";?> type="checkbox" value="<?=$item->id?>" class="ios-switch bigswitch tpl-switch-btn"/>
                                            <div class="tpl-switch-btn-view">
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php if($item->name=='口令红包'||$item->name=='红包雨'):?>
                                <div class="am-form-group setbox">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">红包数量（单位：个）：</label>
                                    <div class="am-u-sm-3" style="float:left">
                                        <input style="width:100%" class="form-control" name="pro[<?=$item->id?>]" placeholder='单位：个' type="number" value="<?=pro($item->id,$bid)?pro($item->id,$bid):'';?>" />
                                    </div>
                                </div>
                                <?php else:?>
                                <?php if($item->name=='神码云直播'):?>
                                    <div class="am-form-group setbox">
                                        <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期至：</label>
                                        <div class="am-u-sm-3" style="float:left">
                                            <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=pro($item->id,$bid)?date("Y-m-d H:i:s",pro($item->id,$bid)):date('Y-m-d H:i:s',time());?>" readonly="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                        </div>
                                    </div>
                                    <div class="am-form-group setbox">
                                        <label for="user-weibo" class="am-u-sm-3 am-form-label">直播流量（单位：GB）：</label>
                                        <div class="am-u-sm-3" style="float:left">
                                            <input style="width:100%" class="form-control" name="zhibo[<?=$item->id?>]" placeholder='单位：GB' type="number" value="<?=zhibo($item->id,$bid)?zhibo($item->id,$bid):'500';?>" />
                                        </div>
                                    </div>
                                <?php else:?>
                                    <div class="am-form-group setbox">
                                        <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期至：</label>
                                        <div class="am-u-sm-3" style="float:left">
                                            <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=pro($item->id,$bid)?date("Y-m-d H:i:s",pro($item->id,$bid)):date('Y-m-d H:i:s',time());?>" readonly="" class="form_datetime form-control datepicker am-form-field" type="text" readonly>
                                        </div>
                                    </div>
                                <?php endif?>
                            <?php endif?>
                </div>
            <?php endforeach?>
        <?php else:?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 添加产品购买权限</p>
                </div>
            </div>
                                 <?php foreach ($items as $item):?>
            <div class="am-form-group">
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label"><?=$item->name?></label>
                                    <div class="am-u-sm-9">
                                        <div class="tpl-switch">
                                        <input name="item[<?=$item->id?>]" type="checkbox" value="<?=$item->id?>" class="ios-switch bigswitch tpl-switch-btn"/>
                                            <div class="tpl-switch-btn-view">
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php if($item->name=='口令红包'||$item->name=='红包雨'):?>
                                <div class="am-form-group setbox">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label">红包数量（单位：个）：</label>
                                    <div class="am-u-sm-3" style="float:left">
                                        <input style="width:100%" class="form-control" name="pro[<?=$item->id?>]" placeholder='单位：个' type="number" value="" />
                                    </div>
                                </div>
                            <?php else:?>
                                    <?php if($item->name=='神码云直播'):?>
                                        <div class="am-form-group setbox">
                                            <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期至：</label>
                                            <div class="am-u-sm-3" style="float:left">
                                                <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=date("Y-m-d H:i:s",time())?>" readonly="" class="datepicker am-form-field form_datetime form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="am-form-group setbox">
                                            <label for="user-weibo" class="am-u-sm-3 am-form-label">直播流量（单位：GB）：</label>
                                            <div class="am-u-sm-3" style="float:left">
                                                <input style="width:100%" class="form-control" name="zhibo[<?=$item->id?>]" placeholder='单位：GB' type="number" value="500" />
                                            </div>
                                        </div>
                                    <?php else:?>
                                        <div class="am-form-group setbox">
                                            <label for="user-weibo" class="am-u-sm-3 am-form-label">有效期至：</label>
                                            <div class="am-u-sm-3" style="float:left">
                                                <input style="width:100%" name="pro[<?=$item->id?>]" size="16" value="<?=date("Y-m-d H:i:s",time())?>" readonly="" class="datepicker am-form-field form_datetime form-control" type="text">
                                            </div>
                                        </div>
                                    <?php endif?>
                            <?php endif?>
                </div>
            <?php endforeach?>
        <?php endif?>
                <hr>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 产品代理权限</p>
                </div>
            </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$flag== 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$flag === "0" || !$flag ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="flag" id="show0" value="<?=$flag==1?1:0?>">
                                </ul>
                            </div>
                </div>
                <div class="am-form-group switch-content <?=$flag == 1 ? '':'hide'?>">

                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-12 am-form-label">代理商名称</label>
                                    <div class="am-u-sm-12">
                                <input type="text" maxlength="20" name="vpn1[name]" value="<?=$_POST['vpn1']['name']?>" class="form-control">
                                    </div>
                                </div>
                                <?php if ($result['action']=='add'):?>
                                <?php foreach ($guide as $k => $v):?>
                                <div class="am-u-sm-12">
                                    <div class="am-u-sm-3">
                                    <label for="user-weibo" class="am-form-label"><?=$v[0]['title']?></label>
                                    </div>
                                    <div class="am-u-sm-9">
                                <?php foreach ($v as $m => $n):?>
                                <div class="am-form-group">
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label"><?=$n['name']?></label>
                                    <div class="am-u-sm-9">
                                        <div class="tpl-switch">
                                            <input name="vpn[<?=$n['sid']?>]" type="checkbox" value="<?=$n['sid']?>" class="ios-switch bigswitch tpl-switch-btn"/>
                                            <div class="tpl-switch-btn-view">
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="am-form-group setbox">
                                    <label for="user-weibo" class="am-u-sm-4 am-form-label">代理价（原价：￥<?=$n['old_price']?>）</label>
                                    <div class="am-u-sm-8" style="float:left">
                                        <input style="width:100%" class="form-control" name="pri[<?=$n['sid']?>]" type="number" value="" />
                                    </div>
                                </div>
                            </div>
                            <?php endforeach?>
                            </div>
                            </div>
                            <?php endforeach?>
                        <?php else:?>

                                <?php foreach ($guide as $k => $v):?>
                                <div class="am-u-sm-12">
                                    <div class="am-u-sm-3">
                                    <label for="user-weibo" class="am-form-label"><?=$v[0]['title']?></label>
                                    </div>
                                    <div class="am-u-sm-9">
                                <?php foreach ($v as $m => $n):?>
                                <div class="am-form-group">
                                <div class="am-form-group">
                                    <label for="user-weibo" class="am-u-sm-3 am-form-label"><?=$n['name']?></label>
                                    <div class="am-u-sm-9">
                                        <div class="tpl-switch">
                                            <input name="vpn[<?=$n['sid']?>]" type="checkbox" <?=existvpn($n['sid'],$bid)->id?"checked='checked'":"";?> value="<?=$n['sid']?>" class="ios-switch bigswitch tpl-switch-btn"/>
                                            <div class="tpl-switch-btn-view">
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="am-form-group setbox">
                                    <label for="user-weibo" class="am-u-sm-4 am-form-label">代理价（原价：￥<?=$n['old_price']?>）</label>
                                    <div class="am-u-sm-8" style="float:left">
                                        <input style="width:100%" class="form-control" name="pri[<?=$n['sid']?>]" type="number" value="<?=vpnpri($n['sid'],$bid)?>" />
                                    </div>
                                </div>
                            </div>
                            <?php endforeach?>
                            </div>
                            </div>
                            <?php endforeach?>
                        <?php endif?>

                                <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 邀请折扣</p>
                </div>
            </div>
            </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on-2" class="green <?=$config['ifdiscount']== 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off-2" class="red <?=$config['ifdiscount'] === "0" || !$config['ifdiscount'] ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="ifdiscount" id="show2" value="<?=$config['ifdiscount']==1?1:0?>">
                                </ul>
                            </div>
                </div>
                <div class="am-form-group switch-content-2 <?=$config['ifdiscount'] == 1 ? '':'hide'?>">
                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-12 am-form-label">代理商折扣率(单位：1%，最大为100)</label>
                                    <div class="am-u-sm-12">
                                <input type="number" Min="1" max="100" name="discount" value="<?=$config['discount']?>" class="form-control">
                                    </div>
                                </div>
                                </div>
                </div>
                            <?php if ($result['action'] == 'edit'):?>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">修改用户</button>
                                    </div>
                                </div>
                            <?php else:?>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">添加用户</button>
                                    </div>
                                </div>
                            <?php endif?>
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

<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
<script src="/qwt/assets/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show0').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show0').val(0);
})
$('#switch-on-2').on('click', function() {
    $('#switch-on-2').addClass('green-on');
    $('#switch-off-2').removeClass('red-on');
    $('.switch-content-2').removeClass('hide');
    $('#show2').val(1);
})
$('#switch-off-2').on('click', function() {
    $('#switch-on-2').removeClass('green-on');
    $('#switch-off-2').addClass('red-on');
    $('.switch-content-2').addClass('hide');
    $('#show2').val(0);
})

    function check(){
        $('input[type=checkbox]').each(function(){
            var i = $(this).prop('checked');
            console.log(i);
            if (i==true) {
                $(this).parent().parent().parent().parent().children('.setbox').show();
            }else{
                $(this).parent().parent().parent().parent().children('.setbox').hide();
            };
        })
    };
    $('.tpl-switch-btn-view').click(function(){
        $(this).parent().parent().parent().parent().children('.setbox').fadeToggle();
    });
    $(document).ready(function(){
        check();
    });


    $('.datepicker').datetimepicker({
  format: 'yyyy-mm-dd hh:ii',
  language: 'zh_CN'
});
    $('#datetimepicker1').datetimepicker({
  format: 'yyyy-mm-dd hh:ii',
  locale: 'zh_CN'
});
    $('#datetimepicker2').datetimepicker({
  format: 'yyyy-mm-dd hh:ii',
  locale: 'zh_CN'
});
    $('#datetimepicker3').datetimepicker({
  format: 'yyyy-mm-dd hh:ii',
  locale: 'zh_CN'
});
    $('.clearoauth').click(function(e) {
        console.log($(this).data('bid'));
        $.ajax({
            url: '/qwtwdla/clearoauth',
            type: 'post',
            dataType: 'json',
            data: {'action': 'clearoauth','bid':$(this).data('bid')},
        })
        .done(function(res) {
            alert(res.content);
            console.log("success");
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

    });
    </script>

