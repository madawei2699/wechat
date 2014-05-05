<?php
namespace Skin\Controller;

use Think\Controller;
use Org\Util\Page;

/**
 * 会员处理类
 *
 * @category Skin
 * @package Skin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 */
class MemberController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$model = $this->getModel('Member');
		$count = $model->where(array('state'=>1))->count();
		$page = new Page($count, 10);
		$list = $model->field('id,create_time,title')->where(array('state'=>1))->limit($page->firstRow, $page->listRows)->order('id DESC')->select();
		$this->assign('page', $page->show());
		$this->assign('list', $list);
		$this->display();
	}
	
	function save() {
		$id = I('post.id', 0, 'int');
		$model = $this->getModel('Member');
		if ($id == 0) {
			$p = $model->add(array(
				'title'       => I('post.title'),
				'content'     => I('post.content'),
				'create_time' => $this->date,
			));
		} else {
			$p = $model->save(array(
				'title'       => I('post.title'),
				'content'     => I('post.content'),
			), array('where'=>'id='.$id));
		};
		redirect('/member');
	}
	
	function delete() {
		$id = I('get.id', 0, 'int');
		$model = $this->getModel('Member');
		$p = $model->save(array('state' => 0), array('where'=>'id='.$id));
		redirect('/member');
	}
	
	function data() {
		$id = I('get.id', 0, 'int');
		$ch = I('get.ch', 0, 'int');
		if ($ch == 0) return;
		$model = $this->getModel('Member');
		$rs = $model->find($id);
		echo json_encode($rs);
	}
}