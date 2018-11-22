
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                代理应用清单
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">后台管理</a></li>
                <li class="am-active">代理应用清单</li>
            </ol>
            <div class="tpl-portlet-components">
                            <form class="am-form">
                <div class="portlet-title">
                    <div class="am-u-sm-12 am-u-md-4">
                    <div class="caption font-green bold">
                        共<? if($result['countall1']) echo $result['countall1'];else echo 0;?> 条代理清单
                    </div>
                    </div>
                    <div class="am-u-sm-12 am-u-md-4">
                    <div class="caption font-green">
                        专属邀请码：<?=$code?>
                    </div>
                    </div>
              <form method="get" name="qrcodesform">
                        <div class="am-u-sm-12 am-u-md-4">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按名称搜索" value="<?=htmlspecialchars($result['s'])?>">
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
                                <table class="am-table am-table-bordered am-table-radius am-table-striped am-table-hover table-main" id="editable-sample">
                                    <thead>
                <tr>
                  <th>应用名称</th>
                  <th>应用规格</th>
                  <th>原价(元)</th>
                  <th>拿货价(元)</th>
                  <th>销量</th>
                </tr>
                  </thead>
                  <tbody>
                <?php foreach ($result['goods'] as $a):
                    $dlskus=ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1)->where('iid','=',$a->id)->find_all();
                    $dlskunum=ORM::factory('qwt_dlsku')->where('bid','=',$bid)->where('state','=',1)->where('iid','=',$a->id)->count_all();
                    foreach ($dlskus as $k => $v):
                    $num=ORM::factory('qwt_score')->where('sid','=',$v->id)->count_all();
                ?>
                <tr>
                <?php if($k==0):?>
                <td rowspan="<?=$dlskunum?>" style="border-top:2px solid #e5e5e5;"><?=$v->sku->item->name?></td>
                <?php endif;?>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->sku->name?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->old_price?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->price?></td>
                   <td style="border-top:2px solid #e5e5e5;"><a href="/qwtwdla/ddorder?sid=<?=$v->id?>"><?=$num?></td>
                </tr>
                <?php endforeach;
                endforeach;
                ?>
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
                </form>
                <div class="tpl-alert"></div>
            </div>
        </div>
    </div>
