<?php
namespace Web\Controller;
use Think\Controller;

/**
 * 项目案例
 * @author guanxuejun
 *
 */
class ProjectController extends Controller {
    function index(){
		$this->display();
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