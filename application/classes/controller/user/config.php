<?php
defined('SYSPATH') or die('No direct script access.');
class Controller_User_Config extends Controller_User_Home{
	public function before(){
		parent::before();
	}
	public function action_index(){
		$order_id=Request::instance()->param('id');
		$config=ORM::factory('config',array('order_id'=>$order_id));
		$order=ORM::factory('myorder',$order_id);
		$array=include Kohana::find_file('vendor', 'code/product');
		$view=View::factory('user/config/'.$array['product'][$order->num_iid]);
		if($config->config_id!=null){
			$value=$config->as_array();
			$value['token']=$array['token'];
			$value['url']=$array['url_head'][$order->num_iid].$array['url'][$order->num_iid].'/'.$value['urlname'];
			$view->set('data',$value);
		}
		else
		{
			$view->set('data',array());
		}
		$view->set('id',$order_id);
		$this->request->response=$view;
	}
	
	public function action_create_hb(){
		$preg='/([^\d]+)(?<num>\d{1,})([^\d]+)/';
		$order_id=$this->request->param('id');
		$config=ORM::factory('config',array('order_id'=>$order_id));
		$order=ORM::factory('myorder',$order_id);
		preg_match($preg, $order->sku_properties_name,$matches);
		$num=$matches['num'];
		require Kohana::find_file('vendor', 'code/CommonHelper');
		$appid=$_POST['appid'];
		$appsecret=$_POST['appsecret'];
		$partnerid=$_POST['partnerid'];
		$partnerkey=$_POST['partnerkey'];
		$youzan_appid=$_POST['youzan_appid'];
		$youzan_appsecret=$_POST['youzan_appsecret'];
		$name=$_POST['shopname'];
		$shopurl=$_POST['shopurl'];
		$saleurl=$_POST['buyurl'];
		$money=$_POST['money'];
		$hb=$_POST["Radio_hb"];
		$this->check(array($appid,$appsecret,
						   $partnerid,$partnerkey,
						   $youzan_appid,$youzan_appsecret,
					       $name,$shopurl,$saleurl,$money								
		));
		if($config->config_id==null){
			$config->order_id=$order_id;			
		}
		$config->nickname=$name;
		$config->appid=$appid;
		$config->appsecret=$appsecret;
		$config->partnerid=$partnerid;
		$config->partnerkey=$partnerkey;
		$config->youzan_appid=$youzan_appid;
		$config->youzan_appsecret=$youzan_appsecret;
		$config->shopurl=$shopurl;
		$config->saleurl=$saleurl;
		$config->money=$money;
		$file_head=Kohana::include_paths()[0].'vendor/weixin/biz/';
		$Toname=Helper::UtfTo($name);
		$file=$file_head.$Toname.'.php';
		if(file_exists($file)){
			unlink($file);
		}
		$config->urlname=$Toname;
		$config->num=$num;
		$config->save();
		if($config->saved()){
			echo '保存成功';
		$tpl=file_get_contents(Kohana::find_file('vendor', 'weixin/tpl'));
		//var_dump($tpl);
		$value=fopen($file,'a+');
		echo $value;
		$content=sprintf(
				$tpl,$appid,$appsecret,$partnerid,$partnerkey,
				$youzan_appid,$youzan_appsecret,$name,$saleurl,
				$saleurl,$shopurl,$shopurl,$shopurl,'%s','%s');
		$flag=fwrite($value,$content);
		$tpl=
		'$money=rand(100,%s);
		$config["max"] = %s;
		$config["hb"]["split"] = %s;
		$config["hb"]["split_guess"] = %s;';
		if($flag){
			if($hb=="0"){
				$flag2=fwrite($value,sprintf($tpl,$money,$num,0,0));
			}
			else if($hb=="1"){
				$flag2=fwrite($value,sprintf($tpl,$money,$num,1,0));
			}
			else{
				$flag2=fwrite($value,sprintf($tpl,$money,$num,0,1));
			}
			$order=ORM::factory('myorder',$order_id);
			$order->state=1;
			$order->save();
			$this->Upload($Toname);
			echo "<script>alert('配置成功！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
		}
		fclose($value);
		}
		else
		{
			echo "<script>alert('配置失败！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
		}
	}
	
	public function action_create_gl(){
		$order_id=$this->request->param('id');
		$config=ORM::factory('config',array('order_id'=>$order_id));
		//echo 'hello';
		require Kohana::find_file('vendor', 'code/CommonHelper');
		$appid=$_POST['appid'];
		$appsecret=$_POST['appsecret'];
		$word=$_POST['word'];//盖楼增加的数字
		$keyword=$_POST['keyword'];
		$youzan_appid=$_POST['youzan_appid'];
		$youzan_appsecret=$_POST['youzan_appsecret'];
		$name=$_POST['shopname'];
		$this->check(array($appid,$appsecret,$word,$keyword,$youzan_appid,$youzan_appsecret));
        if($config->config_id==null){
        	$config->order_id=$order_id;
        }
        $config->nickname=$name;
		$config->appid=$appid;
		$config->appsecret=$appsecret;
		$config->word=$word;
		$config->keyword=$keyword;
		$config->youzan_appid=$youzan_appid;
		$config->youzan_appsecret=$youzan_appsecret;
		$file_head=Kohana::include_paths()[0].'vendor/weixin/gl/';
		$Toname=Helper::UtfTo($name);
		$file=$file_head.$Toname.'.php';
		if(file_exists($file)){
			unlink($file);
		}
		$config->urlname=$Toname;	
		$config->save();
		if($config->saved()){
			echo '保存成功';
			$tpl=file_get_contents(Kohana::find_file('vendor','weixin/tpl_gl'));
			$fh=fopen($file,'a+');
			$content=sprintf(
					$tpl,$name,$youzan_appid,$youzan_appsecret,$keyword,'%s',$word);
			$flag=fwrite($fh,$content);
			//$tpl='$config["gl"]["step"]=%s;';
			if($flag)
			{
				//$flag2=fwrite($fh,sprintf($tpl,$num));
				$order=ORM::factory('myorder',$order_id);
				$order->state=1;
				$order->save();
				echo "<script>alert('配置成功！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
			}
			else {
		echo "<script>alert('配置失败！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
			}
		}		
	}
	
	public function action_create_yy(){
		$order_id=$this->request->param('id');
		$config=ORM::factory('config',array('order_id'=>$order_id));
		//echo 'hello';
		require Kohana::find_file('vendor', 'code/CommonHelper');
		$appid=$_POST['appid'];
		$appsecret=$_POST['appsecret'];
		$word=$_POST['word'];//文案
		$keyword=$_POST['keyword'];
		$youzan_appid=$_POST['youzan_appid'];
		$youzan_appsecret=$_POST['youzan_appsecret'];
		$name=$_POST['shopname'];
		$shopurl=$_POST['shopurl'];
		$this->check(array($appid,$appsecret,$num,$keyword,$youzan_appid,$youzan_appsecret));
		if($config->config_id==null){
			$config->order_id=$order_id;
		}
		$config->nickname=$name;
		$config->appid=$appid;
		$config->appsecret=$appsecret;
		$config->shopurl=$shopurl;
		$config->keyword=$keyword;
		$config->youzan_appid=$youzan_appid;
		$config->youzan_appsecret=$youzan_appsecret;
		$config->word=$word;
		$file_head=Kohana::include_paths()[0].'vendor/weixin/yy/';
		$Toname=Helper::UtfTo($name);
		$file=$file_head.$Toname.'.php';
		if(file_exists($file)){
			unlink($file);
		}
		$config->urlname=$Toname;
		$config->save();
		if($config->saved()){
			echo '保存成功';
			$tpl=file_get_contents(Kohana::find_file('vendor','weixin/tpl_yy'));
			$fh=fopen($file,'a+');
			$content=sprintf(
					$tpl,$appid,$appsecret,$youzan_appid,$youzan_appsecret,'%s',$keyword,$word);
			$flag=fwrite($fh,$content);
			if($flag)
			{
				$order=ORM::factory('myorder',$order_id);
				$order->state=1;
				$order->save();
				echo "<script>alert('配置成功！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
			}
			else {
				echo "<script>alert('配置失败！');location.href='".URL::site('user/config/index/'.$order_id)."';</script>";
			}
		}
	}
	public function action_generate($bid,$num){
		require Kohana::find_file('vendor', 'code/CommonHelper');
		Helper::GenerateCode($bid,$num);
		echo "location.href='../index';</script>";
	}


	public function action_download(){
		$file='code/hongbao.zip';
		header('Content-type: application/force-download');
		header("Content-Type: application/zip");  
		header("Content-Transfer-Encoding: binary"); 
		//header('Content-Length: '. filesize($file));
		header('Content-Disposition: attachment; filename='.basename($file));   
		@readfile($file);
	}

	public function action_changepwd(){
		if(!isset($_POST['pwd']))
		{
			$view=View::factory('user/changepwd');
			$view->set('name',Session::instance()->get('username'));
			$this->request->response=$view;
		}
		else
		{
			$user=ORM::factory('user',array('receiver_mobile'=>Session::instance()->get('username')));
			$user->password=$_POST['pwd'];
			$user->save();
			echo '<script>alert("修改成功！");location.href="'.URL::site('user/index').'";</script>';
		}
	}

	public function action_getdata(){
		//$order_id=3;
		$order_id=$_POST['id'];
		//$order=ORM::factory('order',$order_id);
		//echo $order_id;
		$config=ORM::factory('config',array('order_id'=>$order_id));
		$result=array();
		$num=ORM::factory('weixinkl');
		$result['total']=$num->where('bid','=',$config->nickname)->count_all();
		if($result['total']<=0){
			echo '0';
		}
		else 
		{
		$result['used']=$num->where('bid', '=', $config->nickname)->where('used','>',0)->count_all();
		$result['left']=$result['total']-$result['used'];
		echo json_encode($result);
		}
	}

	private function check($array){
		$reg='/[\|"\'<>]/';
		foreach($array as $arr){
			if($arr==""){
				echo "<script>alert('相关参数不能为空');history.go(-1);</script>";
			}
			else if(preg_match_all($reg, $arr)){
				echo "<script>alert('不能出现非法字符');history.go(-1);</script>";
			}
			else{
				continue;
			}
		}
		return true;
	}
	
	private function Upload($name){
		$dir=Kohana::include_paths()[0].'vendor/weixin/cert/';
		if($_FILES['filecert']['error']>0)
		{
			echo $_FILES['filecert']['error'];
		}
		if(is_uploaded_file($_FILES['filecert']['tmp_name']))
		{
			if(mkdir($dir.$name)){
				if(move_uploaded_file($_FILES['filecert']['tmp_name'], $dir.$name.'/1.zip')){
					$zip = new ZipArchive();
					if ($zip->open($dir.$name.'/1.zip') === TRUE)
					{
						$zip->extractTo($dir.$name.'/');
						$zip->close();						
					}
					else
					{
						echo 'error';
					}
				}
				else 
				{
					echo '上传失败';
				}
			}
			else
			{
				echo '创建文件夹失败';
			}	
		}
		else 
		{
		echo 'error';
		}
	}
}