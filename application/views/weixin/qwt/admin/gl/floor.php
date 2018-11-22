
<?php
  function convert1($a){
  switch ($a) {
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
<style type="text/css">

  .am-form-group{
    min-height: 30px;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                楼层设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">微信盖楼</a></li>
                <li class="am-active">楼层设置</li>
            </ol>
            <div class="tpl-portlet-components" style="overflow: -webkit-paged-x;">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        楼层设置
                    </div>
                    </div>
                </div>
            <div class="am-u-sm-12 am-u-md-12">
                <div class="tpl-form-body tpl-form-line">
                    <?php if ($success =='floor' ):?>
                            <div class="tpl-content-scope">
                              <div class="note note-info">
                                <p> 新建楼层成功!</p>
                              </div>
                            </div>
                    <?php elseif($success =='delete'):?>
                            <div class="tpl-content-scope">
                              <div class="note note-info">
                                <p> 删除成功!</p>
                              </div>
                            </div>
                          <?php endif?>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                        <tr>
                          <th>中奖楼层</th>
                          <th>奖品名字</th>
                          <th>奖品类型</th>
                          <th>建立时间</th>
                          <th>操作</th>
                        </tr>
                                    </thead>
                                    <tbody id="liebiao">
                      <?php if($floor):?>
                     <?php foreach ($floor as $floor): ?>
                        <tr>
                          <td><?=$floor->floor?></td>
                          <td><?=$floor->item->name?></td>
                          <td><?=convert1($floor->item->type)?></td>
                          <td><?=date('Y/m/d H:i:s ',$floor->lastupdate)?></td>
                          <td><form method="post"><input type="hidden" name='delete' value="<?=$floor->id?>"><button type="submit" class="btn btn-danger">删除</button></form></td>
                        </tr>
                     <?php endforeach ?>
                 <?php else:?>
                      <tr>
                          <td>请添加楼层</td>
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
                <div class="tpl-block tpl-amazeui-form">
                      <form class="am-form tpl-form-horizontal" method="post">
                            <div class="am-form-group">
                                <div class="am-u-sm-12">
                                    <a class="am-btn am-btn-danger" href="/qwtgla/delete_floor_all"><i class="fa fa-edit"></i>一键删除所有楼层</a>
                                </div>
                            </div>
                        <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-3 am-form-label">设置中奖楼层
                            </label>
                            <input id="floortype" type="hidden" name="floor[type]" value="1">
                            <div class="am-u-sm-9">
                                <div class="actions" style="float:left">
                                    <ul class="actions-btn">
                                        <li id="switch-handle" class="switch-type green green-on">手动设置
                                        </li>
                                        <li id="switch-auto" class="switch-type green">自动设置
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="handle" class="am-from-group typebox" style="overflow:visible;">
                            <div class="am-form-group">                                <label for="user-name" class="am-u-sm-3 am-form-label">设置中奖楼层
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="floor[floor]" placeholder="输入中奖楼层">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">设置对应中将奖品</label>
                                <div class="am-u-sm-9">
                                    <select name="floor[iid]" data-am-selected="{searchBox: 1}">
                            <?php foreach ($item as $v):?>
                                <option value="<?=$v->id?>"><?=$v->name?></option>
                            <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="auto" class="am-form-group typebox" style="display:none;overflow:visible;">
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">设置中奖楼层尾数
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="floor[tail]" placeholder="输入中奖楼层尾数">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-3 am-form-label">设置中奖楼层数量
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="number" class="form-control" name="floor[num]" placeholder="输入中奖楼层数量">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-phone" class="am-u-sm-3 am-form-label">设置对应中将奖品</label>
                                <div class="am-u-sm-9">
                                    <select name="floor[iid2]" data-am-selected="{searchBox: 1}">
                            <?php foreach ($item as $v):?>
                                <option value="<?=$v->id?>"><?=$v->name?></option>
                            <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                          </div>
                        <div class="am-u-sm-12" style="padding:0">
                                <hr>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                                </div>
                            </div>
                        </div>
                        </form>
                        </div>
                <div class="tpl-alert"></div>
            </div>
            </div>
            </div>










        </div>

<script type="text/javascript">
    $('.switch-type').click(function(){
      $('.switch-type').removeClass('green-on');
      $(this).addClass('green-on');
    })
    $('#switch-handle').click(function(){
      $('#handle').show();
      $('#auto').hide();
      $('#floortype').val(1);
    })
    $('#switch-auto').click(function(){
      $('#handle').hide();
      $('#auto').show();
      $('#floortype').val(2);
    })
</script>
