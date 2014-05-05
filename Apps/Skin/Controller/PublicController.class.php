<?php
namespace Skin\Controller;

use Think\Controller;
use Org\Util\Image;

/**
 * 公共页面处理类
 * 此类主要用于显示登录页面的验证码图片用
 *
 * @category Skin
 * @package Skin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 */
class PublicController extends BaseController {
	/**
	 * 显示登录页面的验证码图片
	 */
	function verify(){
		Image::buildImageVerify(4, 1, 'png', 48, 22, 'ADMIN_VRERIFY');
	}
}