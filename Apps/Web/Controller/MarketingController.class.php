<?php
namespace Web\Controller;

use Think\Controller;

/**
 * 网站页面控制器类
 * @author guanxuejun
 *
 */
class MarketingController extends BaseController {
	function __construct() {
		parent::__construct();
		$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
	}
	
    function index(){
		$this->display('Index/marketing');
    }
    
    function brand() {
    	$this->display('Index/marketing_brand');
    }
    
    function channel() {
    	$this->display('Index/marketing_channel');
    }
    
    function data() {
    	$this->display('Index/marketing_data');
    }
}