<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Index 类
 * 网站首页
 * http://127.0.0.1:122
 * http://x.f-fusion.com
 * 
 * @category Site
 * @package Site
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$model = $this->getModel('Article');
    	$list = $model->field('id,create_time,title')->where(array('ch'=>301, 'state'=>1))->limit(6)->select();
    	$this->assign('list', $list);
		$this->display('index/index');
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