<?php
class Helper{

	//插入红包口令到数据库函数，并实现文件下载,生成的口令缓存在根目录code文件夹下
 public static function GenerateCode($time,$bid,$count=5000,$iid,$ext='csv'){
  include Kohana::find_file('vendor', 'code/PHPExcel');
  include Kohana::find_file('vendor', 'code/PHPExcel/Writer/Excel2007');
  $i = 0;
  $tmp = array();
  // echo $time.'<br>';
  // echo $bid.'<br>';
  // echo $count.'<br>';
  // echo $iid.'<br>';
  // exit();
  $file = "/tmp/$bid.$count.".$ext;
  $fh = fopen($file, 'w+');
  $db = Database::instance();
  $db->query(NULL, 'START TRANSACTION');
  $resultPHPExcel = new PHPExcel();
  $times = ceil($count/100000);
  for ($a=1; $a <= $times; $a++) {
    $i = 0;
    $d_value = $count-$i;
    while ($d_value>0) {
      $values = array();
      while ($i<$count) {
        $str =self::genKouling();
        $i++;
        $values[] = "($bid, $iid,'$str', $time)";
      }
      //统一插入
      $SQL = 'INSERT IGNORE INTO qwt_ywmkls (`bid`,`iid`,`code`,`lastupdate`) VALUES '. join(',', $values);
      $now_count = $colum = DB::query(NULL,$SQL)->execute();
      //当前行数
      $SQL2 = "SELECT * from qwt_ywmkls where `bid`= $bid and `lastupdate`= $time";
      $d_value = $count - DB::query(NULL,$SQL2)->execute();

      $i = DB::query(NULL,$SQL2)->execute();
    }
  }
  $db->query(NULL, 'COMMIT');
 }
  private static function genKouling($length = 16){
      // 密码字符集，可任意添加你需要的字符
      $chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
      $password = '';
      for($i = 0; $i < $length; $i++){
          // 将 $length 个数组元素连接成字符串
          $password .= $chars[rand(0,25)];
      }
      return $password;
  }
	//中文编码转换为全拼
	public static function UtfTo($str){
		include Kohana::find_file('vendor', 'code/utfto');
		return preg_replace('/\s/', '', CUtf8_PY::encode($str,'all'));
	}
//检测一些非法字符
    public static function  check($array)
    {
        $reg='/[\|"\'<>]/';
        foreach($array as $arr){
            if($arr==""){
                echo "<script>alert('相关参数不能为空');</script>";
                return false;
            }
            else if(preg_match_all($reg, $arr)){
                echo "<script>alert('不能出现非法字符');</script>";
                return false;
            }
            else{
                continue;
            }
        }
        return true;
    }
}
