<!DOCTYPE html>
<html>
<head>
 <title></title>
</head>
<body>
<div id="div1">
</div>
<form method="post">
<textarea name='text' id="text1" style="width:100%; height:200px;"></textarea>
<input type="submit" value="点击保存"></input>
</form>
<?=$text?>
</body>
<script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.js"></script>
<script type="text/javascript" src="https://unpkg.com/wangeditor@3.1.0/release/wangEditor.min.js"></script>
<script type="text/javascript">
  var E = window.wangEditor
  var editor = new E('#div1')
  var $text1 = $('#text1')
  editor.customConfig.uploadImgShowBase64 = true   // 使用 base64 保存图片
  editor.customConfig.onchange = function (html) {
      // 监控变化，同步更新到 textarea
      $text1.val(html)
  }
  editor.create();
  // 初始化 textarea 的值
  $text1.val(editor.txt.html());
</script>
</html>
