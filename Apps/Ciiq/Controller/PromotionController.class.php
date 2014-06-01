<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * Promotion 类
 * 主要用于渠道
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class PromotionController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'promotion');
	}
	
	function index() {
		$this->display();
	}
	
	function price() {
		$this->display();
	}
	
	function time() {
		$this->display();
	}
	
	function group() {
		$this->display();
	}
	
	function moneypack() {
		$this->display();
	}
}