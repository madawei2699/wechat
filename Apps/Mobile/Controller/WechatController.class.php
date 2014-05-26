<?php
namespace Mobile\Controller;

use Think\Controller;
use Org\Net\Curl;
use Org\Util\QRcode;
use Common\Controller\BaseController;

/**
 * 微信推广页面
 * @author guanxuejun
 *
 */
class WechatController extends BaseController {
    function index(){
		$this->display();
    }
    
    /**
     * OK 大电商 - 1、微电商
     * http://127.0.0.1:114/wechat/weidianshang
     * http://m.f-fusion.com/wechat/weidianshang
     */
    function weidianshang() {
    	$this->display();
    }
    
    /**
     * OK 大电商 - 2、独立商城
     * http://127.0.0.1:114/wechat/dulishangcheng
     * http://m.f-fusion.com/wechat/dulishangcheng
     */
    function dulishangcheng() {
    	$this->display();
    }
    
    /**
     * OK 大电商 - 3、进销存管理
     * http://127.0.0.1:114/wechat/jinxiaocun
     * http://m.f-fusion.com/wechat/jinxiaocun
     */
    function jinxiaocun() {
    	$this->display();
    }
    
    /**
     * OK 整合营销 - 1、品牌整合
     * http://127.0.0.1:114/wechat/pinpaizhenghe
     * http://m.f-fusion.com/wechat/pinpaizhenghe
     */
    function pinpaizhenghe() {
    	$this->display();
    }
    
    /**
     * OK 整合营销 - 2、渠道整合
     * http://127.0.0.1:114/wechat/qudaozhenghe
     * http://m.f-fusion.com/wechat/qudaozhenghe
     */
    function qudaozhenghe() {
    	$this->display();
    }
    
    /**
     * IGNORE 整合营销 - 3、数据整合
     * http://127.0.0.1:114/wechat/shujuzhenghe
     * http://m.f-fusion.com/wechat/
     */
    function shujuzhenghe() {
    	$this->display();
    }
    
    /**
     * OK 关注熔意 - 1、进化论
     * http://127.0.0.1:114/wechat/jinhualun
     * http://m.f-fusion.com/wechat/jinhualun
     */
    function jinhualun() {
    	$this->display();
    }
    
    /**
     * OK 关注熔意 - 2、合作共赢
     * http://127.0.0.1:114/wechat/hezuo
     * http://m.f-fusion.com/wechat/hezuo
     */
    function hezuo() {
    	$this->display();
    }
    
    /**
     * IGNORE 关注熔意 - 3、精选推送
     * http://127.0.0.1:114/wechat/push
     * http://m.f-fusion.com/wechat/
     */
    function push() {
    	$this->display();
    }
    
    /**
     * OK 关注熔意 - 4、联系我们
     * http://127.0.0.1:114/wechat/contact
     * http://m.f-fusion.com/wechat/contact
     */
    function contact() {
    	$this->display();
    }
    
    /**
     * OK QRCode
     * http://127.0.0.1:114/wechat/guanxuejun
     * http://m.f-fusion.com/wechat/guanxuejun
     */
    function guanxuejun(){
    	$text = 'BEGIN:VCARD
VERSION:3.0
FN:管雪军
ORG:上海熔意网络科技有限公司
TITLE:技术总监 Technical Director
ADR;WORK:上海市闵行区梅陇西路413号310-313室(200237)
URL:http://it.f-fusion.com
TEL;CELL:13916615867
TEL;WORK;VOICE:021-34096793
TEL;WORK;FAX:021-34096792
EMAIL:guanxuejun@f-fusion.com
NOTE:微信开发,行业电商,在线支付,CRM,MIS,ERP
END:VCARD';
    	QRcode::png($text, false, 2, 8);
    }
    
    /**
     * OK QRCode
     * http://127.0.0.1:114/wechat/zhangwanjun
     * http://m.f-fusion.com/wechat/zhangwanjun
     */
    function zhangwanjun(){
    	$text = 'BEGIN:VCARD
VERSION:3.0
FN:张万军
ORG:上海熔意网络科技有限公司
TITLE:客户总监 Account Director
ADR;WORK:上海市闵行区梅陇西路413号310-313室(200237)
URL:http://it.f-fusion.com
TEL;CELL:13701943000
TEL;WORK;VOICE:021-34096793
TEL;WORK;FAX:021-34096792
EMAIL:zhangwanjun@f-fusion.com
NOTE:微信开发,行业电商,在线支付,CRM,MIS,ERP
END:VCARD';
    	QRcode::png($text, false, 2, 8);
    }
    
    /**
     * OK QRCode
     * http://127.0.0.1:114/wechat/zhangzhenyu
     * http://m.f-fusion.com/wechat/zhangzhenyu
     */
    function zhangzhenyu(){
    	$text = 'BEGIN:VCARD
VERSION:3.0
FN:张振宇
ORG:上海熔意网络科技有限公司
TITLE:执行总监 Executive Director
ADR;WORK:上海市闵行区梅陇西路413号310-313室(200237)
URL:http://it.f-fusion.com
TEL;CELL:18602118629
TEL;WORK;VOICE:021-34096793
TEL;WORK;FAX:021-34096792
EMAIL:zhangzhenyu@f-fusion.com
NOTE:微信开发,行业电商,在线支付,CRM,MIS,ERP
END:VCARD';
    	QRcode::png($text, false, 2, 8);
    }
}