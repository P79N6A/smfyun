<div class="page-heading">
            <h3>
                微信一键授权
            </h3>
        </div>
        <!-- page heading end-->

        <!--body wrapper start-->
        <div class="wrapper">
        <div class="row">
        <div class="col-sm-12">
        <section class="panel">
            <div class="panel-body">
                <?php if($user->refresh_token):?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx4d981fffa8e917e7&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/qwta/oauth'><button type="button" class="btn btn-success">点击重新授权</button></a>
                <?php else:?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wx4d981fffa8e917e7&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/qwta/oauth'><button type="button" class="btn btn-warning">点击一键授权</button></a>
                <?php endif?>
            </div>
        </section>
