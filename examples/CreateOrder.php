<?php

require '../vendor/autoload.php';

use SpringExpress\Express\ExpressManager;

//配置文件
$name = 'kdniao';
$config = [
    'app_id' => 'test1324508', 
    'app_key' => '258302ed-b3f2-4057-9a7d-b86597981eb6',
    'create_url' => 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json', 
];

$expressCode = 'SF';
$orderId = 'A1111';
$sender = [
    'name' => 'test',
    'mobile' => '15312002703', 
    'province' => '江苏省',
    'city' => '南京市',
    'district' => '建邺区',
    'address' => '兴隆大街'
];
$receiver = [
    'name' => 'test',
    'mobile' => '15312002703',
    'province' => '江苏省',
    'city' => '南京市',
    'district' => '建邺区',
    'address' => '兴隆大街'
];
$options = [
    'commodity' => [['name' => '中药']],
    'pay_type' => 1, 
    'exp_type' => 1,
    'need_print_tpl' => 1, 
];


$expressManager = new ExpressManager($config);
$express = $expressManager->express($name);

$result = $express->createOrder($expressCode, $orderId, $sender, $receiver, $options);

var_dump($result);