<?php
namespace Demo\Controller;

use Think\Controller;

/**
 * Shop 类
 * http://127.0.0.1:124/agent
 * http://demo.f-fusion.com/agent
 * 
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class ShopController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->display('shop/index');
    }
	
    function quick(){
		$this->display('shop/quick');
    }
	
    function user(){
		$this->display('shop/user');
    }
	
    function goods(){
		$this->display('shop/goods');
    }
    
    function member() {
		$this->display('shop/member');
    }
    
    function lbs() {
		$this->display('shop/lbs');
    }
    
    function media() {
    	$this->display('shop/media');
    }
    
    function qrcode() {
    	$this->display('shop/qrcode');
    }
    
    function menu() {
    	$this->display('shop/menu');
    }
}