<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Controller_Base {
    public $template = 'tpl/html';

    public function action_index() {
        $this->action_500();
    }

    public function action_404() {
        Request::instance()->status = 404;
        //header("HTTP/1.1 404 Not Found");
        $this->template = 'tpl/blank';
        self::before();

        $this->template->title = '您是怎么来到这里的?';
        $this->template->content = View::factory('errors/404');
    }

    public function action_500() {
        Request::instance()->status = 500;
        //header("HTTP/1.1 500 Internal Server Error");

        $this->template->title = '服务器吃不消了';
        $this->template->content = View::factory('errors/500');
    }
}
?>
