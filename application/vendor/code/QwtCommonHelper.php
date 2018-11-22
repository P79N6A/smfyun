<?php
class Helper{

	//插入红包口令到数据库函数，并实现文件下载,生成的口令缓存在根目录code文件夹下
 public static function GenerateCode($time,$bid,$count=5000,$ext='csv'){
  include Kohana::find_file('vendor', 'code/PHPExcel');
  include Kohana::find_file('vendor', 'code/PHPExcel/Writer/Excel2007');

  $i = 0;
  $tmp = array();
  $file = "/tmp/$bid.$count.".$ext;
  $fh = fopen($file, 'w+');
  $db = Database::instance();

  $db->query(NULL, 'START TRANSACTION');

  $resultPHPExcel = new PHPExcel();

  $times = ceil($count/100000);
  $now_count = 0;
  // for ($a=1; $a <= $times; $a++) {
    //$count = 100000;
    $i = 0;
    $d_value = $count-$i;
    while ($d_value>0) {
      Kohana::$log->add('cron_hb:d_value', $d_value);//
      $values = array();
      while ($i<$count) {
        $str =self::genKouling();
        // if (isset($tmp[$str])) $str = self::genKouling(1);
        // if (isset($tmp[$str])) $str = self::genKouling(2);
        // if (isset($tmp[$str])) $str = self::genKouling(3);
        // $tmp[$str] = 1;
        $i++;
        $values[] = "($bid, $str, $time)";
      }
      //统一插入
      $SQL = 'INSERT IGNORE INTO qwt_hbbkls (`bid`,`code`,`lastupdate`) VALUES '. join(',', $values);
      $now_count = $now_count + DB::query(NULL,$SQL)->execute();
      Kohana::$log->add('cron_hb:now_count', $now_count);//
      //当前行数
      // $SQL2 = "SELECT * from qwt_hbbkls where `bid`= $bid and `lastupdate`= $time";
      $d_value = $count - $now_count;
      Kohana::$log->add('cron_hb:d_value2', $d_value);//
      $i = $now_count;
      Kohana::$log->add('cron_hb:i', $i);//
    }
  // }


  // if($now_count<$count){
  //   $i = $now_count;
  //   while ($i<$count) {
  //     $str =self::genKouling();
  //     if (isset($tmp[$str])) $str = self::genKouling(1);
  //     if (isset($tmp[$str])) $str = self::genKouling(2);
  //     if (isset($tmp[$str])) $str = self::genKouling(3);
  //     $tmp[$str] = 1;
  //     $i++;
  //     $values2[] = "($bid, $str, $time)";
  //   }
  //   $SQL = 'INSERT IGNORE INTO qwt_hbbkls (`bid`,`code`,`lastupdate`) VALUES '. join(',', $values2);
  //   echo DB::query(NULL,$SQL)->execute();
  // }
  $db->query(NULL, 'COMMIT');

  // $SQL = "SELECT * from qwt_hbbkls where `bid`= $bid and `lastupdate`= $time";
  // // echo $SQL.'<br>';
  // // echo $db->select_default().'<br>';
  // $codes = DB::query(NULL,$SQL)->execute();
  // // var_dump($codes);
  // $codes2 = DB::query(Database::SELECT,$SQL)->execute();
  // // var_dump($codes2);
  // foreach ($codes2 as $k => $v) {
  //   // echo $v['code'].'<br>';
  //   fputcsv($fh, array($v['code']));
  // }
  // // exit;
  // fclose($fh);

  // $value=fopen($file,'r+');
  // header ( "Content-Type: application/force-download" );
  // header ( "Content-Type: application/octet-stream" );
  // header ( "Content-Type: application/download" );
  // header ( 'Content-Disposition:attachment;filename="' . basename($file) . '"' );
  // header ( "Content-Transfer-Encoding: binary" );

  // if($ext==='xls')
  // {
  //  $xlsWriter = new PHPExcel_Writer_Excel5($resultPHPExcel);
  //  $xlsWriter->save($file);
  // }
  // echo fread($value,filesize($file));
  // fclose($value);

  // @unlink($file);
 }

	//中文编码转换为全拼
	public static function UtfTo($str){
		include Kohana::find_file('vendor', 'code/utfto');
		return preg_replace('/\s/', '', CUtf8_PY::encode($str,'all'));
	}

	//生成红包口令函数
	private static function genKouling($regen=0) {
		$code = (string)mt_rand(100000000, 999999999);
		$j = 0;
		$sum = 0;
		while ($j < 9) {
			$sum += $code{$j};
			$j++;
		}
		$sum = $sum%9;
		return $code.$sum;
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
