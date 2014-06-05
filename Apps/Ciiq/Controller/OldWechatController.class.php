<?php
namespace Ciiq\Controller;

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
	const MY_TOKEN       = 'ee9279cbca374ok';
	/**
	 * 微信app id
	 * @var string
	 */
	const WX_APP_ID      = 'wx826c26712a4abc3d';
	/**
	 * 微信颁发的app安全码
	 * @var string
	 */
	const WX_APP_SECRET  = '0455fb92fa614226d101a605641d59d2';
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

	private $addr = null;
	private $cityID = 0;
	private $city = '';
	private $areaID = 0;
	private $area = '';
	private $lang = array(
		"DEFAULT" => "感谢关注驰球！",
		"SUBSCRIBE" => "感谢关注驰球！",
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
		
		if (!array_key_exists('signature', $_GET) || !array_key_exists('nonce', $_GET) || !array_key_exists('timestamp', $_GET)) {
			
			// 外部调用微信方法API /////////////////////////////////////////////////////////////////////////////
			// 外部调用微信处理必备四个固定参数 method, time, ver, hash
			if (!array_key_exists('method', $_GET)) {
				echo json_encode($this->_err[1000]);
				exit;
			};
			// check input params
			if (I('hash') == '') {
				echo json_encode($this->_err[1000]);
				exit;
			};
			if (strcmp($this->_version, I('ver')) != 0) {
				echo json_encode($this->_err[1001]);
				exit;
			};
			// verify params
			$params['method']= I('method');
			$params['time']  = I('time');
			$params['ver']   = I('ver');
			if (!$this->verifyHash(I('hash'), $params)) {
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
			$userCityID   = 0;
			$userOS       = 0;
			$checkUserBind = $this->checkUserBind($openID); // 检查此微信号是否绑定手机
			if ($checkUserBind === false) {
				// unregist
			} else {
				$userID     = $checkUserBind['user_id'];
				$userToken  = $checkUserBind['user_token'];
				$userMobile = $checkUserBind['user_mobile'];
				$userCityID = $checkUserBind['city_id'];
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
								case 'K_ORDER_ADD': // 我要代驾
									if ($checkUserBind === false) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['UNBIND'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['UNBIND']);
									} else {
										// 使用一个图文消息回复
										$urlTips = $this->buildAsfUrl($this->ANDAIJIA['config']['URL_MOBILE'].'wechat?', array(
											'method' => 'tips',
											'time'   => $this->time,
											'ver'    => '1.0',
										));
										$urlTips .= '&o='.urlencode($openID).'&u='.$userID;
										$textTpl = '<xml><ToUserName><![CDATA['.$openID.']]></ToUserName><FromUserName><![CDATA['.$serviceID.']]></FromUserName><CreateTime>'.$this->time.'</CreateTime><MsgType><![CDATA[news]]></MsgType>';
										$textTpl .= '<ArticleCount>1</ArticleCount>';
										$textTpl .= '<Articles>';
										$textTpl .= '<item>';
										$textTpl .= '<Title><![CDATA[手工提交您的位置信息]]></Title>';
										$textTpl .= '<Description><![CDATA[点击左下角键盘图标，点击 + 按钮，再点击【位置】按钮，地图上显示您的位置后，点击右上角的【发送】按钮，即可将您现在所处的位置发送给我们了~]]></Description>';
										$textTpl .= '<PicUrl><![CDATA['.$this->ANDAIJIA['config']['URL_STATIC'].'/public/images/tips0.jpg]]></PicUrl>';
										$textTpl .= '<Url><![CDATA['.$urlTips.']]></Url>';
										$textTpl .= '</item>';
										$textTpl .= '</Articles>';
										$textTpl .= '</xml>';
										echo $textTpl;
										
										/* 检查是否提交坐标
										$rs = $model->query('SELECT record_id,lng,lat,address FROM record_wechat WHERE flag=1 AND direction=2 AND create_time>DATE_ADD(NOW(), INTERVAL -15 MINUTE) AND user_id='.$userID.' AND open_id="'.$openID.'" AND `comment` IN("AUTO_LOCATION","MAN_LOCATION") ORDER BY record_id DESC');
										if ($rs == null) {
											//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['NO_LOCATION'].'"}}';
											//$this->push($openID, $params);
											$this->response($openID, $serviceID, $this->lang['NO_LOCATION']);
											break;
										};
										$lng = $rs[0]['lng'];
										$lat = $rs[0]['lat'];
										$address = $this->geodecode($lng, $lat, ($userOS==1?'amap':'baidu')); // 逆向地理编码
										if ($address == -2) {
											$respText = str_replace('{City}', mb_substr($this->city, 0, 2, 'UTF-8'), $this->lang['SERVICE_UNAVAILABEL']);
											$this->response($openID, $serviceID, $respText);
											break;
										};
										if ($address == false || $address == '') {
											//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['NO_ADDRESS'].'"}}';
											//$this->push($openID, $params);
											$this->response($openID, $serviceID, $this->lang['NO_ADDRESS']);
											break;
										};
										$this->addReadyOrder($userID, $checkUserBind['user_token'], $userMobile, $lng, $lat, $address, $openID, $serviceID);
										*/
									};
									break;
								case 'K_BALANCE': // 我的账户 -> 回复文本消息
									if ($checkUserBind === false) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['UNBIND'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['UNBIND']);
									} else {
										// 显示用户账户信息
										$userBalance = $checkUserBind['user_balance'];
										if ((double)$userBalance > 0) {
											$userAccountText = str_replace('{UserBalance}', $userBalance, $this->lang['ACCOUNT_CHARGE']);
										} else {
											$userAccountText = $this->lang['ACCOUNT_CASH']; // 现金用户
										};
										$userAgent   = $checkUserBind['user_agent'];
										if ($userAgent > 0) {
											$agent = D('Agent');
											$rs = $agent->find($userAgent);
											if ($rs == null) {} else {
												if ($rs['agent_type'] == 1) {
													if ((double)$userBalance > 0) {
														// 储值卡+后付费
														$userAccountText = $this->lang['ACCOUNT_CHARGE_VIP'];
														$userAccountText = str_replace('{UserBalance}', $userBalance, $userAccountText);
														$userAccountText = str_replace('{VipName}', $rs['agent_name'], $userAccountText);
													} else {
														// 仅后付费
														$userAccountText = $this->lang['ACCOUNT_VIP'];
													};
												};
											};
										};
										$userAccountText = str_replace('{UserMobile}', $userMobile, $userAccountText);
										// 显示用户等级
										$userLevel = $checkUserBind['user_level'];
										$level = array('普通用户', '普通用户', '白银用户', '黄金用户', '钻石用户');
										$userLevelText = str_replace('{UserLevel}', $level[$userLevel], $this->lang['LEVEL']);
										// 显示用户累计里程
										$userScore = $checkUserBind['user_score'];
										if ((int)$userScore > 0) {
											$userScoreText = str_replace('{UserScore}', $userScore, $this->lang['SCORE_AMOUNT']);
										} else {
											$userScoreText = $this->lang['SCORE_EMPTY'];
										};
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$userAccountText.'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $userAccountText."\n".$userLevelText."\n".$userScoreText);
									};
									break;
								case 'K_COUPONS': // 我的抵用券 -> 回复文本消息
									if ($checkUserBind === false) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['UNBIND'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['UNBIND']);
									} else {
										// 检查用户有没有抵用券
										$couponData = D('CouponData');
										$rs = $couponData->where(array(
											'user_id'        => $userID,
											'open'           => 0,
											'coupon_state'   => 0,
											'coupon_times'   => array('neq', 0),
											'available_time' => array('gt', $this->date),
										))->order('available_time ASC')->select();
										if ($rs == null) {
											$couponDataText = $this->lang['COUPON_EMPTY'];
										} else {
											$couponDataText = $this->lang['COUPON_LIST_HEAD'];
											$couponDataText = str_replace('{CouponCount}', count($rs), $couponDataText);
											$itemCashTpl  = $this->lang['COUPON_LIST_ITEM_CASH'];
											$itemRangeTpl = $this->lang['COUPON_LIST_ITEM_RANGE'];
											$couponItemsArr = array();
											foreach ($rs as $val) {
												$couponAmount   = (int)$val['coupon_amount'];
												$couponDistance = (int)$val['coupon_distance'];
												$availableTime  = substr($val['available_time'], 0, 10);
												if ($couponAmount > 0 && $couponDistance == 0) {
													$tItem = $itemCashTpl;
													$tItem = str_replace('{CouponAmount}', $couponAmount, $tItem);
													$tItem = str_replace('{AvailableTime}', $availableTime, $tItem);
													$couponItemsArr[] = $tItem;
													continue;
												};
												if ($couponAmount == 0 && $couponDistance > 0) {
													$tItem = $itemRangeTpl;
													$tItem = str_replace('{CouponDistance}', $couponDistance, $tItem);
													$tItem = str_replace('{AvailableTime}', $availableTime, $tItem);
													$couponItemsArr[] = $tItem;
													continue;
												};
											};
											if (count($couponItemsArr) > 0) {
												$couponDataText = str_replace('{Item}', "\n".implode("\n", $couponItemsArr), $couponDataText);
											} else {
												$couponDataText = $this->lang['COUPON_EMPTY']; // 异常情况
											};
										};
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$couponDataText.'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $couponDataText);
									};
									break;
								case 'K_ORDERS': // 我的订单 -> 回复图文消息 消息链接带有用户user id参数
									if ($checkUserBind === false) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['UNBIND'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['UNBIND']);
									} else {
										$build = '2.0.0.140102A';
										$time = $this->time;
										$ver = '2.0';
										$hash = md5($userID.$userToken.$build.$time.$ver.$this->HASH_STRING_SUFFIX);
										$url = $this->ANDAIJIA['config']['URL_MAIN'].'order/index/?user_id='.$userID.'&user_token='.$userToken.'&build='.$build.'&time='.$time.'&ver='.$ver.'&hash='.$hash;
										
										$result = Curl::get($url, 2);
										if ($result === false) {
											//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['ORDER_EMPTY'].'"}}';
											//$this->push($openID, $params);
											$this->response($openID, $serviceID, $this->lang['ORDER_EMPTY']);
										} else {
											$queryOrders = json_decode($result['result'], true);
											if (count($queryOrders) > 0) {
												// 使用一个图文消息回复
												$urlOrderList = $this->buildAsfUrl($this->ANDAIJIA['config']['URL_MOBILE'].'wechat?', array(
													'method' => 'order-list',
													'time'   => $this->time,
													'ver'    => '1.0',
												));
												$urlOrderList .= '&o='.urlencode($openID).'&u='.$userID;
												$textTpl = '<xml><ToUserName><![CDATA['.$openID.']]></ToUserName><FromUserName><![CDATA['.$serviceID.']]></FromUserName><CreateTime>'.$this->time.'</CreateTime><MsgType><![CDATA[news]]></MsgType>';
												$textTpl .= '<ArticleCount>1</ArticleCount>';
												$textTpl .= '<Articles>';
												$textTpl .= '<item>';
												$textTpl .= '<Title><![CDATA['.$this->lang['MSG_ORDER_TITLE'].']]></Title>';
												$desc = str_replace("{OrderCount}", count($queryOrders), $this->lang['MSG_ORDER_DESCRIPTION']);
												$textTpl .= '<Description><![CDATA['.$desc.']]></Description>';
												$textTpl .= '<PicUrl><![CDATA[]]></PicUrl>';
												$textTpl .= '<Url><![CDATA['.$urlOrderList.']]></Url>';
												$textTpl .= '</item>';
												$textTpl .= '</Articles>';
												$textTpl .= '</xml>';
												echo $textTpl;
											} else {
												//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['ORDER_EMPTY'].'"}}';
												//$this->push($openID, $params);
												$this->response($openID, $serviceID, $this->lang['ORDER_EMPTY']);
											};
										};
									};
									break;
								case 'K_PRICE': // 价格表 -> 回复文本消息
									// 检查是否提交坐标
									$rs = $model->query('SELECT record_id,lng,lat,address FROM record_wechat WHERE flag=1 AND direction=2 AND create_time>DATE_ADD(NOW(), INTERVAL -15 MINUTE) AND user_id='.$userID.' AND open_id="'.$openID.'" AND `comment` IN("AUTO_LOCATION","MAN_LOCATION") ORDER BY record_id DESC');
									if ($rs == null) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['NO_LOCATION'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['NO_LOCATION']);
										return;
									};
									$lng = $rs[0]['lng'];
									$lat = $rs[0]['lat'];
									$address = $this->geodecode($lng, $lat, ($userOS==1?'amap':'baidu')); // 逆向地理编码
									if ($address == -2) {
										$priceText = str_replace('{City}', mb_substr($this->city, 0, 2, 'UTF-8'), $this->lang['SERVICE_UNAVAILABEL']);
										$this->response($openID, $serviceID, $priceText);
										break;
									};
									if ($address == false || $address == '') {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['NO_ADDRESS'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['NO_ADDRESS']);
										return;
									};
									$openUserCity = mb_substr($this->city, 0, 2, 'UTF-8');
									if ($openUserCity == "上海" || $openUserCity == "郑州" || $openUserCity == "武汉" || $openUserCity == "济南") {
										if ($openUserCity == '上海') {
											$priceText = str_replace('{City}', $openUserCity, $this->lang['PRICE_SH']);
										}  else {
											$priceText = str_replace('{City}', $openUserCity, $this->lang['PRICE_OTHER']);
										};
									};
									//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$priceText.'"}}';
									//$this->push($openID, $params);
									$this->response($openID, $serviceID, $priceText);
									break;
								case 'K_BIND_COUPON': // 绑定抵用券 -> 回复文本消息
									$urlCoupon = $this->buildAsfUrl($this->ANDAIJIA['config']['URL_MOBILE'].'wechat?', array(
										'method' => 'coupon',
										'time'   => $this->time,
										'ver'    => '1.0',
									));
									$urlCoupon .= '&o='.urlencode($openID).'&u='.$userID;
									$textTpl = '<xml><ToUserName><![CDATA['.$openID.']]></ToUserName><FromUserName><![CDATA['.$serviceID.']]></FromUserName><CreateTime>'.$this->time.'</CreateTime><MsgType><![CDATA[news]]></MsgType>';
									$textTpl .= '<ArticleCount>1</ArticleCount>';
									$textTpl .= '<Articles>';
									$textTpl .= '<item>';
									$textTpl .= '<Title><![CDATA['.$this->lang['MSG_VERIFY_COUPON_TITLE'].']]></Title>';
									$desc = str_replace('{Rule}', $this->ANDAIJIA['config']['CLIENT_USER_SHARE_RULE'], $this->lang['MSG_VERIFY_COUPON_DESCRIPTION']);
									$textTpl .= '<Description><![CDATA['.$desc.']]></Description>';
									$textTpl .= '<PicUrl><![CDATA[]]></PicUrl>';
									$textTpl .= '<Url><![CDATA['.$urlCoupon.']]></Url>';
									$textTpl .= '</item>';
									$textTpl .= '</Articles>';
									$textTpl .= '</xml>';
									echo $textTpl;
									break;
								case 'K_SHARE': // 分享 -> 回复文本消息
									// 用户点击后，取得注册用户的id构建一个链接，指向安师傅自己的网页
									// 网页内部自己负责获取微信临时二维码，并维护二维码的过期自动重新创建
									// 用户可以把这个页面分享出去
									if ($checkUserBind === false) {
										//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['UNBIND'].'"}}';
										//$this->push($openID, $params);
										$this->response($openID, $serviceID, $this->lang['UNBIND']);
									} else {
										$urlShare = $this->buildAsfUrl($this->ANDAIJIA['config']['URL_MOBILE'].'wechat?', array(
											'method' => 'share',
											'time'   => $this->time,
											'ver'    => '1.0',
										));
										$urlShare .= '&o='.urlencode($openID).'&u='.$userID;
										$textTpl = '<xml><ToUserName><![CDATA['.$openID.']]></ToUserName><FromUserName><![CDATA['.$serviceID.']]></FromUserName><CreateTime>'.$this->time.'</CreateTime><MsgType><![CDATA[news]]></MsgType>';
										$textTpl .= '<ArticleCount>1</ArticleCount>';
										$textTpl .= '<Articles>';
										$textTpl .= '<item>';
										$textTpl .= '<Title><![CDATA['.$this->lang['MSG_SHARE_TILE'].']]></Title>';
										$textTpl .= '<Description><![CDATA['.$this->lang['MSG_SHARE_DESCRIPTION'].']]></Description>';
										$textTpl .= '<PicUrl><![CDATA[]]></PicUrl>';
										//$textTpl .= '<PicUrl><![CDATA['.$this->ANDAIJIA['config']['URL_STATIC_NEW'].'/public/images/640x320.png]]></PicUrl>';
										$textTpl .= '<Url><![CDATA['.$urlShare.']]></Url>';
										$textTpl .= '</item>';
										$textTpl .= '</Articles>';
										$textTpl .= '</xml>';
										echo $textTpl;
									};
									break;
							};
							break;
					};
					break;
				case 'text': // 接收 -> 文本消息
					// 前面都不匹配，可能是确认订单信息，或者是普通上行消息
					if ($checkUserBind === false) {
						// 未绑定手机用户发送手机号，要求绑定
						if (preg_match("/^1[3458][0-9]{9}$/", $content)) {
							$sql = 'SELECT * FROM user_wechat WHERE user_mobile="'.$content.'" LIMIT 1';
							$ss = $model->query($sql);
							if ($ss == null) {
								$f = $this->createUserBindToken($openID, $content);
								if ($f === false) {
									//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['RECEIVE_SEND_VERIFY_CODE_FAILED'].'"}}';
									//$this->push($openID, $params);
									$this->response($openID, $serviceID, $this->lang['RECEIVE_SEND_VERIFY_FAILED']);
								} else {
									//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['RECEIVE_SEND_VERIFY_SUCCESS'].'"}}';
									//$this->push($openID, $params);
									$this->response($openID, $serviceID, $this->lang['RECEIVE_SEND_VERIFY_SUCCESS']);
								};
							} else {
								//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['RECEIVE_CHANGE_MOBILE'].'"}}';
								//$this->push($openID, $params);
								$this->response($openID, $serviceID, $this->lang['RECEIVE_CHANGE_MOBILE']);
							};
							break;
						};
						// 未绑定手机用户发送验证码，要求验证
						if (preg_match("/^[0-9]{6}$/", $content)) {
							$this->doUserBind($openID, $content);
							break;
						};
					} else {
						if (strtoupper($content) == 'X') {
							// [取消派单中超时订单]
							// 取出缓存中保存的两次订单提示日志，在此日志中的才可取消
							$timeoutLog = include '/www/daijia/Task/Conf/timeout.php'; // 记忆已经发出1、2次通知的订单id
							$noticeOne  = $timeoutLog['one']; // 提醒过1次的订单id
							$noticeTwo  = $timeoutLog['two']; // 提醒过2次的订单id
							$notice = array_merge($noticeOne, $noticeTwo);
							$notice = array_unique($notice);
							$notice = array_values($notice);
							$pattern = $this->ANDAIJIA['config']['APP_ORDER_TIMEOUT']; // 单位是分钟
							$myOrderTimeoutId = array();
							$myOrderTimeoutCoupon = array();
							$myOrderTimeoutAddress = array();
							// 找到立即单
							$sql = 'SELECT order_id,coupon_data_id,departure FROM user_order 
									WHERE order_state=1 AND create_time=request_time 
									AND (driver_id=0 OR driver_id IS NULL) 
									AND order_source=1 AND order_flag=0 
									AND ABS(TIMESTAMPDIFF(MINUTE, NOW(), create_time))>='.$pattern[0].' 
									AND user_id='.$userID.' AND order_id IN ('.implode(',', $notice).')';
							$rs = $model->query($sql);
							if ($rs == null) {} else {
								foreach ($rs as $item) {
									$myOrderTimeoutId[] = $item['order_id'];
									$myOrderTimeoutAddress[] = str_replace('[APP]', '', $item['departure']);
									if ($item['coupon_data_id'] != null) $myOrderTimeoutCoupon[] = $item['coupon_data_id'];
								};
							};
							// 找到SH预约单
							$sql = 'SELECT order_id,coupon_data_id,departure FROM user_order 
									WHERE city_id=321 AND order_state=1 AND create_time<>request_time 
									AND (driver_id=0 OR driver_id IS NULL) 
									AND order_source=1 AND order_flag=0 
									AND NOW()>=DATE_ADD(request_time, INTERVAL '.(0-30+$pattern[0]).' MINUTE) 
									AND user_id='.$userID.' AND order_id IN ('.implode(',', $notice).')';
							$rs = $model->query($sql);
							if ($rs == null) {} else {
								foreach ($rs as $item) {
									$myOrderTimeoutId[] = $item['order_id'];
									$myOrderTimeoutAddress[] = str_replace('[APP]', '', $item['departure']);
									if ($item['coupon_data_id'] != null) $myOrderTimeoutCoupon[] = $item['coupon_data_id'];
								};
							};
							// 找到OTHERS预约单
							$sql = 'SELECT order_id,coupon_data_id,departure FROM user_order 
									WHERE city_id<>321 AND order_state=1 AND create_time<>request_time 
									AND (driver_id=0 OR driver_id IS NULL) 
									AND order_source=1 AND order_flag=0 
									AND NOW()>=DATE_ADD(request_time, INTERVAL '.(0-45+$pattern[0]).' MINUTE) 
									AND user_id='.$userID.' AND order_id IN ('.implode(',', $notice).')';
							$rs = $model->query($sql);
							if ($rs == null) {} else {
								foreach ($rs as $item) {
									$myOrderTimeoutId[] = $item['order_id'];
									$myOrderTimeoutAddress[] = str_replace('[APP]', '', $item['departure']);
									if ($item['coupon_data_id'] != null) $myOrderTimeoutCoupon[] = $item['coupon_data_id'];
								};
							};
							if (count($myOrderTimeoutId) > 0) {
								// 取消订单
								$sql = 'UPDATE user_order SET order_state=-1,cancel_time=NOW(),`comment`=CONCAT(`comment`, "客人从微信主动取消") WHERE order_state=1 AND user_id='.$userID.' AND order_id IN ('.implode(',', $myOrderTimeoutId).')';
								$model->execute($sql);
								// 返还优惠券
								if (count($myOrderTimeoutCoupon) > 0) {
									$sql = 'UPDATE coupon_data SET draw_time=NULL, coupon_state=0, coupon_times=(CASE WHEN coupon_times>-1 THEN coupon_times+1 ELSE coupon_times END) WHERE open=0 AND data_id IN ('.implode(',', $myOrderTimeoutCoupon).')';
									$f = $model->execute($sql);
								};
								// 推送
								$pushStr = "\n".implode("\n", $myOrderTimeoutAddress);
								$params = '{"touser": "'.$openID.'", "msgtype": "text", "text": {"content": "'.$this->lang['PUSH_TIMEOUT_ORDER_CANCEL_ADDRESS'].$pushStr.'"}}';
								$this->push($openID, $params);
								break;
							} else {
								// 没有找到订单超时，可能订单已经取消了，或者根本就没有
								$params = '{"touser": "'.$openID.'", "msgtype": "text", "text": {"content": "'.$this->lang['PUSH_TIMEOUT_ORDER_EMPTY'].'"}}';
								$this->push($openID, $params);
								break;
							};
						};
						// 可能是取消订单信息
						if (strtoupper($content) == 'NO') {
							// [取消预定订单]拿到其全部预定信息
							$sql = 'SELECT record_id FROM record_wechat WHERE flag=2 AND direction=1 AND create_time>DATE_ADD(NOW(), INTERVAL -5 MINUTE) AND user_id='.$userID.' AND open_id="'.$openID.'" AND `comment`="WAIT_CONFIRM" ORDER BY record_id DESC';
							$rs = $model->query($sql);
							if ($rs == null) {
								//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['APPLY_SERVICE_EMPTY'].'"}}';
								//$this->push($openID, $params);
								$this->response($openID, $serviceID, $this->lang['APPLY_SERVICE_EMPTY']);
								break;
							} else {
								foreach ($rs as $item) {
									$recordID = $item['record_id'];
									$model->execute('UPDATE record_wechat SET comment="CANCEL_CONFIRM" WHERE record_id='.$recordID);
								};
								//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['APPLY_SERVICE_REMOVED'].'"}}';
								//$this->push($openID, $params);
								$this->response($openID, $serviceID, $this->lang['APPLY_SERVICE_REMOVED']);
								break;
							};
						};
						// 已绑定手机用户
						if (strtoupper($content) == 'Y') {
							$sql = 'SELECT record_id,lng,lat FROM record_wechat WHERE flag=2 AND direction=1 AND create_time>DATE_ADD(NOW(), INTERVAL -5 MINUTE) AND user_id='.$userID.' AND open_id="'.$openID.'" AND `comment`="WAIT_CONFIRM" ORDER BY record_id DESC LIMIT 1';
							$rs = $model->query($sql);
							if ($rs == null) {} else {
								$recordID = $rs[0]['record_id'];
								$orderID = $this->addOrder($userID, $userToken, $userMobile, $rs[0]['lng'], $rs[0]['lat'], $openID); // 尝试创建订单
								if (preg_match("/^[0-9]+$/", $orderID) == false) {
									//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['APPLY_SERVICE_FAILED'].'"}}';
									//$this->push($openID, $params);
									$this->response($openID, $serviceID, $this->lang['APPLY_SERVICE_FAILED'].$orderID);
									break;
								} else {
									// 下单成功，结束流程，等待计划任务操作推送订单信息
									$model->execute('UPDATE record_wechat SET comment="ORDER_COMPLETE+'.$orderID.'" WHERE record_id='.$recordID);
									//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['APPLY_SERVICE_SUCCESS'].'"}}';
									//$this->push($openID, $params);
									$this->response($openID, $serviceID, $this->lang['APPLY_SERVICE_SUCCESS']);
									break;
								};
							};
						};
						// TODO: console
						if (strcmp($openID, 'oLDrijmU0j3gUqBbTGfPfYaqItbQ') == 0) {
							$this->console($content, $openID, $serviceID);
							break;
						};
					};
					//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['DEFAULT'].'"}}';
					//$this->push($openID, $params);
					$this->response($openID, $serviceID, $this->lang['DEFAULT']); // 默认消息
					break;
				case 'image': // 接收 -> 图片消息
				case 'voice': // 接收 -> 语音消息
				case 'video': // 接收 -> 视频消息
				case 'link': // 接收 -> 链接消息
					//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['DEFAULT'].'"}}';
					//$this->push($openID, $params);
					$this->response($openID, $serviceID, $this->lang['DEFAULT']); // 默认消息
					break;
				case 'location': // 地理位置消息(用户手工上报)
					$lng = trim($postObj->Location_Y);
					$lat = trim($postObj->Location_X);
					$address = $this->geodecode($lng, $lat, ($userOS==1?'amap':'baidu')); // 逆向地理编码
					if ($address == -2) {
						$respText = str_replace('{City}', mb_substr($this->city, 0, 2, 'UTF-8'), $this->lang['SERVICE_UNAVAILABEL']);
						$this->response($openID, $serviceID, $respText);
						break;
					};
					$address = (string)trim($postObj->Label); // 使用微信的地址文本替换高德文本
					$key = mb_stripos($address, '邮政编码', 0, 'utf8');
					if ($key === false) {} else {
						$pos = mb_strlen($address, 'utf8') - $key;
						$address = mb_substr($address, 0, 0-$pos, 'utf8');
					};
					$this->recordLocate($lng, $lat, $address, $openID, $userID, 'MAN_LOCATION');
					if ($address == -2) {
						$respText = str_replace('{City}', mb_substr($this->city, 0, 2, 'UTF-8'), $this->lang['SERVICE_UNAVAILABEL']);
						$this->response($openID, $serviceID, $respText);
						break;
					};
					if ($address == false || $address == '') {
						//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['NO_ADDRESS'].'"}}';
						//$this->push($openID, $params);
						$this->response($openID, $serviceID, $this->lang['NO_ADDRESS']);
						break;
					};
					$this->addReadyOrder($userID, $checkUserBind['user_token'], $userMobile, $lng, $lat, $address, $openID, $serviceID);
					break;
				default:
					//$params = '{"touser":"'.$openID.'","msgtype":"text","text":{"content":"'.$this->lang['DEFAULT'].'"}}';
					//$this->push($openID, $params);
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
	 * 高德 - 逆向地理编码得到地址文本
	 * {
		    "status": "1",
		    "info": "OK",
		    "regeocode": {
		        "formatted_address": "上海市浦东新区花木街道汤臣豪园",
		        "addressComponent": {
		            "province": "上海市",
		            "city": [],
		            "district": "浦东新区",
		            "township": "花木街道",
		            "neighborhood": {
		                "name": "汤臣豪园",
		                "type": "商务住宅;住宅区;住宅小区"
		            },
		            "building": {
		                "name": [],
		                "type": []
		            },
		            "streetNumber": {
		                "street": "科苑路",
		                "number": "88",
		                "location": "121.586891,31.2108",
		                "direction": "东",
		                "distance": "103.57"
		            }
		        },
		        "pois": [
		            {
		                "id": "B001519472",
		                "name": "德国中心",
		                "type": "商务住宅;楼宇;商务写字楼",
		                "tel": [],
		                "direction": "东",
		                "distance": "103.198",
		                "location": "121.5868858333,31.2108038889"
		            },
		            {
		                "id": "B001529731",
		                "name": "汤臣豪园",
		                "type": "商务住宅;住宅区;住宅小区",
		                "tel": "021-50801234",
		                "direction": "西",
		                "distance": "112.127",
		                "location": "121.5846688889,31.2104358333"
		            }
		        ],
		        "roads": [
		            {
		                "id": "021H51F0100138226",
		                "name": "松涛路",
		                "direction": "东北",
		                "distance": "146.955",
		                "location": "121.585,31.2097"
		            },
		            {
		                "id": "021H51F010014556",
		                "name": "龙东大道",
		                "direction": "南",
		                "distance": "162.932",
		                "location": "121.585,31.212"
		            },
		            {
		                "id": "021H51F01001394",
		                "name": "科苑路",
		                "direction": "西",
		                "distance": "177.953",
		                "location": "121.588,31.2107"
		            }
		        ],
		        "roadinters": [
		            {
		                "direction": "西",
		                "distance": "177.953",
		                "location": "121.5877039,31.21068917",
		                "first_id": "021H51F01001394",
		                "first_name": "科苑路",
		                "second_id": "021H51F010013959",
		                "second_name": "李时珍路"
		            }
		        ]
		    }
		}
		BD返回格式
		{
		    "status":"OK",
		    "result":{
		        "location":{
		            "lng":121.402252,
		            "lat":31.256636
		        },
		        "formatted_address":"上海市普陀区铜川路1366号101-105室",
		        "business":"真如,长征,梅川路",
		        "addressComponent":{
		            "city":"上海市",
		            "district":"普陀区",
		            "province":"上海市",
		            "street":"铜川路",
		            "street_number":"1366号101-105室"
		        },
		        "cityCode":289
		    }
		}
	 * @param double $lng
	 * @param double $lat
	 * @param string $type 标记坐标类别
	 * @return string
	 */
	private function geodecode($lng, $lat, $type='amap') {
		$type = 'amap';
		
		if ($type == 'amap') { // iOS 设备使用 GPS 坐标，使用高德解析
			$url = $this->_amap_api_url.'/v3/geocode/regeo?location='.$lng.','.$lat.'&extensions=all&output=json&key='.$this->_amap_lbs_key.'&coordsys=google';
			$r = Curl::get($url, 3);
			if ($r === false) return false;
			$info = json_decode($r['result'], true);
			if (is_array($info['regeocode']['addressComponent']['city']) && count($info['regeocode']['addressComponent']['city']) == 0) {
				$city = mb_substr($info['regeocode']['addressComponent']['province'], 0, 2, 'UTF-8'); // 直辖市
			} else {
				$city = mb_substr($info['regeocode']['addressComponent']['city'], 0, 2, 'UTF-8');
			};
			$area = mb_substr($info['regeocode']['addressComponent']['district'], 0, 2, 'UTF-8');
			$address = $info['regeocode']['formatted_address'].'('.$info['regeocode']['roads'][0]['name'].$info['regeocode']['roads'][0]['direction'].')';
		} else { // Android 设备使用 SOSO 地图，坐标格式是 Google，因此可以使用百度解析
			$gps = D('Gps');
			$coord = $gps->google2baidu($lng, $lat);
			$url = $this->_bd_api_url.'/geocoder/v2/?location='.$coord['latitude'].','.$coord['longitude'].'&output=json&ak='.$this->_bd_lbs_key.'&coordsys=bd09ll';
			$r = Curl::get($url, 3);
			if ($r === false) return false;
			$info = json_decode($r['result'], true);
			$city = mb_substr($info['result']['addressComponent']['city'], 0, 2, 'UTF-8');
			$area = mb_substr($info['result']['addressComponent']['district'], 0, 2, 'UTF-8');
			$address = $info['result']['formatted_address'];
		};
		if ($city == '') return false;
		$this->city = $city;
		$this->area = $area;
		$model = M();
		// 
		$rs = $model->query('SELECT * FROM city WHERE LEFT(name, 2)="'.$this->city.'" AND enable=1 ');
		if ($rs == null) return -2;
		if (is_array($rs) && count($rs) == 1) {
			$this->cityID = $rs[0]['city_id'];
		};
		// 再去查城区
		
		$time = $this->time;
		$ver = C('CONST_VER');
		$hash = md5($this->cityID.$area.$time.$ver.$this->HASH_STRING_SUFFIX);
		$url = $this->ANDAIJIA['config']['URL_API'].'/place/relations?city_id='.$this->cityID.'&area='.$area.'&time='.$time.'&ver='.$ver.'&hash='.$hash;
		$data = Curl::get($url, 2);
		if ($data === false) {
			$this->areaID = 0;
		} else {
			$this->areaID = $data['result'];
		};
		return $address;
	}
	
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
	 * 记录用户上报的坐标
	 * @param double $lng
	 * @param double $lat
	 * @param string $address
	 * @param string $openID
	 * @param integer $userID
	 * @param string $comment
	 */
	private function recordLocate($lng, $lat, $address, $openID, $userID, $comment='AUTO_LOCATION') {
		if (trim($openID) == '') return;
		$address = $address === false ? '' : $address;
		$recordWechat = D('RecordWechat');
		$rs = $recordWechat->field('record_id')->where(array(
			'create_time' => array('gt', date('Y-m-d H:i:s', strtotime('-15 minute'))),
			'direction' => 2,
			'flag'    => 1,
			'user_id' => $userID,
			'open_id' => $openID,
			'comment' => $comment,
		))->order('record_id DESC')->select();
		if ($rs == null) {
			$recordWechat->add(array(
				'create_time' => $this->date,
				'direction' => 2,
				'flag'    => 1,
				'user_id' => $userID,
				'open_id' => $openID,
				'lng'     => $lng,
				'lat'     => $lat,
				'address' => $address,
				'comment' => $comment,
			));
			return;
		};
		$recordID = $rs[0]['record_id'];
		$model = M();
		$model->execute('UPDATE record_wechat SET lng='.$lng.', lat='.$lat.', address="'.$address.'" WHERE record_id='.$recordID);
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
	 * API - 批量拉取/更新全部关注者信息列表
	 * 每日运行一次，增加或者更新用户信息
	 * http://test.api.4001002003.com/wechat/?method=get-users&ver=1.0&time=1386582783&hash=90fbad28c54e783dcfd01723c01171cb
	 */
	private function apiGetUsers() {
		$this->getToken();
		$url = self::WX_API_SERVICE_URL.'user/get?access_token='.self::$wx_last_token_string;
		
		$result = Curl::get($url, 2);
		if ($result === false) return;
		$resp = json_decode($result['result'], true);
		if (!array_key_exists('data', $resp)) return;
		$ids = $resp['data']['openid'];
		echo count($ids);
		$i = 1;
		$model = M();
		foreach ($ids as $openID) {
			$urlFull = self::WX_API_SERVICE_URL.'user/info?access_token='.self::$wx_last_token_string.'&openid='.urlencode($openID);
			$info = Curl::get($urlFull, 2);
			if ($info === false) continue;
			$user = json_decode($info['result'], true);
			if (!array_key_exists('openid', $user)) continue;
			$sql = 'INSERT INTO `user_wechat` (`open_id`, `subscribe`, `subscribe_time`, `nickname`, `sex`, `language`, `city`, `province`, `country`, `headimgurl`, `user_id`, `user_mobile`, `create_time`, `comment`) VALUES
			("'.$openID.'", '.$user['subscribe'].', '.$user['subscribe_time'].', "'.$user['nickname'].'", '.$user['sex'].', "'.$user['language'].'", "'.$user['city'].'", "'.$user['province'].'", "'.$user['country'].'", "'.$user['headimgurl'].'", 0, "0", now(), "自动采集")';
			$f = $model->execute($sql);
			if ($f === false) { // 更新已存在的粉丝
				$sql = 'UPDATE user_wechat SET nickname="'.$user['nickname'].'",sex="'.$user['sex'].'",language="'.$user['language'].'",city="'.$user['city'].'",province="'.$user['province'].'",country="'.$user['country'].'",headimgurl="'.$user['headimgurl'].'" WHERE open_id="'.$openID.'"';
				$f = $model->execute($sql);
			};
			$i++;
		};
	}
	
	/**
	 * API - 创建/维护自定义菜单
	 * http://w.f-fusion.com/?method=create-menu&ver=1.0&time=1386582783&hash=930d61815f82eaf80781a2dc3187feb9
	 * http://127.0.0.1:120/?method=create-menu&ver=1.0&time=1386582783&hash=930d61815f82eaf80781a2dc3187feb9
	 */
	private function apiCreateMenu() {
		$menuData = '{
		    "button": [
		        {
		            "name": "大电商", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "微电商", 
		                    "url" : "http://m.f-fusion.com/wechat/weidianshang"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "独立电商", 
		                    "url" : "http://m.f-fusion.com/wechat/dulishangcheng"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "进销存管理", 
		                    "url" : "http://m.f-fusion.com/wechat/jinxiaocun"
		                }
		            ]
		        }, 
		        {
		            "name": "整合营销", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "品牌整合", 
		                    "url" : "http://m.f-fusion.com/wechat/pinpaizhenghe"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "渠道整合", 
		                    "url" : "http://m.f-fusion.com/wechat/qudaozhenghe"
		                }
		            ]
		        }, 
		        {
		            "name": "关注熔意", 
		            "sub_button": [
		                {
		                    "type": "view", 
		                    "name": "品牌进化论", 
		                    "url" : "http://m.f-fusion.com/wechat/jinhualun"
		                }, 
		                {
		                    "type": "view", 
		                    "name": "合作共赢", 
		                    "url" : "http://m.f-fusion.com/wechat/hezuo"
		                },
		                {
		                    "type": "view", 
		                    "name": "联系我们", 
		                    "url" : "http://m.f-fusion.com/wechat/contact"
		                }
		            ]
		        }
		    ]
		}';
		$url = self::WX_API_SERVICE_URL.'menu/create?access_token='.self::$wx_last_token_string;
		$result = Curl::post($url, null, $menuData, 3);
		if ($result === false) {
			echo 'REQUEST FAILED!';
		} else {
			$resp = json_decode($result['result'], true);
			dump($resp);
		};
	}
}