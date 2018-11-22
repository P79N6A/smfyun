<!-- 发送管理 -->
<style type="text/css">
    .change{
        padding-left: 12px;
    }
</style>
<section class="content-header">
  <h1>
    商品设置
  </h1>
  <ol class="breadcrumb">
    <li><a href=" "><i class="fa fa-dashboard"></i> 首页</a ></li>
    <li class="active">商品设置</li>
  </ol>
</section>

<section class="content">
     <form method="post">
     <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <li class="pull-left" style="list-style:none;">
                        <span>商品名称</span>
                                <div class="input-group" style="width: 250px;display:inline-flex;">
                                  <input type="text" name="s" class="form-control input-sm pull-left" placeholder="模糊搜索" value="">
                                  <div class="input-group-btn">
                                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                                  </div>
                                </div>
                        </li>
                    </th>
                    <th style="float:right;"><a href="/kmia/items1" class="btn btn-primary" >点此刷新商品</th></a>
                </tr>
            </thead>
        </table>
    </form>
        <table class="table table-striped table-hover" style="background-color: #fff;">
                <thead>
                    <tr>
                        <th>状态</th>
                        <th>商品名称</th>
                        <th>商品价格</th>
                        <th>购买后发送的奖品</th>
                        <th>发送次数</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                 foreach ($result['items'] as $item):
                ?>
                <form method="post">
                     <tr>
                        <input type="hidden" name='cfg[item]' value="<?=$item->id?>" />
                        <td><?=$item->pid?'<span class="label label-success">已配置</span>':'<span class="label label-warning">未配置</span>'?></td>
                        <td><?=$item->name?></td>
                        <td><?=$item->price?></td>
                        <td>
                        <?php if($item->pid):?>
                            <span><?=$item->prize->km_content?></span>
                        <?else:?>
                        <select name='cfg[pid]'>
                        <?php
                        foreach ($result['pri'] as $pri):
                        ?>
                        <?php if($pri->key=='kmi'&&(ORM::factory('kmi_km')->where('bid','=',$pri->bid)->where('startdate','=',$pri->value)->where('live','=',1)->count_all()>0)):?>
                        <option value='<?=$pri->id?>'><?=$pri->km_content?></option>
                        <?php endif;?>
                        <?php if($pri->key!='kmi'&&$pri->km_num>0):?>
                        <option value='<?=$pri->id?>'><?=$pri->km_content?></option>
                        <?php endif;?>
                        <?php if($pri->key=='freedom'):?>
                        <option value='<?=$pri->id?>'><?=$pri->km_content?></option>
                        <?php endif;?>
                        <?php endforeach;?>
                        </select>
                        <?endif?>
                        </td>
                        <td><?=$item->send_num?></td>
                        <?php if($item->pid):?>
                            <input type="hidden" name='delete[id]' value="<?=$item->id?>" />
                            <td><button class="btn btn-danger" type="submit">修改</button ></td>
                        <?else:?>
                            <td><button class="btn btn-primary" type="submit">提交</button ></td>
                        <?endif?>
                    </tr>
                </form>
                <?php endforeach;?>
                </tbody>
        </table>
    <div class="box-footer clearfix">
            <?=$pages?>
    </div>
</section>
