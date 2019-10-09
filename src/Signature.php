<?php

namespace MissEvanGame;

/**
 * Class Signature 签名处理类
 *
 * @package MissEvanGame
 */
class Signature
{

    /**
     * 转义 uri 字符
     *
     * @param string $uri
     * @param bool $encode_slash
     * @return string
     */
    public static function uriEncode($uri, $encode_slash = true)
    {
        if ($uri === '') {
            return $uri;
        }
        $chars = str_split($uri);
        $encode_chars = array_map(function ($char) use ($encode_slash) {
            if (($char >= 'A' && $char <= 'Z')
                || ($char >= 'a' && $char <= 'z')
                || ($char >= '0' && $char <= '9')
                || $char === '_' || $char === '-' || $char === '~' || $char === '.') {
                return $char;
            } elseif ($char === '/') {
                return $encode_slash ? '%2F' : $char;
            } else {
                return '%' . strtoupper(bin2hex($char));
            }
        }, $chars);
        return implode('', $encode_chars);
    }

    /**
     * 生成签名
     *
     * @param string $access_secret
     * @param string $token
     * @param string $method GET|POST
     * @param string $uri
     * @param array $params 结构 ['GET' => [], 'POST' => [], 'RAW_BODY' => '', 'FILES' => []]
     * @param array $headers
     * @param string $content_type
     * @return string
     *
     * @throws \Exception
     */
    public static function buildSign($access_secret, $token, $method, $uri, $params, $headers, $content_type = 'application/x-www-form-urlencoded')
    {
        // WORKAROUND: 暂时兼容 80 与 443 端口
        $uri = str_replace([':80', ':443'], '', $uri);
        self::processParams($params);
        $canonical_url = self::uriEncode($uri, false);
        $canonical_query_str = self::getCanonicalQueryStr($params['GET']);
        $canonical_headers = self::getCanonicalHeaders($headers, $token);

        $str_to_sign = $method . "\n"
            . $canonical_url . "\n"
            . $canonical_query_str . "\n"
            . $canonical_headers . "\n";
        if ($method === 'POST') {
            $canonical_body = self::getCanonicalBody($content_type, $params);
            $str_to_sign .= $canonical_body . "\n";
        }

        return base64_encode(hash_hmac('sha256', $str_to_sign, $access_secret, true));
    }

    /**
     * @param $params
     */
    private static function processParams(&$params)
    {
        if (!array_key_exists('GET', $params)) {
            $params['GET'] = [];
        }
        if (!array_key_exists('POST', $params)) {
            $params['POST'] = [];
        }
        if (!array_key_exists('RAW_BODY', $params)) {
            $params['RAW_BODY'] = '';
        }
        if (!array_key_exists('FILES', $params)) {
            $params['FILES'] = [];
        }
    }

    /**
     * 获得用于验签的 Query 参数字符串
     *
     * @param array $query_params
     * @return string
     */
    private static function getCanonicalQueryStr($query_params)
    {
        return self::formatRequestKeyValue($query_params);
    }

    /**
     * 格式化请求中的 body 及 query 参数
     *
     * @param array $params 参数
     * @return string 格式化后的参数字符串
     */
    private static function formatRequestKeyValue($params)
    {
        $str = '';
        if (empty($params)) {
            return $str;
        }
        $params_format = [];
        foreach ($params as $param_name => $param) {
            if (is_array($param)) {
                $child_params = [];
                $is_assoc = array_values($param) === $param;
                foreach ($param as $key => $child_param) {
                    if ($is_assoc) {
                        $key_name = "{$param_name}[]";
                        $child_params[] = self::uriEncode($key_name) . '=' . self::uriEncode($child_param);
                    } else {
                        $key_name = "{$param_name}[{$key}]";
                        $child_params[$key_name] = self::uriEncode($key_name) . '=' . self::uriEncode($child_param);
                    }
                }
                ksort($child_params);
                $params_format[$param_name] = implode('&', $child_params);
            } else {
                $params_format[$param_name] = self::uriEncode($param_name) . '=' . self::uriEncode($param);
            }
        }
        ksort($params_format);
        return implode('&', $params_format);
    }

    /**
     * 获得用于验签的 header 字符串
     *
     * @param array $headers
     * @param string $token
     * @return string
     */
    private static function getCanonicalHeaders($headers, $token)
    {
        $header_names = array_keys($headers);
        $pattern_xm = '/^(x-m-.*)$/i';
        $pattern_cookie = '/^cookie$/i';
        $pattern_equip_id = '/.*equip_id=(.+);?/';
        $need_headers = ['equip_id' => 'equip_id:'];
        foreach ($header_names as $name) {
            if (preg_match($pattern_xm, $name)) {
                $need_headers[$name] = strtolower($name) . ':' . trim($headers[$name]);
            } elseif (preg_match($pattern_cookie, $name) && preg_match($pattern_equip_id, $headers[$name], $match)) {
                $need_headers['equip_id'] = 'equip_id:' . $match[1];
            }
        }
        if ($token = trim($token)) {
            $need_headers['token'] ='token:' . $token;
        }
        sort($need_headers);
        return implode("\n", $need_headers);
    }

    /**
     * 获得用于验签的 body 字符串
     *
     * @param $content_type
     * @param array $params 结构 ['GET' => [], 'POST' => [], 'RAW_BODY' => '', 'FILES' => []]
     * @return string
     * @throws \Exception
     */
    private static function getCanonicalBody($content_type, $params)
    {
        if (strpos($content_type, 'application/x-www-form-urlencoded') !== false) {
            $content = self::getPostParamStr($params['POST']);
        } elseif (strpos($content_type, 'multipart/form-data') !== false) {
            $content = self::getPostParamStr($params['POST']);
            if (!empty($params['FILES'])) {
                $param_names = [];
                foreach ($params['FILES'] as $param_name => $param) {
                    if (is_array($param['name'])) {
                        foreach ($param['name'] as $value) {
                            $param_names[] = self::uriEncode($param_name . '[]');
                        }
                    } else {
                        $param_names[] = self::uriEncode($param_name);
                    }
                }
                sort($param_names);
                $param_names_str = implode('&', $param_names);
                $unsigned_params = "UNSIGNED-PARAMTERS:{$param_names_str}";
                $content = $content . "\n" . $unsigned_params;
            }
        } elseif (strpos($content_type, 'application/json') !== false) {
            $content = $params['RAW_BODY'];
        } elseif (!$content_type && !$params['RAW_BODY']) {
            $content = '';
        } else {
            throw new \Exception('不支持的 MIME 类型');
        }

        return base64_encode(hash('sha256', $content, true));
    }

    /**
     * 获得 POST 参数用于验签的字符串
     *
     * @param array $post_params
     * @return string
     */
    private static function getPostParamStr($post_params)
    {
        return self::formatRequestKeyValue($post_params);
    }

    /**
     * 检验回调结果签名是否正确
     *
     * @param string $raw_body
     * @param string $access_secret
     * @return bool
     */
    public static function verifyCallbackSign($raw_body, $access_secret)
    {
        $data_array = json_decode($raw_body, true) ?: [];
        if (empty($data_array)) {
            return false;
        }
        if (!array_key_exists('data', $data_array)) {
            return false;
        }
        if (!array_key_exists('sign', $data_array)) {
            return false;
        }
        $sign = $data_array['sign'];
        return md5($data_array['data'] . $access_secret) === $sign;
    }

}
