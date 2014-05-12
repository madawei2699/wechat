<?php
namespace Web\Controller;

use Think\Controller;
use Common\Controller\BaseController;

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
    	$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
    	$this->assign('id', $_GET['id']);
		$this->display('Index/project'.$_GET['id']);
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