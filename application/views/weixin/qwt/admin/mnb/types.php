
<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                问题分类
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">蒙牛数据开发</a></li>
                <li class="am-active">问题分类</li>
            </ol>

            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($type)?> 个问题分类
                    </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtmnba/types/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新的问题分类</a>
                    </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <div class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-id">排序</th>
                                            <th class="table-id">分类名称</th>
                                            <th class="table-id">可查看该分类的代理等级</th>
                                            <th class="table-id">该问题分类下的问题数量</th>
                                            <th class="table-id">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($type as $k => $v):?>
                                        <tr>
                                            <td><?=$k+1?></td>
                                            <td><?=$v->name?></td>
                                            <td><?=$auth[$k]?></td>
                                            <?php
                                            $num = ORM::factory('qwt_mnbfaq')->where('tid','=',$v->id)->count_all()
                                            ?>
                                            <td><a href="/qwtmnba/faqs?t=<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <?=$num?></a></td>
                                            <td>
                  <a href="/qwtmnba/types/edit/<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
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
