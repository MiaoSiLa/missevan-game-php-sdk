<?php

namespace MaoerGame;

/**
 * Class Http 发送 HTTP 请求类
 *
 * @package MaoerGame
 */
class Http
{
    private static $instance = null;

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private static function getUserAgent()
    {
        return sprintf('MaoerGameSDK PHPClient/%s', Client::SDK_VERSION);
    }

    /**
     * @param array $header k-v 键值对，例 ["Content-type" => "application/json;", "Accept" => "application/json"]
     * @return array
     */
    private function getHeader($header)
    {
        $result = [];
        foreach ($header as $k => $v) {
            $result[] = $k . ':' . $v;
        }
        return $result;
    }

    /**
     * @param string $url
     * @param array $header k-v 键值对，例 ["Content-type" => "application/json;", "Accept" => "application/json"]
     * @return mixed
     */
    public function curl_get($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, self::getUserAgent());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHeader($header));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $header k-v 键值对，例 ["Content-type" => "application/json;", "Accept" => "application/json"]
     * @return mixed
     */
    public function curl_post($url, $data, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, self::getUserAgent());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHeader($header));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}
