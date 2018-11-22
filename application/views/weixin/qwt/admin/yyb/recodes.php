
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送失败记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">预约宝</a></li>
    <li>模板消息群发管理</li>
    <li class="am-active">发送失败记录</li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=$countall?> 条消息发送记录
                    </div>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>预约项目标题</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>性别</th> -->
                  <th>发送状态</th>
                  <th>原因</th>
                  <th>发送时间</th>
                </tr>
                </thead>
                                    <tbody>
                <?php
                foreach ($result['user'] as $user):
                  $reason=$user->reason;
                  $result_reason=explode(" ", $user->reason);
                  if($result_reason[1]=='require'&&$result_reason[2]=='subscribe'&&$result_reason[3]=='hint:') $reason='用户取消关注';
                ?>
                <tr>
                  <td><?=$user->order->title?></td>
                  <td><img style="height:32px;width:32px;"src="<?=$user->qrcode->headimgurl?>"></td>
                  <td><?=$user->qrcode->nickname?></td>
                  <!-- <td><?=$user->qrcode->sex?></td> -->
                  <td><?=$user->state==1?"<span class='label label-success'>已发送</span>":"<span class='label label-warning'>发送失败</span>"?></td>

                  <td><?=$reason?></td>
                  <td><?=date("Y-m-d h:i:s",$user->lastupdate)?></td>
                </tr>
              <?php endforeach?>
                                    </tbody>
                                </table>
                            </form>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
</form>









        </div>
        </div>
