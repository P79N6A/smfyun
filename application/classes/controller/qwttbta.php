<?php defined('SYSPATH') or die('No direct script access.');

//分销宝后台
class Controller_qwttbta extends Controller_Base {

    public $template = 'weixin/qwt/tpl/tbtatpl';
    public $pagesize = 20;
    public $yzaccess_token;
    public $config;
    public $bid;
    public $wx;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',9)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'home'){
                $hasover = 1;
                @View::bind_global('hasover', $hasover);
            }
        }
    }

    public function after() {
        if ($this->bid) {
            $todo['hasbuy'] = ORM::factory('qwt_buy')->where('status', '=', 1)->where('bid', '=', $this->bid)->find_all();
            $this->template->todo = $todo;
        }
        @View::bind_global('bid', $this->bid);
        @View::bind_global('todo', $todo);
        parent::after();
    }
    public function action_index() {
        $this->action_login();
    }
    public function action_home(){
        $bid = $this->bid;
        if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
            $tmp_file = $_FILES ['pic'] ['tmp_name'];
            $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
            $file_type = $file_types [count ( $file_types ) - 1];
             /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower ( $file_type ) != "xls"){
                $result['error'] ='不是Excel文件，重新上传';
            }else{
                require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                $aaa = $tmp_file;
                $PHPExcel = $reader->load($aaa); // 载入excel文件
                $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                echo $highestRow.'<br>';
                $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                echo $highestColumm.'<br>';
                // /** 循环读取每个单元格的数据 */
                for ($row = 2; $row <= $highestRow; $row++){//行数是以第1行开始
                    for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                        $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                    }
                }
                // echo '<pre>';
                // var_dump($dataset);
                // echo '</pre>';
                // exit();
                foreach ( $dataset as $data ) {
                    $km =ORM::factory('qwt_tbtqrcode');
                    $km->bid = $this->bid;
                    $km->flag=1;
                    $km->password=123456;
                    if(isset($data["B"])) $km->name = $data["B"];
                    if(isset($data["C"])) $km->telphone = $data["C"];
                    if(isset($data["D"])){
                        $km->openid = $data["D"];
                    }else{
                        $km->openid = 'wap_user_1_'.$data["C"];
                    }
                    $km->save();
                }
            }
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/tbt/home')
            ->bind('bid',$bid);
    }
    //审核管理
    public function action_qrcodes_m($action='', $id=0) {
        $bid = $this->bid;
        //修改用户
        $qrcode = ORM::factory('qwt_tbtqrcode')->where('bid', '=', $bid)->where('telphone','!=',0)->where('password','IS NOT',NULL);
        $qrcode = $qrcode->reset(FALSE);
         if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='订单记录';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '昵称')
                        ->setCellValue('B'.$num, '姓名')
                        ->setCellValue('C'.$num, '手机号')
                        ->setCellValue('D'.$num, 'openid')
                        ->setCellValue('E'.$num, '行业类型')
                        ->setCellValue('F'.$num, '地址')
                        ->setCellValue('G'.$num, '状态');
            $qrcodes=$qrcode->find_all();
            foreach($qrcodes as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $v->name)
                            ->setCellValue('C'.$num, $v->telphone)
                            ->setCellValue('D'.$num, $v->openid)
                            ->setCellValue('E'.$num, $v->type)
                            ->setCellValue('F'.$num, $v->address)
                            ->setCellValue('G'.$num, $v->flag==1?'已审核':'未审核');
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('telphone', 'like', $s)->or_where('name', 'like', $s)->where_close();//;
        }
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/tbt/pages');
        $result['qrcodes'] = $qrcode->order_by('jointime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '会员管理';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/tbt/qrcodes_m')
            ->bind('pages', $pages)
            ->bind('result', $result);
    }
    //分销审核
    public function action_qrcodes($action='', $id=0) {
        $bid = $this->bid;

        //修改用户
        if ($_POST['form']['id']) {
            $id = $_POST['form']['id'];
            $qrcode_edit = ORM::factory('qwt_tbtqrcode')->where('id', '=', $id)->find();
            $qrcode_edit->flag=$_POST['form']['lock'];
            $qrcode_edit->save();
        }
        $qrcode = ORM::factory('qwt_tbtqrcode')->where('bid', '=', $bid)->where('flag','=',0)->where('telphone','!=',0)->where('password','IS NOT',NULL);
        $qrcode = $qrcode->reset(FALSE);
        if ($_GET['export']=='xls') {
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name='订单记录';
            $objPHPExcel = new PHPExcel();
            /*以下是一些设置 ，什么作者  标题啊之类的*/
            $objPHPExcel->getProperties()->setCreator("转弯的阳光")
                ->setLastModifiedBy("转弯的阳光")
                ->setTitle("数据EXCEL导出")
                ->setSubject("数据EXCEL导出")
                ->setDescription("备份数据")
                ->setKeywords("excel")
                ->setCategory("result file");
             /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
            $num=1;
            $objPHPExcel->setActiveSheetIndex(0)
                         //Excel的第A列，uid是你查出数组的键值，下面以此类推
                        ->setCellValue('A'.$num, '昵称')
                        ->setCellValue('B'.$num, '姓名')
                        ->setCellValue('C'.$num, '手机号')
                        ->setCellValue('D'.$num, 'openid')
                        ->setCellValue('E'.$num, '行业类型')
                        ->setCellValue('F'.$num, '地址');
            $qrcodes=$qrcode->find_all();
            foreach($qrcodes as $k => $v){
                $num=$k+2;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->nickname)
                            ->setCellValue('B'.$num, $v->name)
                            ->setCellValue('C'.$num, $v->telphone)
                            ->setCellValue('D'.$num, $v->openid)
                            ->setCellValue('E'.$num, $v->type)
                            ->setCellValue('F'.$num, $v->address);
            }
            $objPHPExcel->getActiveSheet()->setTitle('User');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$name.date('Ymd').'.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $qrcode = $qrcode->where_open()->where('nickname', 'like', $s)->or_where('name', 'like', $s)->or_where('telphone', 'like', $s)->where_close();
        }
        $result['countall'] = $countall = $qrcode->count_all();
        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/tbt/pages');

        $result['qrcodes'] = $qrcode->order_by('jointime', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '待审核用户';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/tbt/qrcodes')
            ->bind('pages', $pages)
            ->bind('result', $result);
    }
    public function action_qrcodes_edit($id){
        $bid = $this->bid;
        $qrcode = ORM::factory('qwt_tbtqrcode')->where('id','=',$id)->find();
        if($_POST){
            $qrcode->name=$_POST['text']['name'];
            $qrcode->address=$_POST['text']['address'];
            $qrcode->password=$_POST['text']['password'];
            $qrcode->flag=$_POST['text']['flag'];
            $qrcode->type=$_POST['text']['type'];
            $qrcode->save();
            Request::instance()->redirect('/qwttbta/qrcodes_m');
            // echo '<pre>';
            // var_dump($_POST);
            // echo '</pre>';
            // exit();
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/tbt/qrcodes_edit')
            ->bind('qrcode',$qrcode);
    }
    public function action_qrcodes_delete($type,$id){
        $qrcode = ORM::factory('qwt_tbtqrcode')->where('id','=',$id)->find()->delete();
        Request::instance()->redirect('/qwttbta/qrcodes_m');
    }
    //产品图片
    public function action_image1s($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_tbt$type";
        $pic = ORM::factory($table, $id)->shop_pic;
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
    //产品图片
    public function action_image2s($type='item', $id=1, $cksum='') {
        $field = 'pic';
        $table = "qwt_tbt$type";
        $pic = ORM::factory($table, $id)->ic_pic;
        if (!$pic) die('404 Not Found!');
        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
