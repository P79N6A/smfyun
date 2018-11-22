
<?php
  function converta($key){
    switch ($key) {
        case 'shiwu':
        echo '实物奖品';
        break;
      case 'hongbao':
        echo '微信红包';
        break;
      case 'coupon':
        echo '微信卡券';
        break;
      case 'gift':
        echo '赠品';
        break;
      case 'kmi':
        echo '特权商品';
        break;
      case 'yzcoupon':
        echo '有赞优惠券';
        break;
        case 'yhm':
        echo '卡密';
        break;
      default:
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
                <li><a href="#" class="am-icon-home">任务宝服务号版</a></li>
                <li><a href="#">任务设置</a></li>
                <li class="am-active">奖品管理</li>
            </ol>
            <form class="am-form" method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共<?=count($result['items'])?>件产品
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtrwba/items/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新奖品</a>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="搜索">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>奖品名称</th>
                  <th>奖品类型</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['items'] as $key=>$item):?>
                <tbody>
                <tr>
                  <td><?=$item->km_content?></td>
                  <td><?=converta($item->key)?></td>
                  <td><?=date('Y-m-d H:i:s',$item->lastupdate)?></td>
                  <td nowrap="">
                  <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtrwba/items_edit/<?=$item->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                  <!-- <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$item->id?>">
                  <span>删除</span> <i class="am-icon-times"></i></a> -->
                  </td>
                </tr>
                <?php endforeach;?>
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
      window.location.href = "/qwtrwba/items_delete/"+id;
    })
  })
    </script>
