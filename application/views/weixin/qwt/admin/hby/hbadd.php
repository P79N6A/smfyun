<style type="text/css">
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$title?>门店账号
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li>红包充值及投放管理</li>
                <li class="am-active"><?=$title?>门店账号</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <?=$title?>门店账号
                    </div>
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                <form method="post" class="am-form" enctype='multipart/form-data' onsubmit="return toValid()">
                    <div class="am-g tpl-amazeui-form">
                <div class="am-form-group">
                    <label for="name" class="am-u-sm-12 am-form-label">名称</label>
                        <div class="am-u-sm-12">
                        <input id="name" name="name" type="text" class="form-control" value="<?=$account->name?>">
                        </div>
                        </div>
                <div class="am-form-group">
                    <label for="account" class="am-u-sm-12 am-form-label">账号</label>
                        <div class="am-u-sm-12">
                        <input id="account" name="account" type="text" class="form-control" value="<?=$account->account?>">
                        </div>
                        </div>
                <div class="am-form-group">
                    <label for="password" class="am-u-sm-12 am-form-label">密码</label>
                        <div class="am-u-sm-12">
                        <input id="password" name="password" type="text" class="form-control" value="<?=$account->password?>">
                        </div>
                        </div>
                <div class="am-form-group">
                    <label for="menu" class="am-u-sm-12 am-form-label">品牌名称（不超过5个字）</label>
                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="logoname" name="cus[logoname]" value="<?=$rconfig["logoname"]?>">
                    </div>
                </div>
                <div class="am-form-group">
                    <label for="menu" class="am-u-sm-12 am-form-label">店铺链接</label>
                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="logoname" name="cus[shopurl]" value="<?=$rconfig["shopurl"]?>">
                    </div>
                </div>
                    <label for="doc-select-1" class="am-u-sm-12 am-form-label">选择营销规则</label>
                    <div class="am-u-sm-12">
                    <select id="doc-select-1" name='rule'>
                    <?php foreach($rules as $k=>$v):?>
                        <option value="<?=$v->id?>" <?=$account->rid==$v->id?"selected":''?>><?=$v->name?></option>
                    <?php endforeach?>
                    </select>
                    </div>
                </div>
                <div class="am_form_group" style="margin-top:1.5rem">
                            <div class="note note-info">
                                <p> 文案设置</p>
                            </div>
                            </div>
                    <div class="am-g tpl-amazeui-form">
                             <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">页面背景图</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($rconfig['bgpic']):
                  ?>
                  <a href="/qwthbya/images/rcfg/<?=$rconfig['bgpic']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwthbya/images/rcfg/<?=$rconfig['bgpic']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传页面背景图</button>
                                        <div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="bgpic" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 750*1280px，最大不超过 400k，</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">品牌logo</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($rconfig['logo']):
                  ?>
                  <a href="/qwthbya/images/rcfg/<?=$rconfig['logo']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwthbya/images/rcfg/<?=$rconfig['logo']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传品牌logo</button>
                                        <div id="file-pic2" style="display:inline-block;"></div>
                                            <input id="pic2" type="file" name="logo" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 200*200px，最大不超过 200K，</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">分享图标</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($rconfig['sharelogo']):
                  ?>
                  <a href="/qwthbya/images/rcfg/<?=$rconfig['sharelogo']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwthbya/images/rcfg/<?=$rconfig['sharelogo']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传分享图标</button>
                                        <div id="file-pic3" style="display:inline-block;"></div>
                                            <input id="pic3" type="file" name="sharelogo" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 200*200px，最大不超过 200k，</small>

                                    </div>
                                </div>
                                <!-- <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享页面描述</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="success" name="cus[sharedesc]" placeholder="" value="<?=htmlspecialchars($config["sharedesc"])?>">
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享到朋友圈的页面标题</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="success" name="cus[sharetitle]" placeholder="" value="<?=htmlspecialchars($rconfig["sharetitle"])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享链接（点击用户分享的朋友圈跳转的链接）</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="success" name="cus[shareurl]" placeholder="" value="<?=htmlspecialchars($rconfig["shareurl"])?>">
                                    </div>
                                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9  am-u-sm-push-3" style="margin-top:20px;">
                  <button class="am-btn am-btn-secondary" type="submit"><?=$title?></button>
                        <?php if ($title=='修改'): ?>
                  <a class="am-btn am-btn-danger" id="delete">取消此账号（不可恢复）</a>
                        <?php endif ?>
                        </div>
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
    <script type="text/javascript">
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
  $(function() {
    $('#pic2').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic2').html(fileNames);
    });
  });
  $(function() {
    $('#pic3').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic3').html(fileNames);
    });
  });
    <?php if($result['error']):?>
    $(document).ready(function(){
        alert("<?=$result['error']?>");
    })
    <?php endif?>
    function toValid(){
        var flag = 0;
        $(":text").each(function(){
        　　if($(this).val() == "") {
            flag = 1;
            };
        });
        if(flag==1){
            alert('请填写完整');
            return false;
        }else{
            return true;
        }
    }
                        <?php if ($title=='修改'): ?>
$('#delete').click(function(){
    var id = <?=$account->id?>;
    swal({
        title: "你确定吗？",
        text: "确认要删除此门店账号吗？删除后不可撤销",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        closeOnConfirm: false
    },
    function(){
        window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwthbya/delete/"+id;
    });
})
<?php endif?>

    </script>
