<!DOCTYPE html>
<html lang="zh-cmn-Hans">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>核销员申请</title>
    <link rel="stylesheet" href="http://cdn.jfb.smfyun.com/dkl/weui.css" />
    <link rel="stylesheet" href="http://cdn.jfb.smfyun.com/dkl/example.css" />
    <style type="text/css">
    .tips{
        padding: 20px 0 0 0;
    }
    .please{
        border-radius: 0;
        background-color: #EFA520;
    }
    .succes{
        border-radius: 0;
        background-color: #1aad19;
    }
    .warning{
        border-radius: 0;
        line-height: 18px;
        padding: 14px;
    }
    </style>
</head>

<body ontouchstart>
    <div class="weui-toptips weui-toptips_warn js_tooltips">错误提示</div>
    <div class="container" id="container"></div>
    <div class="page navbar js_show">
        <!--主页面-->
        <?php if ($result['content']==1):?>
        <div class="page__hd tips">
        <a href="javascript:;" class="weui-btn weui-btn_warn please">请先输入您的手机号</a>
        </div>
        <?php endif?>
        <?php if ($result['content']==3):?>
        <div class="page__hd tips">
        <a href="javascript:;" class="weui-btn weui-btn_warn warning"><?=$result['err']?></a>
        </div>
        <?php endif?>
        <?php if ($result['content']==4):?>
        <div class="page__hd tips">
        <a href="javascript:;" class="weui-btn weui-btn_warn warning">请输入正确的手机号</a>
        </div>
        <?php endif?>
            <div class="weui-tab" style="height:136px;">
                <form action="" method="post">
                    <div class="weui-cells__title">输入手机号码登录</div>
                    <div class="weui-cells weui-cells_form">
                        <div class="weui-cell">
                            <div class="weui-cell__bd">
                                <input name="form[tel]" class="weui-input" type="tel" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="请输入手机号"/>
                            </div>
                        </div>
                    </div>
                    <div class="weui-btn-area">
                        <button class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips" type="submit">确定</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
    </script>
</body>

</html>
