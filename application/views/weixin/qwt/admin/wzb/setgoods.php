
<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
?>

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
  .nickname{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                有赞商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
    <li><a>直播商品管理</a></li>
    <li class="am-active">有赞商品管理</li>
            </ol>
<form method="get" name="qrcodesform">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品
                    </div>
                    </div>
                           <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtwzba/setgoods1?refresh=1" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-refresh"></span> 刷新商品列表</a>
                        </div>
                            <form class="am-form" method="get">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field" placeholder="搜索">
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
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>缩略图</th>
                  <th>优先级</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th>库存</th>
                  <th>是否上架</th>
                  <th>操作</th>
                </tr>
                </thead>
                                    <tbody>
                <?php foreach ($result['goods'] as $k => $v):?>
                <tr>
                  <td><img src="<?=$v->pic?>" width="32" height="32"></td>
                  <td><?=$v->status?$v->priority:0?></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>

                  <td><?=$v->price?></td>
                  <td><?=$v->num?></td>
                  <td><?=$v->status?'<span class="label label-success">是</span>':'<span class="label label-danger">否</span>'?></td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' data-num_id="<?=$v->goodid?>" data-price="<?=$v->price?>" data-name="<?=$v->title?>" data-priority="<?=$v->priority?>" data-status="<?=$v->status?>" data-pic="<?=$v->pic?>" data-url="<?=$v->url?>" data-price="<?=$v->price?>">
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?=$goodid?>'>
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
</form>









        </div>
        </div>
 <div class="shadow" style="display:none">
    <div class="tpl-page-container tpl-page-header-fixed" style="position:fixed;left:20%;width:60%;margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                  <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold nickname">
                      是否上架直播
                    </div>
                  </div>
                </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" name="qrcodesform">

                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">直播商品展示优先级 <span class="tpl-form-line-small-title">（数字越大越靠前）</span></label>
                            <div class="am-u-sm-12">
            <input class="form-control" id="fscore" name="form[priority]" max="999" style="width:150px" type="number">
                            </div>
                          </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">是否上架： </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green">是</li>
                                    <li id="switch-4" class="switch-type red">否</li>
            <input id="status" class='edithidden' name="form[status]" value='' type="hidden">
                                </ul>
                            </div>
                            </div>
                </div>
            <input id="iid" class='edithidden' name="form[num_iid]" value='' type="hidden">
            <input id="title" class='edithidden' name="form[title]" value='' type="hidden">
            <input id="url" class='edithidden' name="form[url]" value='' type="hidden">
            <input id="price" class='edithidden' name="form[price]" value='' type="hidden">
            <input id="pic" class='edithidden' name="form[pic]" value='' type="hidden">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="button" class="close am-btn am-btn-default pull-left">取消</button>
        <button type="submit" class="am-btn am-btn-primary">修改用户</button>
                            </div>
                          </div>
                          </form>

                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
                          </div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
                    $('.edit').click(function(){
  var id = $(this).data('num_id');
  var name = $(this).data('name');
  var price=$(this).data('price');
  var priority=$(this).data('priority');
  var status=$(this).data('status');
  var url=$(this).data('url');
  var price=$(this).data('price');
  var pic=$(this).data('pic');
  var information=name+"  (价格:"+price+"元)";
  $('#iid').val(id);
  $('#title').val(name);
  $('#url').val(url);
  $('#price').val(price);
  $('#pic').val(pic);
  $('#fscore').val(priority);
  $('#status').val(status);
  if (status==1) {$('#switch-1').addClass('green-on');}else{$('#switch-4').addClass('red-on')};
  $('.nickname').text(information);
  $('.shadow').fadeIn();


                    })

                    $('.close').click(function(){
                      $('.shadow').fadeOut();
                    });

                    $('#switch-1').click(function(){
                      $('#switch-4').removeClass('red-on');
                      $(this).addClass('green-on');
                      $('#status').val(1);
                    });
                    $('#switch-4').click(function(){
                      $('#switch-1').removeClass('green-on');
                      $(this).addClass('red-on');
                      $('#status').val(0);
                    });
</script>

