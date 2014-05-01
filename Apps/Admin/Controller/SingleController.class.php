<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 单页处理类
 *
 * @category Admin
 * @package Admin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 */
class SingleController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$id = I('get.id', 0, 'int');
		if ($id == 0) return;
		switch ($id) {
			case 1:
				$chName = '公司简介';
				break;
			case 2:
				$chName = '公司理念';
				break;
			case 3:
				$chName = '公司荣誉';
				break;
			case 4:
				$chName = '联系我们';
				break;
			default:
				return;
		};
		$model = $this->getModel('Article');
		$info = $model->field('id,create_time,title,content')->where(array('ch'=>0, 'state'=>1))->find($id);
		$this->assign('ch_name', $chName);
		$this->assign('info', $info);
		$this->display();
	}
	
	function save() {
		$id = I('post.id', 0, 'int');
		if ($id == 0) return;
		$model = $this->getModel('Article');
		if ($id == 0) {
			$p = $model->add(array(
				'ch'          => 0,
				'title'       => I('post.title'),
				'content'     => I('post.content', '', false),
				'create_time' => $this->date,
			));
		} else {
			$p = $model->save(array(
				'ch'          => 0,
				'title'       => I('post.title'),
				'content'     => I('post.content', '', false),
			), array('where'=>'id='.$id));
		};
		redirect('/single?id='.$id);
	}
}