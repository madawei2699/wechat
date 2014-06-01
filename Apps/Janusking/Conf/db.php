<?php
defined('DEV_MODE') or exit();
if (strcmp(constant('DEV_MODE'), 'LOCAL') == 0) {
	return array(
		//'配置项'=>'配置值'
		'DB_FIELDS_CACHE' => true, // CACHE FIELD
		'DB_TYPE'		=> 'mysql',
		'DB_HOST' 		=> '127.0.0.1',
		'DB_NAME' 		=> 'bdm0150168_db',
		'DB_USER'		=> 'bdm0150168',
		'DB_PWD' 		=> '123456',
		'DB_PORT' 		=> '3306',
		'DB_PREFIX' 	=> '',
	);
};
if (strcmp(constant('DEV_MODE'), 'REMOTE') == 0) {
	return array(
		//'配置项'=>'配置值'
		'DB_FIELDS_CACHE' => true, // CACHE FIELD
		'DB_TYPE'		=> 'mysql',
		'DB_HOST' 		=> 'bdm-015.hichina.com',
		'DB_NAME' 		=> 'bdm0150168_db',
		'DB_USER'		=> 'bdm0150168',
		'DB_PWD' 		=> 'zy8629ff',
		'DB_PORT' 		=> '3306',
		'DB_PREFIX' 	=> '',
	);
};
