
    <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                账号管理
            </div>
            <ol class="am-breadcrumb">
                <li class="am-active"><a href="#" class="am-icon-home">账号管理</a></li>
            </ol>
            <div class="tpl-portlet-components">
            <form method="get" name="loginsform">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        账号管理
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                                    <a href="/qwta/logins/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新账号</a>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                                <input type="text"  name="s" class="am-form-field" placeholder="搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                </form>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
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
                <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwta/logins/edit/<?=$login->id?>"><i class="fa fa-asterisk">修改</i></a></td>
            </tr>
        <?php endforeach?>
                                    </tbody>
                                </table>
                            </form>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr"><?=$pages?>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>










        </div>

    </div>

    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
