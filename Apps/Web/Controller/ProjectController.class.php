<?php
namespace Web\Controller;

use Think\Controller;

/**
 * 网站页面控制器类
 * @author guanxuejun
 *
 */
class ProjectController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	$id = I('get.id', 0, 'int');
    	$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
    	if ($id < 100) {
	    	$this->assign('id', $_GET['id']);
			$this->display('Index/project'.$_GET['id']);
    	} else {
    		$proj = D('ControlProject');
    		$rs = $proj->find($id);
    		if ($rs == null) {} else {
	    		$this->assign('info', $rs);
	    		$this->display('Index/project0');
    		};
    	}
    }
    
    /**
     * 微信案例
     */
    function wechat() {
    	$this->display();
    }
    
    /**
     * 电商案例
     */
    function shop() {
    	$this->display();
    }
    
    /**
     * 进销存案例
     */
    function invoice() {
    	$this->display();
    }
    
    /**
     * OA案例
     */
    function oa() {
    	$this->display();
    }
    
    /**
     * 客户关系管理案例
     */
    function crm() {
    	$this->display();
    }
}