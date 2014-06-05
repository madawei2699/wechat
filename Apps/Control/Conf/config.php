<?php
/**
 * 配置文件
 */
defined('THINK_PATH') or exit();
return array(
	'URL_CASE_INSENSITIVE' => true,
	'TABLE_PREFIX' => 'Control',
	'TABLE_PREFIX_SYSTEM' => 'control',
	'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
	'TABLE_PREFIX_FANS' => 'fans',
	'LOAD_EXT_CONFIG' => 'db',
	'WECHAT_EXT_CFG' => array(
		'DOMAIN_PREFIX' => 'http://www.f-fusion.com/',
	),
	'WEB_EXT_CFG' => array(
		'PAGE_TITLE' => '熔意 - 品牌 - 电商'
	),
	'APPLICATION_USER_SALT' => '539eaa732d6a25fa2f6e6c38674074ec', // 系统级别扰码，作用在模块扰码前
	'APPLICATION_SESSION_EXPIRE' => 900,
	'APPLICATION_LIST_PAGE_SIZE' => 10,
);
