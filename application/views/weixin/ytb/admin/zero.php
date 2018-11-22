<style>
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>
<section class="content-header">
  <h1>
    积分清零
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">可选功能</li>
  </ol>
</section>

<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
        <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['zero']) $active = 'zero';

        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">
        <!-- 2015.12.16增加积分清零部分 -->
            <div class="tab-pane" id="cfg_zero">
              <span style="font-size:20px;">您确定将所有用户的积分清零？<span>
                <p class="help-block" style="font-size:14px;">仅清空用户积分，用户关系保留，请商户谨慎处理。</p>
                <p class="help-block" style="font-size:14px;">注意：积分清零后，兑换中心-总积分归零，粉丝数保留；我的积分之前的积分记录删除。</p>
                <div class="radio">
                  <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>清空积分</span></a>
                  <div class="modal modal-danger" id="deleteModel">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <h4 class="modal-title">确认要清空用户积分吗？该操作不可恢复！</h4>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
                          <a href="<?php echo URL::site("ytba/empty")?>?DELETE=1" class="btn btn-outline">确认清空</a>
                        </div>
                      </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                  </div><!-- /.modal -->
                </div>
              </div>
              <!-- 2015.12.16增加积分清零部分 -->








        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->
