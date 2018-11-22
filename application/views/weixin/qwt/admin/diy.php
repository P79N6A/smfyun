<?php
$init_menu = 'block';
$add_menu = 'none';
foreach ($menu_0 as $k => $v) {
    if($k==0){
        if($v->iid!=''){
            $init_menu = 'none';
            $add_menu = 'block';
        }else{
            $init_menu = 'block';
            $add_menu = 'none';
        }
    }
}
foreach ($menu_0 as $k => $v) {
    $menu0[$k]['text'] = $v->text;
    $menu0[$k]['iid'] = $v->iid;
    $menu0[$k]['keyword'] = $v->keyword;
    $menu0[$k]['type'] = $v->type;
}
foreach ($menu_1 as $k => $v) {
    $menu1[$k]['text'] = $v->text;
    $menu1[$k]['iid'] = $v->iid;
    $menu1[$k]['keyword'] = $v->keyword;
    $menu1[$k]['type'] = $v->type;
}
foreach ($menu_2 as $k => $v) {
    $menu2[$k]['text'] = $v->text;
    $menu2[$k]['iid'] = $v->iid;
    $menu2[$k]['keyword'] = $v->keyword;
    $menu2[$k]['type'] = $v->type;
}

?>
<style type="text/css">
    body{
        overflow: unset !important;
    }
    .app-preview{
        width: 320px;
        display: inline-block;
    }
    .app-title{
    color: #fff;
    position: absolute;
    top: 30px;
    left: 136px;
    font-weight: bold;
    font-size: 16px;
    }
    .app-body{
    width: 320px;
    height: 400px;
    border-left: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
}
.app-tab-bar{
    height: 50px;
    width: 320px;
    border: 1px solid #e5e5e5;
    background-color: #fafafa;
}
.nav-icon{
    display: inline-block;
    height: 100%;
    width: 40px;
    text-align: center;
    vertical-align: middle;
    border-right: 1px solid #e5e5e5;
}
.add_menu {
    display: <?=$add_menu?>;
}
.init_menu {
    display: <?=$init_menu?>;
}

.menu {
    border: 2px solid #32c5d2;
    border-radius: 10px;

    display: none;
    position: relative;
    /*border: 1px solid #e5e5e5;*/
    padding: 14px;
    background: #fff;
    zoom: 1;
    margin-bottom: 10px;
}

.menu2 {
    display: none;
}

.menu3 {
    display: none;
}

.lv_menu {
    display: none;
}

.model {
    width: 490px;
    display: none;
    position: fixed;
    background: #fff;
    border-radius: 2px;
    padding: 10px 20px;
    z-index: 2;
    /*position: absolute;*/
    z-index: 1010;
    border-radius: 2px;
    -webkit-box-shadow: 0px 1px 6px rgba(0,0,0,0.2);
    box-shadow: 0px 1px 6px rgba(0,0,0,0.2);
}
.editbox{

    display: inline-block;
    /*position: absolute;*/
    left: 350px;
    min-width: 400px;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    background-color: #f8f8f8;
    padding: 20px;
}
.addnew,.menu1_add_btn,.menu2_add_btn,.menu3_add_btn,.menu_add_btn{

    height: 45px;
    line-height: 45px;
    padding: 0 13px;
     margin: 10px 0 0 0;
    border: 1px dashed #ccc;
    background: #fff;
    font-size: 13px;
    text-align: center;
    cursor: pointer;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    width: 100%;
}
.yijicaidan{
    font-size: 14px;
    font-weight: bold;
}
.menu1_lv0,.menu2_lv0,.menu3_lv0,.lv_menu{
    position: relative;
    height: 45px;
    line-height: 25px;
    background-color: #F8F8F8;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 10px 13px;
    margin: 10px 0;
    zoom: 1;
}
.shugang{
    color: #999;
    padding: 0 10px;
    font-size: 14px;
}
.content,.biaoti{
    float: left;
    width: 100px;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    font-size: 14px;
}
.s_edit,.edit{
    padding: 5px 10px;
    color: white;
    background: #ff7600;
    border-radius: 5px;

    width: 240px;
    font-size: 14px;
}
.erjicaidan{
    font-size: 12px;
    padding-left: 14px;
    font-weight: bold;
}
.del{
    position: absolute;
    z-index: 2;
    top: -9px;
    right: -9px;
    width: 20px;
    height: 20px;
    font-size: 16px;
    line-height: 18px;
    color: #fff;
    text-align: center;
    cursor: pointer;
    background: rgb(245, 33, 33);
    border-radius: 10px;
    display: none;
}
.del:hover{
    background-color: #000;
}
.lv_menu:hover .del{
    display: block;
}
.menu1_lv0:hover .del{
    display: block;
}
.menu2_lv0:hover .del{
    display: block;
}
.menu3_lv0:hover .del{
    display: block;
}

.lv_menu{
    margin-left: 14px;
}
.reset,.submit{
    color: #fff;
    background: #38f;
    border-color: #38f;
    display: inline-block;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 12px;
    color: #333;
    background: #fff;
    border: 1px solid #bbb;
    text-align: center;
    vertical-align: middle;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    cursor: pointer;
    -webkit-transition: background-color .3s;
    -moz-transition: background-color .3s;
    transition: background-color .3s;
    margin-top: 10px;
    color: #fff;
    background: #38f;
    border-color: #38f;
    font-size: 13px;
    font-weight: bold;
    border-radius: 3px;
}
.submit{
    background: #38f;
    border-color: #38f;
    float: right;
}
.reset{
    background: #ec0000;
    border-color: #ec0000;
}
.menu_name,.item,.menu_content{

    display: inline-block;
    height: 30px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555;
    vertical-align: middle;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    background-color: #fff;
    border: 1px solid #ccc;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border linear .2s,box-shadow linear .2s;
    -moz-transition: border linear .2s,box-shadow linear .2s;
    -o-transition: border linear .2s,box-shadow linear .2s;
    transition: border linear .2s,box-shadow linear .2s;
    width: 220px;
    font-size: 12px;
}
.confirm,.cancel{

    display: inline-block;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 12px;
    color: #333;
    background: #fff;
    border: 1px solid #bbb;
    text-align: center;
    vertical-align: middle;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    cursor: pointer;
    -webkit-transition: background-color .3s;
    -moz-transition: background-color .3s;
    transition: background-color .3s;
}
.confirm{

    color: #fff;
    background: #38f;
    border-color: #38f;
}
.cancel{
        color: #333;
    text-decoration: none;
}
.arrow{
    left: -3px;
    -webkit-transform: rotate(45deg) translateX(-50%) translateY(-50%);
    -moz-transform: rotate(45deg) translateX(-50%) translateY(-50%);
    -ms-transform: rotate(45deg) translateX(-50%) translateY(-50%);
    transform: rotate(45deg) translateX(-50%) translateY(-50%);
    -webkit-transform-origin: 0 0;
    -moz-transform-origin: 0 0;
    -ms-transform-origin: 0 0;
    transform-origin: 0 0;
    top: 50%;
    -webkit-transform: rotate(45deg) translateY(-50%);
    -moz-transform: rotate(45deg) translateY(-50%);
    -ms-transform: rotate(45deg) translateY(-50%);
    transform: rotate(45deg) translateY(-50%);
    -webkit-transform-origin: 50% 0;
    -moz-transform-origin: 50% 0;
    -ms-transform-origin: 50% 0;
    transform-origin: 50% 0;
    position: absolute;
    width: 6px;
    height: 6px;
    background: #fff;
    -webkit-box-shadow: 0 1px 4px rgba(0,0,0,0.4);
    box-shadow: 0 1px 4px rgba(0,0,0,0.4);
    z-index: 1;
}
.white{
    position: absolute;
    background-color: #fff;
    opacity: 1;
    width: 7px;
    height: 20px;
    left: 0;
    top: 50%;
    z-index: 2;
    margin-top: -10px;
}
.hint{
    font-size: 12px;
    color: #ff99aa;
    height: 30px;
}
</style>

    <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                自定义菜单
            </div>
            <ol class="am-breadcrumb">
                <li class="am-active">自定义菜单</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>自定义菜单</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="overflow:-webkit-paged-x;">
                <div class="tpl-block ">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12">
                            <form method="post" class="am-form am-form-horizontal">
                                    <?php if ($result['ok2'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 菜单配置保存成功!
              <?php if($result['menu']==1):?>
                菜单已生效!
              <?php endif?>
              <?php if($result['menu']==0):?>
                菜单未生效,请按照顺序填写菜单！
              <?php endif?></p>
                </div>
            </div>
          <?php endif?>
                        <!-- <div class="app-preview">
                            <div class="app-header">
                                <img src="/qwt/images/titlebar.png">
                                <span class="app-title">[页面标题]</span>
                            </div>
                            <div class="app-body"></div>
                            <div class="app-tab-bar">
                                <div class="nav-icon">
                                    <img src="/qwt/images/ico_home_20160816.png" style="margin-top:10px;">
                                </div>
                            </div>
                        </div> -->
<div class="editbox">
    <div class="init_menu">
        <button type="button" class="addnew">菜单未设置，请添加一级菜单！</button>
    </div>
    <div class="add_menu" >
        <div class='menu menu1' style="<?=$menu0[0]['iid']?'display:block':'display:none';?>">
            <div class="yijicaidan">一级菜单</div>
            <div class="menu1_lv0"><span class="biaoti" data-iid='<?=$menu0[0]['iid']?>' data-type='<?=$menu0[0]['type']?>' data-keyword='<?=$menu0[0]['keyword']?>' data-do='<?=$menu0[0]['iid']?'yes':'no'?>'><?=$menu0[0]['text']?$menu0[0]['text']:'标题'?></span><span class="shugang">|</span><span class='s_edit' data-menu='1' data-lv='0' >编辑</span><span class='del' data-menu='1'>×</span></div>
            <div class="erjicaidan">二级菜单</div>
            <div style="<?=$menu0[1]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu1 menu1_lv1"><span data-iid='<?=$menu0[1]['iid']?>' data-type='<?=$menu0[1]['type']?>' data-keyword='<?=$menu0[1]['keyword']?>' data-do='<?=$menu0[1]['iid']?'yes':'no'?>' class='content'><?=$menu0[1]['text']?$menu0[1]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='1' data-lv='1' >编辑</span><span class='del' data-menu='1' data-lv='1'>×</span></div>
            <div style="<?=$menu0[2]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu1 menu1_lv2"><span data-iid='<?=$menu0[2]['iid']?>' data-type='<?=$menu0[2]['type']?>' data-keyword='<?=$menu0[2]['keyword']?>' data-do='<?=$menu0[2]['iid']?'yes':'no'?>' class='content'><?=$menu0[2]['text']?$menu0[2]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='1' data-lv='2' >编辑</span><span class='del' data-menu='1' data-lv='2'>×</span></div>
            <div style="<?=$menu0[3]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu1 menu1_lv3"><span data-iid='<?=$menu0[3]['iid']?>' data-type='<?=$menu0[3]['type']?>' data-keyword='<?=$menu0[3]['keyword']?>' data-do='<?=$menu0[3]['iid']?'yes':'no'?>' class='content'><?=$menu0[3]['text']?$menu0[3]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='1' data-lv='3' >编辑</span><span class='del' data-menu='1' data-lv='3'>×</span></div>
            <div style="<?=$menu0[4]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu1 menu1_lv4"><span data-iid='<?=$menu0[4]['iid']?>' data-type='<?=$menu0[4]['type']?>' data-keyword='<?=$menu0[4]['keyword']?>' data-do='<?=$menu0[4]['iid']?'yes':'no'?>' class='content'><?=$menu0[4]['text']?$menu0[4]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='1' data-lv='4' >编辑</span><span class='del' data-menu='1' data-lv='4'>×</span></div>
            <div style="<?=$menu0[5]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu1 menu1_lv5"><span data-iid='<?=$menu0[5]['iid']?>' data-type='<?=$menu0[5]['type']?>' data-keyword='<?=$menu0[5]['keyword']?>' data-do='<?=$menu0[5]['iid']?'yes':'no'?>' class='content'><?=$menu0[5]['text']?$menu0[5]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='1' data-lv='5' >编辑</span><span class='del' data-menu='1' data-lv='5'>×</span></div>




                <!--
            <div <?=$menu0[2]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu1 menu1_lv2"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span>
                <span class='edit' data-menu='1' data-lv='2' >编辑</span><span class='del' data-menu='1' data-lv='2'>×</span></div>
            <div <?=$menu0[3]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu1 menu1_lv3"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span>
                <span class='edit' data-menu='1' data-lv='3' >编辑</span><span class='del' data-menu='1' data-lv='3'>×</span></div>
            <div <?=$menu0[4]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu1 menu1_lv4"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span>
                <span class='edit' data-menu='1' data-lv='4' >编辑</span><span class='del' data-menu='1' data-lv='4'>×</span></div>
            <div <?=$menu0[5]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu1 menu1_lv5"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span>
                <span class='edit' data-menu='1' data-lv='5' >编辑</span><span class='del' data-menu='1' data-lv='5'>×</span></div> -->
            <button type="button" class="menu1_add_btn">添加二级菜单</button>
        </div>
        <div class='menu menu2' style="<?=$menu1[0]['iid']?'display:block':'display:none';?>">
            <div class="yijicaidan">一级菜单</div>
            <div class="menu2_lv0"><span class="biaoti" data-iid='<?=$menu1[0]['iid']?>' data-type='<?=$menu1[0]['type']?>' data-keyword='<?=$menu1[0]['keyword']?>' data-do='<?=$menu1[0]['iid']?'yes':'no'?>'><?=$menu1[0]['text']?$menu1[0]['text']:'标题'?></span><span class="shugang">|</span><span class='s_edit' data-menu='2' data-lv='0' >编辑</span><span class='del' data-menu='2'>×</span></div>
            <div class="erjicaidan">二级菜单</div>
            <div style="<?=$menu1[1]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu2 menu2_lv1"><span data-iid='<?=$menu1[1]['iid']?>' data-type='<?=$menu1[1]['type']?>' data-keyword='<?=$menu1[1]['keyword']?>' data-do='<?=$menu1[1]['iid']?'yes':'no'?>' class='content'><?=$menu1[1]['text']?$menu1[1]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='1' >编辑</span><span class='del' data-menu='2' data-lv='1'>×</span></div>
            <div style="<?=$menu1[2]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu2 menu2_lv2"><span data-iid='<?=$menu1[2]['iid']?>' data-type='<?=$menu1[2]['type']?>' data-keyword='<?=$menu1[2]['keyword']?>' data-do='<?=$menu1[2]['iid']?'yes':'no'?>' class='content'><?=$menu1[2]['text']?$menu1[2]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='2' >编辑</span><span class='del' data-menu='2' data-lv='2'>×</span></div>
            <div style="<?=$menu1[3]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu2 menu2_lv3"><span data-iid='<?=$menu1[3]['iid']?>' data-type='<?=$menu1[3]['type']?>' data-keyword='<?=$menu1[3]['keyword']?>' data-do='<?=$menu1[3]['iid']?'yes':'no'?>' class='content'><?=$menu1[3]['text']?$menu1[3]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='3' >编辑</span><span class='del' data-menu='2' data-lv='3'>×</span></div>
            <div style="<?=$menu1[4]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu2 menu2_lv4"><span data-iid='<?=$menu1[4]['iid']?>' data-type='<?=$menu1[4]['type']?>' data-keyword='<?=$menu1[4]['keyword']?>' data-do='<?=$menu1[4]['iid']?'yes':'no'?>' class='content'><?=$menu1[4]['text']?$menu1[4]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='4' >编辑</span><span class='del' data-menu='2' data-lv='4'>×</span></div>
            <div style="<?=$menu1[5]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu2 menu2_lv5"><span data-iid='<?=$menu1[5]['iid']?>' data-type='<?=$menu1[5]['type']?>' data-keyword='<?=$menu1[5]['keyword']?>' data-do='<?=$menu1[5]['iid']?'yes':'no'?>' class='content'><?=$menu1[5]['text']?$menu1[5]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='5' >编辑</span><span class='del' data-menu='2' data-lv='5'>×</span></div>





            <!-- <div <?=$menu1[1]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu2 menu2_lv1"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='1' >编辑</span><span class='del' data-menu='2' data-lv='1'>×</span></div>
            <div <?=$menu1[2]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu2 menu2_lv2"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='2' >编辑</span><span class='del' data-menu='2' data-lv='2'>×</span></div>
            <div <?=$menu1[3]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu2 menu2_lv3"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='3' >编辑</span><span class='del' data-menu='2' data-lv='3'>×</span></div>
            <div <?=$menu1[4]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu2 menu2_lv4"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='4' >编辑</span><span class='del' data-menu='2' data-lv='4'>×</span></div>
            <div <?=$menu1[5]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu2 menu2_lv5"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='2' data-lv='5' >编辑</span><span class='del' data-menu='2' data-lv='5'>×</span></div> -->
            <button type="button" class="menu2_add_btn">添加二级菜单</button>
        </div>
        <div class='menu menu3' style="<?=$menu2[0]['iid']?'display:block':'display:none';?>">
            <div class="yijicaidan">一级菜单</div>
            <div class="menu3_lv0"><span class="biaoti" data-iid='<?=$menu2[0]['iid']?>' data-type='<?=$menu2[0]['type']?>' data-keyword='<?=$menu2[0]['keyword']?>' data-do='<?=$menu2[0]['iid']?'yes':'no'?>'><?=$menu2[0]['text']?$menu2[0]['text']:'标题'?></span><span class="shugang">|</span><span class='s_edit' data-menu='3' data-lv='0' >编辑</span><span class='del' data-menu='3'>×</span></div>
            <div class="erjicaidan">二级菜单</div>
            <div style="<?=$menu2[1]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu3 menu3_lv1"><span data-iid='<?=$menu2[1]['iid']?>' data-type='<?=$menu2[1]['type']?>' data-keyword='<?=$menu2[1]['keyword']?>' data-do='<?=$menu2[1]['iid']?'yes':'no'?>' class='content'><?=$menu2[1]['text']?$menu2[1]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='1' >编辑</span><span class='del' data-menu='3' data-lv='1'>×</span></div>
            <div style="<?=$menu2[2]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu3 menu3_lv2"><span data-iid='<?=$menu2[2]['iid']?>' data-type='<?=$menu2[2]['type']?>' data-keyword='<?=$menu2[2]['keyword']?>' data-do='<?=$menu2[2]['iid']?'yes':'no'?>' class='content'><?=$menu2[2]['text']?$menu2[2]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='2' >编辑</span><span class='del' data-menu='3' data-lv='2'>×</span></div>
            <div style="<?=$menu2[3]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu3 menu3_lv3"><span data-iid='<?=$menu2[3]['iid']?>' data-type='<?=$menu2[3]['type']?>' data-keyword='<?=$menu2[3]['keyword']?>' data-do='<?=$menu2[3]['iid']?'yes':'no'?>' class='content'><?=$menu2[3]['text']?$menu2[3]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='3' >编辑</span><span class='del' data-menu='3' data-lv='3'>×</span></div>
            <div style="<?=$menu2[4]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu3 menu3_lv4"><span data-iid='<?=$menu2[4]['iid']?>' data-type='<?=$menu2[4]['type']?>' data-keyword='<?=$menu2[4]['keyword']?>' data-do='<?=$menu2[4]['iid']?'yes':'no'?>' class='content'><?=$menu2[4]['text']?$menu2[4]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='4' >编辑</span><span class='del' data-menu='3' data-lv='4'>×</span></div>
            <div style="<?=$menu2[5]['iid']?'display:block':'display:none';?>" class="lv_menu lv_menu3 menu3_lv5"><span data-iid='<?=$menu2[5]['iid']?>' data-type='<?=$menu2[5]['type']?>' data-keyword='<?=$menu2[5]['keyword']?>' data-do='<?=$menu2[5]['iid']?'yes':'no'?>' class='content'><?=$menu2[5]['text']?$menu2[5]['text']:'标题'?></span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='5' >编辑</span><span class='del' data-menu='3' data-lv='5'>×</span></div>


            <!-- <div <?=$menu3[1]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu3 menu3_lv1"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='1'>编辑</span><span class='del' data-menu='3' data-lv='1'>×</span></div>
            <div <?=$menu3[2]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu3 menu3_lv2"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='2'>编辑</span><span class='del' data-menu='3' data-lv='2'>×</span></div>
            <div <?=$menu3[3]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu3 menu3_lv3"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='3'>编辑</span><span class='del' data-menu='3' data-lv='3'>×</span></div>
            <div <?=$menu3[4]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu3 menu3_lv4"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='4'>编辑</span><span class='del' data-menu='3' data-lv='4'>×</span></div>
            <div <?=$menu3[5]['iid']?'display:block':'display:none';?> class="lv_menu lv_menu3 menu3_lv5"><span data-iid='' data-type='' data-keyword='' data-do='no' class='content'>标题</span><span class="shugang">|</span><span class='edit' data-menu='3' data-lv='5'>编辑</span><span class='del' data-menu='3' data-lv='5'>×</span></div> -->
            <button type="button" class="menu3_add_btn">添加二级菜单</button>
        </div>
        <button type="button" class="menu_add_btn">添加一级菜单</button>
    </div>
    <button type="button"  type="button" class="reset">重新编辑</button>
    <button type="button" class="submit">提交</button>
    </div>
    <div class="model" data-menu='' data-lv=''>
        <div class="hint">tag：一级菜单标题最多4个字，二级菜单标题最多7个字</div>
        <div style="display:inline-block">
            <input id="menu-name" maxlength="4" class='menu_name' value="" placeholder='请输入标题' />
        </div>
        <div style="display:inline-block">
            <select class='item' style="
    display: inline-block;
    height: 30px;
    padding: 4px 6px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 20px;
    color: #555;
    vertical-align: middle;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    background-color: #fff;
    border: 1px solid #ccc;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -moz-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border linear .2s,box-shadow linear .2s;
    -moz-transition: border linear .2s,box-shadow linear .2s;
    -o-transition: border linear .2s,box-shadow linear .2s;
    transition: border linear .2s,box-shadow linear .2s;
    width: 220px;
    font-size: 12px;" onChange="selectchange()">
            <!-- iid -->
                <option value="url" data-type='_url' data-url=''>自定义外链</option>
                <option value="text" data-type='_text' data-url=''>文本消息</option>
            <?php foreach ($menu as $k => $v):?>
                <option value="<?=$v['iid']?><?=$v['type']?><?=$v['url']?>" data-url='<?=$v['url']?>' data-type='<?=$v['type']?>'><?=$v['name']?></option>
            <?php endforeach?>
            </select>
        </div>
        <div>
            <input class='menu_content' value="" placeholder='如自定义，请填写相关链接或文案' style="width:450px;" />
        </div>
        <button type="button" class="confirm">确定</button>
        <button type="button" class="cancel">取消</button>
        <div class="white"></div><div class="arrow"></div>
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
                            <?php

        if (!$_POST||$_POST['menu']) $active = 'tab1';
        if ($_POST['text']) $active = 'tab2';
        ?>
<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<script type="text/javascript">
function selectchange(){
    var a=$('.item option:selected').val();
    console.log(a);
    if (a=='url'||a=='text') {
        $('.menu_content').show();
        if(a=='url'){
            $('.menu_content').attr('placeholder',"请填写相关跳转链接，并使用【http://】或【https://】开头");
        }
        if(a=='text'){
            $('.menu_content').attr('placeholder',"请填写相关文案");
        }
    }else{
        $('.menu_content').hide();
    }
}
$(function () {
    $('#<?=$active?>,#<?=$active?>-bar').addClass('am-active');
    json = '<?=json_encode($menu)?>';
    menu = JSON.parse(json);
    var type = $(".item").find("option:selected").data('type');
    var url = $(".item").find("option:selected").data('url');
    $('.model').data('type',type);
    if(type=='url'){
        $('.menu_content').val(url);
    }
    console.log('type::::'+type);
});
$(document).ready(function() {
    $('.init_menu').click(function() {
        $('.init_menu').hide(500);
        init_menu();
    });
});

function init_menu() {
    $('.add_menu').show(500);
    $('.menu1').show(500);
}

function showmenu(i) {
    var n = (5 - $('.lv_menu' + i + ':hidden').length) + 1;
    $('.menu' + i + '_lv' + n).show(500);
}
$(".item").change(function(){
    var type = $(".item").find("option:selected").data('type');
    var url = $(".item").find("option:selected").data('url');
    $('.model').data('type',type);
    if(type=='url'){
        $('.menu_content').val(url);
    }else{
        $('.menu_content').val('');
    }
    console.log('type::::'+type);
});
$('.menu1_add_btn').click(function() {
    // console.log($('.lv_menu1:hidden').length);
    if($('.menu1_lv0').children().eq(0).data('do')=='no'){
        return alert('一级菜单未编辑！');
    }
    if($('.lv_menu1:visible').last().find('.content').data('iid')==''){
        return alert('上个菜单未编辑');
    }
    if ($('.lv_menu1:hidden').length == 0) {
        alert('二级菜单最多添加五个');
    } else {
        showmenu(1);
    }
});
$('.menu2_add_btn').click(function() {
    // console.log($('.lv_menu2:hidden').length);
    if($('.menu2_lv0').children().eq(0).data('do')=='no'){
        return alert('一级菜单未编辑！');
    }
    if($('.lv_menu2:visible').last().find('.content').data('iid')==''){
        return alert('上个菜单未编辑');
    }
    if ($('.lv_menu2:hidden').length == 0) {
        alert('二级菜单最多添加五个');
    } else {
        showmenu(2);
    }
});
$('.menu3_add_btn').click(function() {
    // console.log($('.lv_menu3:hidden').length);
    if($('.menu3_lv0').children().eq(0).data('do')=='no'){
        return alert('一级菜单未编辑！');
    }
    if($('.lv_menu3:visible').last().find('.content').data('iid')==''){
        return alert('上个菜单未编辑');
    }
    if ($('.lv_menu3:hidden').length == 0) {
        alert('二级菜单最多添加五个');
    } else {
        showmenu(3);
    }
});
$('.menu_add_btn').click(function() {
    // console.log($('.menu:hidden').length);
    if ($('.menu:hidden').length == 0) {
        alert('一级菜单最多添加三个');
    } else {
        var n = 3 - $('.menu:hidden').length + 1;
        $('.menu' + n).show(500);
    }
});
$('.edit').click(function() {
    $('#menu-name').attr('maxlength','7');
    var m = $(this).data('menu');
    var lv = $(this).data('lv');
    var tp = $(this).offset().top-160;
    var lf = $(this).offset().left-350;
    var iid = $(this).parent().children().eq(0).data('iid');
    var url = $(this).parent().children().eq(0).data('url');
    var type = $(this).parent().children().eq(0).data('type');
    var keyword = $(this).parent().children().eq(0).data('keyword');
    var text = $(this).parent().children().eq(0).text();
    $('.menu_content').val(keyword);
    $('.menu_name').val(text);
    if(iid) $('.item').val(iid);
    if(type) $('.model').data('type',type);
    console.log("type:"+type);
    console.log("iid:"+iid);
    console.log("keyword:"+keyword);
    console.log("text:"+text);
    // alert($('body').scrollTop());
    // var st = $(document).scrollTop();
    // var sl = $('header').scrollLeft();
    // console.log(st);
    $('.model').css('top',tp);
    $('.model').css('left','440px');
    $('.model').data('menu', m);
    $('.model').data('lv', lv);
    selectchange();
    $('.model').show(500);
});
$('.s_edit').click(function() {
    $('#menu-name').attr('maxlength','4');
    var m = $(this).data('menu');
    var lv = $(this).data('lv');
    var tp = $(this).offset().top-160;
    var lf = $(this).offset().left-350;
    var iid = $(this).parent().children().eq(0).data('iid');
    var url = $(this).parent().children().eq(0).data('url');
    var type = $(this).parent().children().eq(0).data('type');
    var keyword = $(this).parent().children().eq(0).data('keyword');
    var text = $(this).parent().children().eq(0).text();
    $('.menu_content').val(keyword);
    $('.menu_name').val(text);
    if(iid) $('.item').val(iid);
    if(type) $('.model').data('type',type);
    console.log("type:"+type);
    console.log("iid:"+iid);
    console.log("keyword:"+keyword);
    console.log("text:"+text);
    // var st = $('header').scrollTop();
    // var sl = $('header').scrollLeft();
    // console.log(st);
    $('.model').css('top',tp);
    $('.model').css('left','440px');
    $('.model').data('menu', m);
    $('.model').data('lv', lv);
    selectchange();
    $('.model').show(500);
});
$('.del').click(function() {
    var m = $(this).data('menu');
    var lv = $(this).data('lv');
    if(!lv){//删除一级菜单
        console.log(m);
        console.log(lv);
        //更改所有二级菜单
        var max = 3 - m;
        //m=1 max=2
        for (var i = 1; i <= max; i++) {
            var mtext = $('.menu'+(m+i)+'_lv0').children().eq(0).text();
            var miid = $('.menu'+(m+i)+'_lv0').children().eq(0).data('iid');
            var mtype = $('.menu'+(m+i)+'_lv0').children().eq(0).data('type');
            var mkeyword = $('.menu'+(m+i)+'_lv0').children().eq(0).data('keyword');
            var mdo = $('.menu'+(m+i)+'_lv0').children().eq(0).data('do');

            var mmenu = $('.menu'+(m+i)+'lv0').children().eq(2).data('menu');

            $('.menu'+(m+i-1)+'_lv0').children().eq(0).text(mtext);
            $('.menu'+(m+i-1)+'_lv0').children().eq(0).data('iid',miid);
            $('.menu'+(m+i-1)+'_lv0').children().eq(0).data('type',mtype);
            $('.menu'+(m+i-1)+'_lv0').children().eq(0).data('keyword',mkeyword);
            $('.menu'+(m+i-1)+'_lv0').children().eq(0).data('do',mdo);

            $('.menu'+(m+i-1)+'lv0').children().eq(2).data('menu',mmenu);
            console.log('lv');
            for (var a = 0; a<=4; a++) {
                var up = $('.lv_menu' + (m+i) + ':eq(' + a + ') .content').text();
                var iid = $('.lv_menu' + (m+i) + ':eq(' + a + ') .content').data('iid');
                var keyword = $('.lv_menu' + (m+i) + ':eq(' + a + ') .content').data('keyword');
                var d = $('.lv_menu' + (m+i) + ':eq(' + a + ') .content').data('do');
                var type = $('.lv_menu' + (m+i) + ':eq(' + a + ') .content').data('type');

                $('.lv_menu' + (m+i-1) + ':eq(' + a + ') .content').text(up);
                $('.lv_menu' + (m+i-1) + ':eq(' + a + ') .content').data('iid',iid);
                $('.lv_menu' + (m+i-1) + ':eq(' + a + ') .content').data('keyword',keyword);
                $('.lv_menu' + (m+i-1) + ':eq(' + a + ') .content').data('do',d);
                $('.lv_menu' + (m+i-1) + ':eq(' + a + ') .content').data('type',type);

                if(d=='yes'){
                    console.log('show:::'+(m+i-1)+(a+1));
                    $('.menu' + (m+i-1) + '_lv' + (a+1)).show(500);
                }else{
                    console.log('hide:::'+(m+i-1)+(a+1));
                    $('.menu' + (m+i-1) + '_lv' + (a+1)).hide(500);
                }
            }
        }
        //最后一个菜单置为空

        $('.menu:visible').last().find('.biaoti').data('iid','');
        $('.menu:visible').last().find('.biaoti').data('type','');
        $('.menu:visible').last().find('.biaoti').data('keyword','');
        $('.menu:visible').last().find('.biaoti').data('do','');

        var lastm = $('.menu:visible').last().find('.s_edit').data('menu');
        console.log(lastm);
        $('.menu:visible').last().find('.s_edit').data('menu','');
        $('.menu:visible').last().find('.s_edit').data('lv','');

        for (var b = 0; b<=4; b++) {
            $('.lv_menu' + lastm ).eq(b).find('.content').text('标题');
            $('.lv_menu' + lastm ).eq(b).find('.content').data('iid','');
            $('.lv_menu' + lastm ).eq(b).find('.content').data('keyword','');
            $('.lv_menu' + lastm ).eq(b).find('.content').data('do','no');
            $('.lv_menu' + lastm ).eq(b).find('.content').data('type','');
        }
        $('.menu:visible').last().hide(500);
    }else{
        //删除二级菜单
        var max = 5 - lv;
        for (var i = 0; i < max; i++) {
            var up = $('.lv_menu' + m + ':eq(' + (lv + i) + ') .content').text();
            var iid = $('.lv_menu' + m + ':eq(' + (lv + i) + ') .content').data('iid');
            var keyword = $('.lv_menu' + m + ':eq(' + (lv + i) + ') .content').data('keyword');
            var d = $('.lv_menu' + m + ':eq(' + (lv + i) + ') .content').data('do');
            var type = $('.lv_menu' + m + ':eq(' + (lv + i) + ') .content').data('type');
            $('.lv_menu' + m + ':eq(' + (lv - 1 + i) + ') .content').text(up);
            $('.lv_menu' + m + ':eq(' + (lv - 1 + i) + ') .content').data('iid',iid);
            $('.lv_menu' + m + ':eq(' + (lv - 1 + i) + ') .content').data('keyword',keyword);
            $('.lv_menu' + m + ':eq(' + (lv - 1 + i) + ') .content').data('do',d);
            $('.lv_menu' + m + ':eq(' + (lv - 1 + i) + ') .content').data('type',type);
        };
        $('.lv_menu' + m + ':visible').last().find('.content').text('标题');
        $('.lv_menu' + m + ':visible').last().find('.content').data('iid','');
        $('.lv_menu' + m + ':visible').last().find('.content').data('keyword','');
        $('.lv_menu' + m + ':visible').last().find('.content').data('do','no');
        $('.lv_menu' + m + ':visible').last().find('.content').data('type','');
        $('.lv_menu' + m + ':visible').last().hide(500);
    }

});
$('.submit').click(function(event) {
    // console.log($('.content:visible').eq(0).data('iid'));
    // console.log($('.content:visible').eq(0).data('keyword'));
    //小菜单
    arr = {};
    for (var i = 0; i <=2; i++) {
        arr[i] = {};
        for (var a = 0; a <=5; a++) {
            arr[i][a] = {};
        };
    };
    for (var b=0; b<=2; b++) {
        arr[b][0]['text'] = $('.s_edit').eq(b).parent().children().eq(0).text();
        arr[b][0]['iid'] = $('.s_edit').eq(b).parent().children().eq(0).data('iid');
        arr[b][0]['keyword'] = $('.s_edit').eq(b).parent().children().eq(0).data('keyword');
        arr[b][0]['do'] = $('.s_edit').eq(b).parent().children().eq(0).data('do');
        arr[b][0]['type'] = $('.s_edit').eq(b).parent().children().eq(0).data('type');
    };
    $('.content').each(function(index, el) {
        if(index<=4){
            arr[0][index+1]['text'] = $('.content').eq(index).text();
            arr[0][index+1]['iid'] = $('.content').eq(index).data('iid');
            arr[0][index+1]['keyword'] = $('.content').eq(index).data('keyword');
            arr[0][index+1]['do'] = $('.content').eq(index).data('do');
            arr[0][index+1]['type'] = $('.content').eq(index).data('type');
        }
        if(index<=9&&index>=5){
            arr[1][index-5+1]['text'] = $('.content').eq(index).text();
            arr[1][index-5+1]['iid'] = $('.content').eq(index).data('iid');
            arr[1][index-5+1]['keyword'] = $('.content').eq(index).data('keyword');
            arr[1][index-5+1]['do'] = $('.content').eq(index).data('do');
            arr[1][index-5+1]['type'] = $('.content').eq(index).data('type');
        }
        if(index<=14&&index>=10){
            arr[2][index-10+1]['text'] = $('.content').eq(index).text();
            arr[2][index-10+1]['iid'] = $('.content').eq(index).data('iid');
            arr[2][index-10+1]['keyword'] = $('.content').eq(index).data('keyword');
            arr[2][index-10+1]['do'] = $('.content').eq(index).data('do');
            arr[2][index-10+1]['type'] = $('.content').eq(index).data('type');
        }
        // console.log($('.content').eq(index).text());
        // console.log($('.content').eq(index).data('iid'));
        // console.log($('.content').eq(index).data('keyword'));
    });

    console.log(arr);
    console.log(arr[0][0]['do']);
    console.log(arr[1][0]['do']);
    console.log(arr[2][0]['do']);
    if(arr[0][0]['do']=='no'&&arr[1][0]['do']=='no'&&arr[2][0]['do']=='no'){
        return alert('一级菜单务必设置');
    }
    $.ajax({
        url: '/qwta/diy',
        type: 'POST',
        dataType: 'json',
        data: {menu: JSON.stringify(arr)},
        cache:false,
    })
    .done(function(res) {
        // console.log(res);
        alert(res.error);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

    //三个大菜单如下
    // console.log($('.s_edit').eq(0).text());
    // console.log($('.s_edit').eq(0).data('iid'));
    // console.log($('.s_edit').eq(0).data('keyword'));

    // console.log($('.s_edit').eq(1).text());
    // console.log($('.s_edit').eq(1).data('iid'));
    // console.log($('.s_edit').eq(1).data('keyword'));

    // console.log($('.s_edit').eq(2).text());
    // console.log($('.s_edit').eq(2).data('iid'));
    // console.log($('.s_edit').eq(2).data('keyword'));

});
$('.confirm').click(function() {
    var name = $('.menu_name').val();
    if(name.length==''){
        return alert('菜单名称不能为空！');
    }
    var content = $('.menu_content').val();
    if($('.item').val() == 'url'){
        if(content.substring(0,4)!='http'){
            return alert('如果是自定义链接，请务必使用【http://】或者【https://】开头，例如：【http://www.baidu.com】');
        }
    }
    var iid = $('.item').val();
    var m = $('.model').data('menu');
    var lv = $('.model').data('lv');
    var type = $('.model').data('type');
    var url = $('.model').data('url');
    if(lv === 0){
        $('.menu'+m+'_lv'+lv+' span:eq(0)').text(name);
        $('.menu'+m+'_lv'+lv+' span:eq(0)').data('iid',iid);
        $('.menu'+m+'_lv'+lv+' span:eq(0)').data('keyword',content);
        $('.menu'+m+'_lv'+lv+' span:eq(0)').data('do','yes');
        $('.menu'+m+'_lv'+lv+' span:eq(0)').data('type',type);
        $('.menu'+m+'_lv'+lv+' span:eq(0)').data('url',url);
    }else{
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').text(name);
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('iid',iid);
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('keyword',content);
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('do','yes');
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('type',type);
        $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('url',url);
    }
    $('.model').hide(500);
});
$('.cancel').click(function() {
    $('.model').hide(500);
});
$('.reset').click(function() {
    for (var m = 1; m <=3; m++) {
        $('.menu' + m + '_lv0 span:eq(0)').text('编辑');
        $('.menu' + m + '_lv0 span:eq(0)').data('iid','');
        $('.menu' + m + '_lv0 span:eq(0)').data('keyword','');
        $('.menu' + m + '_lv0 span:eq(0)').data('do','no');
        $('.menu' + m + '_lv0 span:eq(0)').data('type','');
        $('.menu' + m ).hide();
        for (var lv=1; lv<=5; lv++) {
            $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').text('编辑');
            $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('iid','');
            $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('keyword','');
            $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('do','no');
            $('.lv_menu' + m + ':eq(' + (lv - 1) + ') .content').data('type','');
            $('.menu'+m+'_lv'+lv).hide();
        };
    };
});
</script>

    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
