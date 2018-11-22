
<style type="text/css">
  .shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    z-index: 2000;
  }
  label{
    text-align: left !important;
  }
  th,td{
    white-space: nowrap;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                代理列表
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">蒙牛数据开发</a></li>
    <li class="am-active"><a href="/qwtmnba/qrcodes">代理列表</a></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-3">
                    <div class="caption font-green bold">
                        共 <?=count($user)?> 个用户
                    </div>
                    </div>
                    <form method="get" id="rankform">
                                    <div class="am-u-sm-3">
                <select name='status' id="rank" data-am-selected="{searchBox: 1}">
                  <option value="all" <?=$result['status']=='all'?'selected="selected"':''?>>全部</option>
                  <option value="zero" <?=$result['status']=='zero'?'selected="selected"':''?>>待登录</option>
                  <option value="1"  <?=$result['status']==1?'selected="selected"':''?>>未完善信息</option>
                  <option value="2" <?=$result['status']==2?'selected="selected"':''?>>待审核</option>
                  <option value="3" <?=$result['status']==3?'selected="selected"':''?>>已通过</option>
                </select>
                                    </div>
                                    <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按姓名，昵称，手机号搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                    </form>

                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtmnba/qrcodes/add" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-plus"></span> 添加新代理</a>
                        </div>
                        <!-- <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div> -->


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                            <div class="am-form" style="overflow:scroll">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                                    <tr>
                  <th>代理等级</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>登录手机号</th>
                  <th>登录密码</th>
                  <th>授权码</th>
                  <th>姓名</th>
                  <th>微信号</th>
                  <th>上级姓名</th>
                  <th>上级授权码</th>
                  <th>状态</th>
                  <th>更新时间</th>
                  <th>修改代理</th>
                </tr>
                </thead>
                                    <tbody>
                                    <?php if ($user[0]->id):?>
                                      <?php foreach ($user as $k => $v):?>
                                        <tr>
                                          <td><?=$v->lv->lv?></td>
                                          <td><?php if ($v->headimgurl):?><img style="width:60px;" src="<?=$v->headimgurl?>"><?php endif?></td>
                                          <td><?=$v->nickname?></td>
                                          <td><?=$v->tel?></td>
                                          <td><?=$v->password?></td>
                                          <td><?=$v->pcode?></td>
                                          <td><?=$v->name?></td>
                                          <td><?=$v->wx_username?></td>
                                          <td><?=$v->fname?$v->fname:'未填写'?></td>
                                          <td><?=$v->fpcode?$v->fpcode:'未填写'?></td>
                                          <td><?php switch ($v->status) {
                                            case 0:
                                              echo "待登录";
                                              break;
                                            case 1:
                                              echo "未完善信息";
                                              break;
                                            case 2:
                                              echo "待审核";
                                              break;
                                            case 3:
                                              echo "已通过";
                                              break;

                                            default:
                                              echo "";
                                              break;
                                          }?></td>
                                          <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                                          <td>
                  <a href="/qwtmnba/qrcodes/edit/<?=$v->id?>" style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' >
                  <span>修改</span></a>
                </td>
                                        </tr>
                                      <?php endforeach?>
                                    <?php endif?>
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
<script type="text/javascript">
$('#rank').change(function(){
  $('#rankform').submit();
})
$('.rankchange').change(function(){
  $(this).parent().submit();
})
</script>
