<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/13
 * Time: 17:00
 */

namespace app\admin\controller;


class Inbox extends Base
{
    public function index(){

        return $this->fetch();
    }

}