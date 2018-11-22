<?php defined('SYSPATH') or die('No direct script access.');
Class Model_Select_experience extends Model {
	//private $num=2000;
    public $config;
    public function dopinion($bid,$alias){
        // if($bid!=6&&$bid!=111439&&$bid!=1) return true;
        $this->config=$config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $item=ORM::factory('qwt_item')->where('alias','=',$alias)->find();
        $buy=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$item->id)->find();
        $rebuy_num=ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('buy_id','=',$buy->id)->where('status','=',1)->count_all();
        $experience=ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('buy_id','=',$buy->id)->where('status','=',1)->where('experience','=',1)->find();
        Kohana::$log->add("qwtexperience1:$bid",print_r($rebuy_num, true));
        Kohana::$log->add("qwtexperience2:$bid",print_r($experience->id, true));
        if($rebuy_num>1||!$experience->id){
            return true;
        }else{
            return $this->$alias($bid);
        }
    }
    public function fenzai($bid,$alias){
        // if($bid!=6&&$bid!=111439) return true;
        $this->config=$config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $item=ORM::factory('qwt_item')->where('alias','=',$alias)->find();
        $buy=ORM::factory('qwt_buy')->where('bid','=',$bid)->where('iid','=',$item->id)->find();
        $rebuy_num=ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('buy_id','=',$buy->id)->where('status','=',1)->count_all();
        $experience=ORM::factory('qwt_rebuy')->where('bid','=',$bid)->where('buy_id','=',$buy->id)->where('status','=',1)->where('experience','=',1)->find();
        Kohana::$log->add("qwtexperience3:$bid",print_r($rebuy_num, true));
        Kohana::$log->add("qwtexperience4:$bid",print_r($experience->id, true));
        if($rebuy_num>1||!$experience->id){
            return true;
        }else{
            return false;
        }
    }
    public function selectnum($bid,$alias){
        $this->config=$config=ORM::factory('qwt_cfg')->getCfg($bid,1);
        $fname=$alias.'num';
        return $this->$fname($bid);
    }
    private function wfb($bid){
        $haibao_num=ORM::factory('qwt_wfbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtwfbexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['wfbhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function wfbnum($bid){
        $haibao_num=ORM::factory('qwt_wfbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtwfbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['wfbhaibao']-$haibao_num;
        return $num;
    }
    private function wdbnum($bid){
        $haibao_num=ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtwdbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['wdbhaibao']-$haibao_num;
        return $num;
    }
    private function dkanum($bid){
        $haibao_num=ORM::factory('qwt_dkaqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtdkaexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['dkahaibao']-$haibao_num;
        return $num;
    }
    private function fxbnum($bid){
        $haibao_num=ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtfxbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['fxbhaibao']-$haibao_num;
        return $num;
    }
    private function rwbnum($bid){
        $haibao_num=ORM::factory('qwt_rwbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtrwbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['rwbhaibao']-$haibao_num;
        return $num;
    }
    private function rwdnum($bid){
        $haibao_num=ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtrwbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['rwdhaibao']-$haibao_num;
        return $num;
    }
    private function xdbnum($bid){
        $haibao_num=ORM::factory('qwt_xdbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtrwbexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['xdbhaibao']-$haibao_num;
        return $num;
    }

    private function qfxnum($bid){
        $haibao_num=ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtqfxexperience:$bid",print_r($haibao_num, true));
        $num=$this->config['qfxhaibao']-$haibao_num;
        return $num;
    }

    private function wdb($bid){
        $haibao_num=ORM::factory('qwt_wdbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtwdbexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['wdbhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function rwb($bid){
        $haibao_num=ORM::factory('qwt_rwbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtrwbexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['rwbhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function rwd($bid){
        $haibao_num=ORM::factory('qwt_rwdqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtrwdexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['rwdhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function dka($bid){
        $haibao_num=ORM::factory('qwt_dkaqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtdkaexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['dkahaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function dld($bid){
        $dl_num=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('lv','=',1)->count_all();
        // echo $dl_num.'<br>';
        // var_dump($this->config);
        if($dl_num>=$this->config['dldnum']){
            return false;
        }else{
            return true;
        }
    }
    public function dldnum($bid){
        $dl_num=ORM::factory('qwt_dldqrcode')->where('bid','=',$bid)->where('lv','=',1)->count_all();
        $num=$this->config['dldnum']-$dl_num;
        return $num;
    }
    private function qfx($bid){
        $haibao_num=ORM::factory('qwt_qfxqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtqfxexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['qfxhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function fxb($bid){
        $haibao_num=ORM::factory('qwt_fxbqrcode')->where('bid','=',$bid)->where('ticket','!=','')->count_all();
        Kohana::$log->add("qwtfxbexperience:$bid",print_r($haibao_num, true));
        if($haibao_num>=$this->config['fxbhaibao']){
            return false;
        }else{
            return true;
        }
    }
    private function gl($bid){
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $lou_key = "qwt_gl:{$bid}:$bid:gl_count";
        $lou_count = $m->get($lou_key);
        Kohana::$log->add("qwtglexperience:$bid",print_r($lou_count, true));
        //$lou_count = ORM::factory('qwt_glusertime')->where('bid','=',$bid)->count_all();
        //Kohana::$log->add("qwtglexperience:$bid",print_r($lou_count, true));
        if($lou_count>=$this->config['glcount']){
            return false;
        }else{
            return true;
        }
    }
    public function glnum($bid){
        $m = new Memcached();
        $m->addServer('ebf7a04a54034b51.m.cnbjalicm12pub001.ocs.aliyuncs.com', 11211);
        $lou_key = "qwt_gl:{$bid}:$bid:gl_count";
        $lou_count = $m->get($lou_key);
        // Kohana::$log->add("qwtglexperience:$bid",print_r($lou_count, true));
        //$lou_count = ORM::factory('qwt_glusertime')->where('bid','=',$bid)->count_all();
        $num= $this->config['glcount']-$lou_count;
        return $num;
    }
    private function yyb($bid){
        return false;
    }
    private function kmi($bid){
        return false;
    }
    public function kminum($bid){
        $kmi_num=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->count_all();
        $num= $this->config['kminum']-$kmi_num;
        return $num;
    }
    public function kminum1($bid){
        $kmi_num=ORM::factory('qwt_kmikm')->where('bid','=',$bid)->count_all();
        return $kmi_num;
    }
}
