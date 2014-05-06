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
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display('index/index');
    }
}