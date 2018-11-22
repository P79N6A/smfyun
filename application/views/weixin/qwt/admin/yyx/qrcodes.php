
<?php
function convert1($a='')
{
  switch ($a) {
    case 'm':
      echo "男";
      break;
    case 'w':
      echo "女";
      break;
    default:
      echo "未知";
      break;
  }
}
$title = '概览';
?>


    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                参与用户
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li class="am-active">用户明细</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>

                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索">
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
                            <form class="am-form">
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                                        <tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>有赞积分</th>
                  <th>订单数量</th>
                  <th>成交金额</th>
                  <th>性别</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['qrcodes'] as $v):

                ?>

                <tr>
                  </td>
                  <td>
                    <?php if($v->avatar):?>
                      <img src="<?=$v->avatar?>" width="32" height="32" title="<?=$v->openid?>">
                    <?php else:?>
                      <?=$v->openid?>
                    <?php endif?>
                  </td>
                  <td><?=$v->nick?></td>
                  <td><?=$v->points?></td>
                  <td><?=$v->traded_num?></td>
                  <td><?=$v->traded_money?></td>
                  <td><?=convert1($v->sex)?></td>
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




