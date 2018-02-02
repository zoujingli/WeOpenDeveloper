<?php

// +----------------------------------------------------------------------
// | WeChatDeveloper
// +----------------------------------------------------------------------
// | 版权所有 2014~2018 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/WeChatDeveloper
// +----------------------------------------------------------------------

$sql = <<<SQL
CREATE TABLE `wechat_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorizer_appid` varchar(100) DEFAULT NULL COMMENT '公众号APPID',
  `authorizer_access_token` varchar(200) DEFAULT NULL COMMENT '公众号Token',
  `authorizer_refresh_token` varchar(200) DEFAULT NULL COMMENT '公众号刷新Token',
  `func_info` varchar(100) DEFAULT NULL COMMENT '公众号集权',
  `nick_name` varchar(50) DEFAULT NULL COMMENT '公众号昵称',
  `head_img` varchar(200) DEFAULT NULL COMMENT '公众号头像',
  `expires_in` bigint(20) DEFAULT NULL COMMENT 'Token有效时间',
  `service_type` tinyint(2) DEFAULT NULL COMMENT '公众号实际类型',
  `service_type_info` tinyint(2) DEFAULT NULL COMMENT '服务类型信息',
  `verify_type` tinyint(2) DEFAULT NULL COMMENT '公众号实际认证类型',
  `verify_type_info` tinyint(2) DEFAULT NULL COMMENT '公众号认证类型',
  `user_name` varchar(100) DEFAULT NULL COMMENT '众众号原始账号',
  `alias` varchar(100) DEFAULT NULL COMMENT '公众号别名',
  `qrcode_url` varchar(200) DEFAULT NULL COMMENT '公众号二维码地址',
  `business_info` varchar(255) DEFAULT NULL,
  `principal_name` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `idc` tinyint(1) unsigned DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态(1正常授权,0取消授权)',
  `total` bigint(20) unsigned DEFAULT '0' COMMENT '统计调用次数',
  `appkey` char(32) DEFAULT NULL COMMENT '接口KEY',
  `appuri` varchar(255) DEFAULT NULL COMMENT '响应接口APP',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人ID',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_wechat_config_authorizer_appid` (`authorizer_appid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='公众号授权参数';
SQL;

try {

    // 1. 手动加载入口文件
    include "../vendor/autoload.php";
    include "../WeOpen/WeChat.php";

    // 2. 准备配置参数
    $config = [
        'component_appid'          => 'wx4e63e993e222df8d',
        'component_token'          => 'P8QHTIxpBEq88IrxatqhgpBm2OAQROkI',
        'component_appsecret'      => '7cfa1afa87a41e2ea3445cea015c0974',
        'component_encodingaeskey' => 'L5uFIa0U6KLalPyXckyqoVIJYLhsfrg8k9YzybZIHsx',
    ];
    // 注册授权公众号 AccessToken 处理
    $config['GetAccessTokenCallback'] = function ($authorizer_appid) use ($config) {
        $open = new \WeOpen\WeChat($config);
        $authorizer_refresh_token = ''; // 从数据库去找吧，在授权绑定的时候获取到了
        $result = $open->refreshAccessToken($authorizer_appid, $authorizer_refresh_token);
        if (empty($result['authorizer_access_token'])) {
            throw new \WeChat\Exceptions\InvalidResponseException($result['errmsg'], '0');
        }
        $data = [
            'authorizer_access_token'  => $result['authorizer_access_token'],
            'authorizer_refresh_token' => $result['authorizer_refresh_token'],
        ];
        // 需要把$data记录到数据库
        return $result['authorizer_access_token'];
    };

    // 3 使用第三方服务创建接口实例
    $open = new \WeOpen\WeChat($config);
    $wechat = $open->instance('授权公众号APPID', 'User');

} catch (Exception $e) {

    // 出错啦，处理下吧
    echo $e->getMessage() . PHP_EOL;

}