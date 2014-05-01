<?php
namespace Web\Controller;

use Think\Controller;
use Common\Controller\BaseController;

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
    	// 案例
    	$projects = array(
    		array('id'=>1,'img'=>'images/project/ciiq/ciiq.jpg','href'=>'#', 'title'=>'推动安防产业升级，颠覆对保险箱行业的定义！安防艺术大师横空出世！','summary'=>'<p>传统的行业，传统的渠道，传统的认识，保险箱行业的发展一直都被固有营销模式所禁锢着，与家居生活的发展有着巨大的落差！驰球是如何通过品牌变革重新赢得发展契机的？</p>', 'title2'=>'驰球保险箱的转型之路'),
    		array('id'=>2,'img'=>'images/project/maomaozhu/maomaozhu.jpg','href'=>'#', 'title'=>'一个服装设计师对孩子的爱，但当与市场发生冲撞的时候，如何把握这个火花呢？','summary'=>'<p>挑战：当所有人都在模仿抄袭的时候，当大多数人追赶韩流的时候，市场对这样的产品不知道如何接纳，是不是不够流行？是不是不适合我的孩子？</p>', 'title2'=>'毛毛猪童装'),
    		array('id'=>3,'img'=>'images/project/p73.png','href'=>'#', 'title'=>'欧琳厨房2010年国庆促销活动','summary'=>'<p>欧琳厨房2010年国庆促销活动</p>', 'title2'=>'厨房收藏概念'),
    		array('id'=>4,'img'=>'images/project/p72.png','href'=>'#', 'title'=>'欧琳厨房','summary'=>'', 'title2'=>'厨房收藏概念'),
    		array('id'=>5,'img'=>'images/project/p71.png','href'=>'#', 'title'=>'欧琳厨房','summary'=>'', 'title2'=>''),
    		array('id'=>6,'img'=>'images/project/p70.png','href'=>'#', 'title'=>'厨房也有收藏价值','summary'=>'<p>业界第一个提出厨房收藏概念的橱柜品牌，世博限量版倾情打造。专属你的世博限量厨房生活，典藏生活，给您极致尊重！</p>', 'title2'=>''),
    		array('id'=>7,'img'=>'images/project/p69.png','href'=>'#', 'title'=>'我的世博，我的典藏','summary'=>'', 'title2'=>''),
    		array('id'=>8,'img'=>'images/project/p68.png','href'=>'#', 'title'=>'前进橡胶门店效果','summary'=>'', 'title2'=>''),
    		array('id'=>9,'img'=>'images/project/p67.png','href'=>'#', 'title'=>'前进橡胶门店效果','summary'=>'', 'title2'=>''),
    		array('id'=>10,'img'=>'images/project/p66.png','href'=>'#', 'title'=>'前进橡胶手册效果','summary'=>'', 'title2'=>''),
    		array('id'=>11,'img'=>'images/project/p65.png','href'=>'#', 'title'=>'前进橡胶产品包装效果','summary'=>'', 'title2'=>''),
    		array('id'=>12,'img'=>'images/project/p64.png','href'=>'#', 'title'=>'前进橡胶产品包装效果','summary'=>'', 'title2'=>''),
    		array('id'=>13,'img'=>'images/project/p63.png','href'=>'#', 'title'=>'百年开创与梦想','summary'=>'', 'title2'=>''),
    		array('id'=>14,'img'=>'images/project/p62.png','href'=>'#', 'title'=>'前进橡胶产品LOGO效果','summary'=>'', 'title2'=>''),
    		array('id'=>15,'img'=>'images/project/p61.png','href'=>'#', 'title'=>'前进橡胶产品LOGO效果','summary'=>'', 'title2'=>''),
    		array('id'=>16,'img'=>'images/project/p60.png','href'=>'#', 'title'=>'前进橡胶产品LOGO效果','summary'=>'', 'title2'=>''),
    		array('id'=>17,'img'=>'images/project/p59.png','href'=>'#', 'title'=>'上海红心店中店','summary'=>'', 'title2'=>''),
    		array('id'=>18,'img'=>'images/project/p58.png','href'=>'#', 'title'=>'上海红心新终端形象-挂烫机岛柜','summary'=>'', 'title2'=>''),
    		array('id'=>19,'img'=>'images/project/p57.png','href'=>'#', 'title'=>'上海红心广告','summary'=>'', 'title2'=>''),
    		array('id'=>20,'img'=>'images/project/p56.png','href'=>'#', 'title'=>'上海红心广告','summary'=>'', 'title2'=>''),
    		array('id'=>21,'img'=>'images/project/p55.png','href'=>'#', 'title'=>'上海红心广告','summary'=>'', 'title2'=>''),
    		array('id'=>22,'img'=>'images/project/p54.png','href'=>'#', 'title'=>'上海红心广告','summary'=>'', 'title2'=>''),
    		array('id'=>23,'img'=>'images/project/p53.png','href'=>'#', 'title'=>'心心相传50年','summary'=>'', 'title2'=>''),
    		array('id'=>24,'img'=>'images/project/p52.png','href'=>'#', 'title'=>'50载承诺与关爱','summary'=>'<p>50年<br/>是一种品牌的传承！<br/>传承的是红心品牌50年不变的品质承诺；<br/>传承的是红心50年来对消费者的关怀；<br/>传承的是中国几代女性的选择。</p>', 'title2'=>''),
    		array('id'=>25,'img'=>'images/project/p51.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>26,'img'=>'images/project/p50.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>27,'img'=>'images/project/p49.png','href'=>'#', 'title'=>'凯邦厨卫-店中店效果','summary'=>'', 'title2'=>''),
    		array('id'=>28,'img'=>'images/project/p48.png','href'=>'#', 'title'=>'凯邦厨卫-店中店效果','summary'=>'', 'title2'=>''),
    		array('id'=>29,'img'=>'images/project/p47.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>30,'img'=>'images/project/p46.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>31,'img'=>'images/project/p45.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>32,'img'=>'images/project/p44.png','href'=>'#', 'title'=>'凯邦厨卫','summary'=>'', 'title2'=>''),
    		array('id'=>33,'img'=>'images/project/p43.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>34,'img'=>'images/project/p42.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>35,'img'=>'images/project/p41.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>36,'img'=>'images/project/p40.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>37,'img'=>'images/project/p39.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>38,'img'=>'images/project/p38.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>39,'img'=>'images/project/p37.png','href'=>'#', 'title'=>'迪迪鹿','summary'=>'', 'title2'=>''),
    		array('id'=>40,'img'=>'images/project/p36.png','href'=>'#', 'title'=>'万福隆','summary'=>'', 'title2'=>''),
    		array('id'=>41,'img'=>'images/project/p35.png','href'=>'#', 'title'=>'万福隆','summary'=>'', 'title2'=>''),
    		array('id'=>42,'img'=>'images/project/p34.png','href'=>'#', 'title'=>'万福隆','summary'=>'', 'title2'=>''),
    		array('id'=>43,'img'=>'images/project/p33.png','href'=>'#', 'title'=>'万福隆','summary'=>'', 'title2'=>''),
    		array('id'=>44,'img'=>'images/project/p32.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>45,'img'=>'images/project/p31.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>46,'img'=>'images/project/p30.png','href'=>'#', 'title'=>'润酿','summary'=>'<p>润酿，在发展生产和保护环境中独树一帜，用以诚为本与守护自然的心来将品牌之路照亮。润酿为消费者生产的是至真至纯的原蜜，滋润心田的同时酿造甜蜜生活。</p>', 'title2'=>''),
    		array('id'=>47,'img'=>'images/project/p29.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>48,'img'=>'images/project/p28.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>49,'img'=>'images/project/p27.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>50,'img'=>'images/project/p26.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>51,'img'=>'images/project/p25.png','href'=>'#', 'title'=>'润酿','summary'=>'', 'title2'=>''),
    		array('id'=>52,'img'=>'images/project/p24.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>53,'img'=>'images/project/p23.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>54,'img'=>'images/project/p22.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>55,'img'=>'images/project/p21.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>56,'img'=>'images/project/p20.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>57,'img'=>'images/project/p19.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>58,'img'=>'images/project/p18.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>59,'img'=>'images/project/p17.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>60,'img'=>'images/project/p16.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>61,'img'=>'images/project/p15.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>62,'img'=>'images/project/p14.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>63,'img'=>'images/project/p13.png','href'=>'#', 'title'=>'大阿嫂','summary'=>'', 'title2'=>''),
    		array('id'=>64,'img'=>'images/project/p12.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>65,'img'=>'images/project/p11.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>66,'img'=>'images/project/p10.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>67,'img'=>'images/project/p08.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>68,'img'=>'images/project/p07.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>69,'img'=>'images/project/p06.png','href'=>'#', 'title'=>'PusaPusus','summary'=>'', 'title2'=>''),
    		array('id'=>70,'img'=>'images/project/p05.png','href'=>'#', 'title'=>'电脑医生2.0','summary'=>'', 'title2'=>''),
    		array('id'=>71,'img'=>'images/project/p04.png','href'=>'#', 'title'=>'电脑医生2.0','summary'=>'', 'title2'=>''),
    		array('id'=>72,'img'=>'images/project/p03.png','href'=>'#', 'title'=>'电脑医生2.0','summary'=>'', 'title2'=>''),
    		array('id'=>73,'img'=>'images/project/p02.png','href'=>'#', 'title'=>'联通有限网络、无限惊喜','summary'=>'', 'title2'=>''),
    		array('id'=>74,'img'=>'images/project/p01.png','href'=>'#', 'title'=>'包省王 - 胜者为王','summary'=>'', 'title2'=>''),
    	);
    	$this->assign('projects', $projects);
    	$this->assign('WEB_EXT_CFG', C('WEB_EXT_CFG'));
		$this->display();
    }
}