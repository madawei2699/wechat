<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Hr 类
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
class HrController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	$model = $this->getModel('Article');
    	$list = $model->field('id,create_time,title')->where(array('ch'=>401, 'state'=>1))->limit(10)->select();
    	$this->assign('list', $list);
		$this->display('index/hr');
    }
    
    function detail() {
    	$id = I('get.id', 0, 'int');
    	if ($id == 0) {
    		$this->index();
    		return;
    	};
    	$model = $this->getModel('Article');
    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>401, 'state'=>1))->find($id);
    	if ($info == null) {
    		$this->index();
    		return;
    	};
    	$this->assign('info', $info);
    	$this->display('index/hr_detail');
    }
}