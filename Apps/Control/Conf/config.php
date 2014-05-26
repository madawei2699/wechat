<?php
/**
 * 配置文件
 */
defined('THINK_PATH') or exit();
return array(
    /* 应用设定 */
    'CONTROL_SALT' => '539eaa732d6a25fa2f6e6c38674074ec',    // 系统级别扰码，作用在模块扰码前
	'URL_CASE_INSENSITIVE' => true,
	'LOAD_EXT_CONFIG' => 'db',
    'TABLE_PREFIX_SYSTEM' => 'control',
    'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
    'TABLE_PREFIX_FANS' => 'fans',
);
