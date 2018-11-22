<style type="text/css">
    th{
        white-space: nowrap;
    }
    td{
        white-space: nowrap;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                账号管理
            </div>
            <ol class="am-breadcrumb">
                <li class="am-active"><a href="#" class="am-icon-home">账号管理</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-2">
                    <div class="caption font-green bold">
                        账号管理
                    </div>
                    </div>
                    <?php if($_SESSION['qwta']['admin'] !=0):?>
              <form id="searchtype" method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-3">
                                <div class="am-form-group">
                                    <div class="am-u-sm-9">
                                        <select id="type" name="type" data-am-selected="{searchBox: 1}" value="<?=$type?>">
  <option value="0" <?=$type==0?'selected':''?>>全部</option>
  <option value="1" <?=$type==1?'selected':''?>>代理商</option>
  <option value="2" <?=$type==2?'selected':''?>>普通用户</option>
</select>
                                    </div>
                                </div>
                                </div>
                                </form>
                              <?php endif;?>
                        <div class="am-u-sm-12 am-u-md-3">
                                    <a href="/qwtwdla/logins/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新账号</a>
                        </div>

            <form method="get" name="loginsform">
                        <div class="am-u-sm-12 am-u-md-4">
                            <div class="am-input-group am-input-group-sm">
                                <input type="text"  name="s" class="am-form-field" placeholder="搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
            <th>ID</th>
            <th>公众号名称</th>
            <th>登录名</th>
            <th>密码</th>
            <th>专属邀请码</th>
            <th>用户类型</th>
            <th>代理商名称</th>
            <th>备注</th>
            <th>来源</th>
            <th>一键登录</th>
            <th>购买记录</th>
            <!-- <th>状态</th> -->
            <th>操作</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($result['logins'] as $login):?>
            <tr class="gradeX">
                <td><?=$login->id?></td>
                <td><?=$login->weixin_name?></td>
                <td><?=$login->user?></td>
                <td><?=$login->pass?></td>
                <td><?=$login->code?></td>
                 <td id="lock<?=$login->id?>">
                  <?php
                  if ($login->flag==1)
                    echo '<span class="label label-success">代理商</span>';
                  else
                    echo '<span class="label label-warning">普通用户</span>';
                  ?>
                </td>
                <td><?=$login->dlname?></td>
                <td><?=$login->memo?></td>
                <td>
              <?php
              $fulogin=ORM::factory('qwt_login')->where('id','=',$login->fubid)->find();
              if($login->fubid&&$fulogin->flag==1){
                echo '<span class="label label-success">经销商：'.$fulogin->dlname.'/'.$fulogin->user.'</span>';
              }else{
                echo '<span class="label label-warning">营销应用平台</span>';
              }
               ?>
                  </td>
               <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtwdla/speedy?bid=<?=$login->id?>"><i class="fa fa-asterisk">一键登录</i></a></td>
                <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtwdla/ddorder?bid=<?=$login->id?>"><i class="fa fa-asterisk">查看</i></a></td>
                <td><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtwdla/logins/edit/<?=$login->id?>"><i class="fa fa-asterisk">修改</i></a></td>
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

        <script type="text/javascript">
        $('#type').change(function(){
            var a = $(this).val();
            console.log(a);
            $('#searchtype').submit();
        })
        </script>
