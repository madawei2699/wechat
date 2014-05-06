<?php
namespace Demo\Controller;

use Think\Controller;

/**
 * Dashboard 类
 * 网站首页
 * http://127.0.0.1:124/dashboard
 * http://demo.f-fusion.com/dashboard
 * 
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class DashboardController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display('index/dashboard');
    }
    
    function serv() {
		$this->display('index/serv');
    }
    
    function db() {
		$this->display('index/db');
    }
    
    function cdn() {
		$this->display('index/cdn');
    }
    
    function dns() {
		$this->display('index/dns');
    }
    
    function lvs() {
		$this->display('index/lvs');
    }
    
    function backup() {
		$this->display('index/backup');
    }
}