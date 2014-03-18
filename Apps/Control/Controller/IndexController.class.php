<?php
namespace Control\Controller;
use Think\Controller;

/**
 * Index 类
 * 主要用于显示控制台登录首页
 * 
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
		//$this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>Control</b>！</p></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
		$this->display();
    }
    
    /**
     * 登录动作
     */
    function signin() {
    	if (IS_POST) {
    		$userName = I('post.user');
    		$userPass = I('post.password');
    		$result = $this->varifyPassword($userName, $userPass);
    		dump($result);
    		echo 'is_post';
    		
    		// 确定当前用户实际所在城市
    		$r = Org\Net\Curl::get('http://127.0.0.1:112/place/ip2city?ip='.$ip, 10);
    		if (array_key_exists('result', $r)) {
    			$info = $r['result'];
    			$info = json_decode($info, true);
    			if ($info) {
    				$abscity = $info['content']['address_detail']['city'];
    				if ($abscity != '') {
    					session('admin_abscity', mb_substr($abscity, 0, 2, 'UTF-8'));
    					session('admin_lng', $info['content']['point']['x']);
    					session('admin_lat', $info['content']['point']['y']);
    				};
    			};
    		};
    		
    		session('admin_id', $admin->admin_id);
    		session('admin_name', $admin->admin_name);
    		session('admin_password_time', $admin_password_time);
    		session('admin_role_id', $roleID);
    		session('admin_role', $role);
    		session('admin_expire', time()+parent::EXPIRE);
    		return;
    	};
    	echo 'no';
    }
}