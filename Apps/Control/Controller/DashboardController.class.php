<?php
namespace Control\Controller;

use Think\Controller;

/**
 * Dashboard 类
 * 主要用于显示控制台概览页面
 *
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class DashboardController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'dashboard');
	}
	
	function index() {
		$this->display();
	}
	
	function settings() {
		$this->display();
	}
	
	function message() {
		$readed = I('get.readed');
		$unread = I('get.unread');
		$this->display();
	}
}