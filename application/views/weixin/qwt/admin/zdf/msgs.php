
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                小程序卡片管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">公众号自动下发小程序卡片工具</a></li>
                <li><a>小程序卡片管理</a></li>
                <li class="am-active">小程序卡片列表</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($msg)?> 个小程序卡片
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtzdfa/msgs/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加小程序卡片</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                        <th>小程序名称</th>
                                        <th>小程序卡片标题</th>
                                        <th>小程序AppID</th>
                                        <th>点击小程序卡片跳转页面路径</th>
                                        <th>小程序卡片预览图</th>
                                        <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($msg as $k => $v):?>
                <tr>
                <td><?=$v->name?></td>
                <td><?=$v->title?></td>
                <td><?=$v->appid?></td>
                <td><?=$v->path?$v->path:'默认前往主页'?></td>
                <td><img src="http://<?=$_SERVER['HTTP_HOST']?>/qwtzdfa/images/msg/<?=$v->id?>?<?=time()?>.jpg" style="height: 100px;"></td>
                  <td><a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwtzdfa/msgs/edit/<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-pencil-square-o"></span> 修改</a>
                  <button type="button" class="delete am-btn am-btn-default am-btn-xs am-text-secondary" data-id="<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-trash"></span> 删除</button></td>
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










        </div>

    </div>

<script type="text/javascript">

    $('.delete').click(function(){
        var id = $(this).data('id')
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwtzdfa/msgs/edit/"+id+"?DELETE=1";
    })
  })
<?php if($result['err3']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
</script>
