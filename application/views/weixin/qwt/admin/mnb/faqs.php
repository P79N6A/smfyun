
<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                问题列表
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">蒙牛数据开发</a></li>
                <li class="am-active">问题列表</li>
            </ol>

            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($faq)?> 个问题
                    </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtmnba/faqs/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新问题</a>
                    </div>
                    <form method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按问题名称或问题描述搜索" value="<?=htmlspecialchars($result['s'])?>">
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
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-id">排序</th>
                                            <th class="table-id">问题名称</th>
                                            <th class="table-id">问题描述</th>
                                            <th class="table-id">创建时间</th>
                                            <th class="table-id">问题所属分类</th>
                                            <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($faq[0]->id):?>
                                        <?php foreach ($faq as $k => $v) :?>
                                            <tr>
                                                <td><?=$k+1?></td>
                                                <td><?=$v->name?></td>
                                                <td><?=$v->title?></td>
                                                <td><?=date('Y-m-d H:i:s',$v->createtime)?></td>
                                                <td><?=$v->type->name?></td>
                                            <td>
                  <a href="/qwtmnba/faqs/edit/<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <span>修改</span></a>
<a style="background-color:#fff;" data-id="<?=$v->id?>" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' >删除</a>
                </td>
                                            </tr>
                                        <?php endforeach?>
                                    <?php endif?>
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
            var id= $(this).data('id');
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
      window.location.href = "/qwtmnba/faqs?id="+id+"&delete=1";
    })
  })
</script>
