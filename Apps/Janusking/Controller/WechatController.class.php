<?php
namespace Janusking\Controller;

use Think\Controller;
use Org\Net\Curl;

/**
 * 微信推广页面
 * @author guanxuejun
 *
 */
class WechatController extends BaseController {
	/**
	 * 微信token
	 * @var string
	 */
	const MY_TOKEN       = 'e74ok2e9379cbca';
	/**
	 * 微信app id
	 * @var string
	 */
	const WX_APP_ID      = 'wxed793db4d8059ae6';
	/**
	 * 微信颁发的app安全码
	 * @var string
	 */
	const WX_APP_SECRET  = '83d55e00e0d2d44f0d23965dbc633de6';
	/**
	 * 微信服务器url
	 * @var string
	 */
	const WX_API_SERVICE_URL = 'https://api.weixin.qq.com/cgi-bin/';
	/**
	 * 微信 token 生命周期(s)
	 * @var integer
	 */
	const WX_TOKEN_LIFE  = 300;
	/**
	 * 获取微信token的时间戳
	 * @var integer
	 */
	static $wx_last_token_time = 0;
	/**
	 * 微信 token
	 * @var string
	 */
	static $wx_last_token_string = '';
	/**
	 * 装载员工微信号，内测用
	 * @var array
	 */
	private $employees = array();
	/**
	 * 判断是否是员工号，内测用
	 * @var boolean
	 */
	private $isEmployee = false;
	/**
	 * 应用专属微信的配置
	 * @var array
	 */
	private $JANUSKING_WECHAT;

	private $lang = array(
		"DEFAULT" => "感谢关注亚斯王！",
		"SUBSCRIBE" => "感谢关注亚斯王！",
		"WORKFLOW_ERR_REC" => "记录流程失败，请重试！",
	);
	
	/**
	 * 微信方法
	 */
	function __construct() {
		parent::__construct();
		$this->employees = array(
 			'ouTetjpJvbB3o43-WzskTlKfHReQ' => 'VrWorking！',
		);
		$this->JANUSKING_WECHAT = $this->WECHATCONFIG;
		
		if (!array_key_exists('signature', $_GET) || !array_key_exists('nonce', $_GET) || !array_key_exists('timestamp', $_GET)) {
			
			// 外部调用微信方法API /////////////////////////////////////////////////////////////////////////////
			// 外部调用微信处理必备四个固定参数 method, time, ver, hash
			if (!array_key_exists('method', $_GET)) {
				echo json_encode($this->_err[1000]);
				exit;
			};
			// check input params
			if (I('get.hash') == '') {
				echo json_encode($this->_err[1000]);
				exit;
			};
			if (strcmp($this->_version, I('get.ver')) != 0) {
				echo json_encode($this->_err[1001]);
				exit;
			};
			// verify params
			$params['method']= I('get.method');
			$params['time']  = I('get.time');
			$params['ver']   = I('get.ver');
			if (!$this->verifyHash(I('get.hash'), $params)) {
				echo json_encode($this->_err[1003]);
				exit;
			};
			
			// 业务参数检查
			if (preg_match("/^[a-zA-Z]{2,}[a-zA-Z-]+[a-zA-Z]{2,}$/", $params['method']) == false) {
				echo json_encode($this->_err[1001]);
				exit;
			};
			$str = explode('-', $params['method']);
			if (count($str) == 1) {
				$params['method'] = 'api'.strtoupper(substr($params['method'], 0, 1)).substr($params['method'], 1);
			} else {
				foreach ($str as $k=>$v) {
					if (strlen($v) > 0) $str[$k] = strtoupper(substr($v, 0, 1)).substr($v, 1);
				};
				$params['method'] = 'api'.implode('', $str);
			};
			unset($str);
			$this->getToken(); // init wechat token
			// auto fire method
			if (method_exists($this, $params['method'])) call_user_func(array(get_class($this), $params['method']));
			exit;
			
		} else {
		
			// 微信服务器回调方法API ////////////////////////////////////////////////////////////////////////////
			
			// valid message
			tt(array('unique_id'=>1, 'comment'=>'WX_GET:'.json_encode($_GET)));
			tt(array('unique_id'=>2, 'comment'=>'WX_HTTP_RAW_POST_DATA:'.json_encode($GLOBALS["HTTP_RAW_POST_DATA"])));
			$tmpArr = array(self::MY_TOKEN, I('get.timestamp'), I('get.nonce'));
			sort($tmpArr, SORT_STRING); // 微信2014-03-04更正
			$tmpStr = implode('', $tmpArr);
			$tmpStr = sha1( $tmpStr );
			if ($tmpStr == I('get.signature')) {
				if (isset($_GET['echostr'])) {
					echo I('get.echostr'); // 来自微信的主动验证，直接返回 echostr 内容即可
					tt(array('unique_id'=>3, 'comment'=>'微信主动验证OK'));
					exit;
				};
			} else {
				tt(array('unique_id'=>4, 'comment'=>'signature ER: '.$tmpStr.' != '.I('get.signature')));
				exit;
			};
			
			// get message content
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			if (empty($postStr)) exit;
			
			// 解析消息
			$postObj      = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$openID       = (string)$postObj->FromUserName; // 发送方的openid
			$serviceID    = (string)$postObj->ToUserName; // 服务号
			$msgType      = (string)$postObj->MsgType; // 消息类型 text event
			$content      = (string)trim($postObj->Content);
			$wxCreateTime = (int)$postObj->CreateTime;
			// 过滤垃圾微信的事件通知
			$cacheFilter = F('WX_FILTER_'.date('Ymd'));
			if ($cacheFilter === false) {
				$cacheFilter = array();
				$cacheFilter[$openID]['event']= '';
				$cacheFilter[$openID]['msg']  = '';
				$cacheFilter[$openID]['time'] = $wxCreateTime;
			} else {
				if (array_key_exists($openID, $cacheFilter)) {
					$lastCreateTime = (int)$cacheFilter[$openID]['time'];
					$lastEvent      = (string)$cacheFilter[$openID]['event'];
					$lastMsg        = (string)$cacheFilter[$openID]['msg'];
					if (strtolower($msgType) == 'event') {
						$event = strtolower((string)trim($postObj->EventKey));
						if ($lastEvent == $event && $lastCreateTime == $wxCreateTime) {
							// 微信连续push相同的事件，忽略
							echo '';
							exit;
						};
						$cacheFilter[$openID]['event']= $event;
						$cacheFilter[$openID]['msg']  = '';
						$cacheFilter[$openID]['time'] = $wxCreateTime;
					} else {
						$msg = strtolower((string)trim($postObj->MsgId));
						if ($lastMsg == $msg && $lastCreateTime == $wxCreateTime) {
							// 微信连续push相同的msg，忽略
							echo '';
							exit;
						};
						$cacheFilter[$openID]['event']= '';
						$cacheFilter[$openID]['msg']  = $msg;
						$cacheFilter[$openID]['time'] = $wxCreateTime;
					};
				} else {
					$cacheFilter[$openID]['msg']  = '';
					$cacheFilter[$openID]['event']= '';
					$cacheFilter[$openID]['time'] = $wxCreateTime;
				};
			};
			F('WX_FILTER_'.date('Ymd'), $cacheFilter);
			$this->getToken(); // init wechat token
			// 以下是业务处理
			$this->recordOpenUser($openID); // 尝试记录用户信息
			$userID       = 0;
			$userToken    = '';
			$userMobile   = '0';
			$userOS       = 0;
			$checkUserBind = $this->checkUserBind($openID); // 检查此微信号是否绑定手机
			if ($checkUserBind === false) {
				// unregist
			} else {
				$userID     = $checkUserBind['user_id'];
				$userToken  = $checkUserBind['user_token'];
				$userMobile = $checkUserBind['user_mobile'];
				$userOS     = $checkUserBind['os'];
			};
			$model = M();
			// 内部员工
			$this->isEmployee = array_key_exists($openID, $this->employees);
			switch (strtolower($msgType)) {
				case 'event': // 接收 -> 事件推送
					$event = (string)trim($postObj->Event);
					switch (strtolower($event)) {
						case 'subscribe': // 关注动作
							$this->doSubscribe($openID, $userID, $userMobile, $this->time);
							$params = '{"touser": "'.$openID.'", "msgtype": "text", "text": {"content": "'.$this->lang['SUBSCRIBE'].'"}}';
							$this->push($openID, $params);
							break;
						case 'unsubscribe': // 取消关注动作
							$this->doUnsubscribe($openID);
							break;
						case 'location': // 地理位置信息(用户自动上报)
							$lng = trim($postObj->Longitude);
							$lat = trim($postObj->Latitude);
							$address = $this->geodecode($lng, $lat, ($userOS==1?'amap':'baidu')); // 逆向地理编码
							$this->recordLocate($lng, $lat, $address, $openID, $userID);
							break;
						case 'click': // 自定义菜单的点击事件
							$eventKey = (string)trim($postObj->EventKey); // 这个是自定义菜单中定义的
							switch (strtoupper($eventKey)) {
								case 'K_PRICE': // 价格表 -> 回复文本消息
									$this->response($openID, $serviceID, $priceText);
									break;
							};
							break;
					};
					break;
				case 'text': // 接收 -> 文本消息
					$this->response($openID, $serviceID, $this->lang['DEFAULT']); // 默认消息
					break;
				case 'image': // 接收 -> 图片消息
				case 'voice': // 接收 -> 语音消息
				case 'video': // 接收 -> 视频消息
				case 'link': // 接收 -> 链接消息
					$this->response($openID, $serviceID, $this->lang['DEFAULT']); // 默认消息
					break;
				case 'location': // 地理位置消息(用户手工上报)
					break;
				default:
					$this->response($openID, $serviceID, $this->lang['DEFAULT']); // 默认消息
			};
		};
		exit;
	}
	
	/**
	 * 从微信服务器获取一个有效令牌
	 * @param boolean $force 是否强制获取新的令牌
	 * @return string
	 */
	private function getToken($force=false) {
		if ($force === true) {
			try {
				F('WX_TOKEN', null);
			} catch(Exception $ex) {}
		};
		$cacheWxToken = F('WX_TOKEN'); // array
		if ($cacheWxToken === false) {
			// 未初始化 - 重新获取
			$url = self::WX_API_SERVICE_URL.'token?grant_type=client_credential&appid='.self::WX_APP_ID.'&secret='.self::WX_APP_SECRET;
			
			$result = Curl::get($url, 3);
			if ($result === false) {
				self::$wx_last_token_string = '';
				self::$wx_last_token_time = 0;
			} else {
				$resp = json_decode($result['result'], true);
				if (array_key_exists('access_token', $resp)) {
					$cacheWxToken = array(
						'token' => $resp['access_token'],
						'time'  => $this->time
					);
					self::$wx_last_token_string = $cacheWxToken['token'];
					self::$wx_last_token_time = $cacheWxToken['time'];
					F('WX_TOKEN', $cacheWxToken);
				} else {
					// record error
					TraceAction::Trace(array(
						'user_id' => 997,
						'comment' => 'TOKEN ERR: '.json_encode($result, JSON_UNESCAPED_UNICODE),
					));
				};
			};
		} else {
			self::$wx_last_token_string = $cacheWxToken['token'];
			self::$wx_last_token_time = $cacheWxToken['time'];
			if (($this->time-self::$wx_last_token_time) > self::WX_TOKEN_LIFE) {
				// 超时 - 重新获取
				$url = self::WX_API_SERVICE_URL.'token?grant_type=client_credential&appid='.self::WX_APP_ID.'&secret='.self::WX_APP_SECRET;
				
				$result = Curl::get($url, 3);
				if ($result === false) {
					self::$wx_last_token_string = '';
					self::$wx_last_token_time = 0;
				} else {
					$resp = json_decode($result['result'], true);
					if (array_key_exists('access_token', $resp)) {
						$cacheWxToken = array(
							'token' => $resp['access_token'],
							'time'  => $this->time
						);
						self::$wx_last_token_string = $cacheWxToken['token'];
						self::$wx_last_token_time = $cacheWxToken['time'];
						F('WX_TOKEN', $cacheWxToken);
					} else {
						// record error
						TraceAction::Trace(array(
							'user_id' => 997,
							'comment' => 'TOKEN ERR: '.json_encode($result, JSON_UNESCAPED_UNICODE),
						));
					};
				};
			};
		};
		return self::$wx_last_token_string;
	}
	
	/**
	 * 以下是对微信开放的 API 方法集合
	 * 这些方法是微信回调时在构造函数中会调用的方法
	 */
	
	/**
	 * 给定 openid 获取指定关注者的信息
	 * 每次用户触发交互时更新信息
	 * @param string $openID
	 */
	private function recordOpenUser($openID) {
		if (trim($openID) == '') return;
		$this->getToken();
		$model = M();
		$sql = 'SELECT last_update FROM user_wechat WHERE open_id="'.$openID.'"';
		$f = $model->query($sql);
		
		$urlFull = self::WX_API_SERVICE_URL.'user/info?access_token='.self::$wx_last_token_string.'&openid='.urlencode($openID);
		if ($f == null) {
			// 记录新粉丝
			$info = Curl::get($urlFull, 2);
			if ($info === false) return;
			$user = json_decode($info['result'], true);
			if (!array_key_exists('openid', $user)) return;
			$sql = 'INSERT INTO `user_wechat` (`open_id`, `subscribe`, `subscribe_time`, `nickname`, `sex`, `language`, `city`, `province`, `country`, `headimgurl`, `user_id`, `user_mobile`, `create_time`) VALUES
			("'.$user['openid'].'", '.$user['subscribe'].', '.$user['subscribe_time'].', "'.$user['nickname'].'", '.$user['sex'].', "'.$user['language'].'", "'.$user['city'].'", "'.$user['province'].'", "'.$user['country'].'", "'.$user['headimgurl'].'", 0, "0", now())';
			$model->execute($sql);
		};
		// 更新粉丝的最后交互时间
		$sql = 'UPDATE user_wechat SET last_time=now() WHERE open_id="'.$openID.'"';
		$model->execute($sql);
		// 更新粉丝信息
		$lastUpdate = $f[0]['last_update'];
		if ($lastUpdate == null || $lastUpdate == 'null') {
			$info = Curl::get($urlFull, 2);
			if ($info === false) return;
			$user = json_decode($info['result'], true);
			if (!array_key_exists('openid', $user)) return;
			$sql = 'UPDATE user_wechat SET last_time=now(), last_update=now(), nickname="'.$user['nickname'].'",sex="'.$user['sex'].'",language="'.$user['language'].'",city="'.$user['city'].'",province="'.$user['province'].'",country="'.$user['country'].'",headimgurl="'.$user['headimgurl'].'" WHERE open_id="'.$openID.'"';
			$model->execute($sql);
		} else {
			// 每 7 天更新粉丝信息
			$lastUpdate = strtotime($lastUpdate);
			if (($this->time - $lastUpdate) > 86400*7) {
				$info = Curl::get($urlFull, 2);
				if ($info === false) return;
				$user = json_decode($info['result'], true);
				if (!array_key_exists('openid', $user)) return;
				$sql = 'UPDATE user_wechat SET last_time=now(), last_update=now(), nickname="'.$user['nickname'].'",sex="'.$user['sex'].'",language="'.$user['language'].'",city="'.$user['city'].'",province="'.$user['province'].'",country="'.$user['country'].'",headimgurl="'.$user['headimgurl'].'" WHERE open_id="'.$openID.'"';
				$model->execute($sql);
			};
		};
	}
	
	/**
	 * 用户关注事件
	 * 由于要求迅速响应，这里仅仅记录最关键的几个信息，用户详情留待自动任务去完善
	 * @param string $openID
	 * @param integer $userID
	 * @param string $userMobile
	 * @param string $subscribeTime
	 */
	private function doSubscribe($openID, $userID, $userMobile, $subscribeTime) {
		if (trim($openID) == '') return false;
		$model = M();
		$model->execute('UPDATE user_wechat SET subscribe=1, user_id='.$userID.', user_mobile="'.$userMobile.'", subscribe_time='.$subscribeTime.' WHERE open_id="'.$openID.'"');
	}
	
	/**
	 * 用户取消关注事件
	 * @param string $openID
	 * @param integer $userID
	 * @param string $userMobile
	 */
	private function doUnsubscribe($openID) {
		if (trim($openID) == '') return false;
		$model = M();
		return $model->execute('UPDATE user_wechat SET subscribe=0,last_time=now() WHERE open_id="'.$openID.'"');
	}
	
	/**
	 * 立即发送被动响应消息
	 * @param string $openID
	 * @param string $serviceID
	 * @param string $contentStr
	 */
	private function response($openID, $serviceID, $contentStr='') {
		if (trim($openID) == '') return false;
		//回复消息		
		$textTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[%s]]></MsgType><Content><![CDATA[%s]]></Content></xml>";
		$time = $this->time;
		$msgType = 'text';
		$resultStr = sprintf($textTpl, $openID, $serviceID, $time, $msgType, $contentStr);
		TraceAction::Trace(array('user_id'=>991, 'comment'=>$resultStr));
		echo $resultStr;
	}
	
	/**
	 * API - 对外提供获取微信 access token 的接口
	 * http://api.andaijia.com/wechat/?method=get-token&ver=1.0&time=1386582783&hash=83e787b3f7751484f5fb7138a66ea92f
	 */
	private function apiGetToken() {
		echo $this->getToken();
	}
	
	/**
	 * 以下是对外部开放的 API 方法集合，方法名均以 api 开头
	 * 访问规则等同于其他 API 接口，只是缺少了代理商一项参数和校验
	 */
	
	/**
	 * API - 创建/维护自定义菜单
	 * http://w.f-fusion.com/?method=create-menu&ver=1.0&time=1386582783&hash=930d61815f82eaf80781a2dc3187feb9
	 * http://127.0.0.1:120/?method=create-menu&ver=1.0&time=1386582783&hash=930d61815f82eaf80781a2dc3187feb9
	 */
	private function apiCreateMenu() {
		/*
		$menuData = '{
		    "button": [
		        {
		            "type": "view", 
		            "name": "在线调查", 
		            "url" : "http://m.f-fusion.com/wechat/vote"
		        }
		    ]
		}';
		*/
		$menuData = array();
		$menuData['button'] = array();
		$menuButton = array();
		$menuButton['name'] = '在线调查';
		$menuButton['type'] = 'view';
		$menuButton['url']  = $this->JANUSKING_WECHAT['DOMAIN_PREFIX'].'wechat/vote';
		$menuData['button'][] = $menuButton;
		$menuDataString = stripslashes(json_encode($menuData, JSON_UNESCAPED_UNICODE));
		$url = self::WX_API_SERVICE_URL.'menu/create?access_token='.self::$wx_last_token_string;
		$result = Curl::post($url, null, $menuDataString, 5);
		if ($result === false) {
			echo 'REQUEST FAILED!';
		} else {
			$resp = json_decode($result['result'], true);
			dump($resp);
		};
	}
}