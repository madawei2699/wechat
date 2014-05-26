<?php
namespace Web\Controller;

use Think\Controller;

/**
 * 网站页面控制器类
 * @author guanxuejun
 *
 */
class IndexController extends BaseController {
	function __construct() {
		parent::__construct();
	}
	
    function index(){
    	// 判断不同的主机头，显示不同的网站
    	$key = (string)strtolower($_SERVER['HTTP_HOST']);
    	if ($key == 'it.f-fusion.com') {
    		redirect('/it');
    		return;
    	};
    	// 案例
    	$projects = array(
    		array('id'=>14,'img'=>'images/project/cityvision/index.jpg','href'=>'#', 'title'=>'不是低档地摊眼镜而是快速时尚饰品','summary'=>'<p>CV代表生活时尚，自由轻松；在时尚的背后，是CV永远创新、与众不同的信念。</p>', 'title2'=>'CityVision'),
    		array('id'=>13,'img'=>'images/project/francispal/index.jpg','href'=>'#', 'title'=>'FRANCEPAL　一个世纪的浮华浪漫梦想','summary'=>'<p>Francispal，追寻那段令人无限神往的华贵生活，于历史的氤氲气息中蔓延出浪漫欧式情怀，以现代时尚来诠释，再现法国巴黎的浮华梦。</p>', 'title2'=>'Francispal'),
    		array('id'=>10,'img'=>'images/project/didilu/index.jpg','href'=>'#', 'title'=>'孩子的梦想！世界的希望！','summary'=>'<p>超越家长的期望，自然与健康不仅体现在衣服本身，更重要的是对儿童精神层面的理解与支持。</p>', 'title2'=>'如何定义迪迪鹿品牌文化'),
    		array('id'=>11,'img'=>'images/project/fapai/index.jpg','href'=>'#', 'title'=>'从本土男装先驱到国际时尚名品的转变','summary'=>'<p>法派不仅仅是有代言人，而是所有上流有成就的名人的集合</p>', 'title2'=>'法派'),
    		array('id'=>12,'img'=>'images/project/bailide/index.jpg','href'=>'#', 'title'=>'活出自我','summary'=>'<p>新生代的“新势力”</p>', 'title2'=>'拜丽德'),
    		array('id'=>1,'img'=>'images/project/ciiq/ciiq.jpg','href'=>'#', 'title'=>'推动安防产业升级，颠覆对保险箱行业的定义！安防艺术大师横空出世！','summary'=>'<p>传统的行业，传统的渠道，传统的认识，保险箱行业的发展一直都被固有营销模式所禁锢着，与家居生活的发展有着巨大的落差！驰球是如何通过品牌变革重新赢得发展契机的？</p>', 'title2'=>'驰球保险箱的转型之路'),
    		array('id'=>2,'img'=>'images/project/maomaozhu/maomaozhu.jpg','href'=>'#', 'title'=>'一个服装设计师对孩子的爱，但当与市场发生冲撞的时候，如何把握这个火花呢？','summary'=>'<p>挑战：当所有人都在模仿抄袭的时候，当大多数人追赶韩流的时候，市场对这样的产品不知道如何接纳，是不是不够流行？是不是不适合我的孩子？</p>', 'title2'=>'毛毛猪童装'),
    		array('id'=>3,'img'=>'images/project/janus/001.png','href'=>'#', 'title'=>'重新挖掘“豪门”文化，开创门业颠覆之作！','summary'=>'<p>挑战：当亚斯王决定进入高端门业的时候，只有高端的产品是远远不够的，要怎样才能突显自己的价值，并赢得高端消费者的心？</p>', 'title2'=>'亚斯王防盗门的华丽转型！'),
    		array('id'=>4,'img'=>'images/project/hongxin/p53.png','href'=>'#', 'title'=>'家电老品牌的品牌升级！价值的传递，情感的流动！','summary'=>'<p>挑战：红心电器如何挖掘品牌传统？如何俘获新一代人的心？</p>', 'title2'=>'上海红心电器如何解决市场老化的冲突！'),
    		array('id'=>5,'img'=>'images/project/runniang/logo.png','href'=>'#', 'title'=>'高端蜂蜜品牌的深度挖掘！','summary'=>'<p>挑战：消费如何才能接受高品质但价格高的蜂蜜呢？</p>', 'title2'=>'品质的坚守，顶级的奢享！'),
    		array('id'=>6,'img'=>'images/project/jinfang/logo1.png','href'=>'#', 'title'=>'金房','summary'=>'<p>金房</p>', 'title2'=>''),
    		array('id'=>7,'img'=>'images/project/p64.png','href'=>'#', 'title'=>'大众','summary'=>'<p>大众</p>', 'title2'=>''),
    		array('id'=>8,'img'=>'images/project/zhongyi/logo1.png','href'=>'#', 'title'=>'中意','summary'=>'<p>中意</p>', 'title2'=>''),
    		array('id'=>9,'img'=>'images/project/lidelang/logo1.png','href'=>'#', 'title'=>'朝阳产业里的朝阳品牌建设！','summary'=>'<p>挑战：如何摆脱同质化的竞争格局？如何打造有价值观的LED？</p>', 'title2'=>'LED发展的品牌化运营！'),
    	);
    	$this->assign('projects', $projects);
    	$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
		$this->display();
    }
}