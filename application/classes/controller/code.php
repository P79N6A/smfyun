<?php defined('SYSPATH') or die('No direct script access.');

class Controller_code extends Controller_Base{
    public $template = 'tpl/blank';
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion = '3.0.0';
    var $we;
    var $client;
    public function before() {
        Database::$default = "flb";
        parent::before();
        if (Request::instance()->action == 'test') return;
    }
    public function action_test(){
        require_once Kohana::find_file('vendor', 'phpqrcode/phpqrcode');
        try {
            $PNG_TEMP_DIR = DOCROOT.'/code/tmp';
            $file_name = $PNG_TEMP_DIR . 'qq.png';
            $download_file_name = 'qq.png';
            QRcode::png(
                'http://www.smfyun.com',
                $file_name,
                QR_ECLEVEL_L,
                16,
                4
            );
            if (file_exists($PNG_TEMP_DIR)) {
                $file = fopen($PNG_TEMP_DIR, "r");
                Header("Content-type: application/octet-stream");

                Header("Accept-Ranges: bytes");

                Header("Accept-Length: " . filesize($PNG_TEMP_DIR));

                Header("Content-Disposition: attachment; filename=aaa" );
                // 输出文件内容
                echo fread($file, filesize($PNG_TEMP_DIR));

                fclose($file);
                //下载完成后,删除该图片
                // unlink($PNG_TEMP_DIR);
            }
        } catch (\Exception $e) {
            echo "无法下载图片\n";
            echo $e->getMessage();
        }

        $value="http://www.smfyun.com";  
        $errorCorrectionLevel = "L"; // 纠错级别：L、M、Q、H  
        $matrixPointSize = "4"; // 点的大小：1到10  
        
        QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize);  
        // header("Content-Type: image/jpeg");
        //header("Content-Length: ".strlen($pic));
        //echo $pic;
        // exit();
    }
}
