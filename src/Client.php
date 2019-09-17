<?php

namespace MaoerGame;

/**
 * Class Client
 *
 * @package MaoerGame
 */
class Client
{
    // PHP Client SDK 版本
    const SDK_VERSION = "1.0.0";

    // 配置参数
    private $config = [];
    // 网关地址
    private $gateway_url = '';

    // 请求方法
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    // 查询用户信息
    const API_USER_INFO = '/api/userinfo';
    // 查询订单
    const API_GET_ORDER = '/api/get-order';

    /**
     * Client constructor.
     *
     * @param string $app_id 游戏 ID
     * @param string $merchant_id 商户 ID
     * @param string $access_id 游戏服务端对应的 ID
     * @param string $access_secret 游戏服务端对应的密钥
     */
    public function __construct($app_id, $merchant_id, $access_id, $access_secret)
    {
        $this->config = [
            'app_id' => $app_id,
            'merchant_id' => $merchant_id,
            'access_id' => $access_id,
            'access_secret' => $access_secret,
        ];
    }

    /**
     * 设置网关地址
     *
     * @param string $url
     */
    public function setGatewayURL($url)
    {
        $this->gateway_url = $url;
    }

    /**
     * 查询用户信息
     *
     * @param array $params k-v 键值对，例 ['token' => ''] 详见 API 文档
     * @param bool $as_array 结果是否作为数组输出，否则为 json 对象
     * @return array|\stdClass
     */
    public function queryUserInfo($params, $as_array = true)
    {
        $output = $this->request(self::METHOD_GET, self::API_USER_INFO, $params, []);
        return json_decode($output, $as_array);
    }

    /**
     * 查询订单
     *
     * @param array $params k-v 键值对，例 ['tr_no' => '10000000900000000090123456789012', 'uid' => 346286] 详见 API 文档
     * @param boolean $as_array 结果是否作为数组输出，否则为 json 对象
     * @return array|\stdClass
     */
    public function queryOrder($params, $as_array = true)
    {
        $output = $this->request(self::METHOD_GET, self::API_GET_ORDER, $params, []);
        return json_decode($output, $as_array);
    }

    public function postUserLogin($params, $as_array = true)
    {
        $output = $this->request(self::METHOD_POST, '/user/login', $params, [
            'User-Agent' => 'MissEvanApp/4.3.3 (iOS;12.0;iPhone9,1)',
            'Cookie' => 'equip_id=1cd36ca3-d966-4e4e-8499-98a60987b78a',
            'Accept' => 'application/json',
//            'Auth' => '123456'
        ]);

        return json_decode($output, $as_array);
    }

    /**
     * 发起请求
     *
     * @param string $method GET|POST
     * @param string $api 例 /api/userinfo
     * @param array $params k-v 键值对
     * @param array $header k-v 键值对
     * @return string json body string
     */
    private function request($method, $api, $params, $header)
    {
        $request_params = array_merge($this->config, $params);
        if (self::METHOD_GET === $method) {
            $query_str = http_build_query($request_params);
            return Http::instance()->curl_get($this->gateway_url . $api . '?' . $query_str, $header);
        } else {
            return Http::instance()->curl_post($this->gateway_url . $api, $params, $header);
        }
    }

}
