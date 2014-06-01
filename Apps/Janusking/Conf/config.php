<?php
defined('DEV_MODE') or exit();
return array(
	'URL_CASE_INSENSITIVE' => true,
	'TABLE_PREFIX' => 'janusking',
	'TABLE_PREFIX_SYSTEM' => 'control',
	'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
	'TABLE_PREFIX_FANS' => 'fans',
	'WECHAT' => array(
		'DOMAIN_PREFIX' => 'http://janusking.f-fusion.com/',
	),
	'LOAD_EXT_CONFIG' => 'db',
	'WEB_EXT_CFG' => array(),
);