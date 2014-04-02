<?php
namespace Control\Controller;
use Think\Controller;

/**
 * Control 基类
 * 主要用于各种基础方法、成员的安排
 * 注意此基类会被其他 module 的类继承
 * 
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class BaseController extends Controller {
	protected $CONTROL_SALT = '1235';
	protected $EXPIRE = 900;
	function __construct() {
		parent::__construct();
		$this->CONTROL_SALT = C('CONTROL_SALT');
	}
	
	/**
	 * 验证密码是否有效
	 * 方法内负责对 session 赋值
	 * 
	 * @param string $userName 指定用户名
	 * @param string $originPassword 登录密码明文
	 * @return boolean
	 */
	protected function varifyPassword($userName, $originPassword) {
		if (trim($userName) == '') return false;
		if (trim($originPassword) == '') return false;
		$params = array($this->CONTROL_SALT, $originPassword);
		// 取出指定用户的 salt
		$user = D('ControlUser');
		$row = $user->field('salt,enterprise_id')->where(array('name'=>$userName,'status'=>1))->find();
		if ($row == null) return false;
		$enterpriseID = $row['enterprise_id']; // =0 是系统用户
		$params[] = $row['salt'];
		// 校验密码
		sort($params, SORT_STRING);
		$password = sha1( implode('', $params) );
		$rs = $user->where(array('name'=>$userName,'status'=>1,'password'=>$password))->find();
		if ($rs == null) return false;
		// success
		session('admin_id',       $user->id);
		session('admin_name',     $user->name);
		session('admin_group_id', $user->group_id);
		session('admin_role_id',  $user->role_id);
		session('admin_ent_id',   $user->enterprise_id); // =0 是系统用户
		session('admin_expire',   time()+$this->EXPIRE);
		return true;
	}
}