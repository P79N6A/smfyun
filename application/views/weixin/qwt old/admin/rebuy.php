<section class="wrapper" >
    <div class="row">
        <div class="page-heading">
            <h3>
                续费信息
            </h3>

        </div>
        <div class="col-sm-12">
        <section class="panel">
        <header class="panel-heading">
            共有<?=$result['countall']?>个产品
            <span class="tools pull-right">
                <a href="javascript:;" class="fa fa-chevron-down"></a>
             </span>
        </header>
        <div class="panel-body">
        <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="dynamic-table">
        <thead>
        <tr>
            <th>产品名称</th>
            <th>产品状态</th>
            <th>到期时间</th>
            <th>产品续费</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result['rebuys'] as $rebuy): ?>
          <?php
          $item =ORM::factory('qwt_item')->where('id','=',$rebuy->iid)->find();
          ?>
        <tr class="gradeX">
            <td><?=$item->name?></td>
            <td><?php
                  if ($rebuy->expiretime && $rebuy->expiretime < time())
                    echo '<span class="label label-danger">已到期</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
            </td>
            <td><?=date("Y-m-d h:i:s",$rebuy->expiretime)?><?php if($item->id==1){echo "（".$rebuy->hbnum."个）";}?></td>
            <td><a href="/qwta/product/<?=$item->id?>"><i class="fa fa-asterisk">续费</i></a></td>
        </tr>
        <?php endforeach ?>
        </tfoot>
        </table>
        </div>
         <div class="box-footer clearfix">
            <?=$pages?>
        </div>
        </div>
        </section>

     </dev>

    </div>
</section>
