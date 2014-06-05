<?php
namespace Ciiq\Controller;

use Think\Controller;

class WechatController extends BaseController {
	function index() {
		echo 'index';
	}
	function paycallback() {
		echo 'paycallback';
	}
	function weiquan() {
		echo 'weiquan';
	}
	function warning() {
		echo 'warning';
	}
}