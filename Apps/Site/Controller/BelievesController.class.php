<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Believes 类
 * 网站关于我们
 * http://127.0.0.1:122
 * http://x.f-fusion.com
 * 
 * @category Site
 * @package Site
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class BelievesController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	$model = $this->getModel('Article');
    	$info = $model->field('id,create_time,title,content')->where(array('ch'=>0, 'state'=>1))->find(2);
    	$this->assign('info', $info);
		$this->display('index/believes');
    }
}