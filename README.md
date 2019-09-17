### PHP Client for Maoer Game SDK

#### 应用示例（见 ./demo.php）

```php
// 初始化配置
$client = new Client('app_id', 'merchant_id', 'access_id', 'access_secret');
// 设置网关地址
$client->setGatewayURL($getway_url);
// 请求 API
$result = $client->queryUserInfo(['key' => 'value']);
print_r($result);
```

#### API

| API | 方法 | 说明 |
|---|---|---|
| /api/userinfo | queryUserInfo() | 查询用户信息 |
| /api/userinfo | queryOrder() | 查询订单 |
