<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * Game 类
 * 主要用于渠道
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class GameController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'game');
	}
	
	function index() {
		$this->display();
	}
	
	function egg() {
		$this->display();
	}
	
	function card() {
		$this->display();
	}
	
	function wheel() {
		$this->display();
	}
}