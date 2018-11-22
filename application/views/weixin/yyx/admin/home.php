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
    基础设置
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li id="cfg_yz_li"><a href="#cfg_yz" data-toggle="tab">绑定有赞</a></li>
          <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性化配置</a></li>
          <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
        </ul>
        <?php
        if (!$_POST || $_POST['yz']) $active = 'yz';
        if ($_POST['text']) $active = 'text';
        if (isset($_POST['password'])) $active = 'account';
        ?>
        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>
        <div class="tab-content">

          <div class="tab-pane" id="cfg_yz">
            <!-- form start -->
            <form role="form" method="post">
              <div class="box-body">

                <?php if($result['access_token']):?>
                  <div class="lab4">
                    <a href='/yyxa/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/yyxa/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                <?endif?>
                <?php if($_SESSION['yyxa']['admin'] >= 1):?>
                <?php foreach ($shops as $k => $v):?>
                  <div class="lab4">
                    <a href='/yyxa/oauth?type=add'><button type="button" class="btn btn-warning">点击给<?=$v->name?>重新授权</button></a>   &nbsp<?=date('Y-m-d',$v->expires_in)?>到期
                  </div>
                  <br>
                <?php endforeach?>
                <div class="lab4">
                    <a href='/yyxa/oauth?type=add'><button type="button" class="btn btn-warning">添加新店铺授权</button></a>
                </div>
                <?php if($result['tb_access_token']):?>
                  <!-- <div class="lab4">
                    <a href='/yyxa/tb_oauth'><button type="button" class="btn btn-primary">点击重新授权淘宝</button></a>
                  </div>
                  <br> -->
                  <?else:?>
                  <!-- <div class="lab4">
                    <a href='/yyxa/tb_oauth'><button type="button" class="btn btn-primary">点击一键授权淘宝</button></a>
                  </div> -->
                <?endif?>
                <?endif?>
              </div><!-- /.box-body -->
            </form>
          </div>


          <div class="tab-pane" id="cfg_text">
            <?php if ($result['ok3'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>个性化信息更新成功!</div>
            <?php endif?>
            <?php if ($result['err3']):?>
              <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err3']?></div>
            <?php endif?>
            <!-- form start -->
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body">

            <div class="row">
              <div class="col-lg-4 col-sm-4">
                <div class="form-group">
                  <label for="goal0">本月销售目标</label>
                  <input type="number" class="form-control" id="goal0" name="text[goal]" value="<?=intval($config['goal'])?>">
                </div>
              </div>
              </div>

              <div class="row">
              <div class="col-lg-4 col-sm-4">
                <div class="form-group">
                  <label for="goal">本月累计销售额增加(为零则不增加)</label>
                  <input type="number" class="form-control" id="goal" name="text[goal1]" value="<?=floatval($config['goal1'])?>">
                </div>
              </div>
              </div>

              <div class="row">
              <div class="col-lg-4 col-sm-4">
                <div class="form-group">
                  <label for="goal2">今日交易额增加(为零则不增加)</label>
                  <input type="number" class="form-control" id="goal2" name="text[goal2]" value="<?=intval($config['goal2'])?>">
                </div>
              </div>
              </div>

              <div class="row">
              <div class="col-lg-4 col-sm-4">
                <div class="form-group">
                  <label for="goal2">昨日交易额增加(为零则不增加)</label>
                  <input type="number" class="form-control" id="goal3" name="text[goal3]" value="<?=intval($config['goal3'])?>">
                </div>
              </div>
              </div>
              <div class="row">
              <div class="col-lg-4 col-sm-4">
                <div class="form-group">
                  <label for="goal2">累计交易额(为零则不增加)</label>
                  <input type="number" class="form-control" id="goal4" name="text[goal4]" value="<?=intval($config['goal4'])?>">
                </div>
              </div>
              </div>

                </div><!-- /.box-body -->

                <div class="box-footer">
                  <button type="submit" class="btn btn-success">更新个性化信息</button>
                </div>
              </form>
            </div>
          <style>
            .laball{
              margin-left: 3px;
              margin-bottom: 13px;
            }
            .laball1{
              margin-left: 3px;
              margin-bottom: 13px;
            }
            .lab3{
              width: 100%;
              /*margin-top:15px;*/
              display:inline-block;
              top:70px;
              margin-bottom:20px;
            }
            #lab4{
              margin-top:10px;
            }
            .add1,.reduce1{
              font-size:14px;
              cursor: pointer;
            }

          </style>
          <br>
          <div class="tab-pane" id="cfg_account">
            <?php if ($result['ok4'] > 0):
            $_SESSION['yyxa'] = null;
            ?>
            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>新密码已生效，请重新登录</div>
          <?php endif?>

          <?php if ($result['err4']):?>
            <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err4']?></div>
          <?php endif?>
          <form role="form" method="post">
            <div class="box-body">

              <div class="form-group">
                <label for="password">旧密码</label>
                <input type="password" class="form-control" id="password" placeholder="请输入旧密码" maxlength="16" name="password">
              </div>

              <div class="form-group">
                <label for="newpassword">新密码</label>
                <input type="password" class="form-control" id="newpassword" placeholder="请输入新密码" maxlength="16" name="newpassword">
              </div>

              <div class="form-group">
                <label for="newpassword2">重复新密码</label>
                <input type="password" class="form-control" id="newpassword2" placeholder="请再次输入新密码" maxlength="16" name="newpassword2">
              </div>

              <div class="box-footer">
                <input type="hidden" name="yz" value="1">
                <button type="submit" class="btn btn-success">修改登录密码</button>
              </div>
            </form>
          </div>

        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->

