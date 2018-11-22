
<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';
$title = '概览';
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
  table{
    table-layout: fixed;
  }
  tbody>tr>td{
    width: 120px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>


    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                会员管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">特别特</a></li>
                <li><a href="#">会员管理</a></li>
                <li class="am-active"><?=$title?></li>
            </ol>
            <form method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        <?=$title?>：共 <?=$result['countall']?> 个用户
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="按昵称，手机号，姓名搜索" value="<?=htmlspecialchars($result['s'])?>">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="am-btn am-btn-default am-btn-success" style="margin-right:10px;margin-bottom:10px;height:40px"><span class="am-icon-save"></span> 导出已审核用户信息</a>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                        <table class="am-table am-table-striped am-table-hover table-main">
                            <thead>
              <tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>门头照片</th> -->
                  <!-- <th>身份证或本人照片</th> -->
                  <!-- <th>地址</th> -->
                  <!-- <th>行业类型</th> -->
                  <th>姓名</th>
                  <th>手机号</th>
                  <th>状态</th>
                  <th>操作</th>
                </tr>
                  </thead>
                  <tbody>
               <?php
                foreach ($result['qrcodes'] as $v):
                ?>
                <tr>
                    <?php if($v->headimgurl):?>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                <?php else:?>
                  <td></td>
                <?php endif;?>
                  <td><?=$v->nickname?></td>
                  <!-- <?php if($v->shop_pic):?>
                  <td><a href="/qwttbta/image1s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" target="_blank"><img src="/qwttbta/image1s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" width="32" height="32" title="<?=$v->openid?>"></a></td>
                  <?php else:?>
                  <td></td>
                <?php endif;?>
                <?php if($v->ic_pic):?>
                  <td><a href="/qwttbta/image2s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" target="_blank"><img src="/qwttbta/image2s/qrcode/<?=$v->id?>.v<?=time()?>.jpg" width="32" height="32" title="<?=$v->openid?>"></a></td>
                   <?php else:?>
                  <td></td>
                <?php endif;?>
                  <td><?=$v->address?></td> -->
                  <!-- <td><?=$v->type?></td> -->
                  <td><?=$v->name?></td>
                  <td><?=$v->telphone?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->flag==0){
                      echo '<span class="label label-danger">未审核</span>';
                    }
                    elseif($v->flag==1){
                      echo '<span class="label label-success">已审核</span>';
                    }
                    ?>
                  </td>
                  <td nowrap="">
                    <a style="background-color:#fff;" class='am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwttbta/qrcodes_edit/<?=$v->id?>">
                      <span>详情</span> <i class="am-icon-edit"></i>
                    </a>
                    <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$v->id?>">
                      <span>删除</span> <i class="am-icon-times"></i>
                    </a>
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
</div>
</div>
</div>
<script type="text/javascript">

    $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwttbta/qrcodes_delete/qrcodes_m/"+id;
    })
  })

</script>


