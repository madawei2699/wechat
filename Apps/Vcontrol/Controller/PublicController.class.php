<?php
namespace Vcontrol\Controller;

use Think\Controller;
use Org\Util\Image;
use Common\Controller\BaseController;

/**
 * 公共页面处理类
 * 此类主要用于显示登录页面的验证码图片用
 *
 * @category Vcontrol
 * @package Vcontrol
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://vc.f-fusion.com/ <http://vc.f-fusion.com/>
 */
class PublicController extends BaseController {
	/**
	 * 显示登录页面的验证码图片
	 */
	function verify(){
		Image::buildImageVerify(4, 1, 'png', 48, 22, 'VCONTROL_VRERIFY');
	}
}