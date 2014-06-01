<?php
namespace Janusking\Controller;

use Think\Controller;

class IndexController extends BaseController {
	/**
	 * 应用专属微信的配置
	 * @var array
	 */
	private $JANUSKING_WECHAT;
	
	function __construct() {
		parent::__construct();
		$this->JANUSKING_WECHAT = $this->WECHATCONFIG;
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
    	$result = ($userName == 'janusking' && $userPass == 'A2cf59');
    	if (!$result) {
    		$this->error('用户名或密码错误！', '/');
    		session('janusking_admin', null);
    		return;
    	};
    	session('janusking_admin', 1);
    	$this->success('登录成功', '/apply/lists');
    }
    
    /**
     * 注销动作
     */
    function signout() {
    	session('janusking_admin', null);
    	$this->success('退出登录', '/');
    }
}