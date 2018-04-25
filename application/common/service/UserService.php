<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/20
 * Time: 23:10
 */

namespace app\common\service;
use app\common\model\User as UserModel;
use app\admin\model\User as AdminModel;
use app\common\lib\Aes;
use think\Cache;
use think\Request;

class UserService
{
    /**
     * @var null
     */
    public $userInfo=null;

    /**
     * @var null
     */
    public $adminInfo=null;

    /**
     * 登录管理员状态检查
     * @return bool|null|static
     * @throws \think\exception\DbException
     */
    public function checkAdminStatus(){

        $auth_cookie = Request::instance()->session(config('app.admin_auth_key'));
        if(!$auth_cookie){
            return false;
        }
        list($auth_token,$uid) = explode("|",$auth_cookie);
        if(!$auth_token || !$uid){
            return false;
        }
        if( $uid && preg_match("/^\d+$/",$uid) ){
            $userinfo = AdminModel::get([ 'uid' => $uid ]);
            if(!$userinfo){
                return false;
            }
            //校验auth_token
            if($auth_token !== $this->createAuthToken($userinfo['uid'],$userinfo['username'],$userinfo['email'],$_SERVER['HTTP_USER_AGENT'])){
                return false;
            }
            return $userinfo;
        }
        return false;
    }
    /**
     * 登录用户状态检查
     * @return bool|null|UserService
     * @throws \think\exception\DbException
     */
    public function checkLoginStatus(){
        $auth_cookie = Request::instance()->session(config('app.user_auth_key'));
        if(!$auth_cookie){
            return false;
        }
        list($auth_token,$uid) = explode("|",$auth_cookie);
        if(!$auth_token || !$uid){
            return false;
        }
        if( $uid && preg_match("/^\d+$/",$uid) ){
            //获取用户信息   cache中获取  无则从mysql读取
            $userinfo = Cache::get($uid);
            if($userinfo == null){
                $userinfo = UserModel::get([ 'uid' => $uid ]);
                if(!$userinfo){
                    return false;
                }
                //校验auth_token
                if($auth_token !== $this->createAuthToken($userinfo['uid'],$userinfo['username'],$userinfo['email'],$_SERVER['HTTP_USER_AGENT'])){
                    return false;
                }
                return $userinfo;
            }
            return $userinfo;
        }
        return false;
    }

    /**
     * 设置登录状态
     * @param $userinfo
     */
    public function createLoginStatus($userinfo){
        $auth_token = $this->createAuthToken($userinfo['uid'],$userinfo['username'],$userinfo['email'],$_SERVER['HTTP_USER_AGENT']);
        //是否要判断管理员？？？
        Cache::set($userinfo['uid'],$userinfo);
        if(strlen($userinfo['uid']) >= 5){
            Session(config('app.user_auth_key'),$auth_token."|".$userinfo['uid']);
        }else{
            Session(config('app.admin_auth_key'),$auth_token."|".$userinfo['uid']);
        }
    }

    /**
     * 生成用户授权码
     * @param $uid
     * @param $name
     * @param $email
     * @param $user_agent
     * @return string
     */
    public function createAuthToken($uid,$name,$email,$user_agent){
        return md5($uid.$name.$email.$user_agent.config('secure.token_salt'));
    }

    /**
     * 生成用户激活码
     * @param $userInfo
     * @return string
     */
    public function createActiveCode($userInfo){

        //生成Code
        $code = sha1($userInfo['uid'].$userInfo['username'].$userInfo['email'].$_SERVER['HTTP_USER_AGENT']);
        //加密
        //1.用户激活的时候返回ActiveToken
        //2.拿到ActiveToken 解密
        //3.比对cache
        //4.完成激活
        $ActiveToken = (new Aes())->encrypt($code);
        //存入cache 2H内验证时间  7200秒
        $cache = Cache::init();
        $cache->set('avtive_code_'.$userInfo['uid'],$code,7200);
        return $ActiveToken;
    }

    /**
     * 用户激活码验证
     * @param $uid
     * @param $ActiveToken
     * @return int
     */
    public function VerificationActiveCode($uid,$ActiveToken){

        $AccessToken = (new Aes())->decrypt($ActiveToken);
        $build = 'avtive_code_'.$uid;
        //获取缓存的值
        $cache = Cache::init();
        $Token = $cache->get($build);

        //判断缓存是否过期
        if(empty( $Token )){
            return 2;
        }else{
            //传入值与缓存值对比正确
            if($AccessToken !== $Token){
                return 0;
            }
            Cache::rm($build);
            return 1;
        }
    }
}