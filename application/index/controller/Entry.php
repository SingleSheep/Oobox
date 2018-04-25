<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/30
 * Time: 13:56
 */

namespace app\index\controller;
use app\common\lib\Aes;
use app\common\service\UserService;
use app\common\model\User;
use think\captcha\Captcha;
use think\Request;

/**
 * 登录入口
 * Class Entry
 * @package app\user\controller
 */
class Entry extends Base
{


    public function index(){

        return $this->fetch();

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
    /**
     * 用户登录
     * @return array|mixed
     * @throws \think\exception\DbException
     */
    public function login(){
        //是否登录
        $res = (new UserService())->checkLoginStatus();
        if(!$res){
            if(Request::instance()->isPost()){
                $param = Request::instance()->param();
                /**
                 * 登录步骤
                 * 1.传入参数及验证码校验
                 * 2.加密验证数据
                 * 3.设置登录状态
                 * 4.（非常用地点）发送验证邮件
                 * 5.完成
                 */

                if (!check_verify($param['verify'])){
                    return show(0,'验证码错误，请重试');
                }
                //实例化加密类
                $aes = new Aes();
                //对比用户
                $resData = User::get([
                    'username'=>$param['username'],
                    'password'=>$aes->encrypt( $param['password'])
                ]);
                if($resData['status'] == 1){
                    return show(0,'账户未激活！！   <a  href="active/remail?name='.$param['username'].'" target="_blank" style="color: #ee162d"><strong>点击发送激活邮件</strong></a>','');
                }
                if (!$resData){
                     return show(0,'账户或密码错误，请重试');
                }else{
                    //登录成功
                    $servce = new UserService();
                    //设置登录态
                    $servce->createLoginStatus($resData);
                    //修改相关数据
                    //转入用户中心
                    return show(1,'登陆成功');
                    //$this->redirect('index/ucenter/dashboard');
                }

            }
            return $this->fetch('',['siteTitle'=>'系统登录']);
        }
        $this->redirect('index/ucenter/settings');
    }

    /**
     * 新用户注册
     * @return mixed|string
     * @throws \think\exception\DbException
     */
    public function register(){
        if(Request::instance()->isPost()){
            $param = Request::instance()->param();
            /**
             * 注册步骤
             * 1.传入参数校验
             * 2.比对用户可用性（是否已注册，是否同邮箱，是否同手机号）
             * 3.数据库存入（待验证用户）
             * 4.发送验证邮件
             * 5.完成
             */
            $userModel = new User();
            //接收数据
            $Data = [
                'username'  => $param['username'],
                'nickname'  => $param['nickname'],
                'password'  => $param['password'],
                'email'  => $param['email'],
            ];
            //验证码
            if (!check_verify($param['verify'])){
                return show(0,'验证码错误','');
            }
            //验证可用性
            $userInfo = $userModel->get(['username'=> $Data['username']]);
            if($userInfo){
                //判断用户是否激活  没激活则发送邮件
                if($userInfo['status' !== 1]){
                    (new Active())->sendActiveCode($userInfo);
                }
                return show(0,'用户已存在,以重新发送激活链接至您的邮箱');
            }else{
                //密码加密
                $aes = new Aes();
                $Data['password'] = $aes->encrypt($Data['password']);
                //数据入库
                $userModel->username = $Data['username'];
                $userModel->nickName = $Data['nickname'];
                $userModel->password = $Data['password'];
                $userModel->email    = $Data['email'];
                $userModel->save();

                //注册完成后发送邮件
                (new Active())->sendActiveCode($userModel);
                //注册成功模板
                return show(1,'注册成功，请检查邮件','User/action');
            }
        }
        return $this->fetch('',['siteTitle'=>'用户注册']);
    }


    public function reset_password(){

        return $this->fetch();
    }
    public function lost_username(){

        return $this->fetch();
    }
    /**
     * 用户名可用性检查
     * @return array
     * @throws \think\exception\DbException
     */
    public function checkName(){
        $param = Request::instance()->param();
        //对比用户
        $resData = User::get(['username'=>$param['username']]);
        if($resData){
            return show(0,'用户已存在');
        }
    }

    /**
     * 邮件可用性检查
     */
    public function checkMail(){
        $param = Request::instance()->param();
        //对比用户
        $resData = User::get(['email'=>$param['email']]);
        if($resData){
            return show(0,'用户已存在');
        }
    }
}