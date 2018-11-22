<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate">
    <title><?=$title?></title>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <style>
    body{
      text-align: center;
    }
    button{
      margin-top: 300px;
    }
    </style>
  <body>
  <div class="container">
  <?php if($over==1):?>
    <span>您的二维码已过期</span>
  <?else:?>
    <?php if($href==1):?>
    <a href="<?=$subhref?>"><button type="button" class="btn btn-primary"><?=$bindcon?></button></a>
    <?else:?>
    <button type="button" class="btn btn-primary"><?=$bindcon?></button>
    <?endif?>
  <?endif?>
  </div>
  </body>
</html>
