<?php
namespace Mobile\Controller;

use Think\Controller;
use Org\Net\Curl;
use Common\Controller\BaseController;

class TestController extends BaseController {
	/**
	 * http://127.0.0.1:114/test
	 */
	function index() {
		$result = Curl::get('http://www.baidu.com', 13);
		dump($result);
	}
}