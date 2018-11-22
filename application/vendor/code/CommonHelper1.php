<?php
class Helper{

    //插入红包口令到数据库函数，并实现文件下载,生成的口令缓存在根目录code文件夹下
    public static function GenerateCode($bid,$count=5000,$ext='csv'){
        include Kohana::find_file('vendor', 'code/PHPExcel');
        include Kohana::find_file('vendor', 'code/PHPExcel/Writer/Excel2007');

        $i = 0;
        $tmp = array();
        $file = "/tmp/$bid.$count.".$ext;
        $fh = fopen($file, 'w+');

        $resultPHPExcel = new PHPExcel();

        $db = Database::instance();
        $db->query(NULL, 'START TRANSACTION');

        while ($i<$count) {
            $str =self::genKouling();

            if (isset($tmp[$str])) $str = self::genKouling(1);
            if (isset($tmp[$str])) $str = self::genKouling(2);
            if (isset($tmp[$str])) $str = self::genKouling(3);
            $tmp[$str] = 1;

            //如果code存在 跳过此code继续
            // $codedata=ORM::factory('yhb_kl')->where('bid','=',$bid)->where('code','=',$str)->find();
            // if($codedata->id) {continue;}

            try {
              $i++;
              if($ext==='xls'){
                  $resultPHPExcel->getActiveSheet()->setCellValue('A'.$i,$str);
              } else {
                  fputcsv($fh, array($str));
              }
              $flag=DB::insert('yhb_kls',array('bid','code','lastupdate'))->values(array($bid,$str,time()))->execute();
            } catch (Exception $e){
                $i--;
                continue;
            }
        }

        fclose($fh);
        $db->query(NULL, 'COMMIT');

        $value=fopen($file,'r+');
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( 'Content-Disposition:attachment;filename="' . basename($file) . '"' );
        header ( "Content-Transfer-Encoding: binary" );

        if($ext==='xls')
        {
            $xlsWriter = new PHPExcel_Writer_Excel5($resultPHPExcel);
            $xlsWriter->save($file);
        }
        echo fread($value,filesize($file));
        fclose($value);

        @unlink($file);
    }

    //中文编码转换为全拼
    public static function UtfTo($str){
        include Kohana::find_file('vendor', 'code/utfto');
        return preg_replace('/\s/', '', CUtf8_PY::encode($str,'all'));
    }

    //生成红包口令函数
    private static function genKouling($regen=0) {
        $code = (string)mt_rand(1000, 9999);
        $j = 0;
        $sum = 0;
        while ($j < 4) {
            $sum += $code{$j};
            $j++;
        }
        $sum = $sum%4;
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
