<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * Index 类
 * 主要用于显示控制台登录首页
 * http://127.0.0.1:121
 * http://xx.f-fusion.com
 * 
 * @category Admin
 * @package Admin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display();
    }
    
    /**
     * 登录动作
     */
    function signin() {
    	if (!IS_POST) return;
    	
    	$userName = I('post.user');
    	$userPass = I('post.password');
    	$verify   = I('post.verify');
    	if ($verify == '') {
    		$this->error('请填写验证码！', '/');
    		return;
    	};
    	$verify = md5($verify);
    	if (strcmp($verify, session('ADMIN_VRERIFY')) != 0) {
    		$this->error('验证码错误！', '/');
    		return;
    	};
    	$result = $this->varifyPassword($userName, $userPass);
    	if (!$result) {
    		$this->error('用户名或密码错误！', '/');
    		return;
    	};
    	$this->success('登录成功', '/frame');
    	// 需要判断角色，根据角色限制权限
    }
    
    /**
     * 注销动作
     */
    function singout() {
    	session('admin_id',       null);
    	session('admin_name',     null);
    	session('admin_group_id', null);
    	session('admin_expire',   $this->time-1);
    	$this->success('您已退出', '/');
    }
}