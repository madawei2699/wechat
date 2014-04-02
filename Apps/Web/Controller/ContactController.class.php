<?php
namespace Web\Controller;

use Think\Controller;
use Common\Controller\BaseController;

/**
 * 网站页面控制器类
 * @author guanxuejun
 *
 */
class ContactController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
		$this->display('index/contact');
    }
}