<?php
namespace Demo\Controller;

use Think\Controller;
use Org\Util\Image;

/**
 * 公共页面处理类
 * 此类主要用于显示登录页面的验证码图片用
 *
 * @category Demo
 * @package Demo
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 */
class PublicController extends BaseController {
	/**
	 * 显示登录页面的验证码图片
	 */
	function verify(){
		Image::buildImageVerify(6, 1, 'png', 48, 22, 'CONTROL_VRERIFY');
	}
}