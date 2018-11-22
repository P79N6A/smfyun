<!DOCTYPE HTML>
<html lang="<?=I18n::$lang?>">
<head>
<meta charset="UTF-8">
<title><?=$title?></title>
<?php include Kohana::find_file('views', 'tpl/head') ?>
</head><?php flush();?>

<body<?=$IEVER?>>
<?php include Kohana::find_file('views', 'tpl/_header') ?>

<div class="headwrap">
    <div class="header">
        <a href="<?=Url::site('home')?>" title="<?=Kohana::config('global')->site_name?>首页" id="head_link"><?=Kohana::config('global')->site_name?>首页</a>
        <?=$header?>
    </div>
    <?=$headwrap?>
</div>

<div class="container">
	<?=$content?>
</div>

<?php include Kohana::find_file('views', 'tpl/footer') ?>
<?php include Kohana::find_file('views', 'tpl/_footer') ?>

</body>
</html>
