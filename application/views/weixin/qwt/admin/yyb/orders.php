<style type="text/css">
  th,td{
    white-space: nowrap;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                模板消息群发管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">预约宝</a></li>
                <li><a>模板消息群发管理</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$countall?> 个预约任务
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtyyba/orders_add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 新建群发</a>
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
                        <div class="am-u-sm-12" style="overflow:scroll;">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                  <th>新建时间</th>
                  <th>模板消息群发标题</th>
                  <th>模板消息群发时间</th>
                  <th>模板消息跳转类型</th>
                  <th>状态（是否发送）</th>
                  <th>发送用户数</th>
                  <th>失败条数</th>
                  <th>发送用户</th>
                  <th>发送方式</th>
                  <th>是否发送</th>
                  <th>操作（开始发送后不可修改和删除）</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['orders'] as $orders):
                ?>
                <tr>
                  <td><?=date('Y-m-d H:i:s',$orders->lastupdate)?></td>
                  <td><?=$orders->title?></td>
                  <td><?=date('Y-m-d H:i:s',$orders->time)?></td>
                  <td id="type<?=$orders->id?>">
                  <?php
                  if ($orders->type == 1&&$orders->url)
                    echo '<span class="label label-warning">链接</span>';
                  elseif ($orders->type == 2)
                    echo '<span class="label label-info">优惠券优惠</span>';
                  elseif ($orders->type == 3)
                    echo '<span class="label label-danger">有赞赠品</span>';
                  elseif ($orders->type == 1&&!$orders->url)
                     echo '<span class="label label-success">无</span>';
                  elseif ($orders->type == 4)
                     echo '<span class="label label-success">跳转微信小程序</span>';
                  ?>
                  </td>
                  <td>
                    <?php
                    if($orders->state==1){
                      echo '<span class="label label-success">发送完成</span>';
                    }elseif ($orders->state==0) {
                      // echo $orders->way.'aaa<br>';
                      // echo $orders->time.'aaa<br>';
                      // echo time().'aaa<br>';
                      // exit();
                      if($orders->start==1){
                          $sending=ORM::factory('qwt_yybqrcode')->where('bid','=',$orders->bid)->count_all()-$orders->has_send+12500;
                          echo '<span class="label label-success">发送中(约'.ceil($sending/2500).'分钟后发送完毕)</span>';
                      }else{
                        if($orders->ifsend==0){
                          echo '<span class="label label-warning">未发送</span>';
                        }else{
                          if($orders->way == 0&&$orders->time>time()){
                            echo '<span class="label label-warning">未发送(未到发送时间)</span>';
                          }elseif ($orders->way == 1||$orders->time<=time()) {
                            $odernum=ORM::factory('qwt_yyborder')->where('bid','!=',$orders->bid)->where('state','=',0)->count_all();
                            if($odernum>0){
                                $dlorders=ORM::factory('qwt_yyborder')->where('bid','!=',$orders->bid)->where('state','=',0)->find_all();
                                foreach ($dlorders as $k => $dlorder) {
                                    $bids[$k]= $dlorder->bid;
                                }
                                $countnum=ORM::factory('qwt_yybqrcode')->where('bid','IN',$bids)->count_all();
                                echo '<span class="label label-warning">队列中,约'.ceil($countnum/2500).'分钟后开始发送</span>';
                            }else{
                                echo '<span class="label label-warning">未发送(五分钟后如还是此状态请联系管理员)</span>';
                            }
                          }
                        }
                      }
                    }
                  ?>
                  </td>
                  <td><?=$orders->state==1?$orders->all_user:''?></td>
                  <td><?=$orders->state==1?ORM::factory('qwt_yybitem')->where('bid','=',$orders->bid)->where('oid','=',$orders->id)->count_all():''?></td>
                   <td id="flag<?=$orders->id?>">
                  <?php
                  if ($orders->flag == 1)
                    echo '<span class="label label-warning">'.$orders->appointment->name.'</span>';
                  elseif ($orders->flag == 0)
                    echo '<span class="label label-info">全部</span>';
                  ?>
                  </td>
                  <td id="way<?=$orders->id?>">
                  <?php
                  if ($orders->way == 1)
                    echo '<span class="label label-warning">立即发送</span>';
                  elseif ($orders->way == 0)
                    echo '<span class="label label-info">定时发送</span>';
                  ?>
                  </td>
                  <td><?=$orders->ifsend==1?'发送':'不发送'?></td>
                  <td nowrap="">
                    <?php if($orders->way==1&&$orders->ifsend==0):?>
                    <a style="background-color:#fff;" class='sendtpl am-btn am-btn-default am-btn-xs am-text-secondary' data-oid="<?=$orders->id?>">
                      <span>发送</span> <i class="am-icon-send"></i>
                    </a>
                    <?php endif?>
                    <?php if(($orders->way==1&&$orders->ifsend==0)||($orders->way==0&&$orders->time>time())):?>
                    <a style="background-color:#fff;" class='yulan am-btn am-btn-default am-btn-xs am-text-secondary' data-oid="<?=$orders->id?>">
                      <span>预览</span> <i class="am-icon-reply"></i>
                    </a>
                    <a style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtyyba/orders_edit/<?=$orders->id?> ">
                      <span>修改</span> <i class="am-icon-edit"></i>
                    </a>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-oid="<?=$orders->id?>">
                      <span>删除</span> <i class="am-icon-times"></i>
                    </a>
                    <?php endif?>
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
<script>
$('.yulan').click(function(){
      var oid = $(this).data('oid');
  swal({
    title: "确认要预览吗？",
    text: "预览前需要有管理员绑定预览！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认',
    closeOnConfirm: true,
    },
    function(){
      $.ajax({
         type: "GET",
         url: "/qwtyyba/yulan",
         data: {oid:oid},
         dataType: "text",
         success: function(data){
                    alert(data);
                   }
                 });
            });
  })
$('.sendtpl').click(function(){
      var oid = $(this).data('oid');
  swal({
    title: "确认要发送吗？",
    text: "发送后无法修改和删除任务！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认发送',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtyyba/orders?sendoid="+oid;
    })
  })
$('.delete').click(function(){
      var oid = $(this).data('oid');
  swal({
    title: "确认要删除吗？",
    text: "删除后无法恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtyyba/orders?deleteoid="+oid;
    })
  })
</script>
