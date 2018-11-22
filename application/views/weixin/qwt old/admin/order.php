
<section class="wrapper" >

    <div class="row">
        <div class="page-heading">
            <h3>
                订购记录
            </h3>
        </div>
        <div class="col-sm-12">
        <section class="panel">
        <header class="panel-heading">
            共有<?=$result['countall']?>条记录
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
            <th>产品规格</th>
            <th>续费价格(元)</th>
            <th>购买时间</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($result['orders'] as $order ): ?>

        <?php
            $iid=ORM::factory('qwt_buy')->where('id','=',$order->buy_id)->find()->iid;
            $name =ORM::factory('qwt_item')->where('id','=',$iid)->find()->name;
            $sh_name=ORM::factory('qwt_sku')->where('id','=',$order->sku_id)->find()->name;
        ?>
        <tr class="gradeX">
            <td><?=$name?></td>
            <td><?=$sh_name?></td>
            <td><?=$order->rebuy_price?></td>
            <td><?=date('Y-m-d H:i:s',$order->rebuy_time)?></td>
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
        </div>
        </div>
</section>


