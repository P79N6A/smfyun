

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送内容管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">消息宝</a></li>
                <li><a>发送内容管理</a></li>
                <li class="am-active">发送内容列表</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($item)?> 条消息
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtxxba/msgs/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 新建发送内容</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                        <th>排序</th>
                                        <th>名称</th>
                                        <th>类型</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($item as $k => $v):?>
                <tr>
                <td><?=$k+1?></td>
                <td><?=$v->name?></td>
                <td><?=$v->type==1?'小程序卡片':'图文消息'?></td>
                <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                  <td><a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwtxxba/msgs/edit/<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-pencil-square-o"></span> 修改</a><a data-id="<?=$v->id?>" class="delete am-btn am-btn-default am-btn-xs am-text-secondary" style="background-color:#fff;"><span class="am-icon-trash"></span> 删除</a></td>
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
        var id = $(this).data('id');
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
      window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/msgs/edit/"+id+"?DELETE=1";
    })
  })
</script>

