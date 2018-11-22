<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言配置
class Model_Qwt_Wfbcfg extends ORM {

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    //从缓存或者数据库读取配置 $db=1 不读缓存
    public function getCfg($bid, $db=0) {
        require Kohana::find_file('vendor', 'weixin/wfb.inc'); //默认配置
        $mem = Cache::instance('memcache');

        //从数据库读取配置文件
        $cfg_key = "qwt:wfbcfg:$bid";

        if ( (!$cache = $mem->get($cfg_key)) || $db == 1) {
            $cfgs = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('value', '<>', "")->find_all()->as_array();
            foreach ($cfgs as $c) $cfg[$c->key] = $c->value;
            $mem->set($cfg_key, $cfg, 0);
            if ($cfg) $cache = $cfg;
        }
        $midmerge =array_merge($config['wfb'], (array)$cache);

        $mem1 = Cache::instance('memcache');
        $cfg_key1 = "qwt:wfbcfg:$bid";
        if ( (!$cache1 = $mem1->get($cfg_key1)) || $db == 1) {
            $cfg1s = ORM::factory('qwt_cfg')->where('bid', '=', $bid)->where('value', '<>', "")->find_all();
            foreach ($cfg1s as $d) $cfg1[$d->key] = $d->value;
            $mem1->set($cfg_key, $cfg1, 0);
            if ($cfg1) $cache1 = $cfg1;
        }
        return array_merge($midmerge, (array)$cache1);
    }

    //保存配置
    public function setCfg($bid, $key, $value, $pic="") {
        $mem = Cache::instance('memcache');
        $cfg_key = "qwt:wfbcfg:$bid";
        $ok = 0;

        $cfg = ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', $key)->find();

        if ($cfg->value !== $value || $pic) {
            $ok++;

            $cfg->bid = $bid;
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
        $cfg_key = "qwt:wfbcfg:$bid";
        $mem->delete($cfg_key);
        return ORM::factory('qwt_wfbcfg')->where('bid', '=', $bid)->where('key', '=', $key)->find()->delete();
    }


}
