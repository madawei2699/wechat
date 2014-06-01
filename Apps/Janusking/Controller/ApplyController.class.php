<?php
namespace Janusking\Controller;

use Think\Controller;

/**
 * 在线报名
 * @author guanxuejun
 *
 */
class ApplyController extends BaseController {
	/**
	 * 报名界面
	 * 这里直接在微信浏览器里面显示
	 */
    public function index(){
    	if (strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger")) {
    		// is wechat browser
    	} else {
    		echo '<span style="font-size:28px">HTTP/1.1 401 Unauthorized</span>';
    		return;
    	};
    	if (IS_POST) {
    		$name   = I('post.name');
    		$city   = I('post.city');
    		$mobile = I('post.mobile');
    		$tel    = I('post.tel');
    		$co     = I('post.co');
    		$addr   = I('post.addr');
    		if ($name == '') {
    			$this->assign('result', '请填写姓名');
    			$this->display('result');
    			return;
    		};
    		if ($city == '') {
    			$this->assign('result', '请填写城市');
    			$this->display('result');
    			return;
    		};
    		if (preg_match("/^1[0-9]{10}$/", $mobile) == false) {
    			$this->assign('result', '手机号格式错误，应当是数字1开头的11位数字');
    			$this->display('result');
    			return;
    		};
    		$apply = D('JanuskingApply');
    		$newid = $apply->add(array(
    			'name' => $name,
    			'city' => $city,
    			'mobile' => $mobile,
    			'tel' => $tel,
    			'co' => $co,
    			'addr' => $addr,
    			'create_time' => $this->date,
    		));
    		if ($newid > 0) {
    			$this->assign('result', '报名成功！<br/>请点击左上角返回！');
    			$this->display('result');
    			return;
    		};
    		$this->assign('result', '糟糕！出错了！请联系我们！');
    		$this->display('result');
    		return;
    	}
		$this->display();
    }
    
    /**
     * 报名清单
     * 这里需要检查登录权限
     */
    function lists() {
    	if (!session('?janusking_admin')) return;
    	if (session('janusking_admin') != '1') return;
    	
    	$apply = D('JanuskingApply');
    	$list = $apply->order('create_time DESC')->select();
    	$this->assign('count', count($list));
    	$this->assign('list', $list);
    	$this->display();
    }
}