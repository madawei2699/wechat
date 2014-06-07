<?php
namespace Ciiq\Controller;

use Think\Controller;

/**
 * Dashboard 类
 * 主要用于显示控制台概览页面
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class DashboardController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'dashboard');
	}
	
	function index() {
		$this->display();
	}
	
	function settings() {
		if (IS_POST) {
			$type = I('get.t', 0, 'int');
			if ($type == 0) {
				$this->error('请提交！', '/');
				exit;
			};
			$systemParams = array('agent_permission_limit', 'shop_permission_limit'); // 仅在系统设置下提交的参数可更新
			$config = $this->getModel('Config');
			foreach ($_POST as $key=>$item) {
				$rs = $config->where(array('key'=>$key, 'type'=>2))->find();
				if ($rs == null) {
					$config->add(array(
						'type' 			=> 2,
						'key' 			=> $key,
						'value' 		=> $item,
						'enable' 		=> 1,
						'comment' 		=> $key,
						'admin_name' 	=> session('admin_name'),
						'update_time' 	=> $this->date,
					));
				} else {
					if ($type == 9 && in_array($key, $systemParams)) {
						$config->save(array(
							'key' 			=> $key,
							'value' 		=> $item,
							'admin_name' 	=> session('admin_name'),
							'update_time' 	=> $this->date,
						), array('where'=>'id='.$rs['id']));
					} else {
						$config->save(array(
							'key' 			=> $key,
							'value' 		=> $item,
							'admin_name' 	=> session('admin_name'),
							'update_time' 	=> $this->date,
						), array('where'=>'id='.$rs['id']));
					};
				};
			};
			F('FFCONFIG', null);
			redirect('/dashboard/settings');
			return;
		};
		$this->display();
	}
	
	function message() {
		$readed = I('get.readed');
		$unread = I('get.unread');
		$this->display();
	}
}