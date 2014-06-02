<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * Shop 类
 * 主要用于电商
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class ShopController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'shop');
	}
	
	function index() {
		$this->display();
	}
	
	function quick() {
		$this->display();
	}
	
	function user() {
		$this->display();
	}
	
	function goods() {
		$this->display();
	}
	
	function member() {
		$this->display();
	}
	
	function lbs() {
		$this->display();
	}
	
	function media() {
		$this->display();
	}
	
	function qrcode() {
		$this->display();
	}
	
	function menu() {
		$this->display();
	}
}