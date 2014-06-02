<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * 首页
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class IndexController extends BaseController {
	
	function __construct() {
		parent::__construct();
	}
    
    public function index(){
		$this->display();
    }
    
    /**
     * 登录动作
     */
    function signin() {
    	if (!IS_POST) return;
    	
    	$userName = I('post.username');
    	$userPass = I('post.password');
    	$verify   = I('post.verify');
    	if ($verify == '') {
    		$this->error('请填写验证码！', '/');
    		return;
    	};
    	$verify = md5($verify);
    	if (strcmp($verify, session('CIIQ_VRERIFY')) != 0) {
    		$this->error('验证码错误！', '/');
    		return;
    	};
    	$result = $this->varifyPassword($userName, $userPass);
    	if (!$result) {
    		$this->error('用户名或密码错误！', '/');
    		return;
    	};
    	$this->success('登录成功', '/dashboard');
    }
    
    /**
     * 注销动作
     */
    function signout() {
    	session('enterprise_id',       null);
    	session('enterprise_name',     null);
    	session('enterprise_role_id',  null); // 1=admin,2=agent,3=shop
    	session('enterprise_group_id', null);
    	session('enterprise_expire',   null);
    	$this->success('退出登录', '/');
    }
}