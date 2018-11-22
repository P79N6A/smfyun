
<?php
  function convert1($a){
  switch ($a) {
    case 0:
      echo '实物奖品';
      break;
    case 5:
      echo '优惠券';
      break;
    case 4:
      echo '微信红包';
      break;
    case 6:
      echo '有赞赠品';
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
                奖品设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">微信盖楼</a></li>
                <li class="am-active">奖品设置</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        奖品设置
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="item_add/<?=$config['buy_id']?>" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新奖品</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                        <tr>
                          <th>奖品名字</th>
                          <th>奖品类型</th>
                          <th>库存</th>
                          <th>中奖回复文案</th>
                          <th>奖品内容</th>
                          <th>配置时间</th>
                          <th>操作</th>
                        </tr>
                                    </thead>
                                    <tbody id="liebiao">
                      <?php if($item):?>
                     <?php foreach ($item as $item): ?>
                        <tr>
                          <td ><?=$item->name?></td>
                          <td><?=convert1($item->type)?></td>
                          <td><?=!$item->stock?'':$item->stock?></td>
                          <th><?=$item->word?></th>
                          <td><?=$item->type==4?number_format((($item->code)/100),2).'元':$item->code?></td>
                          <td ><?=date('Y/m/d H:i:s ',$item->lastupdate)?></td>
                          <td  nowrap=""><a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtgla/item_edit/<?=$config['bid']?>/<?=$item->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                          <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$item->id?>">
                          <span>删除</span> <i class="am-icon-times"></i></a>
                          </td>
                        </tr>
                     <?php endforeach ?>
                 <?php else:?>
                      <tr>
                          <td>请添加盖楼所需奖品</td>
                        </tr>
                 <?php endif;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                    <?=$pages?>
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
      window.location.href = "/qwtgla/item?delete="+id;
    })
  })
    </script>

