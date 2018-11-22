<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言配置
class Model_Qwt_hbyrcfg extends ORM {

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    //从缓存或者数据库读取配置 $db=1 不读缓存
    public function getCfg($bid, $db=0) {
        $mem = Cache::instance('memcache');

        //从数据库读取配置文件
        $cfg_key = "qwthby:rcfg:$bid";

        if ( (!$cache = $mem->get($cfg_key)) || $db == 1) {
            $cfgs = ORM::factory('qwt_hbyrcfg')->where('rid', '=', $bid)->find_all();
            foreach ($cfgs as $c) $cfg[$c->key] = $c->value?$c->value:$c->id;
            $mem->set($cfg_key, $cfg, 0);
            if ($cfg) $cache = $cfg;
        }
        return $cache;
    }

    //保存配置
    public function setCfg($bid, $key, $value, $pic="") {
        $mem = Cache::instance('memcache');
        $cfg_key = "hby:rcfg:$bid";
        $ok = 0;

        $cfg = ORM::factory('qwt_hbyrcfg')->where('rid', '=', $bid)->where('key', '=', $key)->find();

        if ($cfg->value !== $value || $pic) {
            $ok++;

            $cfg->rid = $bid;
            $cfg->key = $key;
            $cfg->value = $value;

            if ($pic) {
                $cfg->value = '';
                $cfg->pic = $pic;
            }

            $cfg->save();
            $mem->delete($cfg_key);
        }

        if ($ok) return true;
    }

    //删除配置
    public function delCfg($bid, $key) {
        $mem = Cache::instance('memcache');
        $cfg_key = "hby:rcfg:$bid";
        $mem->delete($cfg_key);
        return ORM::factory('qwt_hbyrcfg')->where('rid', '=', $bid)->where('key', '=', $key)->find()->delete();
    }


}
