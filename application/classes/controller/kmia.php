<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kmia extends Controller_Base {

    public $template = 'weixin/kmi/tpl/atpl';
    public $pagesize = 10;
    public $config;
    public $bid;
    public $access_token;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "kmi";
        $_SESSION =& Session::instance()->as_array();
        parent::before();

        $this->bid = $_SESSION['kmia']['bid'];
        $this->config = $_SESSION['kmia']['config'];
        $this->access_token=ORM::factory('kmi_login')->where('id', '=', $this->bid)->find()->access_token;
        //未登录
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/kmia/login');
            header('location:/kmia/login?from='.Request::instance()->action);
            exit;
        }
    }
    public function after() {
        if ($this->bid) {
            //$todo['users'] = ORM::factory('kmi_qrcode')->where('bid', '=', $this->bid)->count_all();
            //$todo['tickets'] = ORM::factory('kmi_qrcode')->where('bid', '=', $this->bid)->where('ticket', '<>', '')->count_all();

            //$todo['prizes'] = ORM::factory('kmi_order')->where('bid', '=', $this->bid)->where('status', '=', 0)->count_all();

            //$todo['all'] = $todo['prizes'] + $todo['users'];
            //$this->template->todo = $todo;
            $this->template->config = $this->config;
        }

        @View::bind_global('bid', $this->bid);
        parent::after();
    }

    public function action_index() {
        $this->action_login();
    }
    //卡密配置
    public function action_kmi(){

        $bid = $this->bid;
        // echo $bid.'<br>';
        $config = ORM::factory('kmi_cfg')->getCfg($bid);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            $total_num=$result['total_num'];
            //echo $total_num."<br>";
            $num=floor($total_num/50)+1;
            //echo $num."<br>";
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) { 
                //echo $last_num."<br>";
                $result=$we->getCardIdList($last_num,50);
                // echo '<pre>';
                // var_dump($result);
                // echo '</pre>';
                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$we->getCardInfo($card_id);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;
               
            }  
            // echo '<pre>';
            // var_dump($wxcards);
            // echo '</pre>';
        }
        // echo '<pre>';
        // var_dump($wxcards);
        // echo '<pre>';
        // echo '------------<br>';
        //拉取有赞优惠券优惠码  title       group_id
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        // echo '<pre>';
        // var_dump($yzcoupons);
        // echo '<pre>';
        // echo '------------<br>';
        // //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        // echo '<pre>';
        // var_dump($yzgifts);
        // echo '<pre>';
        // echo '------------<br>';
        // exit();
        if ($_POST['hongbao']) {
            $text = $_POST['hongbao']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid= $this->bid;
            $prize->key= 'hongbao';
            $prize->value =$_POST['hongbao']['value'];
            $prize->km_num =$_POST['hongbao']['km_num'];
            $prize->km_num1 =$_POST['hongbao']['km_num'];
            $prize->km_content= $_POST['hongbao']['km_content'];
            $prize->km_text=$text;
            $prize->km_limit=$_POST['hongbao']['km_limit'];
            $prize->startdate=time();
            if($_POST['hongbao']['enddate']){
                $prize->enddate=strtotime($_POST['hongbao']['enddate']);
                if (time()>strtotime($_POST['hongbao']['enddate'])) $result['error']['hongbao'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['hongbao']['km_num'] || !$_POST['hongbao']['value']) $result['error']['hongbao'] = '请填写完整后再提交（请在基础设置-微信参数，将支付商户号、API密钥、证书填写后再设置微信红包奖品）';
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        if ($_POST['coupon']) {
            $text = $_POST['coupon']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid =$this->bid;
            $prize->key= 'coupon';
            $prize->value =$_POST['coupon']['value'];
            $prize->km_num =$_POST['coupon']['km_num'];
            $prize->km_num1 =$_POST['coupon']['km_num'];
            $prize->km_content=$_POST['coupon']['km_content'];
            $prize->km_text= $text;
            $prize->km_limit=$_POST['coupon']['km_limit'];
            $prize->startdate=time();
            if($_POST['coupon']['enddate']){
                $prize->enddate=strtotime($_POST['coupon']['enddate']);
                if (time()>strtotime($_POST['coupon']['enddate'])) $result['error']['coupon'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['coupon']['km_num'] || !$_POST['coupon']['value']) $result['error']['coupon'] = '请填写完整后再提交';
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        if ($_POST['gift']) {
            $text = $_POST['gift']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid=$this->bid;
            $prize->key= 'gift';
            $prize->value =$_POST['gift']['value'];
            $prize->km_num =$_POST['gift']['km_num'];
            $prize->km_num1 =$_POST['gift']['km_num'];
            $prize->km_content=$_POST['gift']['km_content'];
            $prize->km_text= $text;
            $prize->km_limit=$_POST['gift']['km_limit'];
            $prize->startdate=time();
            if($_POST['gift']['enddate']){
                $prize->enddate=strtotime($_POST['gift']['enddate']);
                if (time()>strtotime($_POST['gift']['enddate'])) $result['error']['gift'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['gift']['km_num'] || !$_POST['gift']['value']) $result['error']['gift'] = '请填写完整后再提交';
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        if ($_POST['yhq']) {
            $text = $_POST['yhq']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid=$this->bid;
            $prize->key= 'yzcoupon';
            $prize->value =$_POST['yhq']['value'];
            $prize->km_num =$_POST['yhq']['km_num'];
            $prize->km_num1 =$_POST['yhq']['km_num'];
            $prize->km_content=$_POST['yhq']['km_content'];
            $prize->km_text=$text;
            $prize->km_limit=$_POST['yhq']['km_limit'];
            $prize->startdate=time();
            if($_POST['yhq']['enddate']){
                $prize->enddate=strtotime($_POST['yhq']['enddate']);
                if (time()>strtotime($_POST['yhq']['enddate'])) $result['error']['yhq'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['yhq']['km_num'] || !$_POST['yhq']['value']) $result['error']['yhq'] = '请填写完整后再提交';
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        if ($_POST['freedom']) {
            $text = $_POST['freedom']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid=$this->bid;
            $prize->key= 'freedom';
            $prize->km_content=$_POST['freedom']['km_content'];
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize->km_text= $text;
            $prize->startdate=time();
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        if ($_POST['kmi']){
            $text = $_POST['kmi']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('kmi_prize');
            $prize->bid=$this->bid;
            $prize->key= 'kmi';
            $prize->value=time();
            $prize->km_num =$_POST['kmi']['km_num'];
            $prize->km_num1 =$_POST['kmi']['km_num'];
            $prize->km_content=$_POST['kmi']['km_content'];
            $prize->km_text=$text;
            $prize->km_limit=$_POST['kmi']['km_limit'];
            $time = time();
            $prize->startdate=$time;
            if($_POST['kmi']['enddate']){
                $prize->enddate=strtotime($_POST['kmi']['enddate']);
                if (time()>strtotime($_POST['kmi']['enddate'])) $result['error']['kmi'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['kmi']['km_num'] || !is_uploaded_file($_FILES['pic']['tmp_name'])) $result['error']['kmi'] = '请填写完整后再提交';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error']['kmi'] ='不是Excel文件，重新上传';
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
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    echo '<pre>';
                    //var_dump($dataset);
                    echo '</pre>';
                    //exit;
                    //$km =ORM::factory('kmi_km');
                    foreach ( $dataset as $data ) {
                        if(!isset($data["A"])&&!isset($data["B"])&&!isset($data["C"])){
                            continue;
                        }
                        $km =ORM::factory('kmi_km');
                        $km->bid = $this->bid;
                        $km->startdate = $time;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
            }
            if (!$result['error']) {
                $prize->save();
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('kmia/prizes');
            }
        }
        $this->template->title = '奖品配置';
        $this->template->content = View::factory('weixin/kmi/admin/kmi')
            ->bind('wxcards', $wxcards)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('result', $result);
    }
    public function action_items1(){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        //require_once Kohana::find_file('vendor/kdt','KdtApiClient');
        $bid = $this->bid;
        $config = ORM::factory('kmi_cfg')->getCfg($bid);
        //$yz['appid'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appid')->find()->value;
        //$yz['appsecret'] = ORM::factory('kmi_cfg')->where('bid', '=', $bid)->where('key','=','yz_appsecret')->find()->value;
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $pg=1;
            $method = 'kdt.items.onsale.get';
            $params =[
                //'fields' =>'total_results',
            ];
            $total_result= $client->post($method, '1.0.0', $params, $files);
            $total =$total_result['response']['total_results'];
            $a = ceil($total/100);
            for($k=0;$k<$a;$k++){
                $method = 'kdt.items.onsale.get';
                $params = array(
                    'page_size'=>100,
                    'page_no'=>$k+1,
                    'fields' => 'num_iid,title,price,pic_url,num,sold_num',
                    );
                $results = $client->post($method, '1.0.0', $params, $files);
                for($i=0;$results['response']['items'][$i];$i++){
                    $res=$results['response']['items'][$i];
                    $num_iid=$res['num_iid'];
                    $name=$res['title'];
                    $price=$res['price'];
                    $pic=$res['pic_url'];
                    $num=$res['num'];
                    $sold_num=$res['sold_num'];
                    $num_num = ORM::factory('kmi_item')->where('num_iid', '=', $num_iid)->count_all();
                    if($num_num==0 && $num_iid!=""){
                        $sql = DB::query(Database::INSERT,"INSERT INTO `kmi_items` (`bid`,`num_iid`,`name`,`price`, `pic`,`num`,`sold_num`,`state`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic',$num,$sold_num,1)");
                        $sql->execute();
                    }else{
                        $sql = DB::query(Database::UPDATE,"UPDATE `kmi_items` SET `bid` = $bid ,`num_iid` = $num_iid,`name` ='$name',`price`=$price, `pic`='$pic',`num`=$num,`sold_num`=$sold_num ,`state` = 1 where `num_iid` = $num_iid ");
                        $sql->execute();
                    }
                    $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from kmi_tids where `bid` = $bid and `num_iid` = $num_iid ");
                    $result =$sql->execute()->as_array();
                    $sum = $result[0]['sum'];
                    $send_num=ORM::factory('kmi_item')->where('num_iid','=',$num_iid)->where('bid','=',$bid)->find();
                    $send_num->send_num = $sum;
                    $send_num->save();
                }

            }
            $sql = DB::query(Database::DELETE,"DELETE FROM `kmi_items` where `state` =0 and `bid` = $bid ");
            $sql->execute();
            $sql = DB::query(Database::UPDATE,"UPDATE `kmi_items` SET `state` =0 where `bid` = $bid");
            $sql->execute();


        }

        Request::instance()->redirect('kmia/items');
    }
    public function action_items(){

        //require_once Kohana::find_file('vendor/kdt','KdtApiClient');
        // require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        // if($this->access_token){
        //     $client = new YZTokenClient();
        // }else{
        //     Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        // }
        $bid = $this->bid;
        $config = ORM::factory('kmi_cfg')->getCfg($bid);

        if ($_GET['export']=='csv') {
            $value1=$_GET['value1'];
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $order1 =ORM::factory('kmi_km')->where('bid','=',$this->bid)->where('startdate','=',$value1)->find();
            if($order1->password3){
                $title = array('字段1', '字段2', '字段3','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('kmi_km')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->password2, $o->password3, $o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            }elseif ($order1->password2) {
                $title = array('字段1', '字段2','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('kmi_km')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->password2,$o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            } else{
                $title = array('字段1','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('kmi_km')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
                foreach ($order as $o) {
                    $array = array($o->password1, $o->live);
                    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                        foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                    }
                    fputcsv($fp, $array);
                }
            }
            exit;
        }
        if(isset($_POST['cfg'])){
            $cfg = $_POST['cfg'];
            $item = ORM::factory('kmi_item')->where('id','=',$cfg['item'])->find();
            $item->pid=$cfg['pid'];
            $item->save();
        }
        if(isset($_POST['delete'])){
            $delete = $_POST['delete'];
            $item = ORM::factory('kmi_item')->where('id','=',$delete['id'])->find();
            $item->pid=0;
            $item->save();
        }
        $items = ORM::factory('kmi_item')->where('bid','=',$bid);
        $items = $items->reset(FALSE);
        $pri = ORM::factory('kmi_prize')->where('bid','=',$bid);
        $pri = $pri->reset(FALSE);
        if ($_POST['s']) {
            $items = $items->and_where_open();
            $result['s'] = $_POST['s'];
            $s = '%'.trim($_POST['s'].'%');
            $items = $items->where('name', 'like', $s);
            $items = $items->and_where_close();
        }


        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $items->count_all(),
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/kmi/admin/pages');
        $result['items'] = $items->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['pri'] = $pri->order_by('id', 'DESC')->find_all();
        $this->template->title = '发送配置';
        $this->template->content = View::factory('weixin/kmi/admin/items')
            ->bind('result', $result)->bind('pages', $pages);
    }
    public function action_prizes_edit($id){
        $bid = $this->bid;
        $config = ORM::factory('kmi_cfg')->getCfg($bid,1);
        require_once Kohana::find_file("vendor/kdt","YZTokenClient");
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');//
        require_once Kohana::find_file('vendor', 'weixin/inc');
        if($config['appid']){
            $we = new Wechat($config);
            $result=$we->getCardIdList();
            $total_num=$result['total_num'];
            //echo $total_num."<br>";
            $num=floor($total_num/50)+1;
            //echo $num."<br>";
            $last_num=0;
            $a=0;
            for ($i=0; $i < $num; $i++) { 
                //echo $last_num."<br>";
                $result=$we->getCardIdList($last_num,50);
                // echo '<pre>';
                // var_dump($result);
                // echo '</pre>';
                if($result['errmsg']=='ok'){
                    foreach ($result['card_id_list'] as $card_id) {
                        $wxcards[$a]['id']=$card_id;
                        $infos=$we->getCardInfo($card_id);
                        if($infos['errmsg']=='ok'){
                            if($infos['card']['card_type']=='DISCOUNT'){
                                $base_info=$infos['card']['discount']['base_info'];
                            }elseif($infos['card']['card_type']=='GENERAL_COUPON'){
                                $base_info=$infos['card']['general_coupon']['base_info'];
                            }elseif($infos['card']['card_type']=='CASH'){
                                $base_info=$infos['card']['cash']['base_info'];
                            }elseif($infos['card']['card_type']=='MEMBER_CARD'){
                                $base_info=$infos['card']['member_card']['base_info'];
                            }elseif($infos['card']['card_type']=='GIFT'){
                                $base_info=$infos['card']['gift']['base_info'];
                            }
                            $title=$base_info['title'];
                            $wxcards[$a]['title']=$title;
                        }
                        $a++;
                    }
                }
                $last_num+=50;
               
            }  
            // echo '<pre>';
            // var_dump($wxcards);
            // echo '</pre>';
        }
        //赠品  present_id title
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.presents.ongoing.all';
            $params = [
                'fields'=>"present_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzgifts=$results['response']['presents'];
        }
        //拉取有赞优惠券优惠码  title       group_id
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
            $method = 'youzan.ump.coupons.unfinished.search';
            $params = [
                'fields'=>"group_id,title"
            ];
            $results = $client->post($method, $this->methodVersion, $params, $files);
            $yzcoupons=$results['response']['coupons'];

        }
        $prize1 = ORM::factory('kmi_prize')->where('bid','=',$this->bid)->where('id','=',$id)->find()->as_array();
        $this->template->title = '修改';
        if(isset($_POST['edit'])){
            $prize = ORM::factory('kmi_prize')->where('bid','=',$this->bid)->where('id','=',$id)->find();
            $edit = $_POST['edit'];
            if($edit['key'] == 'kmi'){
                $text = $edit['km_text'];
                $text = str_replace("<p>",'', $text);
                $text = str_replace("</p>",'',$text);
                $text = str_replace("target=\"_blank\"",'',$text);
                if($edit['enddate']) $prize->enddate=strtotime($edit['enddate']);//时间戳不存在不存
                $prize->km_content=$edit['km_content'];
                $prize->km_limit=$edit['km_limit'];
                $prize->km_text= $text;
                $prize->save();
                if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                    $tmp_file = $_FILES ['pic'] ['tmp_name'];
                    $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                    $file_type = $file_types [count ( $file_types ) - 1];
                     /*判别是不是.xls文件，判别是不是excel文件*/
                    if (strtolower ( $file_type ) != "xls"){
                        $result['error']['kmi'] ='不是Excel文件，重新上传';
                    }else{
                        require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                        $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                        $aaa=$savePath.$tmp_file;
                        $PHPExcel = $reader->load($aaa); // 载入excel文件
                        $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                        $highestRow = $sheet->getHighestRow(); // 取得总行数
                        echo $highestRow.'<br>';
                        $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                        echo $highestColumm.'<br>';
                        // /** 循环读取每个单元格的数据 */
                        for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                            for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                                $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                            }
                        }
                        // echo '<pre>';
                        //var_dump($dataset);
                        // echo '</pre>';
                        //exit;
                        //$km =ORM::factory('kmi_km');
                        foreach ( $dataset as $data ) {
                            if(!isset($data["A"])&&!isset($data["B"])&&!isset($data["C"])){
                                continue;
                            }
                            $km =ORM::factory('kmi_km');
                            $km->bid = $this->bid;
                            $km->startdate = $prize->value;
                            if(isset($data["A"])) $km->password1 = $data["A"];
                            if(isset($data["B"])) $km->password2 = $data["B"];
                            if(isset($data["C"])) $km->password3 = $data["C"];
                            $km->save();
                        }
                    }
                }
                Request::instance()->redirect('kmia/prizes');
            }else{
                if($edit['enddate']) $prize->enddate=strtotime($edit['enddate']);//时间戳不存在不存
                $text = $edit['km_text'];
                $text = str_replace("<p>",'', $text);
                $text = str_replace("</p>",'',$text);
                $text = str_replace("target=\"_blank\"",'',$text);
                $prize->km_content=$edit['km_content'];
                $prize->km_num1=$prize->km_num1+$edit['km_num'];
                $prize->km_num=$prize->km_num+$edit['km_num'];
                $prize->km_limit=$edit['km_limit'];
                if($edit['key']=='coupon'){
                    $edit['value']=$edit['coupon'];
                }elseif($edit['key']=='yzcoupon'){
                    $edit['value']=$edit['yzcoupon'];
                }elseif($edit['key']=='gift'){
                    $edit['value']=$edit['gift'];
                }
                if(!$edit['value']&&$edit['key'] != 'freedom'){
                    $result['error']='奖品内容不完整，提交失败';
                }
                Kohana::$log->add('values',print_r($edit,true));
                if($edit['key'] != 'freedom'){
                   $prize->value=$edit['value']; 
                }
                $prize->km_text=$text;
                if(!$result['error']){
                    $prize->save();
                    Request::instance()->redirect('kmia/prizes');
                }
            }
        }
        $this->template->content = View::factory('weixin/kmi/admin/edit')
            ->bind('wxcards', $wxcards)
            ->bind('result',$result)
            ->bind('yzcoupons', $yzcoupons)
            ->bind('yzgifts', $yzgifts)
            ->bind('prize', $prize1);
    }
    //卡密记录
    public function action_orders(){
        $bid = $this->bid;
        $config = ORM::factory('kmi_cfg')->getCfg($bid);
        $order = ORM::factory('kmi_tid')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if ($_GET['s']) {
            $order = $order->and_where_open();
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $order = $order->where('name', 'like', $s)->or_where('tradename', 'like', $s)->or_where('km_type', 'like', $s);
            $order = $order->and_where_close();
        }
        $countall = $order->count_all();
        //下载
        if ($_GET['export']=='csv') {
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $title = array('id', '订单号', '商品id', '时间', '姓名', '商品名称', '商品价格','数量','卡密类型', '卡密内容', '卡密状态');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = ORM::factory('kmi_tid')->where('bid', '=', $bid)->find_all();
            foreach ($order as $o) {
                $array = array($o->id, $o->tid, $o->num_iid, $o->time, $o->name, $o->tradename, $o->price,$o->num, $o->km_type,$o->km_comtent, $o->state);
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) {
                    //非 Mac 转 gbk
                    foreach ($array as $k=>$v) $array[$k] = iconv('utf-8', 'gbk', $v);
                }
                fputcsv($fp, $array);
            }
            exit;
        }

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/kmi/admin/pages');

        $result['orders'] = $order->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '发送记录';
        $this->template->content = View::factory('weixin/kmi/admin/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_prizes_delete($id){
        $value =ORM::factory('kmi_prize')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('kmi_prize')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `kmi_prizes` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        if($type == 'kmi'){
            $sql = DB::query(Database::DELETE,"DELETE FROM `kmi_kms` where `bid` = $this->bid and `startdate` = $value");
            $sql->execute();
        }
        Request::instance()->redirect('kmia/prizes');
    }
    //卡密管理
    public function action_prizes(){
        $bid = $this->bid;
        $config = ORM::factory('kmi_cfg')->getCfg($bid);

        $prize =ORM::factory('kmi_prize')->where('bid', '=', $bid)->where('key','!=','');
        $prize = $prize->reset(FALSE);
        $num =ORM::factory('kmi_prize')->where('bid', '=', $bid)->where('key','!=','')->count_all();

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/kmi/admin/pages');
        $result['prizes'] = $prize->order_by('km_num', 'DESC')->limit($this->pagesize)->offset($offset)->find_all()->as_array();
        $this->template->title = '添加发送奖品';
        $this->template->content = View::factory('weixin/kmi/admin/prizes')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;

        $config = ORM::factory('kmi_cfg')->getCfg($bid, 1);

        //密码修改
        if ($_POST['password'] && $_POST['newpassword']) {
            $biz = ORM::factory('kmi_login', $bid);
            $old_password = $biz->pass;
            if ($old_password != $_POST['password']) $result['err4'] = '旧密码不正确！';
            if ($_POST['newpassword'] != $_POST['newpassword2']) $result['err4'] = '两次输入的新密码不匹配！';

            if (!$result['err4']) {
                $biz->pass = $_POST['newpassword'];
                $biz->save();
                $result['ok4'] = 1;
            }
        }
        $cert_file = DOCROOT."kmi/tmp/$bid/cert.{$config['appsecret']}.pem";
        $key_file = DOCROOT."kmi/tmp/$bid/key.{$config['appsecret']}.pem";
        $result['cert_file_exists'] = file_exists($cert_file);
        $result['key_file_exists'] = file_exists($key_file);
        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('kmi_cfg');

            foreach ($_POST['cfg'] as $k=>$v) {

                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
            //证书上传
            if ($_FILES['cert']['error'] == 0) {
                @mkdir(dirname($cert_file));
                $ok = move_uploaded_file($_FILES['cert']['tmp_name'], $cert_file);
                // $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if ($_FILES['key']['error'] == 0) {
                @mkdir(dir($key_file));
                $ok = move_uploaded_file($_FILES['key']['tmp_name'], $key_file);
                // $result['ok'] += $ok;
                $result['err1'] = '证书文件已更新！';
            }

            if (file_exists($cert_file)) $cfg->setCfg($bid, 'fxb_file_cert', '', file_get_contents($cert_file));
            if (file_exists($key_file)) $cfg->setCfg($bid, 'fxb_file_key', '', file_get_contents($key_file));
            //重新读取配置
            $config = ORM::factory('kmi_cfg')->getCfg($bid, 1);
        }
        $access_token = ORM::factory('kmi_login')->where('id', '=', $bid)->find()->access_token;

        if(!$access_token){
            $oauth=1;
        }
        $this->template->title = '首页';
        $this->template->content = View::factory('weixin/kmi/admin/home')
            ->bind('oauth',$oauth)
            ->bind('result', $result)
            ->bind('config', $config);
    }
     //有赞授权刷新脚本 七天一次
    public function action_oauthscript($bid=39){
        $shop = ORM::factory('kmi_login')->where('id','=',$bid)->find();
        $url="https://open.youzan.com/oauth/token";
        if($shop->access_token&&$shop->id){
            $data=array(
                "client_id"=>"b8da602aa7006efe50",
                "client_secret"=>"265c5e4a2b4af7f96b15e1df1f45c478",
                "grant_type"=>"refresh_token",
                "refresh_token"=>$shop->refresh_token
            );
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $output = curl_exec($ch);
            curl_close($ch);
            $result=json_decode($output);
            $shop->access_token = $result->access_token;
            $shop->expires_in = time()+$result->expires_in;
            $shop->refresh_token = $result->refresh_token;
            $shop->save();
            echo '<pre>';
            var_dump($result);
            echo '刷新 token 成功';
            echo '</pre>';
            exit;
        }else{
            die('no id or no access_token!');
        }
    }
    public function action_oauth(){

        Request::instance()->redirect('https://open.youzan.com/oauth/authorize?client_id=b8da602aa7006efe50&response_type=code&state=teststate&redirect_uri=http://'.$_SERVER["HTTP_HOST"].'/kmia/callback');
    }
     //回调获取 商户信息
    public function action_callback(){
        $url="https://open.youzan.com/oauth/token";
        if(isset($_GET["code"]))
        {
            $code=$_GET["code"];
        }
        $data=array(
            "client_id"=>"b8da602aa7006efe50",
            "client_secret"=>"265c5e4a2b4af7f96b15e1df1f45c478",
            "grant_type"=>"authorization_code",
            "code"=>$code,
            "redirect_uri"=>'http://'.$_SERVER["HTTP_HOST"].'/kmia/callback'
        );
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $result=json_decode($output);

        if(isset($result->access_token))
        {
            require Kohana::find_file("vendor","kdt/YZTokenClient");
            $oauth=new YZTokenClient($result->access_token);
            $value=$oauth->get('youzan.shop.get',$this->methodVersion)["response"];//获取用户基本信息
            //var_dump($value);
            $sid = $value['id'];
            $name = $value['name'];

            $usershop = ORM::factory('kmi_login')->where('id','=',$this->bid)->find();
            $usershop->access_token = $result->access_token;
            $usershop->expires_in = time()+$result->expires_in;
            $usershop->refresh_token = $result->refresh_token;
            $usershop->shopid = $sid;
            $usershop->save();
            echo "<script>alert('授权成功');location.href='".URL::site("kmia/home")."';</script>";
        }
        //Request::instance()->redirect('kmia/home');
    }
    //用户管理
    public function action_logins($action='', $id=0) {
        if ($_SESSION['kmia']['admin'] < 1) Request::instance()->redirect('kmia/home');

        if ($action == 'add') return $this->action_logins_add();
        if ($action == 'edit') return $this->action_logins_edit($id);

        $logins = ORM::factory('kmi_login');
        $logins = $logins->reset(FALSE);

       if ($_GET['s']) {
            $result['s'] = $_GET['s'];
            $s = '%'.trim($_GET['s'].'%');
            $logins = $logins->where('user', 'like', $s)->or_where('name', 'like', $s);
        }
        $result['countall'] = $countall = $logins->count_all();

        //分页
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/kmi/admin/pages');

        $result['logins'] = $logins->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['title'] = $this->template->title = '账号管理';
        $this->template->content = View::factory('weixin/kmi/admin/logins')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_logins_add() {
        if ($_SESSION['kmia']['admin'] < 2) Request::instance()->redirect('kmia/home');

        $bid = $this->bid;

        if ($_POST['data']) {
            $login = ORM::factory('kmi_login');
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('kmi_login')->where('user', '=', $_POST['data']['user'])->count_all() > 0) $result['error'] = '该登录名已经存在';
            if (!$result['error']) {
                $login->pass = Text::random(NULL, 6);
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();
                Request::instance()->redirect('kmia/logins');
            }
        }

        $result['action'] = 'add';

        $result['title'] = $this->template->title = '添加用户';
        $this->template->content = View::factory('weixin/kmi/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_logins_edit($id) {
        if ($_SESSION['kmia']['admin'] < 2) Request::instance()->redirect('kmia/home');

        $bid = $this->bid;

        $login = ORM::factory('kmi_login', $id);
        if (!$login) die('404 Not Found!');

        $cfg = ORM::factory('kmi_cfg');

        if ($_GET['DELETE'] == 1) {
            Request::instance()->redirect('kmia/prizes');
        }

        if ($_POST['data']) {
            $login->values($_POST['data']);
            if (!$_POST['data']['name'] || !$_POST['data']['user']) $result['error'] = '请填写完整后再提交';
            if (ORM::factory('kmi_login')->where('user', '=', $_POST['data']['user'])->where('id', '<>', $id)->count_all() > 0)
                $result['error'] = '该登录名已经存在';

            if (!$result['error']) {
                if ($_POST['pass']) $login->pass = $_POST['pass'];
                $login->save();

                //appid 重置
                if ($_POST['data']['appid']) {
                    $ok = $cfg->setCfg($id, 'appid', $_POST['data']['appid']);
                }

                Request::instance()->redirect('kmia/logins');
            }
        }

        $cfgs = $cfg->getCfg($id, 1);
        $_POST['data'] = $result['login'] = $login->as_array();
        $_POST['data']['appid'] = $cfgs['appid'];

        $result['action'] = 'edit';

        $result['title'] = $this->template->title = '修改用户';
        $this->template->content = View::factory('weixin/kmi/admin/logins_add')
            ->bind('result', $result)
            ->bind('config', $config);
    }

    public function action_login() {
        $this->template = 'weixin/kmi/tpl/login';
        $this->before();
        $agent = $this->GetAgent();
        Session::instance()->set("agent",$agent);

        if ($_POST['username'] && $_POST['password']) {
            $biz = ORM::factory('kmi_login')->where('user', '=', $_POST['username'])->where('pass', '=', $_POST['password'])->find();

            if ($biz->id) {

                //判断账号是否到期
                if ($biz->expiretime && (strtotime($biz->expiretime)+86400) < time()) {
                    $this->template->error = '您的账号已到期';
                } else {

                    $_SESSION['kmia']['bid'] = $biz->id;
                    $_SESSION['kmia']['user'] = $_POST['username'];
                    $_SESSION['kmia']['admin'] = $biz->admin; //超管
                    $_SESSION['kmia']['config'] = ORM::factory('kmi_cfg')->getCfg($biz->id);

                    $biz->lastlogin = time();
                    $biz->logins++;
                    $biz->save();
                }
            } else {
                $this->template->error = '天王盖地虎';
            }
        }

        if ($_SESSION['kmia']['bid']) {
            if (!$_GET['from']) $_GET['from'] = 'home';
            header('location:/kmia/'.$_GET['from']);
            exit;
        }
    }

    public function action_logout() {
        $_SESSION['kmia'] = null;
        header('location:/kmia/home');
        exit;
    }



    private function GetAgent(){

            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is_pc = (strpos($agent, 'windows nt')) ? true : false;
            $is_mac = (strpos($agent, 'mac os')) ? true : false;
            $is_iphone = (strpos($agent, 'iphone')) ? true : false;
            $is_android = (strpos($agent, 'android')) ? true : false;
            $is_ipad = (strpos($agent, 'ipad')) ? true : false;

            $device="unknow";
            if($is_pc){
                  $device = 'pc';
            }

            if($is_mac){
                  $device = 'mac';
            }

            if($is_iphone){
                  $device = 'iphone';
            }

            if($is_android){
                  $device = 'android';
            }

            if($is_ipad){
                  $device = 'ipad';
            }

            return $device;
    }
}

