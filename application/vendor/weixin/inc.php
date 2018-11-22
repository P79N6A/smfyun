<?php
define('ROOT_PATH', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

function curl_post_ssl($url, $vars, $second=30, $aHeader=array(), $bid=1)
{
    $ch = curl_init();

    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


    curl_setopt($ch, CURLOPT_SSLCERTTYPE,'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, ROOT_PATH.DS.'cert'.DS.$bid.DS.'apiclient_cert.pem');
    curl_setopt($ch, CURLOPT_SSLKEYTYPE,'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, ROOT_PATH.DS.'cert'.DS.$bid.DS.'apiclient_key.pem');
    // curl_setopt($ch, CURLOPT_CAINFO, ROOT_PATH.DS.'cert'.DS.$bid.DS.'rootca.pem');

    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);

    $data = curl_exec($ch);

    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        echo curl_error($ch);
        curl_close($ch);
        return false;
    }

}

//代替 file_get_contents
function curls($url, $timeout=5)
{
    // 1. 初始化
    $ch = curl_init();

    // 2. 设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

    // 3. 执行并获取HTML文档内容
    $info = curl_exec($ch);
    // 4. 释放curl句柄
    curl_close($ch);
    return $info;
}

// 加密
function encrypt($key, $plain_text)
{
    $plain_text = trim($plain_text);
    $iv = substr(md5($key), 0,mcrypt_get_iv_size(MCRYPT_CAST_256,MCRYPT_MODE_CFB));
    $c_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $plain_text, MCRYPT_ENCRYPT, $iv);
    return trim(chop(base64_encode($c_t)));
}

function decrypt($key, $c_t)
{
    $c_t = trim(chop(base64_decode($c_t)));
    $iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256,MCRYPT_MODE_CFB));
    $p_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $c_t, MCRYPT_DECRYPT, $iv);
    return trim(chop($p_t));
}

// echo encrypt('111', 'abcdefg');