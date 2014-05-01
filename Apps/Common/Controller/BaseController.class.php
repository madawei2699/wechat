<?php
namespace Common\Controller; // 定义为公用空间

use Think\Controller;

/**
 * 使用基类来处理每个业务类均会调用的方法
 * @author guanxuejun
 *
 */
class BaseController extends Controller {
	/**
	 * 当前时间戳（受时区影响）
	 * @var integer
	 */
	protected $time;
	/**
	 * 当前日期时间字串（受时区影响）
	 * @var string
	 */
	protected $date;
	protected $_app_os = 0;
	/**
	 * 令牌生命周期，秒
	 * @var integer
	 */
	protected $_token_expire = 1800;
	/**
	 * 默认接口版本
	 * @var string
	 */
	protected $_version = '1.0';
	/**
	 * 每个代理商每日访问限制
	 * @var integer
	 */
	protected $_connect_limit = 100000;
	/**
	 * 错误输出提示
	 * @var array
	 */
	protected $_err = array();
	
	protected $_bd_lbs_key = 'A2cf591a7c8e8fdfe367e26c93f02811';
	protected $_bd_api_url = 'http://api.map.baidu.com';
	protected $_amap_lbs_key = 'ae2d731f519af0502a0575161a88d610';
	protected $_amap_api_url = 'http://restapi.amap.com';
	protected $FFCONFIG = array();
	
	function __construct() {
		parent::__construct();
		$this->time = time();
		$this->date = date('Y-m-d H:i:s');
		header('Content-Type:text/html;charset=utf-8');
		$this->_app_os = $this->checkAppOS(); // 确定请求方是iOS还是Android
		// load my config
		$this->FFCONFIG = F('FFCONFIG');
		if ($this->FFCONFIG === false) {
			$config = D('Config');
			$cfg = $config->where(array('enable'=>0))->order('`key` ASC')->select();
			foreach ($cfg as $item) {
				// 0=整型1=浮点2=字符串3=json字串4=序列化字串
				switch ((int)$item['type']) {
					case 0:
						$this->FFCONFIG['config'][$item['key']] = (int)$item['value'];
						break;
					case 1:
						$this->FFCONFIG['config'][$item['key']] = (float)$item['value'];
						break;
					case 2:
						$this->FFCONFIG['config'][$item['key']] = (string)$item['value'];
						break;
					case 3:
						$this->FFCONFIG['config'][$item['key']] = json_decode($item['value'], true);
						break;
					case 4:
						$this->FFCONFIG['config'][$item['key']] = unserialize($item['value']);
						break;
				};
			};
			F('FFCONFIG', $this->FFCONFIG);
		};
		$this->HASH_STRING_SUFFIX = $this->FFCONFIG['config']['HASH_STRING_SUFFIX'];
		$this->_err = array(
			0 => array('result'=>0, 'message'=>'操作成功'),
			1000 => array('result'=>1000, 'message'=>'缺少输入参数'),
			1001 => array('result'=>1001, 'message'=>'输入参数格式错误'),
			1002 => array('result'=>1002, 'message'=>'关键参数不能为空'),
			1003 => array('result'=>1003, 'message'=>'关键参数校验失败'),
			1004 => array('result'=>1004, 'message'=>'超出每日访问限制'),
			1005 => array('result'=>1005, 'message'=>'获取令牌失败'),
			1006 => array('result'=>1006, 'message'=>'令牌错误或已过期'),
			1100 => array('result'=>1100, 'message'=>'查询结果为空'),
		);
		// 随机宣传口号
		$flag = C('STRING_FLAG1');
		$seed = mt_rand(0, count($flag)-1);
		$flag = $flag[$seed];
		$this->assign('STRING_FLAG', $flag);
	}
	
	/**
	 * 检测请求头，判断是 iOS（1）还是Android（2）客户端的请求
	 * 安师傅代驾 2.1.2 (iPhone; iPhone OS 6.1.3; zh_CN)
	 * android-async-http/1.4.1 (http://loopj.com/android-async-http)
	 * 
	 * 
	Device              OS       CFNetwork Version
	iPod Touch 2G       3.1.3    CFNetwork/459 Darwin/10.0.0d3
	iPod Touch 3G       4.0      CFNetwork/485.2 Darwin/10.3.1
	iPhone 3GS          4.1      CFNetwork/485.10.2 Darwin/?
	iPhone 3G           4.2.1    CFNetwork/485.12.7 Darwin/10.4.0
	iPhone 4            4.3.2    CFNetwork/485.13.9 Darwin/?
	iPod Touch 4G       4.3.5    CFNetwork/485.13.9 Darwin/11.0.0
	iPhone 3GS          5.0      CFNetwork/548.0.3 Darwin/11.0.0
	iPhone 4S           5.0.1    CFNetwork/548.0.4 Darwin/11.0.0
	iPhone 4S           5.1      CFNetwork/548.1.4 Darwin/11.0.0
	iPhone 4S           6.0-b3   CFNetwork/602 Darwin/13.0.0
	iPhone 4S           6.0      CFNetwork/609 Darwin/13.0.0
	iPhone 4S           6.1.2    CFNetwork/609.1.4 Darwin/13.0.0
	 * @return number
	 */
	private function checkAppOS() {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if (stripos($agent, 'iphone') !== false) return 1;
		if (stripos($agent, 'cfnetwork') !== false) return 1;
		if (stripos($agent, 'android') !== false) return 2;
		return 0;
	}
	
	/**
	 * 构建API URL
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	protected function buildUrl($url, array $params) {
		if (count($params) == 0) return '';
		ksort($params);
		$keys = array_keys($params);
		$vals = array_values($params);
		$str = '';
		for ($i=0; $i<count($vals); $i++) {
			$str .= $vals[$i];
			$url .= '&'.$keys[$i].'='.$vals[$i];
		};
		$url .= '&hash='.md5($str); // 最后得到完整的url字串
		return $url;
	}
	
	/**
	 * 校验参数hash值
	 * @param string $hash
	 * @param array $params
	 * @return boolean
	 */
	protected function verifyHash($hash, array $params) {
		if (trim($hash) == '' || count($params) == 0) return false;
		ksort($params);
		$vals = array_values($params);
		$combine = '';
		for ($i=0; $i<count($vals); $i++) $combine .= urldecode($vals[$i]);
		return strcmp(md5($combine), $hash) == 0;
	}
	
	/**
	 * 校验令牌
	 * 代理商是否存在
	 * 令牌是否过期(超过30分钟、IP变更都会导致令牌失效)
	 * @param integer $agentID
	 * @param string $token
	 */
	protected function verifyToken($agentID, $token) {
		if (preg_match("/^[0-9]+$/", $agentID) == false) return false;
		if (preg_match("/^[0-9a-z]{32}$/", $token) == false) return false; // eg.1d627434e53805d79799aa4ca5698a6c
		$ip = get_client_ip();
		
		$agentToken = D('AgentToken');
		$rs = $agentToken->where(array(
			'agent_id' => $agentID,
			'token' => $token,
			'ip' => $ip,
		))->order('create_time DESC')->limit(1)->select();
		if ($rs) {
			$ceateTime = strtotime($rs[0]['create_time']);
			return (time() - $ceateTime) < $this->_token_expire;
		};
		return false;
	}
	
	/**
	 * 校验接口代理商的优惠权限
	 * 目前只有两项优惠参数
	 * @param integer $agentID
	 * @param array $params
	 */
	protected function verifyPermissions($agentID, array $params) {
		if (preg_match("/^[0-9]+$/", $agentID) == false) return false;
		if (count($params) == 0) return false;
		if (!array_key_exists('coupon_amount', $params)) return false;
		if (!array_key_exists('coupon_distance', $params)) return false;
		if (preg_match("/^[0-9]+$/", $params['coupon_amount']) == false) return false;
		if (preg_match("/^[0-9]+$/", $params['coupon_distance']) == false) return false;
		if ((int)$params['coupon_amount'] == 0 && (int)$params['coupon_distance'] == 0) return true;
		$agent = D('Agent');
		$rs = $agent->find($agentID);
		if ($rs == null) return false;
		$pmsCouponAmount   = $rs['pms_coupon_amount'];
		$pmsCouponDistance = $rs['pms_coupon_distance'];
		if (strlen($pmsCouponAmount) > 0 && (int)$params['coupon_amount'] > 0 && (int)$params['coupon_distance'] == 0) {
			$amount = (int)$params['coupon_amount'];
			$pmsCouponAmount = explode(',', $pmsCouponAmount);
			foreach ($pmsCouponAmount as $item) if ($item == $amount) return true;
		};
		if (strlen($pmsCouponDistance) > 0 && (int)$params['coupon_amount'] == 0 && (int)$params['coupon_distance'] > 0) {
			$distance = (int)$params['coupon_distance'];
			$pmsCouponDistance = explode(',', $pmsCouponDistance);
			foreach ($pmsCouponDistance as $item) if ($item == $distance) return true;
		};
		return false;
	}
	
	/**
	 * 记录和检测每日访问限额
	 * 超出限额和创建记录失败返回 false
	 * @param string $cacheName 缓存名称
	 * @return boolean
	 */
	protected function verifyLimit($cacheName) {
		$count = F($cacheName);
		if ($count === false) return F($cacheName, 1);
		$count = (int)$count;
		if ($count > $this->_connect_limit) return false;
		return F($cacheName, $count+1);
	}
};