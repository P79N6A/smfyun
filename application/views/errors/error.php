<style>
.headwrap {
    position: fixed;
    top: 0; left: 0;
}

.container {
    width: 1000px;
    background: #FFF;
    top: 76px;
    padding-top: 0;
    position: relative;
    overflow: hidden;
}

.footer {
    position: relative;
    top: 76px;
    padding-bottom: 20px;
}
.sales_wrap_c {
    background:white;
}

.sales_wrap {
    overflow:hidden;
    padding:20px 30px;
    min-height: 300px;
}

.why_wrap ul {
    color: #666;
    line-height: 200%;
    list-style: square;
    margin: 1em 3em;
}
</style>

<div class="sales_wrap_c">
    <div class="sales_wrap why_wrap">

        <h1><?=$title?></h1>

        <center>
            <img alt="<?=$title?>" src="<?=$icon?>" style="margin:20px" />
            <br clear="all" />
            <span class="error" style="font-size:14px;"> <?=$msg?> </span>

            <div class="clearit">&nbsp;</div>
            <br clear="all" /><br />
            <a href="javascript:history.back()" class="button">返回上页 &raquo;</a>
        </center>

    </div>
</div>

<?php $_SERVER['NOQQ'] = 1;?>
