<?php defined('SYSPATH') or die('No direct script access.');

class ORM extends Kohana_ORM {
	public $qrcodearray=array('qwt_wfbqrcode','qwt_wdbqrcode','qwt_rwbqrcode','qwt_dkaqrcode','qwt_qfxqrcode','qwt_fxbqrcode','qwt_dldqrcode','qwt_hbyqrcode','qwt_wzbqrcode','qwt_xxbqrcode','qwt_ywmqrcode','qwt_yybqrcode','qwt_yyhbqrcode','qwt_yyxqrcode','qwt_zdfqrcode');
	public static function factory($model, $id = NULL,$tbid=NULL)
	{
		// Set class name
		//Kohana::$log->add('qwtormselect', print_r($model,true));
		//Kohana::$log->add('qwtormselect1', print_r($tbid,true));
		$model_name=$model;
		$model = 'Model_'.ucfirst($model);
		return new $model($id,$tbid,$model_name);
	}
	public function __construct($id = NULL,$tbid=NULL,$model_name=NULL){
		parent::__construct($id);
		//Kohana::$log->add('qwtormselect2', print_r($model_name,true));
		//Kohana::$log->add('qwtormselect3', print_r($tbid,true));
        if($model_name=='qwt_qrcode'){
        	if($tbid==1){
	        	$this->_table_name = 'qwt_qrcodes';
	        }elseif ($tbid==2) {
	        	$this->_table_name = 'qwt_qrcode1s';
	        }elseif ($tbid==3) {
	        	$this->_table_name = 'qwt_qrcode2s';
	        }elseif ($tbid==4) {
	        	$this->_table_name = 'qwt_qrcode3s';
	        }elseif ($tbid==5) {
	        	$this->_table_name = 'qwt_qrcode4s';
	        }
        }
        if(in_array($model_name, $this->qrcodearray)){
        	if($tbid==1){
	        	$this->_belongs_to = array('qrcodes'  => array('model' => 'qwt_qrcode', 'foreign_key' => 'qid'));
	        }elseif ($tbid==2) {
	        	$this->_belongs_to = array('qrcodes'  => array('model' => 'qwt_qrcode1', 'foreign_key' => 'qid'));
	        }elseif ($tbid==3) {
	        	$this->_belongs_to = array('qrcodes'  => array('model' => 'qwt_qrcode2', 'foreign_key' => 'qid'));
	        }elseif ($tbid==4) {
	        	$this->_belongs_to = array('qrcodes'  => array('model' => 'qwt_qrcode3', 'foreign_key' => 'qid'));
	        }elseif ($tbid==5) {
	        	$this->_belongs_to = array('qrcodes'  => array('model' => 'qwt_qrcode4', 'foreign_key' => 'qid'));
	        }
        }
    }
}
