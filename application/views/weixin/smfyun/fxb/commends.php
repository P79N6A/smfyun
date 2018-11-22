<div class="bd">
        <div class="weui_panel weui_panel_access">
            <div class="weui_panel_hd"><?=$result['title']?></div>
            <div class="weui_panel_bd">
            <?php foreach ($result['commends'] as $v):?>
              <a href="/qwtfxb/shareopenid/<?=base64_encode($result['openid'])?>/<?=$v->id?>/<?=$bid?>" class="weui_media_box weui_media_appmsg">
                    <div class="weui_media_hd">
                        <img class="weui_media_appmsg_thumb" src="<?=$v->pic?>" alt="">
                    </div>
                    <div class="weui_media_bd">
                        <h4 class="weui_media_title"><?=$v->title?></h4>
                        <p class="weui_media_desc" style="color:#FF3030"><?=$v->price?></p>
                    </div>
              </a>
            <?php endforeach?>
            </div>
        </div>
</div>

