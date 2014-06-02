<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * Agent 类
 * 主要用于渠道
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class AgentController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'agent');
	}
	
	function index() {
		$l1 = region(array('level'=>1));
		$l2 = region(array('level'=>2));
		$l3 = region(array('level'=>3));
		$this->assign('level1', json_encode($l1));
		$this->assign('level2', json_encode($l2));
		$this->assign('level3', json_encode($l3));
		$this->display();
	}
	
	function user() {
		$this->display();
	}
	
	function shop() {
		$this->display();
	}
	
	function lbs() {
		$this->display();
	}
}