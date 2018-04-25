<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/30
 * Time: 13:43
 */

namespace app\index\controller;
use app\common\service\UserService;
use app\common\model\User;
use think\Request;

/**
 * 用户中心
 * Class Center
 * @package app\user\controller
 */
class Ucenter extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $Service = new UserService();
        $res = $Service->checkLoginStatus();
        if(!$res){
            $this->redirect('index/entry/login');
        }

    }

    public function index(){
        return $this->fetch();
    }

    public function dashboard(){
        return $this->fetch('',['siteTitle'=>'主页']);
    }
    public function settings(){
        return $this->fetch('',['siteTitle'=>'用户中心']);
    }
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function changeInfo(){
        $param = Request::instance()->param();
        if(Request::instance()->isAjax()){
            echo 'ajax';
        }
        $res = User::get(['username'    => $param['name']]);
        $this->assign('Data',$res);
        return $this->fetch('edit',['siteTitle'=>'个人设置']);
    }

    public function changePwd(){
        return $this->fetch('pwd',['siteTitle'=>'密码修改']);
    }

    public function changeAvatar(){
        return $this->fetch('avatar',['siteTitle'=>'头像设置']);
    }
    /**
     * 用户注销登录
     */
    public function logout(){
        /**
         * 1.清除Session
         * 2.跳转Login
         */
        //清除session
        session(config('app.user_auth_key'),null);
        $this->redirect('index/entry/login');
    }
}