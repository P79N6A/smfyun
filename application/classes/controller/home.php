<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

class Controller_Home extends Controller_Base {

    public function action_index() {
        // $index['dd.smfyun.com'] = 'fxba';
        // $index['jfb.smfyun.com'] = 'wdya';
        // $index['dkb.smfyun.com'] = 'dkaa';
        $index['adaada.top'] = 'qwta/login';
        // $index['www.smfyun.com'] = 'index/index';
        // $index['smfyun.com'] = 'index/index';

        if (isset($index[$_SERVER['HTTP_HOST']])) Request::instance()->redirect($index[$_SERVER['HTTP_HOST']]);
        // die('404 Not Found.');
    }

}
