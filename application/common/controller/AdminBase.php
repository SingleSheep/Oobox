<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/19
 * Time: 15:58
 */

namespace app\common\controller;

use app\common\service\UserService;
use think\Auth;
use think\Controller;
use think\Request;

class AdminBase extends Controller
{
    /**
     * 初始化方法
     */
    public function __construct()
    {
        parent::__construct();
        $auth = new Auth();
        $request = Request::instance();
        $m = $request->module();
        $c = $request->controller();
        $a = $request->action();
        $rule = $m . '/' . $c . '/' . $a;
        $this->assign('error', $str = '' );
        //	登录检查
        $userinfo = (new UserService())->checkAdminStatus();
        if ($userinfo) {
            $result = $auth->check($rule, $userinfo['uid']);
            $this->assign('userData', $userinfo);
            if (!$result) {
                return $this->error('没有权限', url('admin/index/index'));exit;
            }
        } else {
            $this->redirect('admin/login/index');
        }
    }

}