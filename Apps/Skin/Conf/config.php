<?php
/**
 * 配置文件
 */
defined('THINK_PATH') or exit();
return  array(
    /* 应用设定 */
    'ADMIN_SALT' => '86eafa274074eca539f6e6c3732d6a25',    // 系统级别扰码，作用在模块扰码前
	'MODEL_PREFIX_SYSTEM' => 'Admin',
    'TABLE_PREFIX_SYSTEM' => 'admin',
    'TABLE_PREFIX_ENTERPRISE' => 'enterprise',
	'LOAD_EXT_CONFIG' => 'db',
);
