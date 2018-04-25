<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2018/1/4
 * Time: 17:41
 */

namespace app\index\controller;

use app\user\model\User;
use think\Request;

class Search extends Base
{

    public function index(){
    $param = Request::instance()->get();

    $results = (new User())->paginate();
    $this->assign('results',$results);
    return $this->fetch();
    }
}