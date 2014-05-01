<?php
namespace Vshop\Controller;

use Think\Controller;
use Common\Controller\BaseController;

/**
 * 网站页面控制器类
 * @author guanxuejun
 *
 */
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display();
    }
}