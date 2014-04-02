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
		//$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>Control</b>！</p></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
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
    	$this->success('登录成功', '/frame');
    	// 需要判断角色，根据角色限制权限
    }
}