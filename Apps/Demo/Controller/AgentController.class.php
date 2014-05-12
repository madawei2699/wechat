<?php
namespace Demo\Controller;

use Think\Controller;

/**
 * Agent 类
 * http://127.0.0.1:124/agent
 * http://demo.f-fusion.com/agent
 * 
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class AgentController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display('agent/index');
    }
    
    function latest() {
		$this->display('agent/latest');
    }
    
    function user() {
		$this->display('agent/user');
    }
    
    function shop() {
		$this->display('agent/shop');
    }
    
    function lbs() {
		$this->display('agent/lbs');
    }
}