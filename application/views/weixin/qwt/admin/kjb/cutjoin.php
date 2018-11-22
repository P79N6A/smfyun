
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                砍价记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">砍价宝</a></li>
                <li><a href="#">砍价管理</a></li>
                <li class="am-active">砍价记录</li>
            </ol>
            <form class="am-form" method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($cut)?> 条砍价
                    </div>
                    </div>

                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>发起者头像</th>
                  <th>发起者昵称</th>
                  <th>砍价的商品</th>
                  <th>砍掉的价格（元）</th>
                  <th>砍价时间</th>
                </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($cut):?>
                                      <?php foreach ($cut as $k => $v):?>
                                        <tr>
                  <td><img src="<?=$v->event->qrcode->headimgurl?>" width="32" height="32" title="<?=$v->qrcode->openid?>"></td>
                  <td><?=$v->event->qrcode->nickname?></td>
                  <td><?=$v->event->item->name?></td>
                  <td><?=$v->money/100?></td>
                  <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                                        </tr>
                                      <?php endforeach?>
                                    <?php endif?>
                                    </tbody>
                                </table>
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
<!--     <script type="text/javascript">
      $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要终止吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认终止',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtrwba/tasks?tid="+id+"&DELETE=1";
    })
  })
    </script> -->
