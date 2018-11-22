
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
  th,td{
    white-space: nowrap;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                用户管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">充值拼团</a></li>
    <li class="am-active"><a href="/qwtwfba/qrcodes">用户管理</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 个用户
                    </div>
                    </div>
                    <form method="get" id="rankform">
                                    <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <div class="am-form" style="overflow:scroll">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                                      <th>昵称</th>
                                      <th>头像</th>
                                      <th>加入时间</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($result['qrcode'] as $qrcode):?>
                                    <tr>
                                    <td><?=$qrcode->nickName?></td>
                                    <td><img src="<?=$qrcode->avatarUrl?>" width="32" height="32" title="<?=$qrcode->openid?>"></td>
                                    <td><?=date("Y-m-d H:i:s",$qrcode->jointime)?></td>
                                     </tr>
                                 <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
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
        </div>
        </div>
<script type="text/javascript">
</script>
