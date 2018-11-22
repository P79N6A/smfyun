
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">商品管理</li>
            </ol>
            <div class="tpl-portlet-components" style="overflow:visible;">
            <form method="post">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        商品管理
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtkmia/items1?refresh=1" class="am-btn am-btn-default am-btn-success"><span class="am-icon-refresh"></span> 点此刷新商品</a>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="模糊搜索" value="">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                </form>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                    <tr>
                        <th>状态</th>
                        <th>商品名称</th>
                        <th>商品价格</th>
                        <th>添加要发送的卡密</th>
                        <th>已发送</th>
                        <th>当前卡密库存</th>
                        <th>操作</th>
                    </tr>
                                    </thead>
                                    <tbody>
                <?php
                 foreach ($result['items'] as $item):
                    $prize=ORM::factory('qwt_kmiprize')->where('id','=',$item->pid)->find();
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
                        <select name='cfg[pid]' data-am-selected="{searchBox: 1}">
                        <?php
                        foreach ($result['pri'] as $pri):
                        ?>
                        <?php if((ORM::factory('qwt_kmikm')->where('bid','=',$pri->bid)->where('startdate','=',$pri->value)->where('live','=',1)->where('flag','=',1)->count_all()>0)):?>
                        <option value='<?=$pri->id?>'><?=$pri->km_content?></option>
                        <?php endif;?>
                        <?php endforeach;?>
                        </select>
                        <?endif?>
                        </td>
                        <td><?=$item->send_num?></td>
                        <td><?=$item->pid&&$prize->type==8?ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$prize->value)->where('live','=',1)->where('flag','=',1)->count_all():''?></td>
                        <?php if($item->pid):?>
                            <input type="hidden" name='delete[id]' value="<?=$item->id?>" />
                            <td><button class="am-btn am-btn-default am-btn-xs am-text-secondary" type="submit">修改</button ></td>
                        <?else:?>
                            <td><button class="am-btn am-btn-default am-btn-xs am-text-secondary" type="submit">提交</button ></td>
                        <?endif?>
                    </tr>
                </form>
                <?php endforeach;?>
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
                <div class="tpl-alert"></div>
            </div>

        </div>

    </div><!-- 发送管理 -->
