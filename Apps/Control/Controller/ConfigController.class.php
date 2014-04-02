<?php
namespace Control\Controller;

use Think\Controller;

/**
 * 基本配置管理操作类
 * 
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class ConfigController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->display();
	}
}