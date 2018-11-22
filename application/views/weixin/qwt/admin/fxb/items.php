

 <?php
function convert1($type,$url=''){
 switch ($type) {
   case 0:
     echo '实物奖品';
     break;
   case 1:
     echo '微信卡券';
     break;
   case 2:
     echo '虚拟奖品';
     break;
   case 3:
     echo '话费流量';
     break;
   case 4:
     echo '微信红包';
     break;
   case 5:
     echo '有赞赠品';
     break;
  case 6:
     echo '有赞优惠券';
     break;
   default:
     # code...
     break;
 }
}
?>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                奖品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">订单宝</a></li>
                <li>兑换商城</li>
                <li class="am-active">奖品管理</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($result['items'])?> 件产品
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtfxba/items/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新奖品</a>
                        </div>

                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="搜索">
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
                  <th>排序</th>
                  <th>品名</th>
                  <th>类型</th>
                  <th>兑换数量</th>
                  <th>剩余数量</th>
                  <th>限购</th>
                  <th>价格</th>
                  <th>积分</th>
                  <th>状态</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['items'] as $key=>$item):?>

                <tr>
                  <td><?=$item->pri?></td>
                  <td><?=$item->name?></td>
                  <td><?=convert1($item->type,$item->url)?></td>
                  <td><?=$convert[$key]?></td>
                  <td><?=$item->stock?></td>
                  <td><?=$item->limit?></td>
                  <td><?=$item->price?></td>
                  <td><?=$item->score?></td>
                  <td>
                  <?php
                  if ($item->endtime && strtotime($item->endtime) < time())
                    echo '<span class="label label-danger">已过期</span>';
                  else if ($item->show == 0)
                    echo '<span class="label label-warning">隐藏</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>
                  <td><?=date('Y-m-d H:i', $item->lastupdate)?></td>
                  <td nowrap=""><a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwtfxba/items/edit/<?=$item->id?>"><span class="am-icon-edit"></span> 修改</a>
                  <?php if($convert[$key]==0):?>
                  <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$item->id?>">
                  <span>删除</span> <i class="am-icon-times"></i></a>
                  <?php endif;?>
                  </td>
                </tr>

                <?php endforeach;?>
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
      window.location.href = "/qwtfxba/items?id="+id+"&DELETE=1";
    })
  })
    </script>
