<?php

namespace MaoerGame;

class Client
{
    const SDK_VERSION = "1.0.0";

    private $app_id = 0;
    private $merchant_id = 0;
    private $access_id = '';
    private $access_secret = '';
    private $gateway_url = '';

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    // 查询用户信息
    const API_USER_INFO = '/api/userinfo';
    // 查询订单
    const API_GET_ORDER = '/api/get-order';

    public function __construct($app_id, $merchant_id, $access_id, $access_secret)
    {
        $this->app_id = $app_id;
        $this->merchant_id = $merchant_id;
        $this->access_id = $access_id;
        $this->access_secret = $access_secret;
    }

    public function setGatewayURL($url)
    {
        $this->gateway_url = $url;
    }

    public function queryUserInfo($token)
    {
        Http::instance()->curl_get($this->gateway_url . self::API_USER_INFO . '?token=' . $token, [

        ]);
    }

    public function queryOrder($trade_no, $uid)
    {

    }

}
