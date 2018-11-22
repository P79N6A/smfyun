<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    //生产环境
    'default' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            'hostname'   => '127.0.0.1',
            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),

    //开发环境
    'local' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => FALSE,
        'profiling'    => TRUE,

        'connection' => array (
            'hostname'   => '127.0.0.1',
            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'slave' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'slave1' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //积分宝
    'sns' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'wdy' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'wdyr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //订阅宝
     'dyb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
       //订阅宝
     'dybr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //微订宝
     'wdb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'wdbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //优惠宝
     'yhb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'yhbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //签到
     'qd' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //签到
     'qdr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //红包
     'hbb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //红包
     'hbbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //wujiu
     'wj' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'wjr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
         //xiaochengxu
     'xcx' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun_com',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'xcxr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun_com',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //码上
     'qwt' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'qwtr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //微红包
     'whb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //微红包
     'whbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'scb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'scbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //打卡宝
    'dka' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'dkar' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => isset($_GET['debug']),

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //分销宝
    'fxb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'fxbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //江中鱼塘
    'ytb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //江中鱼塘
    'ytbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //全员分销
    'qfx' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //全员分销
    'qfxr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //福利宝
    'flb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //福利宝
    'flbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    //km
    'kmi' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //km
    'kmir' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     //wfb
    'wfb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'wfbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'rwb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'rwbr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'yyx' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'yyxr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'yyb' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
     'yybr' => array
    (
        'type'       => 'MySQLi',
        'charset'      => 'utf8',
        'caching'      => TRUE,
        'profiling'    => FALSE,

        'connection' => array (
            // 'hostname'   => 'rds85t28eo9t64wgn8k0.MySQLi.rds.aliyuncs.com',
            // 'hostname'   => 'smfyunsz.MySQLi.rds.aliyuncs.com',
            'hostname'   => '127.0.0.1', //bj

            'database'   => 'smfyun',
            'username'   => 'root',
            'password'   => '199361',
            'persistent' => FALSE,
        ),
    ),
    'smfyun' => array
        (
            'type'       => 'MySQLi',
            'connection' => array(
                'hostname'   => '127.0.0.1',
                'database'   => 'smfyun.com',
                'username'   => 'root',
                'password'   => '199361',
                'persistent' => FALSE,
            ),
            'table_prefix' => '',
            'charset'      => 'utf8',
            'caching'      => TRUE,
            'profiling'    => FALSE,
        ),
);
