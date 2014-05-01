<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Product 类
 * 网站产品列表
 * http://127.0.0.1:122
 * http://x.f-fusion.com
 * 
 * @category Site
 * @package Site
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class ProductController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	$model = $this->getModel('Article');
    	$list1 = $model->field('id,create_time,title')->where(array('ch'=>101, 'state'=>1))->limit(3)->select();
    	$this->assign('list1', $list1);
    	$list2 = $model->field('id,create_time,title')->where(array('ch'=>102, 'state'=>1))->limit(3)->select();
    	$this->assign('list2', $list2);
    	$list3 = $model->field('id,create_time,title')->where(array('ch'=>103, 'state'=>1))->limit(3)->select();
    	$this->assign('list3', $list3);
    	$list4 = $model->field('id,create_time,title')->where(array('ch'=>104, 'state'=>1))->limit(3)->select();
    	$this->assign('list4', $list4);
		$this->display('index/product');
    }
	
    function safe(){
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>101, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/product_safe');
    	} else {
	    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>101, 'state'=>1))->find($id);
	    	if ($info == null) {
	    		$this->safe();
	    		return;
	    	};
	    	$this->assign('info', $info);
			$this->display('index/product_safe_detail');
    	};
    }
	
    function monitor(){
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>102, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/product_monitor');
    	} else {
	    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>102, 'state'=>1))->find($id);
	    	if ($info == null) {
	    		$this->monitor();
	    		return;
	    	};
	    	$this->assign('info', $info);
			$this->display('index/product_monitor_detail');
    	};
    }
	
    function parts(){
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>103, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/product_parts');
    	} else {
	    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>103, 'state'=>1))->find($id);
	    	if ($info == null) {
	    		$this->parts();
	    		return;
	    	};
	    	$this->assign('info', $info);
			$this->display('index/product_parts_detail');
    	};
    }
	
    function maintain(){
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>104, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/product_maintain');
    	} else {
	    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>104, 'state'=>1))->find($id);
	    	if ($info == null) {
	    		$this->maintain();
	    		return;
	    	};
	    	$this->assign('info', $info);
			$this->display('index/product_maintain_detail');
    	};
    }
}