<?php

require '../vendor/autoload.php';

use SpringExpress\Express\ExpressManager;

//配置文件
$name = 'kdniao';
$config = [
    'app_id' => 'test1324508', 
    'app_key' => '258302ed-b3f2-4057-9a7d-b86597981eb6',
    'query_url' => 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json', 
];

$expressCode = 'SF';
$orderId = 'A1111';
$expressNo = '25231454052';


$expressManager = new ExpressManager($config);
$express = $expressManager->express($name);

$result = $express->queryRoute($expressCode, $expressNo, $orderId);

var_dump($result);