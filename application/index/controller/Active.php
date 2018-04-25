<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2018/1/1
 * Time: 21:14
 */

namespace app\index\controller;

use app\common\service\UserService;
use app\common\model\User;
use think\Request;

/**
 * 用户激活相关
 * Class Active
 * @package app\user\controller
 */
class Active extends Base
{

    public function index(){

        return $this->fetch();
    }
    public function actionUser(){
        //halt(Request::instance()->param());
        $this->assign('errorMsg','邮箱验证后，即可完成注册！');
        return $this->fetch('entry/verify');
    }
    /**
     * 发送激活邮件
     * @param $userInfo
     * @return bool
     */
    public function sendActiveCode($userInfo){

        //生成code
        $activecode = urlencode((new UserService())->createActiveCode($userInfo));

        //收件人邮箱
        $toemail    =$userInfo['email'];
        //发件人昵称
        $name       =$userInfo['username'];
        //邮件标题
        $subject    ='欢迎使用OoBox';

        $sendUrl = 'http://'.$_SERVER['HTTP_HOST'].'/User/active?uid='.$userInfo['uid'].'&code='.$activecode.'&time='.time();

        //邮件内容
        ///User/active?uid=10003&code=8118260f5bd966fe8b1616672b2a6616
        $content="<h1>感谢你，感谢您注册成为OoBox用户。</h1>
                    <a href='$sendUrl'>点击激活</a><br>
                    <p>$sendUrl</p>";

        //发送激活邮件
        send_mail($toemail,$name,$subject,$content);
        return true;
    }


    /**
     * 重新发送
     * @return bool
     * @throws \think\exception\DbException
     */
    public function remail(){
        $request = Request::instance();
        $param =  $request->param();
        //获取用户信息
        $userInfo = User::get(['username'    => $param['name']]);
        if($request->isPost()){
            //$res = $this->sendActiveCode($userInfo);
            if(0 == 0){
                return show(1,'发送成功','/User/action');
            }
            return 0;
        }
        $this->assign('uid',$userInfo['uid']);
        $this->assign('email',$userInfo['email']);
        return $this->fetch('');
    }

    /**
     * 用户激活
     * @return array
     * @throws \think\exception\DbException
     */
    public function activationCode(){
        $request = Request::instance();
        //获取邮件请求中的激活加密串和code
        $param =  $request->param();

        /**
         * 用户激活过程
         * 1.获取参数  比对用户是否存在
         * 2.用户存在  验证是否已经激活过了（这注意  激活链接激活成功之后 后续直接跳转登录页面）
         * 3.code校验
         * 4.End
         */
        //比对用户是否存
        $userData = User::get(['uid'=>$param['uid']]);
        if (!$userData) {
            $this->assign('errorMsg','用户不存在');
        }
        else {
            //是否已经激活
            if($userData['status'] == 1){
                $this->assign('errorMsg','用户已激活');
            }else{
                //验证可用性
                $Verification = (new UserService())->VerificationActiveCode($param['uid'],$param['code']);
                if ($Verification == 1){
                    //code正确  数据库用户可用  [status => 1]
                    $userData->save(['status' => 1]);
                    $this->success('激活成功','index/entry/login');
                }else if($Verification == 2){
                    $this->assign('errorMsg','激活链接已过期，请重新发送');
                    //return show(0,'激活链接已过期，请重新发送。','');
                }else{
                    $this->assign('errorMsg','激活失败');
                    //return show(0,'激活失败','');
                }
            }

        }
        return $this->fetch('entry/verify');

    }

}