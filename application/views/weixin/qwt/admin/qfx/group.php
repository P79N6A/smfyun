

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                分销商分组管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">全员分销</a></li>
                <li><a>分销商设置</a></li>
                <li class="am-active">分销商分组管理</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($result['group'])?> 组
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtqfxa/group/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新分销商分组</a>
                        </div>

                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead><tr>
                  <th>组ID</th>
                  <th>分组名称</th>
                  <th>当前分组分销商人数</th>
                  <th>创建时间</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['group'] as $key=>$group):?>
                  <?php $sum = ORM::factory('qfx_qrcode')->where('bid','=',$bid)->where('group_id','=',$group->id)->count_all()?>
                <tr>
                  <td><?=$group->id?></td>
                  <td><?=$group->name?></td>
                  <td><a href="/qwtqfxa/qrcodes_m/?group=<?=$group->id?>"><?=$sum?></a></td>
                  <td><?=date('Y-m-d H:i:s',$group->lastupdate)?></td>
                  <td><a href="/qwtqfxa/group/edit/<?=$group->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
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


