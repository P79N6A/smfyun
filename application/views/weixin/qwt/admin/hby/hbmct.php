
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                生成记录
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li><a href="#">数据统计</a></li>
                <li class="am-active">红包码生成记录</li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            共<?=$result['countall']?>条记录
                        </div>

                        <div class="am-u-sm-12 am-u-md-3" style="float:right">
                            <div class="am-input-group am-input-group-sm">
                          <input type="text" name="s" class="am-form-field form-control input-sm pull-left" placeholder="红包码id或口令搜索" value="">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main">
                                    <thead>
          <tr>
            <th>红包码编号</th>
            <th>红包码串码</th>
            <th>来源门店</th>
            <th>创建时间</th>
            <th>是否使用</th>
            <th>使用明细</th>
          </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['orders'] as $key => $orders):?>
                <tr>
              <td><?=$orders->id?></td>
              <td><?=$orders->code?></td>
              <td><a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/hbmct?from_lid=<?=$orders->from_lid?>"><?=$orders->logins->name?></a></td>
              <td><?=date('Y-m-d H:i:s',$orders->createtime)?></td>
              <td><?=$orders->used>0?"已使用":'未使用'?></td>
              <td>
                <?php if($orders->used>0):?>
                  <a href="/qwthbya/qrcodes?code=<?=$orders->code?>">点击查看</a>
                <?php endif?>
              </td>
            </tr>
                <?php endforeach;?>
                                    </tbody>
                                </table>
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
            </form>










        </div>

    </div>
