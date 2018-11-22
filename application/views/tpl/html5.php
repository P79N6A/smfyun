基础设置<!DOCTYPE HTML>
<html lang="<?=I18n::$lang?>">
<head>
<meta charset="UTF-8">
<title><?=$title?></title>
</head>
<?php flush();?>

<body<?=$IEVER?>>
<?php include Kohana::find_file('views', 'tpl/_header') ?>

<?=$content?>

<?php //include Kohana::find_file('views', 'tpl/_footer') ?>

</body>
</html>
