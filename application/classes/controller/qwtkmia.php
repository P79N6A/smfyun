<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Qwtkmia extends Controller_Base {

    public $template = 'weixin/qwt/tpl/kmiatpl';
    public $pagesize = 10;
    public $config;
    public $bid;
    public $access_token;
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
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',5)->where('expiretime','>',time())->where('status','=',1)->find()->id){
            if(Request::instance()->action == 'tplmsg'){
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
    //卡密配置
    public function action_test1(){
        $tid='aa';
        $iid=1234;
        $num=12;
        $bid=6;
        $trade_detail = ORM::factory('qwt_kmidetail')->where('bid','=',$bid)->where('tid','=',$tid)->where('iid','=',$iid)->find();
        $trade_detail->bid = $bid;
        $trade_detail->tid = $tid;
        $trade_detail->iid = $iid;
        $trade_detail->num = $num;//件数
        $trade_detail->save();
    }
    public function action_kmi(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        if ($_POST['kmi']){
            $text = $_POST['kmi']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize = ORM::factory('qwt_kmiprize');
            $prize->bid=$this->bid;
            $prize->key= 'kmi';
            $time = time();
            $prize->value=$time;
            $prize->type=8;
            $prize->km_content=$_POST['kmi']['km_content'];
            $prize->km_text=$text;
            $prize->startdate=$time;
            if($_POST['kmi']['enddate']){
                $prize->enddate=strtotime($_POST['kmi']['enddate']);
                if (time()>strtotime($_POST['kmi']['enddate'])) $result['error'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!is_uploaded_file($_FILES['pic']['tmp_name'])) $result['error'] = '请上传正确的卡密xls文件';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error'] ='不是Excel文件，重新上传';
                }
                // echo 'a<br>';
                // echo Model::factory('select_experience')->dopinion($bid,'kmi');
                // exit();
                if(!Model::factory('select_experience')->dopinion($bid,'kmi')){
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5');
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $num=Model::factory('select_experience')->selectnum($bid,'kmi');
                    // echo $num.'<br>';
                    // echo $highestRow.'<br>';
                    // exit();
                    if($highestRow>$num){
                        $result['error'] ='上传的卡密数量超过试用限定';
                    }
                }
            }
            if (!$result['error']) {
                $prize->save();
                if (is_uploaded_file($_FILES['pic']['tmp_name'])){
                   require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('qwt_kmikm');
                        $km->bid = $this->bid;
                        $km->startdate = $time;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtkmia/prizes');
            }
        }
        $this->template->title = '添加卡密';
        $result['title']='添加卡密';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/kmi')
            ->bind('result', $result);
    }
    public function action_items1(){
        require_once Kohana::find_file('vendor', 'kdt/YZTokenClient');
        if($this->access_token){
            $client = new YZTokenClient($this->access_token);
        }else{
            Kohana::$log->add("weixin2:$bid:bname", print_r('有赞参数未填', true));
        }
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        $admin=ORM::factory('qwt_login')->where('id','=',$bid)->find();
        if($admin->yzaccess_token){
            $client = new YZTokenClient($admin->yzaccess_token);
            $pg=1;
            $method = 'youzan.items.onsale.get';
            $params =[
                //'fields' =>'total_results',
            ];
            $total_result= $client->post($method, '3.0.0', $params, $files);
            $total =$total_result['response']['count'];
            $item_num=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->count_all();
            if($total!=$item_num||$_GET['refresh']==1){
                $a = $total/100;
            for($k=0;$k<$a;$k++){
                $method = 'youzan.items.onsale.get';
                $params = array(
                    'page_size'=>100,
                    'page_no'=>$k+1,
                    );
                $results = $client->post($method, '3.0.0', $params, $files);
                for($i=0;$results['response']['items'][$i];$i++){
                    $res=$results['response']['items'][$i];
                    $method = 'youzan.item.get';
                    $params = array(
                         'item_id'=>$res['item_id'],
                    );
                    $result = $client->post($method, '3.0.0', $params, $files);
                    $item=$result['response']['item'];
                    $num_iid=$item['item_id'];
                    $name=$item['title'];
                    $price=$item['price']/100;
                    $pic=$item['pic_url'];
                    $num=$item['quantity'];
                    $sold_num=$item['sold_num'];
                    $num_num = ORM::factory('qwt_kmiitem')->where('num_iid', '=', $num_iid)->count_all();
                    if($num_num==0 && $num_iid!=""){
                        $sql = DB::query(Database::INSERT,"INSERT INTO `qwt_kmiitems` (`bid`,`num_iid`,`name`,`price`, `pic`,`num`,`sold_num`,`state`) VALUES ($bid,$num_iid,'$name' ,$price,'$pic',$num,$sold_num,1)");
                        $sql->execute();
                    }else{
                        $sql = DB::query(Database::UPDATE,"UPDATE `qwt_kmiitems` SET `bid` = $bid ,`num_iid` = $num_iid,`name` ='$name',`price`=$price, `pic`='$pic',`num`=$num,`sold_num`=$sold_num ,`state` = 1 where `num_iid` = $num_iid ");
                        $sql->execute();
                    }
                    $sql = DB::query(Database::SELECT,"SELECT SUM(num) AS sum from qwt_kmitids where `bid` = $bid and `num_iid` = $num_iid ");
                    $result =$sql->execute()->as_array();
                    $sum = $result[0]['sum'];
                    $send_num=ORM::factory('qwt_kmiitem')->where('num_iid','=',$num_iid)->where('bid','=',$bid)->find();
                    $send_num->send_num = $sum;
                    $send_num->save();
                }
            }
            $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_kmiitems` where `state` =0 and `bid` = $bid ");
            $sql->execute();
            $sql = DB::query(Database::UPDATE,"UPDATE `qwt_kmiitems` SET `state` =0 where `bid` = $bid");
            $sql->execute();

            }
        }
        Request::instance()->redirect('qwtkmia/items');
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
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);

        if ($_GET['export']=='csv') {
            $value1=$_GET['value1'];
            $tempname="全部";
            $filename = 'ORDERS.'.$tempname. date('Ymd') .'.csv';
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$filename);
            $fp = fopen('php://output', 'w');
            $order1 =ORM::factory('qwt_kmikm')->where('bid','=',$this->bid)->where('startdate','=',$value1)->find();
            if($order1->password3){
                $title = array('字段1', '字段2', '字段3','状态');
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
                fputcsv($fp, $title);
                $order = ORM::factory('qwt_kmikm')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
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
                $order = ORM::factory('qwt_kmikm')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
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
                $order = ORM::factory('qwt_kmikm')->where('bid', '=', $this->bid)->where('startdate','=',$value1)->find_all();
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
            $item = ORM::factory('qwt_kmiitem')->where('id','=',$cfg['item'])->find();
            $item->pid=$cfg['pid'];
            $item->save();
        }
        if(isset($_POST['delete'])){
            $delete = $_POST['delete'];
            $item = ORM::factory('qwt_kmiitem')->where('id','=',$delete['id'])->find();
            $item->pid=0;
            $item->save();
        }
        $items = ORM::factory('qwt_kmiitem')->where('bid','=',$bid);
        $items = $items->reset(FALSE);
        $pri = ORM::factory('qwt_kmiprize')->where('bid','=',$bid);
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
        ))->render('weixin/qwt/admin/kmi/pages');
        $result['items'] = $items->order_by('id', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();

        $result['pri'] = $pri->order_by('id', 'DESC')->find_all()->as_array();
        $this->template->title = '发送配置';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/items')
            ->bind('result', $result)
            ->bind('bid', $bid)
            ->bind('pages', $pages);
    }
    public function action_prizes_edit($id){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid,1);
        $prize=ORM::factory('qwt_kmiprize')->where('bid','=',$bid)->where('id','=',$id)->find();
        if ($_POST['kmi']){
            $text = $_POST['kmi']['km_text'];
            $text = str_replace("<p>",'', $text);
            $text = str_replace("</p>",'',$text);
            $text = str_replace("target=\"_blank\"",'',$text);
            $prize->bid=$this->bid;
            $prize->key= 'kmi';
            $prize->type=8;
            $prize->km_content=$_POST['kmi']['km_content'];
            $prize->km_text=$text;
            if($_POST['kmi']['enddate']){
                $prize->enddate=strtotime($_POST['kmi']['enddate']);
                if (time()>strtotime($_POST['kmi']['enddate'])) $result['error'] ='兑换截止时间在当前时间之前，无效！！';
            }
            if (!$_POST['kmi']['km_content']) $result['error'] = '请填写完整后再提交';
            if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                $tmp_file = $_FILES ['pic'] ['tmp_name'];
                $file_types = explode ( ".", $_FILES ['pic'] ['name'] );
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls"){
                    $result['error'] ='不是Excel文件，重新上传';
                }
                // echo 'a<br>';
                // echo Model::factory('select_experience')->dopinion($bid,'kmi');
                // exit();
                if(!Model::factory('select_experience')->dopinion($bid,'kmi')){
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5');
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $num=Model::factory('select_experience')->selectnum($bid,'kmi');
                    // echo $num.'<br>';
                    // echo $highestRow.'<br>';
                    // exit();
                    if($highestRow>$num){
                        $result['error'] ='上传的卡密数量超过试用限定';
                    }
                }
            }
            if (!$result['error']) {
                $prize->save();
                if (is_uploaded_file($_FILES['pic']['tmp_name'])) {
                    require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
                    $aaa = $tmp_file;
                    $PHPExcel = $reader->load($aaa); // 载入excel文件
                    $sheet = $PHPExcel->getActiveSheet(); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    // echo $highestRow.'<br>';
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数
                    // echo $highestColumm.'<br>';
                    // /** 循环读取每个单元格的数据 */
                    for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                        for ($column = 'A'; $column <= $highestColumm; $column++) {//列数是以A列开始
                            $dataset[$row][$column]= $sheet->getCell($column.$row)->getValue();
                        }
                    }
                    foreach ( $dataset as $data ) {
                        $km =ORM::factory('qwt_kmikm');
                        $km->bid = $this->bid;
                        $km->startdate = $prize->value;
                        if(isset($data["A"])) $km->password1 = $data["A"];
                        if(isset($data["B"])) $km->password2 = $data["B"];
                        if(isset($data["C"])) $km->password3 = $data["C"];
                        $km->save();
                    }
                }
                $mem = Cache::instance('memcache');
                $key = "kmi:prizes:{$this->bid}";
                $mem->delete($key);
                Request::instance()->redirect('qwtkmia/prizes');
            }
        }
        $prize1 = ORM::factory('qwt_kmiprize')->where('bid','=',$this->bid)->where('id','=',$id)->find();
        $_POST['kmi']=$prize1->as_array();
        $this->template->title = '修改卡密';
        $result['title']='修改卡密';
        $act='edit';
        $result['id']=$id;
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/kmi')
            ->bind('bid',$bid)
            ->bind('result',$result)
            ->bind('act',$act);
    }
    public function action_kmi_again($oid){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid,1);
        $tpl=$config['kmitpl'];
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $options['token'] = $this->token;
        $options['encodingaeskey'] = $this->encodingAesKey;
        $options['appid'] = ORM::factory('qwt_login')->where('id','=',$bid)->find()->appid;
        if($options['appid']){
           $wx = new Wxoauth($this->bid,$options);
        }
        $order=ORM::factory('qwt_kmitid')->where('id','=',$oid)->find();
        $openid=$order->openid;
        $num_iid=$order->num_iid;
        $key = ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->key;
        $value=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->value;
        $num=$order->residue;
        $kmi_num=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$value)->where('live','=',1)->where('flag','=',1)->count_all();
        if($kmi_num>=$num){
            if($key=='kmi'){
                for ($i=0; $i < $num; $i++) {
                    do {
                        $sql = DB::query(Database::SELECT,"SELECT * FROM qwt_kmikms where `live`=1 and `flag`=1 and `startdate`= $value  and `bid`= $bid");
                        $kmikm = $sql->execute()->as_array();
                        $keyname="qkmi_id:{$bid}:{$kmikm[0]['id']}";
                        if(!$kmikm[0]['id']){
                            return;
                        }
                        $m->add($keyname,$kmikm[0]['id'],5);
                    } while ($m->getResultCode() != Memcached::RES_SUCCESS);
                    $password1=$kmikm[0]['password1'];
                    $password2=$kmikm[0]['password2'];
                    $password3=$kmikm[0]['password3'];
                    $id =$kmikm[0]['id'];
                    $msgs =ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->prize->km_text;
                    $msgs = str_replace("「%a」",$password1,$msgs);
                    $password = $password1;
                    if($password2){
                        $msgs = str_replace("「%b」",$password2,$msgs);
                        $password = $password.','.$password2;
                        if($password3){
                            $msgs = str_replace("「%c」",$password3,$msgs);
                            $password = $password.','.$password3;
                        }
                    }
                    if(isset($tpl)){
                        $tplmsg['touser'] = $openid;
                        $tplmsg['template_id'] = $tpl;
                        $tplmsg['url'] = 'http://'.$_SERVER['HTTP_HOST'].'/qwtkmi/kmpass/'.$id.'/'.$bid.'/'.$num_iid;
                        $tplmsg['data']['keyword1']['value'] = '卡密';
                        $tplmsg['data']['keyword1']['color'] = '#999999';
                        $tplmsg['data']['remark']['value'] = $msgs;
                        $tplmsg['data']['remark']['color'] = '#999999';
                        $a=$wx->sendTemplateMessage($tplmsg);
                    }else{
                        $msg['msgtype'] = 'text';
                        $msg['touser'] = $openid;
                        $msg['text']['content'] = $msgs;
                        $a=$wx->sendCustomMessage($msg);
                    }
                    if($a['errmsg']=='ok'){
                        $pid=ORM::factory('qwt_kmiitem')->where('bid','=',$bid)->where('num_iid','=',$num_iid)->find()->pid;
                        $tids=ORM::factory('qwt_kmitid')->where('id','=',$oid)->find();
                        $tids->pid=$pid;
                        $tids->kid=$id;
                        $tids->state=1;
                        $tids->residue--;
                        if($tids->km_comtent){
                            $tids->km_comtent=$tids->km_comtent.'<br>'.$password;
                            Kohana::$log->add('cxb:kmiesult', print_r($tids->km_comtent.'<br>'.$password, true));
                        }else{
                            $tids->km_comtent=$password;
                            Kohana::$log->add('cxb:kmiesult1', print_r($password, true));
                        }
                        $tids->save();
                        $sql = DB::query(Database::UPDATE,"UPDATE  `qwt_kmikms` set `live`=0 where `id`= $id");
                        $sql->execute();
                    }else{
                        $tids=ORM::factory('qwt_kmitid')->where('id','=',$oid)->find();
                        if(isset($a['errmsg'])){
                            $tids->log = $a['errmsg'];
                        }else{
                            $tids->log = $a;
                        }
                        $tids->save();
                    }
                }

            }
        }else{
            $order->log='卡密库存不够';
            $order->save();
        }
        Request::instance()->redirect('qwtkmia/orders');
    }
    //卡密记录
    public function action_orders(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        $order = ORM::factory('qwt_kmitid')->where('bid', '=', $bid);
        $order = $order->reset(FALSE);
        if($_GET['pid']){
            $order = $order->where('pid','=',$_GET['pid']);
        }
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
            $title = array('id', '昵称','订单号', '商品id', '时间', '收货人', '商品名称', '商品价格','数量','卡密类型', '卡密内容', '卡密状态');
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh') == false) foreach ($title as $k=>$v) $title[$k] = iconv('utf-8', 'gbk', $v);
            fputcsv($fp, $title);
            $order = ORM::factory('qwt_kmitid')->where('bid', '=', $bid)->find_all();
            foreach ($order as $o) {
                $array = array($o->id,$o->nikename, $o->tid, $o->num_iid, $o->time, $o->name, $o->tradename, $o->price,$o->num, $o->km_type,$o->km_comtent, $o->state);
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
        ))->render('weixin/qwt/admin/kmi/pages');

        $result['orders'] = $order->order_by('time', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '发送记录';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/orders')
            ->bind('pages', $pages)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_prizes_delete($id){
        $value =ORM::factory('qwt_kmiprize')->where('bid','=',$this->bid)->where('id','=',$id)->find()->value;
        $type =ORM::factory('qwt_kmiprize')->where('bid','=',$this->bid)->where('id','=',$id)->find()->key;
        $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_kmiprizes` where `bid` = $this->bid and `id` = $id");
        $sql->execute();
        if($type == 'kmi'){
            $sql = DB::query(Database::DELETE,"DELETE FROM `qwt_kmikms` where `bid` = $this->bid and `startdate` = $value");
            $sql->execute();
        }
        Request::instance()->redirect('qwtkmia/prizes');
    }
    public function action_kmi_delete($pid,$id){
        $kmi=ORM::factory('qwt_kmikm')->where('id','=',$id)->find();
        if($kmi->live==0){
            $code='fail';
        }else{
            $kmi->flag=0;
            $kmi->save();
            $code='success';
        }
        Request::instance()->redirect('qwtkmia/kmi_detail/'.$pid.'/'.$code);
    }
    public function action_kmi_recover($id){
        $kmi=ORM::factory('qwt_kmikm')->where('id','=',$id)->find();
        $kmi->flag=1;
        $kmi->save();
        Request::instance()->redirect('qwtkmia/recycle');
    }
    public function action_kmi_realdelete($id){
        $kmi=ORM::factory('qwt_kmikm')->where('id','=',$id)->find();
        $kmi->delete();
        Request::instance()->redirect('qwtkmia/recycle');
    }
    //卡密管理
    public function action_prizes(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);

        $prize =ORM::factory('qwt_kmiprize')->where('bid', '=', $bid)->where('key','=','kmi');
        $prize = $prize->reset(FALSE);
        if ($_POST['s']) {
            //$prize = $prize->and_where_open();
            $result['s'] = $_POST['s'];
            $s = '%'.trim($_POST['s'].'%');
            $prize = $prize->where('km_content', 'like', $s);
            //$order = $order->and_where_close();
        }
        if ($_GET['export']=='xls') {
            $prize=ORM::factory('qwt_kmiprize')->where('id','=',$_GET['pid'])->find();
            $kms=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$prize->value)->where('live','=',1)->where('flag','=',1)->find_all();
            require_once Kohana::find_file("vendor/kdt","Classes/PHPExcel");
            require_once Kohana::find_file('vendor/kdt','Classes/PHPExcel/IOFactory');
            $name=$prize->km_content.'未发送卡密';
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
            foreach($kms as $k => $v){
                $num=$k+1;
                $objPHPExcel->setActiveSheetIndex(0)
                             //Excel的第A列，uid是你查出数组的键值，下面以此类推
                            ->setCellValue('A'.$num, $v->password1)
                            ->setCellValue('B'.$num, $v->password2)
                            ->setCellValue('C'.$num, $v->password3);
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
        $num =ORM::factory('qwt_kmiprize')->where('bid', '=', $bid)->where('key','=','kmi')->count_all();

        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kmi/pages');
        $result['prizes'] = $prize->order_by('startdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $act='build';
        $this->template->title = '卡密列表';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/prizes')
            ->bind('pages', $pages)
            ->bind('act',$act)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    public function action_kmi_detail($pid,$code=''){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        if($code&&$code=='fail'){
            $result['error']='该卡密已被使用，无法删除!';
        }
        $prize =ORM::factory('qwt_kmiprize')->where('id', '=', $pid)->find();
        if($_POST['kmi']){
            $kmi=ORM::factory('qwt_kmikm')->where('id','=',$_POST['id'])->find();
            if($_POST['kmi']['password1']){
                $kmi->password1=$_POST['kmi']['password1'];
            }
            if($_POST['kmi']['password2']){
                $kmi->password2=$_POST['kmi']['password2'];
            }
            if($_POST['kmi']['password3']){
                $kmi->password3=$_POST['kmi']['password3'];
            }
            $kmi->save();
            Request::instance()->redirect('qwtkmia/kmi_detail/'.$pid);
        }
        $kmis=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('startdate','=',$prize->value)->where('live','=',1)->where('flag','=',1);
        $kmis = $kmis->reset(FALSE);
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kmi/pages');
        $result['kmis'] = $kmis->order_by('startdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '卡密详情';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/kmi_detail')
            ->bind('pages', $pages)
            ->bind('prize', $prize)
            ->bind('result', $result)
            ->bind('config', $config);

    }
    public function action_recycle(){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        $kmis=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->where('live','=',1)->where('flag','=',0);
        $kmis = $kmis->reset(FALSE);
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));
        $pages = Pagination::factory(array(
            'total_items'   => $num,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/kmi/pages');
        $result['kmis'] = $kmis->order_by('startdate', 'DESC')->limit($this->pagesize)->offset($offset)->find_all();
        $this->template->title = '回收站';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/recycle')
            ->bind('pages', $pages)
            ->bind('bid', $bid)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_kmi_edit($pid,$kid){
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid);
        $prize=ORM::factory('qwt_kmiprize')->where('id','=',$pid)->find();
        $kmi=ORM::factory('qwt_kmikm')->where('id','=',$kid)->find();
        if($_POST['kmi']){
            if($_POST['kmi']['password1']){
                $kmi->password1=$_POST['kmi']['password1'];
            }
            if($_POST['kmi']['password2']){
                $kmi->password2=$_POST['kmi']['password2'];
            }
            if($_POST['kmi']['password3']){
                $kmi->password3=$_POST['kmi']['password3'];
            }
            $kmi->save();
            Request::instance()->redirect('qwtkmia/kmi_detail/'.$pid);
        }
        $_POST['kmi']=$kmi->as_array();
        $this->template->title = '卡密修改';
        $result['title']='卡密修改';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/kmi_edit')
            ->bind('kmi', $kmi)
            ->bind('prize', $prize)
            ->bind('result', $result)
            ->bind('config', $config);
    }
    //系统配置
    public function action_home() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid, 1);

        //提交表单
        if ($_POST['cfg']) {
            $cfg = ORM::factory('qwt_kmicfg');
            foreach ($_POST['cfg'] as $k=>$v) {
                //AppID 填写后不能修改
                if ($config['appid'] && $k == 'appid') continue;

                $ok = $cfg->setCfg($bid, $k, trim($v));
                $result['ok'] += $ok;
            }
            //重新读取配置
            $config = ORM::factory('qwt_kmicfg')->getCfg($bid, 1);
        }
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/home')
            ->bind('result', $result)
            ->bind('config', $config);
    }
    public function action_tplmsg() {
        require_once Kohana::find_file('vendor', 'weixin/wechat.class');
        $bid = $this->bid;
        if($_POST['text']){
            $ok=ORM::factory('qwt_kmicfg')->setCfg($bid,'kmitpl',trim($_POST['text']['mgtpl']));
        }
        $result['ok']=$ok;
        $config = ORM::factory('qwt_kmicfg')->getCfg($bid, 1);
        $this->template->title = '消息设置';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/kmi/tplmsg')
            ->bind('result',$result)
            ->bind('config', $config);
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

