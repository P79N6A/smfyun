<!-- 卡密记录 -->
<?php
    function converta($a){
        switch ($a) {
            case 0:
            echo '未处理';
                break;
            case 1:
            echo "已处理";
            default:
            echo '';
                break;
        }
    }
?>
<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
</style>
<section class="wrapper" style="width:85%;float:right;">
 <div wclass="wrapper">
      <div class="row">
          <div class="page-heading">
            <h3>
                领取记录
                <small><?=$desc?></small>
              </h3>
         </div>
<ul class="breadcrumb" style="margin-left:15px">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">领取记录</li>
</ul>


                <div class="col-sm-12">
                <section class="panel">
                <header class="panel-heading">
                    共 <?=count($result['items'])?> 件产品
                    <span class="tools pull-right">
                        <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出奖品发送记录</span></a>
                     </span>
                </header>
                 <div class="input-group" style="width: 150px;margin-left:15px">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="panel-body">
                <div class="adv-table editable-table ">
                <div class="clearfix">
                <table class="table table-striped table-hover table-bordered">

              <thead>
              <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>活动名称</th>
                <th>奖品名称</th>
                <th>时间</th>
                <th>发送状态</th>
                <th>原因</th>
              </tr>
              </thead>
                <?php foreach ($result['orders'] as $order):

                ?>
              <tbody>
                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td>
                    <a href="/qwtrwba/qrcodes?id=<?=$order->user->id?>"><?=$order->name?></a>
                  </td>
                  <td><?=$order->task_name?></td>
                  <td><?=$order->item_name?></td>
                  <td><?=date('m-d H:i',$order->lastupdate)?></td>
                  <th><?=converta($order->state)?></th>
                  <td><?=$order->log?></td>
                </tr>

                <?php endforeach;?>
                <input type="hidden" name="action" value="oneship">

              </tbody>
              </table>
                </div>
            <div class="box-footer clearfix">
                <?=$pages?>
            </div>
                </div>
                </section>
                </div>
                </div>
        </div>
        <!--body wrapper end-->

</section><!-- /.content -->
