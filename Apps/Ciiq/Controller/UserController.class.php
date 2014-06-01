<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * User 类
 * 主要用于渠道
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class UserController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!session('?enterprise_id') || !session('?enterprise_expire')) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$expire = session('enterprise_expire');
    	if ($this->time > (int)$expire) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	session('enterprise_expire',   $this->time+C('APPLICATION_SESSION_EXPIRE'));
	}
	
	function index() {
		$this->display();
	}
}