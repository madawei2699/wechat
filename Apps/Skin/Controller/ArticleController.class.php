<?php
namespace Skin\Controller;

use Think\Controller;
use Org\Util\Page;

/**
 * 文章处理类
 *
 * @category Skin
 * @package Skin
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.ximin.cn/ <http://www.ximin.cn/>
 */
class ArticleController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$ch = I('get.ch', 0, 'int');
		if ($ch == 0) return;
		switch ($ch) {
			case 101:
				$chName = '生命安全';
				break;
			case 102:
				$chName = '环境监测';
				break;
			case 103:
				$chName = '备品备件';
				break;
			case 104:
				$chName = '维护维修';
				break;
			case 201:
				$chName = 'PDF资料';
				break;
			case 202:
				$chName = '服务网点';
				break;
			case 203:
				$chName = '指导培训';
				break;
			case 301:
				$chName = '最新动态';
				break;
			case 401:
				$chName = '招聘信息';
				break;
			default:
				return;
		};
		$params = '&ch='.$ch;
		$model = $this->getModel('Article');
		$count = $model->where(array('ch'=>$ch,'state'=>1))->count();
		$page = new Page($count, 10, $params);
		$list = $model->field('id,create_time,title')->where(array('ch'=>$ch,'state'=>1))->limit($page->firstRow, $page->listRows)->order('id DESC')->select();
		$this->assign('ch', $ch);
		$this->assign('ch_name', $chName);
		$this->assign('page', $page->show());
		$this->assign('list', $list);
		$this->display();
	}
	
	function save() {
		$id = I('post.id', 0, 'int');
		$ch = I('post.ch', 0, 'int');
		if ($ch == 0) return;
		$model = $this->getModel('Article');
		if ($id == 0) {
			$p = $model->add(array(
				'ch'          => $ch,
				'title'       => I('post.title'),
				'content'     => I('post.content', '', false),
				'create_time' => $this->date,
			));
		} else {
			$p = $model->save(array(
				'title'       => I('post.title'),
				'content'     => I('post.content', '', false),
			), array('where'=>'id='.$id));
		};
		redirect('/article?ch='.$ch);
	}
	
	function delete() {
		$id = I('get.id', 0, 'int');
		$ch = I('get.ch', 0, 'int');
		if ($ch == 0) return;
		$model = $this->getModel('Article');
		$p = $model->save(array('state' => 0), array('where'=>'id='.$id));
		redirect('/article?ch='.$ch);
	}
	
	function data() {
		$id = I('get.id', 0, 'int');
		$ch = I('get.ch', 0, 'int');
		if ($ch == 0) return;
		$model = $this->getModel('Article');
		$rs = $model->where(array('ch'=>$ch))->find($id);
		echo json_encode($rs);
	}
}