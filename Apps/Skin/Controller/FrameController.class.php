<?php
namespace Skin\Controller;

use Think\Controller;

/**
 * Frame 类
 * 主要用于显示控制台框架页面
 * 
 * @category Skin
 * @package Skin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class FrameController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!session('?admin_id') || !session('?admin_expire')) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$expire = session('admin_expire');
    	if ($this->time > (int)$expire) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	session('admin_expire',   $this->time+$this->EXPIRE);
	}
	
    function index(){
		$this->display();
    }
    
    function head() {
    	$this->display();
    }
    
    function side() {
    	$this->display();
    }
    
    function content() {
    	$this->display(); 
    }
}