<?php
/**
 * 配置文件
 */
defined('THINK_PATH') or exit();
return  array(
    /* 应用设定 */
    'MEMBER_SALT' => 'a539f66a2386eafa27405e6c74ec732d',    // 系统级别扰码，作用在模块扰码前
	'MODEL_PREFIX_SYSTEM' => 'Admin',
    'TABLE_PREFIX_SYSTEM' => 'admin',
    'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
	'LOAD_EXT_CONFIG' => 'db',
	'WEB_PAGE_TITLE' => '（原福建省环境保护总公司-工程及设备部）',
	'WEB_PAGE_CO' => '西闽科仪',
);
