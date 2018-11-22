<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtxxba extends Controller_Base {

    public $template = 'weixin/qwt/tpl/xxbatpl';
    public $pagesize = 20;
    public $config;
    public $bid;
    public $methodVersion='3.0.0';
    public function before() {
        Database::$default = "qwt";
        $_SESSION =& Session::instance()->as_array();
        parent::before();
        require_once Kohana::find_file('vendor', 'oauth/wxoauth.class');
        $this->bid = $_SESSION['qwta']['bid'];
        //未登录
        if (Request::instance()->action == 'images') return;
        if (Request::instance()->action != 'login' && !$this->bid) {
            // header('location:/qwta/login');
            header('location:http://'.$_SERVER['HTTP_HOST'].'/qwta/login');
            exit;
        }
        if(!ORM::factory('qwt_buy')->where('bid','=',$this->bid)->where('iid','=',12)->where('expiretime','>',time())->where('status','=',1)->find()->id){
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
    // public function action_index() {
    //     $this->action_home();
    // }
    public function action_home(){
        $bid = $this->bid;

        $config = ORM::factory('qwt_xxbcfg')->getCfg($bid,1);
        if ($_POST['text']) {
            $config = ORM::factory('qwt_xxbcfg')->setCfg($bid,'max_send',$_POST['text']['max_send']);
            $config = ORM::factory('qwt_xxbcfg')->getCfg($bid,1);
            $result['ok3'] = 1;
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/home')
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid', $bid);
    }
    //系统配置
    public function action_msgs($action='', $id=0){
        if ($action=='add') return $this->action_msgs_add();
        if ($action=='edit') return $this->action_msgs_edit($id);
        $bid = $this->bid;
        $item = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('exist','=',1)->find_all();
        $count = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('exist','=',1)->count_all();

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/msgs')
            ->bind('count',$count)
            ->bind('item',$item)
            ->bind('bid', $bid);
    }
    public function action_msgs_add() {
        $bid = $this->bid;
        //文案配置
        if ($_POST['form']) {
            if ($_POST['form']['type']==1) {
                $msg = ORM::factory('qwt_xxbitem');
                $msg->bid = $bid;
                if ($_FILES['picxcx']['error'] == 0||$_FILES['picxcx']['error'] ==2) {
                    if ($_FILES['picxcx']['size'] > 1024*300) {
                        $result['err3'] = '海报模板文件不能超过 300K';
                    } else {
                        $qrfile = DOCROOT."qwt/xxb/tmp/tpl.$bid.jpg";
                        umask(0002);
                        @mkdir(dirname($qrfile),0777,true);
                        $msg->img = file_get_contents($_FILES['picxcx']['tmp_name']);
                        @unlink($qrfile);
                        move_uploaded_file($_FILES['picxcx']['tmp_name'], $qrfile);
                        $options['token'] = $this->token;
                        $options['encodingaeskey'] = $this->encodingAesKey;
                        $options['appid'] = $shop->appid;
                        $wx = new Wxoauth($bid,$options);
                        $wximg = $wx->uploadForever(array('media'=>"@$qrfile"), 'image');
                        $msg->media_id = $wximg['media_id'];
                        $msg->type = $_POST['form']['type'];
                        $msg->name = $_POST['form']['title'];
                        $msg->title = $_POST['xcx']['title'];
                        $msg->appid = $_POST['xcx']['appid'];
                        $msg->path = $_POST['xcx']['path'];
                        $msg->save();
                        Request::instance()->redirect('qwtxxba/msgs');
                    }
                } else if ($_FILES['picxcx']['error'] == 4) {
                    $result['err3'] = '必须上传预览图！';
                }
            }else{
                if ($_POST['text']) {
                    $item = ORM::factory('qwt_xxbitem');
                    $item->bid = $bid;
                    $item->name = $_POST['form']['title'];
                    $item->type = $_POST['form']['type'];
                    $item->save();
                    // echo '<pre>';
                    // var_dump($_POST['text']);
                    // exit;
                    foreach ($_POST['text'] as $k => $v) {
                        if ($_FILES['pic'][$k]['size'] > 1024*400) {
                                $result['err3'] = '第'.($k+1).'张图片文件不能超过 400K';
                        } else {
                            $msg = ORM::factory('qwt_xxbmsg')->where('bid','=',$bid)->where('id','=',$v['mid'])->find();
                            $msg->bid = $bid;
                            $msg->lv = $k+1;
                            $msg->title = $v['title'];
                            $msg->url = $v['url'];
                            $msg->iid = $item->id;
                            if($_FILES['pic'.$k]['tmp_name']){
                                $msg->img = file_get_contents($_FILES['pic'.$k]['tmp_name']);
                            }
                            $msg->save();
                        }
                        $b[$k] = $v['mid'];
                    }
                    Request::instance()->redirect('qwtxxba/msgs');
                }
            }
        }
        $result['action'] = 'add';
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/msgs_add')
            ->bind('result', $result)
            ->bind('bid', $bid);
    }
    public function action_msgs_edit($iid) {
        $bid = $this->bid;
        $msgs = ORM::factory('qwt_xxbmsg')->where('bid','=',$bid)->where('iid','=',$iid)->find_all();
        //文案配置
        if ($_POST['form']['type']==1) {
            $msg = ORM::factory('qwt_xxbitem')->where('id','=',$iid)->find();
            if ($msg->media_id) {
                $msg->bid = $bid;
                if ($_FILES['picxcx']['error'] == 0||$_FILES['picxcx']['error'] ==2) {
                    if ($_FILES['picxcx']['size'] > 1024*300) {
                        $result['err3'] = '海报模板文件不能超过 300K';
                    } else {
                        $qrfile = DOCROOT."qwt/zdf/tmp/tpl.$bid.jpg";
                        umask(0002);
                        @mkdir(dirname($qrfile),0777,true);
                        $msg->img = file_get_contents($_FILES['picxcx']['tmp_name']);
                        @unlink($qrfile);
                        move_uploaded_file($_FILES['picxcx']['tmp_name'], $qrfile);
                        $options['token'] = $this->token;
                        $options['encodingaeskey'] = $this->encodingAesKey;
                        $options['appid'] = $shop->appid;
                        $wx = new Wxoauth($bid,$options);
                        $wximg = $wx->uploadForever(array('media'=>"@$qrfile"), 'image');
                        $msg->media_id = $wximg['media_id'];
                        $msg->url = $wximg['url'];
                    }
                }
                $msg->name = trim($_POST['form']['title']);
                $msg->type = $_POST['form']['type'];
                $msg->title = trim($_POST['xcx']['title']);
                $msg->appid = trim($_POST['xcx']['appid']);
                $msg->path = trim($_POST['xcx']['path']);
                $msg->save();
                Request::instance()->redirect('qwtxxba/msgs');
            }else{
                $msg->bid = $bid;
                if ($_FILES['picxcx']['error'] == 0||$_FILES['picxcx']['error'] ==2) {
                    if ($_FILES['picxcx']['size'] > 1024*300) {
                        $result['err3'] = '海报模板文件不能超过 300K';
                    } else {
                        $qrfile = DOCROOT."qwt/xxb/tmp/tpl.$bid.jpg";
                        umask(0002);
                        @mkdir(dirname($qrfile),0777,true);
                        $msg->img = file_get_contents($_FILES['picxcx']['tmp_name']);
                        @unlink($qrfile);
                        move_uploaded_file($_FILES['picxcx']['tmp_name'], $qrfile);
                        $options['token'] = $this->token;
                        $options['encodingaeskey'] = $this->encodingAesKey;
                        $options['appid'] = $shop->appid;
                        $wx = new Wxoauth($bid,$options);
                        $wximg = $wx->uploadForever(array('media'=>"@$qrfile"), 'image');
                        $msg->media_id = $wximg['media_id'];
                        $msg->type = $_POST['form']['type'];
                        $msg->name = trim($_POST['form']['title']);
                        $msg->title = trim($_POST['xcx']['title']);
                        $msg->appid = trim($_POST['xcx']['appid']);
                        $msg->path = trim($_POST['xcx']['path']);
                        $msg->save();
                        Request::instance()->redirect('qwtxxba/msgs');
                    }
                } else if ($_FILES['picxcx']['error'] == 4) {
                    $result['err3'] = '必须上传预览图！';
                }
            }
        }else{
            if ($_POST['text']) {
                $item = ORM::factory('qwt_xxbitem')->where('id','=',$iid)->find();
                $item->bid = $bid;
                $item->name = trim($_POST['form']['title']);
                $item->type = $_POST['form']['type'];
                $item->save();
                // echo '<pre>';
                // var_dump($_POST['text']);
                // exit;
                $flag = 0;
                foreach ($_POST['text'] as $k => $v) {
                    if ($_FILES['pic'][$k]['size'] > 1024*400) {
                            $result['err3'] = '第'.($k+1).'图片文件不能超过 400K';
                            $flag = 1;
                    } else {
                        $msg = ORM::factory('qwt_xxbmsg')->where('bid','=',$bid)->where('iid','=',$iid)->where('id','=',$v['mid'])->find();
                        $msg->bid = $bid;
                        $msg->lv = $k+1;
                        $msg->title = trim($v['title']);
                        $msg->url = trim($v['url']);
                        $msg->iid = $item->id;
                        if($_FILES['pic'.$k]['tmp_name']){
                            $msg->img = file_get_contents($_FILES['pic'.$k]['tmp_name']);
                        }
                        $msg->save();
                    }
                    $b[$k] = $v['mid'];
                }
                // echo '<pre>';
                foreach ($msgs as $k => $v) {
                    if(!in_array($v->id,$b)){
                        $v->delete();
                        // echo $v->id.'<br>';
                    }
                }
                if($flag==0) Request::instance()->redirect('qwtxxba/msgs');
                // var_dump($b);
                // exit;
                //重新读取配置
            }

        }

        if ($_GET['DELETE'] == 1) {
            $delete = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('id','=',$iid)->find();
            $xxb_list = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('exist','=',1)->where('iid','=',$iid)->find();
            if($xxb_list->id){//有规则的不能删掉
                $result['err3'] = '该发送内容已经应用于了发送规则，不能删除！';
            }else{
                $delete->exist = 0;
                $delete->save();
                // $delete->delete();
                // $empty = ORM::factory('qwt_xxbmsg')->where('bid','=',$bid)->where('iid','=',$iid);
                // $empty->delete_all();
                Request::instance()->redirect('qwtxxba/msgs');
            }
        }
        $result['action'] = 'edit';
            $item = ORM::factory('qwt_xxbitem')->where('id','=',$iid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/msgs_add')
            ->bind('iid', $iid)
            ->bind('result', $result)
            ->bind('msgs', $msgs)
            ->bind('item', $item)
            ->bind('bid', $bid);
    }
    public function action_stats_totle($action='')
    {
         $daytype='%Y-%m-%d';
         $length=10;
         $status=1;
        if($_GET['iid']){
            $iid = $_GET['iid'];
            $flag = $iid;
        }else{
            $flag = 'all';
        }
        if($_GET['qid']==3||$action=='shaixuan')
        {
            $status=3;
            $newadd=array();
            if($_GET['data']['begin']!=null&&$_GET['data']['over']!=null)//搜索
            {
                $begin=$_GET['data']['begin'];
                $over=$_GET['data']['over'];
               if(strtotime($begin)>strtotime($over))
               {
                 $begin=$_GET['data']['over'];
                 $over=$_GET['data']['begin'];
               }
               // echo $begin.$over;
               if(strtotime($begin)==strtotime($over))
               {
                 $newadd[0]['time']=$begin;
               }
               else
               {
                $newadd[0]['time']=$begin.'~'.$over;
               }

                //新增用户
                // $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_xxbqrcodes where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`subscribe_time`, '$daytype')<='$over' ")->execute()->as_array();
                // $newadd[0]['fansnum']=$fans[0]['fansnum'];
                //接入客户数
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_xxbscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_xxbscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }
                $newadd[0]['fansnum']=$fans[0]['fansnum'];
                //发送消息数量
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_xxbscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_xxbscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }

                $newadd[0]['keru']=$fans[0]['keru'];
            }
        }
        else
        {

            if($_GET['qid']==2||$action=='month')//按月统计
            {
                $daytype='%Y-%m';
                $length=7;
                $status=2;
            }
            //$days=DB::query(Database::SELECT,"SELECT  FROM_UNIXTIME(`jointime`, '$daytype')as time FROM `qwt_wfbqrcodes` where bid=$this->bid UNION select FROM_UNIXTIME(`lastupdate`, '$daytype') from qwt_wfbscores where bid =$this->bid ORDER BY `time` DESC ")->execute()->as_array();
            // $days0=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`subscribe_time`, '$daytype')as time FROM `qwt_xxbqrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            if($flag=='all'){
                $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_xxbscores` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            }else{
                $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_xxbscores` where bid=$this->bid and iid=$iid  ORDER BY `time` DESC ")->execute()->as_array();
            }
            // echo '<pre>';
            // var_dump($days);
            // exit;
            // $days = array_merge($days0,$days1);
            // echo "<pre>";
            // var_dump($days);
            // echo "<pre>";
            // exit;
            //$pagesize=2;
            $num=count($days);
            $page = max($_GET['page'], 1);
            $offset = ($this->pagesize * ($page - 1));
            $pages = Pagination::factory(array(
                'total_items'   => $num,
                'items_per_page'=> $this->pagesize,
            ))->render('weixin/qwt/admin/wfb/pages');
            // $days0=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`subscribe_time`, '$daytype')as time FROM `qwt_xxbqrcodes` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            if($flag=='all'){
                $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_xxbscores` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            }else{
                $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_xxbscores` where bid=$this->bid and iid=$iid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            }

            // $days = array_merge($days0,$days1);
            // echo '<pre>';
            // var_dump($days);
            // exit;
            $newadd=array();
            for($i=0;$days[$i];$i++)
            {

                $time=$days[$i]['time'];
                $newadd[$i]['time']=$time;
                // //新增用户
                // $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_xxbqrcodes where bid=$this->bid and FROM_UNIXTIME(`subscribe_time`, '$daytype')='$time'")->execute()->as_array();
                // $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_xxbscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_xxbscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                }
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //客入量
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_xxbscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_xxbscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ")->execute()->as_array();
                }
                // var_dump($fans);
                // echo "select count(qid) as keru from qwt_xxbscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ";
                // echo '<pre>';
                // var_dump($fans);
                // exit;
                $newadd[$i]['keru']=$fans[0]['keru'];
            }
        }
        // $duringdata=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`jointime`, '%Y-%m-%d')as time FROM `qwt_wfbqrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
        // $num=count($duringdata);
        // if(strtotime($duringdata[0]['time'])<strtotime($duringdata[$num-1]['time']))
        // {
        // $duringtime['begin']=$duringdata[0]['time'];
        // echo $duringtime['begin']."pppp";
        // $duringtime['over']=$duringdata[$num-1]['time'];
        // }
        // else
        // {
        // $duringtime['begin']=$duringdata[$num-1]['time'];
        // $duringtime['over']=$duringdata[0]['time'];
        // }
        $item = ORM::factory('qwt_xxbitem')->where('bid','=',$this->bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/stats_totle')
        ->bind('item',$item)
        ->bind('newadd',$newadd)
        ->bind('status',$status)
        ->bind('pages', $pages)
        ->bind('duringtime',$duringtime);
    }
    public function action_rules($action='', $id=0){
        if ($action=='add') return $this->action_rules_add();
        if ($action=='edit') return $this->action_rules_edit($id);
        $bid = $this->bid;
        $list = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('exist','=',1)->find_all();

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/rules')
            ->bind('list', $list)
            ->bind('bid', $bid);
    }
    public function action_rules_add(){
        $bid = $this->bid;
        $items = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('exist','=',1)->find_all();
        if ($_POST['rule']) {
            $gendar = $_POST['rule']['gendar'];
            $pro = $_POST['area']['pro'];
            $city = $_POST['area']['city'];
            $dis = $_POST['area']['dis'];
            $lists = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('exist','=',1)->find_all();
            foreach ($lists as $key => $value) {
                $rules[$key]['sex'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','sex')->find()->value;
                $rules[$key]['pro'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','pro')->find()->value;
                $rules[$key]['city'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','city')->find()->value;
                $rules[$key]['dis'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','dis')->find()->value;
            }
            $result['ok'] = 1;
            foreach ($rules as $k => $v) {
                if ($gendar==$v['sex']||$gendar==3||$v['sex']==3) {
                    if ($pro==$v['pro']||$pro=='全部省'||$v['pro']=='全部省'||$pro==''||$v['pro']==''||$pro==null||$v['pro']==null) {
                        if ($city==$v['city']||$city=='全部市'||$v['city']=='全部市'||$city==''||$v['city']==''||$city==null||$v['city']==null) {
                            if ($dis==$v['dis']||$dis=='全部区'||$v['dis']=='全部区'||$dis==''||$v['dis']==''||$dis==null||$v['dis']==null) {
                                $result['ok'] = 0;
                            }
                        }
                    }
                }
            }
            if ($result['ok']==1) {
                $list = ORM::factory('qwt_xxblist');
                $list->bid = $bid;
                $list->iid = $_POST['rule']['content'];
                $list->save();
                $rid = $list->id;

                $rule = ORM::factory('qwt_xxbrule');
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'sex';
                $rule->value = $_POST['rule']['gendar'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule');
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'pro';
                $rule->value = $_POST['area']['pro'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule');
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'city';
                $rule->value = $_POST['area']['city'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule');
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'dis';
                $rule->value = $_POST['area']['dis'];
                $rule->save();

                Request::instance()->redirect('qwtxxba/rules');
            }else{
                $result['err'] = '选择的条件与已经存在的条件有冲突，请重新编辑';
            }
        }
        $result['action'] = 'add';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/rules_add')
            ->bind('result',$result)
            ->bind('items',$items)
            ->bind('bid', $bid);
    }
    public function action_rules_edit($rid){
        $bid = $this->bid;
        $items = ORM::factory('qwt_xxbitem')->where('bid','=',$bid)->where('exist','=',1)->find_all();
        $rule0 = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->find_all();
        $gendar0 = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','sex')->find()->value;
        $pro0 = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','pro')->find()->value;
        $city0 = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','city')->find()->value;
        $dis0 = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','dis')->find()->value;
        if ($_POST['rule']) {
            $gendar = $_POST['rule']['gendar'];
            $pro = $_POST['area']['pro'];
            $city = $_POST['area']['city'];
            $dis = $_POST['area']['dis'];
            $lists = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('exist','=',1)->where('id','!=',$rid)->find_all();
            foreach ($lists as $key => $value) {
                $rules[$key]['sex'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','sex')->find()->value;
                $rules[$key]['pro'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','pro')->find()->value;
                $rules[$key]['city'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','city')->find()->value;
                $rules[$key]['dis'] = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$value->id)->where('keyword','=','dis')->find()->value;
            }
            $result['ok'] = 1;
            foreach ($rules as $k => $v) {
                if ($gendar==$v['sex']||$gendar==3||$v['sex']==3) {
                    if ($pro==$v['pro']||$pro=='全部省'||$v['pro']=='全部省'||$pro==''||$v['pro']==''||$pro==null||$v['pro']==null) {
                        if ($city==$v['city']||$city=='全部市'||$v['city']=='全部市'||$city==''||$v['city']==''||$city==null||$v['city']==null) {
                            if ($dis==$v['dis']||$dis=='全部区'||$v['dis']=='全部区'||$dis==''||$v['dis']==''||$dis==null||$v['dis']==null) {
                                $result['ok'] = 0;
                            }
                        }
                    }
                }
            }
            if ($result['ok']==1) {

                $list = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('id','=',$rid)->find();
                $list->bid = $bid;
                $list->iid = $_POST['rule']['content'];
                $list->save();

                $rule = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','sex')->find();
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'sex';
                $rule->value = $_POST['rule']['gendar'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','pro')->find();
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'pro';
                $rule->value = $_POST['area']['pro'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','city')->find();
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'city';
                $rule->value = $_POST['area']['city'];
                $rule->save();

                $rule = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid)->where('keyword','=','dis')->find();
                $rule->bid = $bid;
                $rule->rid = $rid;
                $rule->keyword = 'dis';
                $rule->value = $_POST['area']['dis'];
                $rule->save();

            Request::instance()->redirect('qwtxxba/rules');
            }else{
                $result['err'] = '选择的条件与已经存在的条件有冲突，请重新编辑';
            }
        }
        if ($_GET['DELETE'] == 1) {
            $delete = ORM::factory('qwt_xxblist')->where('bid','=',$bid)->where('id','=',$rid)->find();
            $delete->exist = 0;
            $delete->save();
            // $delete->delete();
            // $empty = ORM::factory('qwt_xxbrule')->where('bid','=',$bid)->where('rid','=',$rid);
            // $empty->delete_all();
            Request::instance()->redirect('qwtxxba/rules');
        }
        $iid = ORM::factory('qwt_xxblist')->where('id','=',$rid)->find()->iid;
        $result['action'] = 'edit';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/xxb/rules_add')
            ->bind('rid',$rid)
            ->bind('result',$result)
            ->bind('items',$items)
            ->bind('rule0',$rule0)
            ->bind('gendar0',$gendar0)
            ->bind('pro0',$pro0)
            ->bind('city0',$city0)
            ->bind('dis0',$dis0)
            ->bind('iid',$iid)
            ->bind('bid', $bid);
    }

    //产品图片
    public function action_images($type='msg', $id=1, $cksum='') {
        $field = 'img';
        $table = "qwt_xxb$type";

        $pic = ORM::factory($table, $id)->img;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
