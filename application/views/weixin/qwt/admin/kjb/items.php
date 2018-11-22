<style type="text/css">
  th, td{
    white-space: nowrap;
  }
</style>
<div class="tpl-page-container tpl-page-header-fixed">
  <div class="tpl-content-wrapper">
    <div class="tpl-content-page-title">
        商品管理
    </div>
    <ol class="am-breadcrumb">
      <li><a href="#" class="am-icon-home">砍价宝</a></li>
      <li class="am-active">商品管理</li>
    </ol>
    <form class="am-form" method="get">
      <div class="tpl-portlet-components">
        <div class="portlet-title">
          <div class="am-u-sm-12 am-u-md-6">
            <div class="caption font-green bold">
              共<?=count($result['items'])?>件商品
            </div>
          </div>
         <!--  <div class="am-u-sm-12 am-u-md-3">
            <a href="/qwtkjba/items/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-save"></span> 一键导入有赞商品</a>
          </div> -->
          <div class="am-u-sm-12 am-u-md-3">
            <a href="/qwtkjba/items/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新商品</a>
          </div>
          <div class="am-u-sm-12 am-u-md-3">
            <div class="am-input-group am-input-group-sm">
              <input type="text" name="s" class="am-form-field form-control input-sm pull-right" value="<?=$result['s']?>" placeholder="搜索">
              <span class="am-input-group-btn">
                <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
              </span>
            </div>
          </div>
        </div>
        <div class="tpl-block">
          <div class="am-g">
            <div class="am-u-sm-12" style="overflow:scroll;">
              <table class="am-table am-table-striped am-table-hover table-main">
                <thead>
                  <tr>
                    <th>商品名称</th>
                    <th>商品图片</th>
                    <th>商品状态</th>
                    <th>商品库存</th>
                    <th>商品原价</th>
                    <th>是否关注用户才能发起砍价</th>
                    <th>是否关注用户才能帮忙砍价</th>
                    <th>活动标题</th>
                    <th>活动副标题</th>
                    <th>商品最低价</th>
                    <th>商品砍价活动有效期</th>
                    <th>商品最大可砍次数</th>
                    <!-- <th>商品砍价活动详情</th> -->
                    <!-- <th>商品砍价活动规则</th> -->
                    <!-- <th>商品uv</th> -->
                    <th>商品pv</th>
                    <!-- <th>商品转发次数</th> -->
                    <!-- <th>该商品正在砍价中的订单数（点击进入）</th> -->
                    <!-- <th>该商品已付款的订单数（点击进入）</th> -->
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                <?php if ($result['items']):?>
                  <?php foreach ($result['items'] as $k => $v):?>
                    <tr>
                      <td><?=$v->name?></td>
                      <td><img style="max-height:50px;" src="/qwtkjba/images/item/<?=$v->id?>.v<?=$v->lastupdate?>.jpg"></td>
                      <td><?php switch ($v->status) {
                        case 0:
                          echo '<span class="label label-success">开放中</span>';
                          break;
                        case 3:
                          echo '<span class="label label-danger">已终止</span>';
                          break;
                        default:
                          echo '<span class="label label-fail">未知</span>';
                          break;
                      }?></td>
                      <td><?=$v->stock?></td>
                      <td><?=round($v->old_price/100,2).'元'?></td>
                      <td><?php switch ($v->need_sub) {
                        case 1:
                          echo "否";
                          break;
                        case 2:
                          echo "是";
                          break;

                        default:
                          echo "未设置";
                          break;
                      }?></td>
                      <td><?php switch ($v->cut_sub) {
                        case 1:
                          echo "否";
                          break;
                        case 2:
                          echo "是";
                          break;

                        default:
                          echo "未设置";
                          break;
                      }?></td>
                      <td><?=$v->title?></td>
                      <td><?=$v->subtitle?></td>
                      <td><?=round($v->price/100,2).'元'?></td>
                      <td><?php
                      if ($v->begintime==0) {
                        $left='';
                      }else{
                        $left=date('Y-m-d H:i:s',$v->begintime).'起';
                      }
                      if ($v->endtime==0) {
                        $right='永久有效';
                      }else{
                        $right='至'.date('Y-m-d H:i:s',$v->endtime).'截止';
                      }
                      echo $left.$right;?></td>
                      <td><?=$v->cut_num?></td>
                      <!-- <td>暂空</td> -->
                      <?php
                      $pv = 0;
                      $event = ORM::factory('qwt_kjbevent')->where('bid','=',$v->bid)->where('iid','=',$v->id)->find_all();
                      if ($event){
                        foreach ($event as $m => $n) {
                          $pv = $pv + $n->PV;
                        }
                      }?>
                      <td><?=$pv?></td>
                      <!-- <td>暂空</td> -->
                      <!-- <td>暂空</td> -->
                      <!-- <td>暂空</td> -->
                      <td>
                      <?php if($v->status==0):?>
                      <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtkjba/items_edit/<?=$v->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                    <?php endif;?>
                    <?php if($v->status==3):?>
                      <a style="background-color:#fff;" class='recover am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>">
                      <span>恢复</span> <i class="am-icon-rotate-left"></i>
                    </a>
                    <?php endif;?>
                    <?php if($v->status==0):?>
                      <a style="background-color:#fff;" class='terminate am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>">
                      <span>终止</span> <i class="am-icon-times"></i>
                    </a>
                     <?php endif;?>
                     <?php if(ORM::factory('qwt_kjbevent')->where('iid','=',$v->id)->count_all()==0):?>
                      <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>">
                      <span>删除</span> <i class="am-icon-times"></i>
                    </a>
                  <?php endif;?>
                      </td>
                    </tr>
                  <?php endforeach?>
                <?php endif?>
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
        window.location.href = "/qwtkjba/items_delete/"+id;
      })
  })
  $('.terminate').click(function(){
    var id= $(this).data('id');
    swal({
      title: "确认要终止吗？",
      text: "终止后可以恢复！",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      cancelButtonText: '取消',
      confirmButtonText: '确认终止',
      closeOnConfirm: false
      },
      function(){
        window.location.href = "/qwtkjba/items_terminate/"+id;
      })
  })
  $('.recover').click(function(){
    var id= $(this).data('id');
    swal({
      title: "确认要恢复吗？",
      text: "恢复后次商品可以正常砍价！",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: '#DD6B55',
      cancelButtonText: '取消',
      confirmButtonText: '确认恢复',
      closeOnConfirm: false
      },
      function(){
        window.location.href = "/qwtkjba/items_recover/"+id;
      })
  })
</script>
