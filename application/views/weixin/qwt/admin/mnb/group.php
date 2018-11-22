
<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                代理等级
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">蒙牛数据开发</a></li>
                <li class="am-active">代理等级</li>
            </ol>

            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($lv)?> 个代理等级
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtmnba/groups/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新代理等级</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                                    <?php if ($result['err']):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p><span class="label label-danger">注意:</span> <?=$result['err']?> </p>
                </div>
            </div>
          <?php endif?>
                        <div class="am-u-sm-12">
                            <div class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-id">排序</th>
                                            <th class="table-id">等级名称</th>
                                            <th class="table-id">该代理等级下的人数</th>
                                            <th class="table-id">创建时间</th>
                                            <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($lv as $k => $v):?>
                                        <tr>
                                            <td><?=$k+1?></td>
                                            <td><?=$v->lv?></td>
                  <?php
                  $num = ORM::factory('qwt_mnbqrcode')->where('lid','=',$v->id)->count_all();
                  ?>
                                            <td>
                  <a href="/qwtmnba/qrcodes?t=<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <?=$num?></a></td>
                  <td><?=date('Y-m-d H:i:s',$v->createtime)?></td>
                                            <td style="white-space:nowarp;">
                  <a href="/qwtmnba/groups/edit/<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <span>修改</span></a>
                  <form method="post">
<button type="submit" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >删除</button>
    <input type="hidden" value="<?=$v->id?>" name="delete">
                </form>
                  </td>
                                        </tr>
                                    <?php endforeach?>
                                    </tbody>
                                </table>
                            </div>
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
