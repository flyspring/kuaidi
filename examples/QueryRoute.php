<?php

require '../vendor/autoload.php';

use SpringExpress\Express\ExpressManager;

//配置文件
// $name = 'kdniao';
// $config = [
//     'app_id' => 'test1324508', 
//     'app_key' => '258302ed-b3f2-4057-9a7d-b86597981eb6',
//     'query_url' => 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json', 
// ];

// $expressCode = 'SF';
// $orderId = 'A1111';
// $expressNo = '25231454052';

$name = 'kdwang';
$config = [
    'app_id' => '432ca6761e6194eb3cea4cbf26ebfae4',
    'query_url' => 'http://api.kuaidi.com/openapi.html', //http://www.kuaidi.com/index-ajaxselectcourierinfo-4307161657065-yunda.html
];
$expressCode = 'yunda';
$orderId = 'A1111';
$expressNo = '4306361911851';


$expressManager = new ExpressManager($config);
$express = $expressManager->express($name);

$result = $express->queryRoute($expressCode, $expressNo, $orderId);

var_dump($result);