<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/1
 * Time: 19:24
 */

namespace app\admin\controller;
use app\common\lib\Aes;
use app\admin\model\User;
use think\Controller;
use think\Request;
use app\common\service\UserService;
class Login extends Controller
{

    public function index(){
        return $this->fetch();
    }
    /**
     * 生成验证码
     * @return \think\Response
     */
    public function verify(){
        $config = config('verify');//配置
        $captcha =  new \think\captcha\Captcha($config);
        return $captcha->entry();
    }

    public function login(){
        if(request()->isPost()){
            // 做一个简单的登录 组合where数组条件
            $data = Request::instance()->post();
            //验证码验证
            $res= check_verify($data['code']);
            if (!$res) {
                return show(config('app.error_code'), '验证码错误', $data, 200);
            }
            //Aes 加密
            $aes = new Aes();
            $map['username'] =  $data['username'];
            $map['password'] =  $aes->encrypt($data['password']);
            //账户密码验证
            $model = new User();
            $userdata= $model->where($map)->find();

            if (empty($userdata)) {
                return show(config('app.error_code'), '账号或者密码错误', $userdata, 200);
            }else{
                //登录成功 数据更新
                $info = [
                    'online'    => 1, //登录状态
                    'last_login_ip'   => request()->ip(), //登录IP
                    'last_login_time' => time()  //登录时间
                ];
                $userdata->save($info);
                //基本数据存入session
                (new UserService())->createLoginStatus($userdata);
                return show(config('app.success_code'), '登录成功', url('admin/index/index'), 200);
            }
        }else{
            return false;
        }
    }
    /**
     * 退出
     */
    public function logout(){
        //   基本数据更新
        $data = [
            'online'    => 0,
            'last_login_ip'   => request()->ip(),
            'last_login_time' => time()
        ];
        $userData = (new UserService())->checkLoginStatus();
        $model = new User();
        $res = $model
            ->where('uid',$userData['uid'])
            ->update($data);

        if(!$res){
            $this->error('系统异常');exit();
        }
        //清除session
        session(config('app.admin_auth_key'),null);
        $this->redirect('admin/login/index');
    }


}