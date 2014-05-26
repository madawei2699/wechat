<?php
namespace Demo\Controller;

use Think\Controller;

/**
 * Index 类
 * 网站首页
 * http://127.0.0.1:124
 * http://demo.f-fusion.com
 * 
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class IndexController extends Controller {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	if (IS_POST) {
    		$user = I('post.username');
    		$pswd = I('post.password');
    		if ($user == 'david' && $pswd == '18051802289') {
    			session('admin', 'david');
    			redirect('/dashboard');
    			return;
    		};
    	};
		$this->display('index/index');
    }
}