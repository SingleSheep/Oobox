<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2018/1/4
 * Time: 17:42
 */

namespace app\index\controller;
use app\common\service\UserService;
use think\Controller;
use think\Request;

class Base extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $userData = (new UserService())->checkLoginStatus();
        $this->assign('userData',$userData);
        $this->assign('siteTitle','分享快乐');
    }

}