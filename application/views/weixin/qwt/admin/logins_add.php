
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    .datepicker{
        min-width: 200px;
    }
    label{
        text-align: left !important;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
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
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">邀请码 / Invite Code</label>
                                    <div class="am-u-sm-12">
                                <input type="text" maxlength="20" name="data[code]" placeholder="" class="form-control" id="exampleInputPassword1" placeholder="为空就不做修改" value="<?=trim($_POST['data']['code'])?>">
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
                            if($buyitem->iid==1){
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
                        ?>

                            <?php if ($result['action'] == 'edit'):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 修改产品购买权限</p>
                </div>
            </div>
                                <?php foreach ($items as $item):?>
            <div class="am-u-sm-12">
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
                                    <?php if($item->name=='口令红包'):?>
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
            <div class="am-u-sm-12">
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
                                    <?php if($item->name=='口令红包'):?>
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
    </script>

    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
