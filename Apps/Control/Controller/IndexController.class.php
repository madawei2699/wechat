<?php
namespace Control\Controller;

use Think\Controller;

/**
 * Index 类
 * 主要用于显示控制台登录首页
 * 
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
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
    	if (strcmp($verify, session('CONTROL_VRERIFY')) != 0) {
    		$this->error('验证码错误！', '/');
    		return;
    	};
    	$result = $this->varifyPassword($userName, $userPass);
    	if (!$result) {
    		$this->error('用户名或密码错误！', '/');
    		return;
    	};
    	$this->success('登录成功', '/web/project');
    	// 需要判断角色，根据角色限制权限
    }
    
    /**
     * 注销动作
     */
    function signout() {
    	session('admin_id',       null);
		session('admin_name',     null);
		session('admin_group_id', null);
		session('admin_role_id',  null);
		session('admin_ent_id',   null);
		session('admin_expire',   null);
    	$this->success('退出登录', '/');
    }
}