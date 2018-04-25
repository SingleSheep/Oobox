<?php
namespace app\index\controller;
use app\common\lib\Aes;
use app\common\model\User;
use think\captcha\Captcha;
use think\Request;

class Index extends Base
{
    public function index(){
//        $M = new Aes();
//        $a = $M->encrypt('123456');
//        echo $a;
//        echo "<br/>";
//        $b = $M->decrypt($a);
//        echo $b;
        return $this->fetch('');
    }
    public function getUserInfo(){
        $param = Request::instance()->param();;
        $user = (new User())->where(['username'=>$param['name']])->find();
        //dump($user);
        $this->assign('userData',$user);
        $this->assign('items','');
        return $this->fetch('user');
    }

    public function sendEmail($id = 1){
        $userData = User::get(['uid'=>$id]);
        $m = new Active();
        $res = $m->sendActiveCode($userData);
        echo $res;
    }
    /**
     * 生成验证码
     * @return \think\Response
     */
    public function verifyCode(){
        $config = config('verify');//配置
        $captcha =  new Captcha($config);
        return $captcha->entry();
    }

}
