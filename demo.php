<?php

require './src/Client.php';
require './src/Http.php';
require './src/Signature.php';

use MaoerGame\Client;

$app_id = '8';
$merchant_id = '3000';
$access_id = 'access_id';
$access_secret = 'access_secret';
$getway_url = 'http://127.0.0.1:8999';

// 初始化配置
$client = new Client($app_id, $merchant_id, $access_id, $access_secret);
// 设置网关地址
$client->setGatewayURL($getway_url);

// 查询用户信息
$result = $client->queryUserInfo(['token' => '2|5d809a482f66aa4f4f93b110|33037d3bf648260c|1568709192|4237748454c9daf2589a59cbfbbbd686432968068f14646b']);
var_export($result);
// 返回示例
/*
array (
  'code' => 0,
  'info' =>
  array (
    'uid' => 6,
    'username' => 'chenhao1234569',
    'avatar' => 'http://static.missevan.com/avatars/201812/17/131eefbfd4b463f7038ff3ce8d2fe8c7144631.jpg',
    'realname_verified' => true,
  ),
  'request_id' => '12312321',
  'timestamp' => 1568709216,
)
*/

// 查询订单
$result = $client->queryOrder(['tr_no' => '12345678901234567890123456789012', 'uid' => '3013620']);
var_export($result);
// 返回示例
/*
array (
  'code' => 0,
  'info' =>
  array (
    'subject' => '猫耳游戏-荣耀点',
    'body' => '全职高手荣耀点',
    'out_trade_no' => '10000000900000000090123456789012',
    'total_fee' => 100,
    'server_id' => 1,
    'role_id' => 1,
    'role' => '叶修',
    'game_money' => 10,
    'extension_info' => '',
    'app_id' => 8,
    'user_id' => 3013620,
    'Type' => 2,
    'Status' => -1,
  ),
  'request_id' => '12312321',
  'timestamp' => 1568702546,
)
*/
