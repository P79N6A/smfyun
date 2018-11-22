<div class="page-heading">
            <h3>
                账号管理
            </h3>
</div>
        <!-- page heading end-->

        <!--body wrapper start-->
<div class="wrapper">
        <div class="row">
        <div class="col-sm-12">
        <section class="panel">
        <header class="panel-heading">
            <form method="get" name="loginsform">
            <div class="input-group" style="width: 200px;">
              <input type="text" name="s" class="form-control input-sm pull-right" placeholder="搜索" value="<?=htmlspecialchars($result['s'])?>">
              <div class="input-group-btn">
                <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
              </div>
            </div>
            <div  style="float:right;margin-top:-32px">
            <a href="/qwta/logins/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新用户</span></a>
            </div>
            </form>
        </header>
        <div class="panel-body">
        <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="dynamic-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>商户名</th>
            <th>登录名</th>
            <th>密码</th>
            <th>邀请码</th>
            <th>商户备注</th>
            <!-- <th>状态</th> -->
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result['logins'] as $login):?>
            <tr class="gradeX">
                <td><?=$login->id?></td>
                <td><?=$login->name?></td>
                <td><?=$login->user?></td>
                <td><?=$login->pass?></td>
                <td><?=$login->code?></td>
                <td><?=$login->memo?></td>
                <td><a href="/qwta/logins/edit/<?=$login->id?>"><i class="fa fa-asterisk">修改</i></a></td>
            </tr>
        <?php endforeach?>
        </tbody>
        </table>
        </div>
        <div class="box-footer clearfix">
            <?=$pages?>
        </div>
        </div>
        </section>
        </div>
        </div>

</div>
        <!--body wrapper end-->

