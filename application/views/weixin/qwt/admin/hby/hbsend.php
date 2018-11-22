
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                投放管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li class="am-active">门店管理及投放</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共<?=$count?>个门店账号（登陆链接：http://<?=$_SERVER['HTTP_HOST']?>/qwthby/sns_login/<?=$bid?>）
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwthbya/hbsend/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加门店账号</a>
                        </div>
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12" style="overflow:scroll">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                        <tr>
                                          <th class="table-type">门店名称</th>
                                          <th class="table-type">状态</th>
                                          <th class="table-type">营销规则</th>
                                          <th class="table-type">账号</th>
                                          <th class="table-title">密码</th>
                                          <th class="table-title">绑定的微信账号</th>
                                          <th class="table-id">生成的红包码数量</th>
                                          <th class="table-id">使用的红包码数量</th>
                                          <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($account as $k => $v):?>
                                    <tr>
                                      <td><?=$v->name?></td>
                                      <td>
                                         <?=$v->status==1?'<span class="label label-success">有效</span>':'<span class="label label-danger">已取消</span>'?>
                                      </td>
                                      <td><?=$v->rules->name?></td>
                                      <td><?=$v->account?></td>
                                      <td><?=$v->password?></td>
                                      <td>
                                      <?php if($v->status==1):?>
                                          <?php if($v->wx_bind>0):?>
                                              <?=$v->logins->nickname?><br><a style="background-color:#fff;" class='unbind am-btn am-btn-default am-btn-xs am-text-warning' data-id="<?=$v->id?>"><span class="am-icon-pencil-square-o"></span> 解除微信账号绑定</a>
                                          <?php else:?>
                                              <a style="background-color:#fff;" class='bind am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>"><span class="am-icon-pencil-square-o"></span> 绑定微信账号</a>'</td>
                                          <?php endif?>
                                      <?php endif?>
                                      <?php
                                      $hb_made = ORM::factory('qwt_hbykl')->where('bid','=',$v->bid)->where('from_lid','=',$v->id)->count_all();
                                      $hb_used = ORM::factory('qwt_hbykl')->where('bid','=',$v->bid)->where('from_lid','=',$v->id)->where('used','>',0)->count_all();
                                      ?>
                                      <td><a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/hbmct?from_lid=<?=$v->id?>"><?=$hb_made?></a></td>
                                      <td><a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/qrcodes?from_lid=<?=$v->id?>"><?=$hb_used?></a></td>

                                    <?php if($v->status==1):?>
                                      <td>
                                      <a style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwthbya/hbsend/edit/<?=$v->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                                      </td>
                                    <?php endif?>
                                    </tr>
                                <?php endforeach?>
                                    </tbody>
                                </table>
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
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
<script type="text/javascript">
$('.bind').click(function(){
    var id = $(this).data('id');
    var bid = <?=$bid?>;
    swal(
        {
            title: "绑定微信账号",
            text: "手机微信扫一扫绑定",
            imageUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwthby/bind_qr/"+bid+"/"+id,
            imageSize: "200x200",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "已绑定",
            cancelButtonText: "取消",
            closeOnConfirm: false,
            closeOnCancel: true,
            html: true,
        },
        function(isConfirm){
            window.location.reload();
        }
    )
})
$('.unbind').click(function(){
    var id = $(this).data('id');
    swal({
        title: "解除绑定",
        text: "确认要解除当前绑定的微信账号吗？",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        closeOnConfirm: false
    },
    function(){
        window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/unbind/"+id;
    });
})

</script>
