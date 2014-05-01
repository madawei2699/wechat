<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Member 类
 * 网站新闻动态
 * http://127.0.0.1:122
 * http://x.f-fusion.com
 * 
 * @category Site
 * @package Site
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class MemberController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->login();
    }
    
    function reg() {
		$this->display('index/member_reg');
    }
    
    function login() {
		$this->display('index/member_login');
    }
    
    function logout() {
		$this->display('index/member_login');
    }
}