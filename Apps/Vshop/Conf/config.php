<?php
/**
 * 配置文件
 */
defined('THINK_PATH') or exit();
return  array(
    /* 应用设定 */
    'CONTROL_SALT' => '5fa2f6e6a26740746c38539eaa732dec',    // 系统级别扰码，作用在模块扰码前
    'URL_CASE_INSENSITIVE' => true,
	'LOAD_EXT_CONFIG' => 'db',
);
