<?php defined('SYSPATH') or die('No direct script access.');
Class Model_Select_qwtorm extends Model {
	private $num=2000;
    public function selectorm($bid){
    	$loginnum=ORM::factory('qwt_login')->where('id','<=',$bid)->count_all();
    	if($loginnum<=$this->num){
    		return 1;
    	}elseif($this->num<$loginnum&&$loginnum<=2*$this->num){
    		return 2;
    	}elseif(2*$this->num<$loginnum&&$loginnum<=3*$this->num){
    		return 3;
    	}elseif(3*$this->num<$loginnum&&$loginnum<=4*$this->num){
    		return 4;
    	}elseif(4*$this->num<$loginnum&&$loginnum<=5*$this->num){
    		return 5;
    	}
    }
}
