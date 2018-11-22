<?php defined('SYSPATH') or die('No direct access allowed.');

//微代言奖品
class Model_Qwt_Xdbitem extends ORM {
    // var $_table_name = 'test';

    protected $_created_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );

    protected $_updated_column = array(
        'column'    => 'lastupdate',
        'format'    => TRUE,
    );
}

/*
产品名称：
产品图片：
限制兑换：限购几件
剩余数量：
产品排序:
是否隐藏:
虚拟产品？
领取链接
开始日期：
截止日期：
实际价格：
消耗积分：
详细说明：
*/
