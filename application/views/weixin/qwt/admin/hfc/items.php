
<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">话费充值</a></li>
                <li class="am-active">商品管理</li>
            </ol>

            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($item)?> 个商品
                    </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwthfca/items/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新商品</a>
                    </div>
                    <!-- <form method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按问题名称或问题描述搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form> -->
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-id">商品名称</th>
                                            <th class="table-id">商品图片</th>
                                            <th class="table-id">商品原价</th>
                                            <th class="table-id">商品团购价格</th>
                                            <th class="table-id">几人成团</th>
                                            <th class="table-id">团购有效期</th>
                                            <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($item[0]->id):?>
                                        <?php foreach ($item as $k => $v):?>
                                            <tr>
                                                <td><?=$v->name?></td>
                                                <td><img src="/qwthfca/images/item/<?=$v->id?>.v<?=time()?>.jpg" style="height:30px;"></td>
                                                <td><?=$v->old_price?></td>
                                                <td><?=$v->price?></td>
                                                <td><?=$v->groupnum?></td>
                                                <td><?php
                                                    if ($v->timeouttype==1) {
                                                        echo "拼团发起后".($v->timeout/86400)."天内有效";
                                                    }else{
                                                        echo date('Y-m-d H:i:s',$v->timeout);
                                                    }?></td>
                                            <td>
                  <a href="/qwthfca/items/edit/<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <span>修改</span></a>
<a style="background-color:#fff;" data-id="<?=$v->id?>" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' >删除或隐藏</a>
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
    title: "确认要删除或隐藏吗？",
    text: "已有交易记录的商品不能删除只能隐藏，该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwthfca/itemsdelete/"+id;
    })
  })
</script>
