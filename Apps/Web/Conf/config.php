<?php
defined('DEV_MODE') or exit();
return array(
	'URL_CASE_INSENSITIVE' => true,
	'LOAD_EXT_CONFIG' => 'db',
	'WEB_EXT_CFG' => array(
		'MENU_SITE' => array(
			array('NAME'=>'Home', 'URL'=>'http://www.f-fusion.com/'),
			array('NAME'=>'关于熔意', 'URL'=>'http://www.f-fusion.com/about'),
			array('NAME'=>'品牌进化论®', 'URL'=>'http://www.f-fusion.com/evolution'),
			array('NAME'=>'熔意服务', 'URL'=>'http://www.f-fusion.com/service'),
			array('NAME'=>'熔意案例', 'URL'=>'http://www.f-fusion.com/'),
			array('NAME'=>'联系我们', 'URL'=>'http://www.f-fusion.com/contact'),
		),
		'MENU_CATEGORY' => array(
			array('NAME'=>'整合营销', 'URL'=>'/marketing'),
			array('NAME'=>'品牌建设', 'URL'=>'/brand'),
			array('NAME'=>'大电商', 'URL'=>'/ecommerce'),
			array('NAME'=>'移动营销', 'URL'=>'/it'),
			array('NAME'=>'品牌孵化X', 'URL'=>'/brandx'),
		),
		'MENU_IT' => array(
			array('NAME'=>'微店&移动电商分销系统', 'URL'=>'/it'),
			array('NAME'=>'店铺策划&开发', 'URL'=>'/it'),
			array('NAME'=>'APP定制开发', 'URL'=>'/it'),
			array('NAME'=>'电商托管', 'URL'=>'/it'),
		),
		'STRING_SITE_TITLE' => 'FlickerFusion 熔意',
		'STRING_FLAG1' => array('做明白生意，创科学品牌','品牌价值创造者','品牌进化论创立者'),
		'STRING_FLAG2' => '服务客户，不是帮客户花钱，而是为客户赚钱！用企业家视角，运作品牌事业！',
		'STRING_FLAG3' => '移动电商&系统开发解决方案',
		'STRING_ADDRESS' => 'FlickerFusion Branding Agency<br/>ADD: 上海市梅陇西路413号311-313<br/>TEL: 021-34096792 www.f-fusion.com',
		'STRING_COPYRIGHTS' => '&copy; 2008-2014 上海熔意品牌营销管理机构 All Rights Reserved.<br/>本网站所有图片及资料均为本公司版权所有，对于任何形式的侵权行为，我们将保留一切追究法律责任的权利。',
	),
);