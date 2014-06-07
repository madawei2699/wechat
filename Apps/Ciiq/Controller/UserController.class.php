<?php
namespace Ciiq\Controller;

use Think\Controller;
use Org\Util\Page;

/**
 * User 类
 * 主要用于系统用户管理
 * 管理顶级企业用户、渠道用户和微店用户
 *
 * @category Ciiq
 * @package Ciiq
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://ciiq.f-fusion.com/ <http://ciiq.f-fusion.com/>
 *
 */
class UserController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'user');
	}
	
	function index() {
		$params = '';
		$user = $this->getModel('User');
		$count = $user->count();
		$page = new Page($count, C('APPLICATION_LIST_PAGE_SIZE'), $params);
		$rs = $user->order('id DESC')->limit($page->firstRow, $page->listRows)->select();
		foreach ($rs as $key=>$value) {
			$rs[$key]['role'] = $this->USER_ROLE[$rs[$key]['role_id']];
			$rs[$key]['status'] = $this->USER_STATUS[$rs[$key]['status']];
			unset($rs[$key]['password']);
		};
		$this->assign('count', $count);
		$this->assign('list', $rs);
		$this->assign('page', $page->show());
		$this->assign('salt', md5(time()));
		// agent
		$agent = $this->getModel('Agent');
		$agents = $agent->field('id,enterprise')->order('enterprise ASC')->select();
		$this->assign('agents', $agents);
		$id = I('get.id', 0, 'int');
		if ($id > 0) {
			// 提取信息准备修改
			$rs = $user->find($id);
			if ($rs == null) {} else {
				$this->assign('info', $rs);
			};
		};
		$this->assign('roles', $this->USER_ROLE);
		$this->display();
	}
	
	/**
	 * 改密界面和方法
	 */
	function pswd() {
		$id = I('get.id');
		if (preg_match("/^[0-9]+$/", $id) == false) {
			$this->error('请指定用户！');
			return;
		};
		$from = I('get.from');
		$user = $this->getModel('User');
		$rs = $user->find($id);
		if ($rs == null) {
			$this->error('指定用户不存在！');
			return;
		};
		if (IS_POST) {
			// process
			$id = I('post.id');
			$password = I('post.password');
			$password1 = I('post.password1');
			if ($password == '' || $password1 == '') {
				$this->error('请填写登录密码！');
				return;
			};
			if (strcmp($password, $password1) != 0) {
				$this->error('您两次填写的登录密码不同！');
				return;
			};
			// process password
			$params = array(C('APPLICATION_USER_SALT'), $password, $rs['salt']);
			sort($params, SORT_STRING);
			$password = sha1( implode('', $params) );
			$update = $user->save(array('password' => $password), array('where'=>'id='.$id));
			if ($update) {
				$this->success('改密成功！', (isset($_POST['from']) ? $_POST['from'] : '/user'));
				return;
			};
			$this->error('改密失败！'.$user->getDbError());
		} else {
			// 提取信息准备修改
			$rs = $user->find($id);
			if ($rs == null) {} else {
				$this->assign('info', $rs);
			};
		};
		$this->assign('info', $rs);
		$this->assign('from', $from);
		$this->display();
	}
	
	/**
	 * 修改和新增方法
	 * 以是否得到信息id来判断是修改还是新增
	 */
	function save() {
		if (!IS_POST) {
			redirect('/user');
			return;
		};
		$id = I('post.id');
		$name = I('post.name');
		$nickName = I('post.nickname');
		$number = I('post.number');
		$password = I('post.password');
		$password1 = I('post.password1');
		$salt = I('post.salt');
		$role = I('post.role');
		$enterprise = I('post.enterprise');
		$agent = I('post.agent');
		$shop = I('post.shop');
		$status = I('post.status');
		$email = I('post.email');
		$qq = I('post.qq');
		$mobile = I('post.mobile');
		$comment = I('post.comment');
		if (preg_match("/^[0-9]+$/", $role) == false) {
			$this->error('请指定用户角色！');
			return;
		};
		$enterprise = preg_match("/^[0-9]+$/", $enterprise) ? $enterprise : 0;
		$agent = preg_match("/^[0-9]+$/", $agent) ? $agent : 0;
		$shop = preg_match("/^[0-9]+$/", $shop) ? $shop : 0;
		if (preg_match("/^[0-9]+$/", $status) == false) {
			$this->error('请指定用户状态！');
			return;
		};
		$user = $this->getModel('User');
		if (preg_match("/^[0-9]+$/", $id)) {
			// update
			$update = $user->save(array(
				'nickname' => $nickName,
				'number' => $number,
				'role_id' => $role,
				'enterprise_id' => $enterprise,
				'agent_id' => $agent,
				'shop_id' => $shop,
				'status' => $status,
				'email' => $email,
				'qq' => $qq,
				'mobile' => $mobile,
				'comment' => $comment,
			), array('where'=>'id='.$id));
			if ($update) {
				$this->success('更新成功！', (isset($_POST['from']) ? $_POST['from'] : '/user'));
				return;
			};
			$this->error('更新失败！'.$user->getDbError());
		} else {
			// create
			if ($name == '') {
				$this->error('请填写登录账号！');
				return;
			};
			$nickName = $nickName == '' ? $name : $nickName;
			if (preg_match("/^[a-zA-Z0-9]{6,64}$/", $name) == false) {
				$this->error('登录账号只能是英文字母(A-Z)和数字(0-9)的组合，长度在6位和64位之间！');
				return;
			};
			if ($password == '' || $password1 == '') {
				$this->error('请填写登录密码！');
				return;
			};
			if (strcmp($password, $password1) != 0) {
				$this->error('您两次填写的登录密码不同！');
				return;
			};
			$salt = $salt == '' ? md5(time()) : $salt;
			// process password
			$params = array(C('APPLICATION_USER_SALT'), $password, $salt);
			sort($params, SORT_STRING);
			$password = sha1( implode('', $params) );
			$create = $user->add(array(
				'name' => $name,
				'nickname' => $nickName,
				'number' => $number,
				'password' => $password,
				'salt' => $salt,
				'role_id' => $role,
				'enterprise_id' => $enterprise,
				'agent_id' => $agent,
				'shop_id' => $shop,
				'status' => $status,
				'email' => $email,
				'qq' => $qq,
				'mobile' => $mobile,
				'comment' => $comment,
				'create_time' => $this->date,
			));
			if ($create) {
				$this->success('添加成功！', (isset($_POST['from']) ? $_POST['from'] : '/user'));
				return;
			};
			$this->error('添加失败！'.$user->getDbError());
		};
	}
}