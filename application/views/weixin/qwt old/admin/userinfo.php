<section class="wrapper" >

    <div class="row">
        <div class="page-heading">
    <h3>
        账户信息
    <h3>
</div>
<div class="col-lg-12">

<section class="panel">
    <header class="panel-heading">
        账户信息
    </header>
    <div class="panel-body">
        <form class="form-horizontal adminex-form" method="post">
            <?php if ($success=='ok'):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>账户信息保存成功!</div>
            <?php endif?>

             <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">用户名</label>
                <div class="col-sm-10">
                    <input type="text" name="userinfo[user]" class="form-control " value="<?=$userinfo->user?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">邀请码</label>
                <div class="col-sm-10">
                    <input type="text"  disabled="disabled" class="form-control" value="<?=$userinfo->code?>">
                </div>
            </div>
           <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" name ="userinfo[name]" class="form-control" value="<?=$userinfo->name?>">
                </div>
            </div>
            <!--<div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">邮箱</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control tooltips" data-trigger="hover" data-toggle="tooltip" title="" placeholder="Hover me" data-original-title="Tooltip goes here">
                </div>
            </div>-->
            <div class="form-group">
                <label class="col-sm-2 col-sm-2 control-label">公众号名称</label>
                <div class="col-sm-10">
                    <input type="text" name="userinfo[weixin_name]" class="form-control " value="<?=$userinfo->weixin_name?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-primary" type="submit">提交</button>
                </div>
            </div>
        </form>
    </div>
</section>
</div>
</div>
</section>
