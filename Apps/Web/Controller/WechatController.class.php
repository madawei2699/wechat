<?php
namespace Web\Controller;
use Think\Controller;

/**
 * 微信推广页面
 * @author guanxuejun
 *
 */
class WechatController extends Controller {
    function index(){
		$this->display();
    }
    
    /**
     * 大电商 - 1、微电商
     * http://127.0.0.1:108/wechat/weidianshang
     */
    function weidianshang() {
    	$this->display();
    }
    
    /**
     * 大电商 - 2、独立商城
     * http://127.0.0.1:108/wechat/dulishangcheng
     */
    function dulishangcheng() {
    	$this->display();
    }
    
    /**
     * 大电商 - 3、进销存管理
     * http://127.0.0.1:108/wechat/jinxiaocun
     */
    function jinxiaocun() {
    	$this->display();
    }
    
    /**
     * 整合营销 - 1、品牌整合
     * http://127.0.0.1:108/wechat/pinpaizhenghe
     */
    function pinpaizhenghe() {
    	$this->display();
    }
    
    /**
     * 整合营销 - 2、渠道整合
     * http://127.0.0.1:108/wechat/qudaozhenghe
     */
    function qudaozhenghe() {
    	$this->display();
    }
    
    /**
     * 整合营销 - 3、数据整合
     * http://127.0.0.1:108/wechat/shujuzhenghe
     */
    function shujuzhenghe() {
    	$this->display();
    }
    
    /**
     * 关注熔意 - 1、进化论
     * http://127.0.0.1:108/wechat/jinhualun
     */
    function jinhualun() {
    	$this->display();
    }
    
    /**
     * 关注熔意 - 2、合作共赢
     * http://127.0.0.1:108/wechat/hezuo
     */
    function hezuo() {
    	$this->display();
    }
    
    /**
     * 关注熔意 - 3、精选推送
     * http://127.0.0.1:108/wechat/push
     */
    function push() {
    	$this->display();
    }
    
    /**
     * 关注熔意 - 4、联系我们
     * http://127.0.0.1:108/wechat/contact
     */
    function contact() {
    	$this->display();
    }
}