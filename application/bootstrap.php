<?php
// die('积分宝服务器升级中，请 10 分钟后再试，请您见谅。');

defined('SYSPATH') or die('No direct script access.');
//$Id$
$_ver = '$Id$';
$_ver = explode(' ', $_ver);

error_reporting(E_ALL & ~ E_NOTICE);
define('IN_PRODUCTION', ($_SERVER['SERVER_ADDR'] !== '127.0.0.1' && $_SERVER['SERVER_ADDR'] !== '192.168.31.173'));

//开发环境
if (!IN_PRODUCTION) {
    define('VER', time());
}

//产品环境
else {
    define('VER', '0.'. $_ver[2]);
}

/**
 * Set the environment string by the domain (defaults to Kohana::DEVELOPMENT).
 */
Kohana::$environment = IN_PRODUCTION ? Kohana::PRODUCTION : Kohana::DEVELOPMENT;

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Asia/Chongqing');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'zh_CN.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Set Kohana::$environment if $_ENV['KOHANA_ENV'] has been supplied.
 *
 */
if (isset($_ENV['KOHANA_ENV'])) {
    Kohana::$environment = $_ENV['KOHANA_ENV'];
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
        'base_url'     => '/',
        'index_file'   => FALSE,
        'cache_dir'    => DOCROOT.'../cache',
        'errors'       => ! IN_PRODUCTION,
        'profiling'    => isset($_GET['debug']),
        'caching'      => IN_PRODUCTION,
    ));


/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
        'auth'       => MODPATH.'auth',       // Basic authentication
        'cache'      => MODPATH.'cache',      // Caching with multiple backends
        'database'   => MODPATH.'database',   // Database access
        'orm'        => MODPATH.'orm',        // Object Relationship Mapping
        'mailer'     => MODPATH.'mailer',     // SwiftMailer Transports
        'pagination' => MODPATH.'pagination', // Paging of results

        //'goo.gl'     => MODPATH.'kohana-google-shorten', //Goo.gl URL Shorten API
        //'mailchimp'  => MODPATH. 'kohana-mailchimp', //Mailchimp API

        //'unittest'   => MODPATH.'unittest',   // Unit testing
        //'codebench'  => MODPATH.'codebench',  // Benchmarking tool
        // 'image'      => MODPATH.'image',      // Image manipulation
        // 'oauth'      => MODPATH.'oauth',      // OAuth authentication
        // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    ));


//开发环境数据库
if (!IN_PRODUCTION) Database::$default = "local";

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
if ( ! Route::cache() || isset($_GET['update'])) {

    Route::set('api', 'api(/<controller>(/<id>(/<ext>)))')->defaults(array('directory' => 'api', 'controller' => 'test',));
    Route::set('MP_verify', 'MP_verify_<hash>.txt')->defaults(array('controller' => 'txt','action' => 'txt'));
    //defaults
    Route::set('default', '(<controller>(/<action>(/<id>(/<ext>(/<extt>)))))', array('controller' => '[^/]+', 'action' => '[^/]+', 'id' => '[^/]+', 'ext' => '[^/]+', 'extt' => '[^/]+'))->defaults(array('controller' => 'home', 'action'     => 'index',));

    // Cache the routes
    if (IN_PRODUCTION) Route::cache(TRUE);
}
if($_SERVER['HTTP_HOST']=='top.smfyun.com'){
        Route::set('default', '(<controller>(/<action>(/<id>)))')->defaults(array('controller' => 'smfyun','action' => 'all_top'));
    }
//Set default lang
i18n::lang('zh');

// Begin Output
if ( ! defined('SUPPRESS_REQUEST')) {
    $request = Request::instance();

    try
    {
        $request->execute();
    }
    catch (ReflectionException $e)
    {
        if ( ! IN_PRODUCTION) throw $e;

        //Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));

        $request->status = 404;
        $request->response = Request::factory('error/404')->execute();
    }

    catch (Exception $e)
    {
        if ( ! IN_PRODUCTION) throw $e;

        Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));

        $request->status = 500;
        $request->response = Request::factory('error/500')->execute();
    }

    if ($request->response) {
        // Get the total memory and execution time
        $total = array(
            '{memory_usage}'   => number_format((memory_get_peak_usage() - KOHANA_START_MEMORY) / 1024, 2).'KB',
            '{execution_time}' => number_format(microtime(TRUE) - KOHANA_START_TIME, 5).' seconds',
        );

        // Insert the totals into the response
        $request->response = strtr((string) $request->response, $total);

        // Email Anti Spam
        $request->response = str_replace('@@', '&#64;', $request->response);

        // Amazon CloudFront Rewrite
        // Source: http://nanrenwa.s3.amazonaws.com/
        if (IN_PRODUCTION) {
            $CDN = Request::$protocol == 'https' ? Kohana::config('global')->site_cdn_ssl : Kohana::config('global')->site_cdn;
            $request->response = str_replace('/_img_cdn/', Request::$protocol.'://'. $CDN .'/_img/', $request->response);
        } else {
            // Disable ssl on local
            //$request->response = str_replace(array('https', 'nanrenwa.com'), array('http', 'nanrenwa.net'), $request->response);
        }

        //压缩 html
        function _commentCB($m) {
            return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<![')) ? $m[0] : '';
        }

        $search = array(
            '/(\>|\;|\})[^\S ]+/s',  // strip whitespaces after tags|;|}, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '\\1',
            '<',
            '\\1'
        );

        //Remove HTML comments (not containing IE conditional comments)
        /*
        if (!isset($_REQUEST['debug']) && !Auth::instance()->logged_in('admin') && IN_PRODUCTION) {
            $request->response = preg_replace_callback('/<!--([\\s\\S]*?)-->/', '_commentCB', $request->response);
            //Remove Js comments - source: http://stackoverflow.com/questions/19509863/how-to-remove-js-comments-using-php
            $request->response = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/.*))/', '', $request->response);
            $request->response = preg_replace($search, $replace, $request->response);
        }
        */
    }

    /**
     * Display the request response.
     */
    echo $request->send_headers()->response;
}
