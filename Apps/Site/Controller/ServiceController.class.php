<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Service 类
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
class ServiceController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		$this->pdf();
    }
	
    function pdf(){
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>201, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/service_pdf');
    	} else {
	    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>201, 'state'=>1))->find($id);
	    	if ($info == null) {
	    		$this->pdf();
	    		return;
	    	};
	    	$this->assign('info', $info);
			$this->display('index/service_pdf_detail');
    	};
    }
    
    function point() {
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>202, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/service_point');
		} else {
			$info = $model->field('id,create_time,title,content')->where(array('ch'=>202, 'state'=>1))->find($id);
			if ($info == null) {
				$this->point();
				return;
			};
			$this->assign('info', $info);
			$this->display('index/service_point_detail');
		};
    }
    
    function edu() {
    	$id = I('get.id', 0, 'int');
    	$model = $this->getModel('Article');
    	if ($id == 0) {
	    	$list = $model->field('id,create_time,title')->where(array('ch'=>203, 'state'=>1))->limit(10)->select();
	    	$this->assign('list', $list);
			$this->display('index/service_edu');
		} else {
			$info = $model->field('id,create_time,title,content')->where(array('ch'=>203, 'state'=>1))->find($id);
			if ($info == null) {
				$this->edu();
				return;
			};
			$this->assign('info', $info);
			$this->display('index/service_edu_detail');
		};
    }
}