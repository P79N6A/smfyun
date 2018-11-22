<style>
.score1 {color:green;}
.score0 {color:red;}
</style>
<head>
    <meta charset="utf-8">
    <title>表格</title>
    <style type="text/css">
        *{
            margin: 0;
            padding: 0;
        }
        body{
            font: italic 20px Georgia, serif;
            letter-spacing: normal;
            background-color: #f0f0f0;
        }
        #content{
            width: 100%;
            margin: 0 auto;
            background-color: #fff;
            border-right: 1px solid #ddd;
            box-shadow: 0px 0px 16px #aaa;
        }
        #table1{
            font: bold 16px/1.4em "Trebuchet MS", sans-serif;
            font-size: 10px;
            width: 100%;
        }
        #table1 thead th{
            padding: 15px;
            border: 1px solid #93CE37;
            border-bottom: 3px solid #9ED929;
            text-shadow: 1px 1px 1px #568F23;
            color: #fff;
            background-color: #9DD929;
            border-radius: 5px 5px 0px 0px;
        }
        #table1 thead th:empty{
            background-color: transparent;
            border: none;
        }
        #table1 tbody th{
            padding: 0px 10px;
            border: 1px solid #93CE37;
            border-right: 3px solid #9ED929;
            text-shadow: 1px 1px 1px #568F23;
            color: #666;
            background-color: #9DD929;
            border-radius: 5px 0px 0px 5px;
        }
        #table1 tbody td{
            padding: 10px;
            border: 2px solid #E7EFE0;
            text-align: center;
            text-shadow: 1px 1px 1px #fff;
            color: #666;
            background-color: #DEF3CA;
            border-radius: 2px;
        }
        #table1 tbody span.check::before{
            content: url(images/check0.png);
        }
        #table1 tfoot td{
            padding: 10px 0px;
            font-size: 32px;
            color: #9CD009;
            text-align: center;
            text-shadow: 1px 1px 1px #444;
        }
    </style>
</head>
<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<div class="js-feed block-feed block top-0 bottom-0 border-0">
<?php
$id = $num+1;

if ($id == 1):
?>
<div class="js-list">
    <a class="block-item">
        <p class="line-height-30">
            <div style="text-align:center">没有记录</div>
        </p>
    </a>
</div>
<?php
endif;
?>
<div id="content">
<table id="table1">
    <tbody>
    <?php
    if ($id != 1):
    ?>
    <tr>
        <td scope="col">日期</td>
        <td scope="col">进货额度</td>
        <td scope="col">消耗额度</td>
        <td scope="col">变更原因</td>
        <td scope="col">余额</td>
    </tr>
    <?php
    endif;
    foreach ($inputmoneys as $input):
     $title = $input->getTypeName($input->type);
    ?>
    <tr>
        <td scope="row"><?=date('Y-m-d', $input->lastupdate)?></td>
        <td><?=$input->money>=0?$input->money:'/'?></td>
        <td><?=$input->money<=0?-$input->money:'/'?></td>
        <td><?=$title?></td>
        <td><?=$input->money_all?></td>
    </tr>
    <?php endforeach;?>
  </tbody>
  </table>
</div>
<div style="height: 100px;background: #DEF3CA;"></div>
<footer class="page-footer fixed-footer">
    <ul style="height: 0px;">
        <li>
            <a href="/yty/home">
                <img src="/yty/images/footer002.png"/>
                <p font-style: normal; style="font-size: 14px;line-height:14px;">个人中心</p>
            </a>
        </li>

        <li >
            <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
                <img src="/yty/images/footer004.png"/>
                <p font-style: normal; style="font-size: 14px;line-height:14px;">推荐商品</p>
            </a>
        </li>
    </ul>
</footer>
