### PHP Client for Maoer Game SDK

#### 启用
```sh
composer require xchenhao/maoergame-sdk-phpclient
```

#### 示例（见 ./demo.php）

```php
use MissEvanGame\Client;

// 初始化配置
$client = new Client('app_id', 'merchant_id', 'access_id', 'access_secret');
// 设置网关地址
$client->setGatewayURL('getway_url');
// 请求 API
$result = $client->queryUserInfo(['token' => 'xxxxxx']);
print_r($result);
```

#### API

| API | 方法 | 说明 |
| --- | --- | --- |
| /api/userinfo | queryUserInfo(array params, bool $as_array) | 查询用户信息 |
| /api/get-order | queryOrder(array params, bool $as_array) | 查询订单 |

#### 其它方法
| 方法 | 说明 |
| --- | --- |
| verifyCallbackSign(string $raw_body, string $access_secret) | 检验回调签名 |
