<?php

define('LOCAL', 0);
define('DEV', 1);
define('APP_KEY', 'wxa85c8c3343fea40d');
define('APP_SECRET', 'dafc7c3d74ddddbbe1bf81e65a3938c2');

function initRedbean() {

    require_once __DIR__ . '/lib/rb.php';
    if (LOCAL) {
        R::setup('mysql:host=localhost;dbname=sntime;charset=utf8;encoding:utf8', 'root', '');
    } else {
        R::setup('mysql:host=localhost;dbname=sntime;charset=utf8;encoding:utf8', 'root', '123456');
    }
}

function render($tpl, $variables = array()) {
    require_once __DIR__ . '/lib/Twig/Autoloader.php';
    Twig_Autoloader::register(); 
    $loader = new Twig_Loader_Filesystem( __DIR__ . '/views');
    $twig = new Twig_Environment($loader, array(
        'cache' => __DIR__ . '/cache',
        'debug' => DEV
    ));
    
    $variables['authUrl'] = getWxAuthUrl('index.php', true);
    echo $twig->render($tpl, $variables);
}

function redirect($url, $permanent = false) {
    header('Location: ' . $url, true, $permanent ? 301 : 302);
    exit();
}

function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function getWxAuthUrl($relativeUri, $base = false) {
    $redirectUri = urlencode('http://mfgame.neone.com.cn/wheel/' . $relativeUri);
    $scope = $base ? 'snsapi_base' : 'snsapi_userinfo';
    $authUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . APP_KEY .'&redirect_uri=' . $redirectUri . '&response_type=code&scope=' . $scope . '&state=1#wechat_redirect';
    return $authUrl;
}

function getWxUserInfo($code, $debug = false) {
    if ($debug) {
        return array(
            'nick' => uniqid('u_'),
            'head' => 'http://baidu.com',
            'openid' => uniqid('wx_')
        );
    }
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    $url .= 'appid=' . APP_KEY . '&secret=' . APP_SECRET;
    $url .= '&code=' . $code . '&grant_type=authorization_code';
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url); 
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    $ret = curl_exec($c);
    $retArr = json_decode($ret, true);
    curl_close($c);
    
    $accessToken = $retArr['access_token'];
    $openid = $retArr['openid'];
    
    if (!$accessToken || !$openid) {
        return false;
    }
    
    $url2 = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $accessToken . '&openid=' . $openid . '&lang=zh_CN';
    $c2 = curl_init();
    curl_setopt($c2, CURLOPT_URL, $url2); 
    curl_setopt($c2, CURLOPT_RETURNTRANSFER, TRUE);
    $ret = curl_exec($c2);
    $retArr = json_decode($ret, true);
    curl_close($c2);
    
    return array(
        'nick' => $retArr['nickname'],
        'head' => $retArr['headimgurl'],
        'openid' => $openid
    );
}

function getWxBaseInfo($code, $debug = false) {
    if ($debug) {
        return uniqid('wx_');
    }
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    $url .= 'appid=' . APP_KEY . '&secret=' . APP_SECRET;
    $url .= '&code=' . $code . '&grant_type=authorization_code';
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url); 
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
    if (!LOCAL) {
        curl_setopt($c, CURLOPT_PROXY, PROXY);
        curl_setopt($c, CURLOPT_PROXYPORT, '8080');
    }
    curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
    $ret = curl_exec($c);
    $retArr = json_decode($ret, true);
    curl_close($c);
    
    if (!isset($retArr['openid'])) {
        return false;
    }
    
    return $retArr['openid'];
}
