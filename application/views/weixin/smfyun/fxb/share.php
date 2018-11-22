<?php if($status==2):?>
<div class=""><!-- 自己看自己 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><img style="width:120px" src="<?=$commend->pic?>"/></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title"><?=$commend->title?></h2>
            <p class="weui_media_desc" style="color:#FF3030">￥<?=$commend->price?></p>
            <p class="weui_msg_desc"><?=$result['content']?></p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($status==1):?>
<script type="text/javascript">
    window.open('<?=$commend->url?>','_self');
</script>
<?php endif?>
