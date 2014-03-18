<?php
namespace Api\Controller;
use Think\Controller;

/**
 * 后台地理信息转换
 * @author guanxuejun
 *
 */
class PlaceController extends Control\Controller\BaseController {
	
	function __construct() {
		parent::__construct();
		header('Content-Type:text/html;charset=utf-8');
	}
	
	/**
	 * Google反编译到地址文本
	 * http://main.andaijia.com/place/revert?longitude=121.402252&latitude=31.256636&format=bd
	 * http://main.andaijia.com/place/revert?longitude=121.495901&latitude=31.263135&format=bd
	 * http://127.0.0.1:116/place/revert?longitude=121.402252&latitude=31.256636&format=bd
	 * BD返回格式
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
	 * @param double longitude Google
	 * @param double latitude  Google
	 * @param string format=bd 本参数主要用于内部查询使用，直接返回百度地址全部信息
	 */
	public function revert($gglongitude = 0, $gglatitude = 0, $format = true) {
		$isGet = true;
		if ($gglongitude > 0 && $gglatitude > 0) $isGet = false;
		if ((!isset($_GET['longitude']) || trim($_GET['longitude']) == '') && $gglongitude <= 0) {echo '无法反向编译地址'; return;};
		if ((!isset($_GET['latitude'])  || trim($_GET['latitude'])  == '') && $gglatitude <= 0) {echo '无法反向编译地址'; return;};
		if($isGet){
			$format       = $_GET['format'] == 'bd' ? true : false;
			$gglongitude  = $_GET['longitude'];
			$gglatitude   = $_GET['latitude'];
			$build 		  = $_GET['build'];
    		$time		  = $_GET['time'];
			$ver		  = $_GET['ver'];
			$hash		  = $_GET['hash'];
			if (is_null(C('LOCAL'))) {
				if ($hash != md5($format.$gglongitude.$gglatitude.$build.$time.$ver.C('HASH_STRING_SUFFIX'))) return;
			};
		};
		
		$gps = D('Gps');
		$coodr = $gps->google2baidu($gglongitude, $gglatitude); // 提交的坐标当谷歌坐标转成百度
		if ($coodr) {
			$bdlongitude = $coodr['longitude'];
			$bdlatitude  = $coodr['latitude'];
		} else {
			return;
		};
		// 当心BD的组合方式：lat<纬度>,lng<经度>  例如 38.76623,116.43213
		$url = $this->_bd_api_url.'/geocoder/v2/?&coordtype=bd09ll&location='.$bdlatitude.','.$bdlongitude.'&output=json&ak='.$this->_bd_lbs_key;
		
		import('ORG.Net.Curl');
		$r = Curl::get($url, 10);
		$r = json_decode($r['result'], true);
		$r['result']['outService'] = 0;
		if (!$this->inServiceCity($bdlongitude, $bdlatitude)) {
			$r['result']['outService'] = 1;
		};
		if ($format) {
			if($isGet){
				echo json_encode($r);
				return;
			}else {
				return json_encode($r);
			}
		};
		if($isGet){
			echo $r['result']['formatted_address'];
		}else {
			return $r['result']['formatted_address'];
		}
	}
	
	/**
	 * 周边地标查询
	 * 给定当前查询的字串，前往百度查询 
	 * type=1表示返回google坐标
	 * type=2表示返回baidu坐标
	 * http://main.andaijia.com/place/search?query=龙东大道666号&longitude=121.402252&latitude=31.256636&output=1
	 * http://127.0.0.1:116/place/search?query=龙东大道666号&longitude=121.402252&latitude=31.256636&output=1
		{
		    "status": 0,
		    "message": "ok",
		    "results": [
		        {
		            "name": "龙东大道666号",
		            "location": {
		                "lat": 31.217843,   GPS坐标？
		                "lng": 121.59098    GPS坐标？
		            },
		            "address": "上海市浦东新区"
		        }
		    ]
		}
	 * @param string query 查询地址
	 * @param double longitude google
	 * @param double latitude  gogle
	 * @param integer output 输出坐标格式：0=GPS格式,1=bd格式,2=google格式
	 * ************ 每个key支持每天100000次的调用，超过限制不返回数据 ***************
	 */
	public function search() {
		if (!isset($_GET['longitude']) || trim($_GET['longitude']) == '') {echo '无法反向编译地址1'; return;};
		if (!isset($_GET['latitude'])  || trim($_GET['latitude'])  == '') {echo '无法反向编译地址2'; return;};
		if (!isset($_GET['query']) || trim($_GET['query']) == '') {echo '无法反向编译地址3'; return;};
		$query = $_GET['query'];
		$gglongitude 	= $_GET['longitude'];
		$gglatitude  	= $_GET['latitude'];
		$_GET['output'] = preg_match("/^[012]$/", $_GET['output']) == false ? 1 : (int)$_GET['output'];
		$output = $_GET['output'];
		$page      	= preg_match("/^[0-9]+$/", $_GET['page']) ? (int)$_GET['page'] : 1;
		$page       = $page <= 0 ? 1 : $page; // 接口页码从 1 开始
		$build 		= $_GET['build'];
    	$time		= $_GET['time'];
		$ver		= $_GET['ver'];
		$hash		= $_GET['hash'];
		if (is_null(C('LOCAL'))) {
			if ($hash != md5($query.$gglongitude.$gglatitude.$output.$page.$build.$time.$ver.C('HASH_STRING_SUFFIX'))) return;
		};
		
		// 先判断所在城市
		$r = $this->revert($gglongitude, $gglatitude, true);
		if ($r === false) {echo '无法反向编译地址4'; return;};
		$r = json_decode($r, true);
		$cityCode = $r['result']['cityCode'];
		if (preg_match("/^[0-9]+$/", $cityCode) == false) {echo '无法反向编译地址5'; return;};
		
		// 正式查询
		$url = $this->_bd_api_url.'/place/v2/search?&query='.urlencode($_GET['query']).'&region='.$cityCode.'&scope=2&page_size=10&page_num='.($page-1).'&output=json&ak='.$this->_bd_lbs_key;
		
		import('ORG.Net.Curl');
		$r = Curl::get($url, 10);
		$r = json_decode($r['result'], true);
		if ($_GET['output'] == 0) {
			echo json_encode($r);
			return;
		};
		$gps = D('Gps');
		for ($i=0; $i<count($r['results']); $i++) {
			$coodr = $gps->gps2baidu($r['results'][$i]['lng'], $r['results'][$i]['lat']);
			if ($_GET['output'] == 2) $coodr = $gps->baidu2google($coodr['longitude'], $coodr['latitude']);
			$r['results'][$i]['lng'] = $coodr['longitude'];
			$r['results'][$i]['lat'] = $coodr['latitude'];
		};
		echo json_encode($r);
	}
	
	/**
	 * 根据 ip 获取所在城市详细信息
	 * 不指定ip表示获取当前操作者所在的位置
	 * http://main.andaijia.com/place/ip2city
{
    "address": "CN|上海|上海|None|OTHER|1|None",
    "content": {
        "address": "上海市",
        "address_detail": {
            "city": "上海市",
            "city_code": 289,
            "district": "",
            "province": "上海市",
            "street": "",
            "street_number": ""
        },
        "point": {
            "x": "121.48789949",
            "y": "31.24916171"
        }
    },
    "status": 0
}
	 */
	public function ip2city() {
		$ip = isset($_GET['ip']) ? $_GET['ip'] : '';
		$url = $this->_bd_api_url.'/location/ip?ip='.$ip.'&coor=bd09ll&ak='.$this->_bd_lbs_key;
		import('ORG.Net.Curl');
		$r = Curl::get($url, 10);
		echo $r['result'];
	}
}