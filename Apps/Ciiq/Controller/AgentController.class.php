<?php
namespace Ciiq\Controller;

use Think\Controller;
use Org\Util\Page;

/**
 * Agent 类
 * 主要用于渠道
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class AgentController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'agent');
	}
	
	function index() {
		$l1 = region(array('level'=>1));
		$l2 = region(array('level'=>2));
		$l3 = region(array('level'=>3));
		$this->assign('level1', json_encode($l1));
		$this->assign('level2', json_encode($l2));
		$this->assign('level3', json_encode($l3));
		$params = '';
		$agent = $this->getModel('Agent');
		$count = $agent->count();
		$page = new Page($count, C('APPLICATION_LIST_PAGE_SIZE'), $params);
		$rs = $agent->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
		foreach ($rs as $key=>$value) {
			$rs[$key]['type'] = $this->USER_AGENT_TYPE[$rs[$key]['type']];
			foreach ($l1 as $row) if ($row['id'] == $rs[$key]['province']) $rs[$key]['province'] = $row['short'];
			foreach ($l2 as $row) if ($row['id'] == $rs[$key]['city']) $rs[$key]['city'] = $row['short'];
			foreach ($l3 as $row) if ($row['id'] == $rs[$key]['district']) $rs[$key]['district'] = $row['short'];
			unset($rs[$key]['comment']);
		};
		$this->assign('count', $count);
		$this->assign('list', $rs);
		$this->assign('page', $page->show());
		$this->assign('agent_type', $this->USER_AGENT_TYPE);
		$id = I('get.id');
		if (preg_match("/^[0-9]+$/", $id)) {
			// 提取信息准备修改
			$rs = $agent->find($id);
			if ($rs == null) {} else {
				$this->assign('info', $rs);
			};
		};
		$this->display();
	}
	
	/**
	 * 修改和新增方法
	 * 以是否得到信息id来判断是修改还是新增
	 */
	function save() {
		if (!IS_POST) {
			redirect('/agent');
			return;
		};
		$id = I('post.id');
		$enterprise = I('post.enterprise');
		$province = I('post.select1');
		$city = I('post.select2');
		$district = I('post.select3');
		$type = I('post.type');
		$addr = I('post.addr');
		$post = I('post.post');
		$tel = I('post.tel');
		$fax = I('post.fax');
		$comment = I('post.comment');
		$contactName = I('post.contact_name');
		$contactMobile = I('post.contact_mobile');
		$contactTitle = I('post.contact_title');
		$contactGender = I('post.contact_gender');
		if (trim($enterprise) == '') {
			$this->error('请指定企业全称！');
			return;
		};
		if (trim($addr) == '') {
			$this->error('请指定企业详细地址！');
			return;
		};
		if (preg_match("/^[0-9]+$/", $type) == false) {
			$this->error('请指定代理类别！');
			return;
		};
		$agent = $this->getModel('Agent');
		if (preg_match("/^[0-9]+$/", $id)) {
			// update
			$update = $agent->save(array(
				'enterprise' => $enterprise,
				'province' => $province,
				'city' => $city,
				'district' => $district,
				'type' => $type,
				'addr' => $addr,
				'post' => $post,
				'tel' => $tel,
				'fax' => $fax,
				'contact_name' => $contactName,
				'contact_mobile' => $contactMobile,
				'contact_title' => $contactTitle,
				'contact_gender' => $contactGender,
				'create_time' => $this->date,
				'oprator_id' => session('admin_id'),
				'oprator_name' => session('admin_name'),
			), array('where'=>'id='.$id));
			if ($update) {
				$this->success('更新成功！', '/agent');
				return;
			};
			$this->error('更新失败！'.$agent->getDbError());
		} else {
			// create
			$create = $agent->add(array(
				'enterprise' => $enterprise,
				'province' => $province,
				'city' => $city,
				'district' => $district,
				'type' => $type,
				'addr' => $addr,
				'post' => $post,
				'tel' => $tel,
				'fax' => $fax,
				'contact_name' => $contactName,
				'contact_mobile' => $contactMobile,
				'contact_title' => $contactTitle,
				'contact_gender' => $contactGender,
				'create_time' => $this->date,
				'oprator_id' => session('admin_id'),
				'oprator_name' => session('admin_name'),
			));
			if ($create) {
				$this->success('添加成功！', '/agent');
				return;
			};
			$this->error('添加失败！'.$agent->getDbError());
		};
	}
	
	/**
	 * 渠道管理员清单
	 * 和修改删除方法
	 */
	function user() {
		$params = '';
		$user = $this->getModel('User');
		$count = $user->where(array('role_id'=>2, 'agent_id'=>array('gt',0)))->count();
		$page = new Page($count, C('APPLICATION_LIST_PAGE_SIZE'), $params);
		$rs = $user->where(array('role_id'=>2, 'agent_id'=>array('gt',0)))->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
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
		$this->assign('roles', $this->USER_ROLE);
		$this->display();
	}
	
	function shop() {
		$this->display();
	}
	
	function lbs() {
		$this->display();
	}
}