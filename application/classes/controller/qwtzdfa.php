<?php defined('SYSPATH') or die('No direct script access.');

class Controller_qwtzdfa extends Controller_Base {

    public $template = 'weixin/qwt/tpl/zdfatpl';
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

        $config = ORM::factory('qwt_zdfcfg')->getCfg($bid,1);
        if ($_POST['text']) {
            $config = ORM::factory('qwt_zdfcfg')->setCfg($bid,'max_send',$_POST['text']['max_send']);
            $config = ORM::factory('qwt_zdfcfg')->getCfg($bid,1);
            $result['ok3'] = 1;
        }
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/home')
            ->bind('config',$config)
            ->bind('result',$result)
            ->bind('bid', $bid);
    }
    //系统配置
    public function action_msgs($action='', $id=0){
        if ($action=='add') return $this->action_msgs_add();
        if ($action=='edit') return $this->action_msgs_edit($id);
        $bid = $this->bid;
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->find_all();
        $count = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->count_all();
        //分页
        $countall = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/zdf/pages');
        // echo $result['sort'].'<br>';
        // exit();

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/msgs')
            ->bind('count',$count)
            ->bind('msg',$msg)
            ->bind('bid', $bid);
    }
    public function action_msgs_add() {
        $bid = $this->bid;
        if ($_POST['text']) {
            $msg = ORM::factory('qwt_zdfmsg');
            $msg->bid = $bid;
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                if ($_FILES['pic']['size'] > 1024*300){
                    $result['err3'] = '海报模板文件不能超过 300K';
                } else {
                    $qrfile = DOCROOT."qwt/zdf/tmp/tpl.$bid.jpg";
                    umask(0002);
                    @mkdir(dirname($qrfile),0777,true);
                    $msg->img = file_get_contents($_FILES['pic']['tmp_name']);
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                    $options['token'] = $this->token;
                    $options['encodingaeskey'] = $this->encodingAesKey;
                    $options['appid'] = $shop->appid;
                    $wx = new Wxoauth($bid,$options);
                    $wximg = $wx->uploadForever(array('media'=>"@$qrfile"), 'image');
                    $msg->media_id = $wximg['media_id'];
                    $msg->url = $wximg['url'];
                    $msg->name = $_POST['text']['name'];
                    $msg->title = $_POST['text']['title'];
                    $msg->appid = $_POST['text']['appid'];
                    $msg->path = $_POST['text']['path'];
                    $msg->save();
                    Request::instance()->redirect('qwtzdfa/msgs');
                }
            } else if ($_FILES['pic']['error'] ==4) {
                $result['err3']='必须上传预览图！';
            }
        }
        $result['action'] = '添加小程序';
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/msgs_add')
            ->bind('result', $result)
            ->bind('bid', $bid);
    }
    public function action_msgs_edit($mid) {
        $bid = $this->bid;
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->where('id','=',$mid)->find();
        //文案配置
        if ($_POST['text']) {
            $msg = ORM::factory('qwt_zdfmsg')->where('id','=',$mid)->find();
            $msg->bid = $bid;
            if ($_FILES['pic']['error'] == 0||$_FILES['pic']['error'] ==2) {
                if ($_FILES['pic']['size'] > 1024*300) {
                    $result['err3'] = '海报模板文件不能超过 300K';
                } else {
                    $qrfile = DOCROOT."qwt/zdf/tmp/tpl.$bid.jpg";
                    umask(0002);
                    @mkdir(dirname($qrfile),0777,true);
                    $msg->img = file_get_contents($_FILES['pic']['tmp_name']);
                    @unlink($qrfile);
                    move_uploaded_file($_FILES['pic']['tmp_name'], $qrfile);
                    $options['token'] = $this->token;
                    $options['encodingaeskey'] = $this->encodingAesKey;
                    $options['appid'] = $shop->appid;
                    $wx = new Wxoauth($bid,$options);
                    $wximg = $wx->uploadForever(array('media'=>"@$qrfile"), 'image');
                    $msg->media_id = $wximg['media_id'];
                    $msg->url = $wximg['url'];
                }
            }
            $msg->name = trim($_POST['text']['name']);
            $msg->title = trim($_POST['text']['title']);
            $msg->appid = trim($_POST['text']['appid']);
            $msg->path = trim($_POST['text']['path']);
            $msg->save();
            Request::instance()->redirect('qwtzdfa/msgs');
        }
        if ($_GET['DELETE'] == 1) {
            $delete = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->where('id','=',$mid)->find();
            //有规则的不能删掉，building
            $zdf_rule = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('mid','=',$mid)->find();
            $zdf_follow = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->where('mid','=',$mid)->find();
            $zdf_payed = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->where('mid','=',$mid)->find();
            if($zdf_rule->id||$zdf_follow->id||$zdf_payed->id){//有规则的不能删掉
                $result['err3'] = '该小程序已经应用于了发送规则，不能删除！';
            }else{
                $delete->delete();
                // $empty = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->where('iid','=',$iid);
                // $empty->delete_all();
                Request::instance()->redirect('qwtzdfa/msgs');
            }
        }
        $result['action'] = '修改小程序';
        $msg = ORM::factory('qwt_zdfmsg')->where('id','=',$mid)->find();
        $this->template->title = '首页';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/msgs_add')
            ->bind('mid', $mid)
            ->bind('result', $result)
            ->bind('msg', $msg)
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
                // $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_zdfqrcodes where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`subscribe_time`, '$daytype')<='$over' ")->execute()->as_array();
                // $newadd[0]['fansnum']=$fans[0]['fansnum'];
                //接入客户数
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_zdfscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_zdfscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }
                $newadd[0]['fansnum']=$fans[0]['fansnum'];
                //发送消息数量
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_zdfscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_zdfscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')>='$begin' and FROM_UNIXTIME(`lastupdate`, '$daytype')<='$over' ")->execute()->as_array();
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
            // $days0=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`subscribe_time`, '$daytype')as time FROM `qwt_zdfqrcodes` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            if($flag=='all'){
                $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_zdfscores` where bid=$this->bid  ORDER BY `time` DESC ")->execute()->as_array();
            }else{
                $days=DB::query(Database::SELECT,"SELECT  distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_zdfscores` where bid=$this->bid and iid=$iid  ORDER BY `time` DESC ")->execute()->as_array();
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
            // $days0=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`subscribe_time`, '$daytype')as time FROM `qwt_zdfqrcodes` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            if($flag=='all'){
                $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_zdfscores` where bid=$this->bid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
            }else{
                $days=DB::query(Database::SELECT,"SELECT distinct FROM_UNIXTIME(`lastupdate`, '$daytype')as time FROM `qwt_zdfscores` where bid=$this->bid and iid=$iid  ORDER BY `time` DESC limit $this->pagesize offset $offset")->execute()->as_array();
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
                // $fans=DB::query(Database::SELECT,"select count(openid) as fansnum from qwt_zdfqrcodes where bid=$this->bid and FROM_UNIXTIME(`subscribe_time`, '$daytype')='$time'")->execute()->as_array();
                // $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_zdfscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(distinct qid) as fansnum from qwt_zdfscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time' ")->execute()->as_array();
                }
                $newadd[$i]['fansnum']=$fans[0]['fansnum'];
                //客入量
                if($flag=='all'){
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_zdfscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ")->execute()->as_array();
                }else{
                    $fans=DB::query(Database::SELECT,"select count(qid) as keru from qwt_zdfscores where bid=$this->bid and iid=$iid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ")->execute()->as_array();
                }
                // var_dump($fans);
                // echo "select count(qid) as keru from qwt_zdfscores where bid=$this->bid and FROM_UNIXTIME(`lastupdate`, '$daytype')='$time'  ";
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
        $item = ORM::factory('qwt_zdfitem')->where('bid','=',$this->bid)->find_all();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/stats_totle')
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
        $rule = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->find_all();
        //分页
        $countall = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->count_all();
        $page = max($_GET['page'], 1);
        $offset = ($this->pagesize * ($page - 1));

        $pages = Pagination::factory(array(
            'total_items'   => $countall,
            'items_per_page'=> $this->pagesize,
        ))->render('weixin/qwt/admin/zdf/pages');
        // echo $result['sort'].'<br>';
        // exit();

        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/rules')
            ->bind('rule', $rule)
            ->bind('bid', $bid);
    }
    public function action_rules_add(){
        $bid = $this->bid;
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->find_all();
        if ($_POST['rule']) {
            $keyword = $_POST['rule']['keyword'];
            $conflict = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('keyword','=',$keyword)->find();
            if ($conflict->id) {
                $result['err3'] = '已存在该关键词的下发规则，请重新输入！';
            }else{
                $rule = ORM::factory('qwt_zdfrule');
                $rule->bid = $bid;
                $rule->keyword = $_POST['rule']['keyword'];
                $rule->mid = $_POST['rule']['content'];
                $rule->text = $_POST['rule']['text'];
                $rule->save();
                Request::instance()->redirect('qwtzdfa/rules');
            }
        }
        $result['action'] = '添加关键词';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/rules_add')
            ->bind('result',$result)
            ->bind('msg',$msg)
            ->bind('bid', $bid);
    }
    public function action_follow(){
        $bid = $this->bid;
        $rule = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->find();
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->find_all();
        if ($_POST['rule']) {
            $rule = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->find();
            if ($rule->id) {
                $rule_new = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->find();
                $rule_new->switch = $_POST['rule']['switch'];
                $rule_new->mid = $_POST['rule']['content'];
                $rule_new->text = $_POST['rule']['text'];
                $rule_new->save();
                $result['content']='ok';
            }else{
                $rule_new = ORM::factory('qwt_zdffollow');
                $rule_new->bid = $bid;
                $rule_new->switch = $_POST['rule']['switch'];
                $rule_new->mid = $_POST['rule']['content'];
                $rule_new->text = $_POST['rule']['text'];
                $rule_new->save();
                $result['content']='ok';
            }
        }
        $rule = ORM::factory('qwt_zdffollow')->where('bid','=',$bid)->find();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/follow')
            ->bind('result',$result)
            ->bind('msg',$msg)
            ->bind('rule',$rule)
            ->bind('bid', $bid);
    }
    public function action_payed(){
        $bid = $this->bid;
        $rule = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->find();
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->find_all();
        if ($_POST['rule']) {
            $rule = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->find();
            if ($rule->id) {
                $rule_new = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->find();
                $rule_new->switch = $_POST['rule']['switch'];
                $rule_new->mid = $_POST['rule']['content'];
                $rule_new->text = $_POST['rule']['text'];
                $rule_new->save();
                $result['content']='ok';
            }else{
                $rule_new = ORM::factory('qwt_zdfpayed');
                $rule_new->bid = $bid;
                $rule_new->switch = $_POST['rule']['switch'];
                $rule_new->mid = $_POST['rule']['content'];
                $rule_new->text = $_POST['rule']['text'];
                $rule_new->save();
                $result['content']='ok';
            }
        }
        $youzan = ORM::factory('qwt_login')->where('id','=',$bid)->find()->yzaccess_token;
        $rule = ORM::factory('qwt_zdfpayed')->where('bid','=',$bid)->find();
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/payed')
            ->bind('youzan',$youzan)
            ->bind('result',$result)
            ->bind('msg',$msg)
            ->bind('rule',$rule)
            ->bind('bid', $bid);
    }
    public function action_rules_edit($rid){
        $bid = $this->bid;
        $rule = ORM::factory('qwt_zdfrule')->where('id','=',$rid)->find();
        $msg = ORM::factory('qwt_zdfmsg')->where('bid','=',$bid)->find_all();
        if ($_POST['rule']) {
            $keyword = $_POST['rule']['keyword'];
            $conflict = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('keyword','=',$keyword)->find();
            if ($conflict->id==$rule->id || !$conflict->id) {
                $rule = ORM::factory('qwt_zdfrule')->where('id','=',$rid)->find();
                $rule->keyword = $_POST['rule']['keyword'];
                $rule->mid = $_POST['rule']['content'];
                $rule->text = $_POST['rule']['text'];
                $rule->save();
                Request::instance()->redirect('qwtzdfa/rules');
            }else{
                $result['err3'] = '已存在该关键词的下发规则，请重新输入！';
            }
        }
        if ($_GET['DELETE'] == 1) {
            $delete = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('id','=',$rid)->find();
            // $delete->exist = 0;
            // $delete->save();
            $delete->delete();
            // $empty = ORM::factory('qwt_zdfrule')->where('bid','=',$bid)->where('rid','=',$rid);
            // $empty->delete_all();
            Request::instance()->redirect('qwtzdfa/rules');
        }
        $result['action'] = '编辑关键词';
        $this->template->father = View::factory('weixin/qwt/tpl/atpl');
        $this->template->content = View::factory('weixin/qwt/admin/zdf/rules_add')
            ->bind('rid',$rid)
            ->bind('bid',$bid)
            ->bind('rule',$rule)
            ->bind('msg',$msg)
            ->bind('result',$result);
    }

    //产品图片
    public function action_images($type='msg', $id=1, $cksum='') {
        $field = 'img';
        $table = "qwt_zdf$type";

        $pic = ORM::factory($table, $id)->img;
        if (!$pic) die('404 Not Found!');

        header("Content-Type: image/jpeg");
        header("Content-Length: ".strlen($pic));
        echo $pic;
        exit;
    }
}
