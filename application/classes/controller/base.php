<?php
abstract class Controller_Base extends Controller_Template {

    public $template = 'tpl/base';
    public $require_ssl = FALSE;

    //是否加载公共 CSS 和 JS
    public $global_style = TRUE;

    public function error($msg, $title='非常抱歉，出错了', $icon='/_img/edm/cry.png')
    {
        $view = 'errors/error';
        $this->template->title = $title;
        $this->template->content = View::factory($view)
        ->bind('msg', $msg)
        ->bind('title', $title)
        ->bind('icon', $icon);
    }

    public function before()
    {
        parent::before();

        if (Request::$is_ajax) {
            $this->profiler = NULL;
            //$this->auto_render = FALSE;
            header('content-type: application/json');
        }

        /*
        if (IN_PRODUCTION && $this->require_ssl && Request::$protocol == 'http') {
            Request::Instance()->redirect(URL::site(Request::Instance()->uri, 'https'));
        }
        */

        if ($this->auto_render) {
            // Initialize empty values
            $this->template->title   = '';
            $this->template->content = '';

            $this->template->styles = array();
            $this->template->scripts = array();

        }

        //订单来源
        if (isset($_GET['utm_campaign'])) {
            Cookie::set('source', $_GET['utm_campaign'], Date::YEAR);
            //Cookie::set('free', 1); //广告来的可双免费试用
        }

        //CPS
        if ($_GET['utm_source'] == 'cps') {
            $uid = preg_replace('/^cps(\d+)/', '$1', $_GET['utm_campaign']);
            $user = ORM::factory('user', $uid);

            if ($user && Auth::instance()->logged_in() == 0) {
                Cookie::set('r_hash', $user->hash, Date::WEEK);
                Cookie::set('cps', 1, Date::WEEK);
                // Cookie::set('free', 1); //广告来的可双免费试用
            }
        }
    }

    public function after()
    {
        if ($this->auto_render) {
            $styles = array();

            $styles = array(
                '_css/reset.css' => 'screen',
                '_css/global.css' => 'screen',
                '_css/jquery.alerts.css' => 'screen',
                '_js/fancybox/jquery.fancybox-1.3.4.css' => 'screen',
            );

            //for cdn only
            if (Request::$protocol == 'https') $styles['_css/ssl.css'] = 'screen';

            $scripts = array(
                '_js/global.js',
                '_js/jquery.cook.js',
                // '_js/jquery.corner.js',
                // '_js/jquery.alerts.js',
                '_js/fancybox/jquery.fancybox-1.3.4.pack.js',
                '_js/fancybox/jquery.easing-1.3.pack.js',
                // '_js/uniform1.8/jquery.uniform.min.js',
                '_js/placeholder/jquery.placeholder.min.js',
            );

            //当前用户
            @View::bind_global('is_login', Auth::instance()->logged_in());
            @View::bind_global('user', Auth::instance()->get_user());

            //加载公有文件
            if ($this->global_style) {
                @$this->template->styles = @array_merge( $styles, $this->template->styles );
                @$this->template->scripts = @array_merge( $scripts, $this->template->scripts );
            }
        }

        parent::after();
    }
}

