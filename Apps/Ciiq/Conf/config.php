<?php
/**
 * 配置文件
 */
defined('DEV_MODE') or exit();
return array(
	'URL_CASE_INSENSITIVE' => true,
	'TABLE_PREFIX' => 'Ciiq',
	'TABLE_PREFIX_SYSTEM' => 'control',
	'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
	'TABLE_PREFIX_FANS' => 'fans',
	'LOAD_EXT_CONFIG' => 'db',
	'WECHAT_EXT_CFG' => array(
		'DOMAIN_PREFIX' => 'http://ciiq.f-fusion.com/',
	),
	'WEB_EXT_CFG' => array(
		'PAGE_TITLE' => '驰球保险箱'
	),
	'APPLICATION_USER_SALT' => '63c129111d4b3868728ca6fabca8887d',
	'APPLICATION_SESSION_EXPIRE' => 900,
);