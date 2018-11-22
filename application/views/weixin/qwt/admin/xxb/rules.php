<?php
$a[3] = '全部';
$a[1] = '男性';
$a[2] = '女性';
$a[0] = '未知性别';
?>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送规则
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">消息宝</a></li>
                <li><a>发送规则</a></li>
                <li class="am-active">发送规则列表</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=count($list)?> 条规则
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtxxba/rules/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新规则</a>
                        </div>
                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th class="table-id">排序</th>
                                            <th class="table-title">发送的用户群体</th>
                                            <th class="table-type">发送的内容</th>
                                            <th class="table-set">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
            <?php foreach ($list as $k => $v):?>
                <?php
                $sex = ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','sex')->find()->value;
                $pro = ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','pro')->find()->value?ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','pro')->find()->value:'全部省';
                $city = ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','city')->find()->value?ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','city')->find()->value:'全部市';
                $dis = ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','dis')->find()->value?ORM::factory('qwt_xxbrule')->where('rid','=',$v->id)->where('keyword','=','dis')->find()->value:'全部区';
                ?>
                <tr>
                  <td><?=$k+1?></td>
                  <td><?=$pro.$city.$dis.$a[$sex]?>用户</td>
                  <td><?=$v->item->name?></td>
                  <td><a class="am-btn am-btn-default am-btn-xs am-text-secondary" href="/qwtxxba/rules/edit/<?=$v->id?>" style="background-color:#fff;"><span class="am-icon-pencil-square-o"></span> 修改</a></td>
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


