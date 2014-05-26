<?php
namespace Demo\Controller;
use Think\Controller;

/**
 * Control 基类
 * 主要用于各种基础方法、成员的安排
 * 注意此基类会被其他 module 的类继承
 * 
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class BaseController extends Controller {
	protected $TABLE_PRIFIX = '';
	protected $MODEL_PRIFIX = '';
	protected $MEMBER_SALT = '1235';
	protected $EXPIRE = 900;
	protected $date = '';
	protected $time = 0;
	function __construct() {
		parent::__construct();
		if (!session('?admin')) exit;
		$this->TABLE_PRIFIX = C('TABLE_PREFIX_SYSTEM');
		$this->MODEL_PRIFIX = C('MODEL_PREFIX_SYSTEM');
		$this->MEMBER_SALT   = C('MEMBER_SALT');
		$this->date = date('Y-m-d H:i:s');
		$this->time = time();
		$this->assign('WEB_PAGE_TITLE', C('WEB_PAGE_TITLE'));
		$this->assign('WEB_PAGE_CO', C('WEB_PAGE_CO'));
	}
	
	/**
	 * 获取模型对象
	 * @param string $name
	 * @return \Think\Model
	 */
	protected function getModel($name) {
		return D($this->MODEL_PRIFIX.$name);
	}
	
	/**
	 * 获取模型对象
	 * @param string $name
	 * @return string
	 */
	protected function getTable($name) {
		return $this->TABLE_PRIFIX.'_'.$name;
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
		$params = array($this->MEMBER_SALT, $originPassword);
		// 取出指定用户的 salt
		$user = D('Member');
		$row = $user->field('salt')->where(array('name'=>$userName,'status'=>1))->find();
		if ($row == null) return false;
		$params[] = $row['salt'];
		// 校验密码
		sort($params, SORT_STRING);
		$password = sha1( implode('', $params) );
		$rs = $user->where(array('name'=>$userName,'status'=>1,'password'=>$password))->find();
		if ($rs == null) return false;
		// success
		session('member_id',       $user->id);
		session('member_name',     $user->name);
		session('member_group_id', $user->group_id);
		session('member_expire',   time()+$this->EXPIRE);
		return true;
	}
}