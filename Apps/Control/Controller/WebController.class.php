<?php
namespace Control\Controller;

use Think\Controller;

/**
 * Web 类
 * 主要用于显示控制台登录首页
 * 
 * @category Control
 * @package Control
 * @author guanxuejun <guanxuejun@gmail.com>
 * @copyright http://www.f-fusion.com/ <http://www.f-fusion.com/>
 *
 */
class WebController extends BaseController {
	function __construct() {
		parent::__construct();
    	if (!$this->checkSession()) {
    		$this->error('抱歉，登录超时！请重新登录！', '/');
    		exit;
    	};
    	$this->assign('current', 'web');
	}
	
    function index(){
		$this->display();
    }
    
    function project() {
    	$project = $this->getModel('Project');
    	if (IS_POST && isset($_GET['save'])) {
    		$id = I('post.id', 0, 'int');
    		$name = I('post.name');
    		$subname = I('post.subname');
    		if (get_magic_quotes_gpc()) {
    			$content = stripslashes($_POST['content']); // 处理在线编辑器的转义
    		} else {
    			$content = $_POST['content'];
    		};
    		$imagePath = '/public/kindeditor4110/attached/';
    		$imageDir = realpath(dirname('.')).$imagePath;
    		if (!file_exists($imageDir)) mkdir($imageDir, 0777, true);
    		if ($id == 0) {
    			// add
    			if (isset($_FILES['main']) && $_FILES['main']['error'] == 0) {
    				$file = $_FILES['main'];
    				$fileName = $file['name'];
    				$fileTmpName = $file['tmp_name'];
    				$fileExt = substr($fileName, strrpos($fileName, '.')+1);
    				$imageName1 = md5(time()).'.'.$fileExt;
    				$imageFile1 = $imageDir.$imageName1;
    				if (file_exists($imageFile1)) unlink($imageFile1);
    				move_uploaded_file($fileTmpName, $imageFile1);
    			} else {
    				$this->error('请上传首页用题图');
    				return;
    			};
    			if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    				$file = $_FILES['logo'];
    				$fileName = $file['name'];
    				$fileTmpName = $file['tmp_name'];
    				$fileExt = substr($fileName, strrpos($fileName, '.')+1);
    				$imageName2 = md5(time()+1).'.'.$fileExt;
    				$imageFile2 = $imageDir.$imageName2;
    				if (file_exists($imageFile2)) unlink($imageFile2);
    				move_uploaded_file($fileTmpName, $imageFile2);
    			} else {
    				$this->error('请上传详情页Logo');
    				return;
    			};
    			$create = $project->add(array(
    				'name'      => $name,
    				'subname'   => $subname,
    				'content'   => $content,
    				'main'      => $imagePath.$imageName1,
    				'logo'      => $imagePath.$imageName2,
    				'main_path' => $imageFile1,
    				'logo_path' => $imageFile2,
    				'create_time'=> $this->date,
    			));
    			if ($create) {
    				$this->success('案例保存成功', '/web/project');
    				return;
    			};
    			$this->error('案例保存失败'.$project->getDbError());
    			return;
    		} else {
    			// edit
    			$rs = $project->find($id);
    			if ($rs == null) {
    				$this->error('要修改的案例不存在');
    				return;
    			};
    			$params = array(
    				'name' => $name,
    				'subname' => $subname,
    				'content' => $content,
    			);
    			if (isset($_FILES['main']) && $_FILES['main']['error'] == 0) {
    				$file = $_FILES['main'];
    				$fileName = $file['name'];
    				$fileTmpName = $file['tmp_name'];
    				$fileExt = substr($fileName, strrpos($fileName, '.')+1);
    				$imageName1 = md5(time()).'.'.$fileExt;
    				$imageFile1 = $imageDir.$imageName1;
    				if (file_exists($imageFile1)) unlink($imageFile1);
    				$f = move_uploaded_file($fileTmpName, $imageFile1);
    				if ($f) {
    					unlink($rs['main_path']);
    					$params['main'] = $imagePath.$imageName1;
    					$params['main_path'] = $imageFile1;
    				};
    			};
    			if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    				$file = $_FILES['logo'];
    				$fileName = $file['name'];
    				$fileTmpName = $file['tmp_name'];
    				$fileExt = substr($fileName, strrpos($fileName, '.')+1);
    				$imageName2 = md5(time()+1).'.'.$fileExt;
    				$imageFile2 = $imageDir.$imageName2;
    				if (file_exists($imageFile2)) unlink($imageFile2);
    				$f = move_uploaded_file($fileTmpName, $imageFile2);
    				if ($f) {
    					unlink($rs['logo_path']);
    					$params['logo'] = $imagePath.$imageName2;
    					$params['logo_path'] = $imageFile1;
    				};
    			};
    			$update = $project->save($params, array('where'=>'id='.$id));
    			if ($update) {
    				$this->success('案例更新成功', '/web/project');
    				return;
    			};
    			$this->error('案例更新失败'.$project->getDbError());
    			return;
    		};
    	};
    	$rs = $project->order('id DESC')->select();
    	$this->assign('count', count($rs));
    	$this->assign('list', $rs);
    	$id = I('get.id', 0, 'int');
    	if ($id > 0) {
    		// 提取信息准备修改
    		$rs = $project->find($id);
    		if ($rs == null) {} else {
    			$rs['content'] = htmlspecialchars($rs['content']); // 处理在线编辑器的转义
    			$this->assign('info', $rs);
    		};
    	};
		$this->display();
    }
}