

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                关键词列表
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">公众号自动下发小程序卡片工具</a></li>
                <li><a>发送规则</a></li>
                <li><a>输入关键词后下发</a></li>
                <li class="am-active">关键词列表</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($rule)?> 条关键词
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtzdfa/rules/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新关键词</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-title">关键词</th>
                                            <th class="table-type">发送的小程序卡片</th>
                                            <th class="table-type">小程序卡片下发后发送的文案</th>
                                            <th class="table-set">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rule as $k => $v):?>
                                            <tr>
                                                <td><?=$v->keyword?></td>
                                                <td><?=$v->msg->name?></td>
                                                <td><?=$v->text?></td>
                                                <td>
                                                    <a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwtzdfa/rules/edit/<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-pencil-square-o"></span> 修改</a>
                                                    <button type="button" class="delete am-btn am-btn-default am-btn-xs am-text-secondary" data-id="<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-trash"></span> 删除</button>
                                                </td>
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
      window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwtzdfa/rules/edit/"+id+"?DELETE=1";
    })
  })
</script>

