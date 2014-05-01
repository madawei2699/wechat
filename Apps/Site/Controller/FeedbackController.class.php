<?php
namespace Site\Controller;

use Think\Controller;

/**
 * Feedback 类
 * http://127.0.0.1:122
 * http://x.f-fusion.com
 * 
 * @category Site
 * @package Site
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 *
 */
class FeedbackController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	if (IS_POST) {
    		$enterprise = I('post.enterprise', '', 'strip_tags');
    		$name = I('post.name', '', 'strip_tags');
    		$mobile = substr(I('post.mobile', '', 'strip_tags'), 0, 11);
    		$email = I('post.email', '', 'strip_tags');
    		$tel = I('post.tel', '', 'strip_tags');
    		$fax = I('post.fax', '', 'strip_tags');
    		$content = I('post.content', '', 'strip_tags');
    		if ($content == '') $this->error('请填写反馈内容');
    		$ip = get_client_ip();
    		
	    	$model = $this->getModel('Feedback');
	    	$p = $model->add(array(
	    		'create_time'=> $this->date,
	    		'enterprise' => $enterprise,
	    		'name' => $name,
	    		'mobile' => $mobile,
	    		'email' => $email,
	    		'tel' => $tel,
	    		'fax' => $fax,
	    		'content' => $content,
	    		'ip' => $ip,
	    	));
	    	if ($p) {
	    		$this->success('意见反馈提交成功！谢谢！', '/');
	    		return;
	    	};
	    	$this->error('反馈失败！');
	    	return;
    	};
		$this->display('index/feedback');
    }
}