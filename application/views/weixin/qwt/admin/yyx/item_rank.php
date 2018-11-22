
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                热销商品
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">热销商品排行</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=count($result['countall'])?> 件产品
                    </div>
                    </div>

                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按商品名称搜索">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        </form>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <form class="am-form" method="post">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                  <th>商品图片</th>
                  <th>商品名称</th>
                 <!--  <th>店铺名称</th> -->
                  <th><button class="btn btn-sm btn-warning" type="submit">点此修改下方产品优先级</button></th>
                  <th>商品价格</th>
                  <th>商品销量</th>
                </tr>
                                    </thead>
                                    <tbody>

                <?php
                foreach ($result['items'] as $v):
                  $sname=ORM::factory('qwt_yyxshop')->where('id','=',$v->sid)->find()->name;
                ?>

                <tr>
                  </td>
                  <td><img src="<?=$v->pic_url?>" width="32" height="32" title="<?=$v->num_iid?>"></td>
                  <td><?=$v->title?></td>
                  <!-- <td><?=$sname?></td> -->
                  <td><input type="number" name='rank[<?=$v->id?>]' value="<?=$v->lv?>" /></td>
                  <td><?=$v->price?></td>
                  <td><?=$v->sold_num?></td>
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
