<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/9
 * Time: 1:32
 */

namespace app\admin\controller;
use app\common\lib\AliSms;
use think\Request;

class Extend extends Base
{
    public function index(){

        return $this->fetch();
    }

    public function email(){

        return $this->fetch();
    }
    /**
     * 支付宝支付
     * @return mixed
     */
    public function aliPay(){

        return $this->fetch();
    }

    /**
     * 微信支付
     * @return mixed
     */
    public function wePay(){

        return $this->fetch();
    }

    /**
     * 生成二维码
     * @return mixed
     */
    public function qrcode(){
        if(Request::instance()->isPost()){
            $data = Request::instance()->post('data');

            vendor('phpqrcode.phpqrcode');
            //生成二维码图片
            $object = new \QRcode();
            $level=3;
            $size=10;
            $errorCorrectionLevel =intval($level) ;//容错级别
            $matrixPointSize = intval($size);//生成图片大小
            $res =  $object->png($data, UPLOAD_PATH, $errorCorrectionLevel, $matrixPointSize,2);
            return $res;

        }
        $this->assign('data', '/static/oenui/img/favicons/favicon-192x192.png');
        return $this->fetch();
    }

    /**
     * 短信
     */
    public function sendSms(){

        return $this->fetch();
    }
    /***
     * 阿里云短信
     */
    public function aliSms($phoneNum){

        $phoneNumber    = $phoneNum;
        $signName       = config('aliyun.signName');
        $templateCode   = config('aliyun.templateCode');
        $code = getRandChar(6,'NUMBER');//生成验证码

        $templateParam = [
            'code'  => $code
        ];

        $ali = new AliSms();
        //短信模板
        $res = $ali->sendSms(
            $signName,                  //短信签名
            $templateCode ,             //模板代码
            $phoneNumber,               //手机号
            $templateParam,             //模板参数
            $outId = null,              //流水ID
            $smsUpExtendCode = null     //上行短信扩展码
        );
        return $res->Message;
    }


}