<?php
namespace Ciiq\Controller;

use Think\Controller;
use Org\Util\Page;

/**
 * Site 类
 * 主要用于微站
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class SiteController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'site');
	}
	
	function index() {
		$this->display();
	}
	
	function config() {
		$this->display();
	}
	
	function map() {
		$this->display();
	}
	
	/**
	 * 微站管理员清单
	 * 和修改删除方法
	 */
	function user() {
		$params = '';
		$user = $this->getModel('User');
		$count = $user->where(array('role_id'=>4, 'agent_id'=>array('gt',0)))->count();
		$page = new Page($count, C('APPLICATION_LIST_PAGE_SIZE'), $params);
		$rs = $user->where(array('role_id'=>4, 'agent_id'=>array('gt',0)))->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
		foreach ($rs as $key=>$value) {
			$rs[$key]['role'] = $this->USER_ROLE[$rs[$key]['role_id']];
			$rs[$key]['status'] = $this->USER_STATUS[$rs[$key]['status']];
			unset($rs[$key]['password']);
		};
		$this->assign('count', $count);
		$this->assign('list', $rs);
		$this->assign('page', $page->show());
		$this->assign('salt', md5(time()));
		$id = I('get.id');
		if (preg_match("/^[0-9]+$/", $id)) {
			// 提取信息准备修改
			$rs = $user->find($id);
			if ($rs == null) {} else {
				$this->assign('info', $rs);
			};
		};
		$this->display();
	}
}