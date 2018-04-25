<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/9
 * Time: 0:50
 */

namespace app\admin\controller;


class Webset extends Base
{
    public function index(){
        $smtp = config('smtp_server');

        $model = db('webset');
        $data = $model->find(['id'=>1]);
        $this->assign('webset',$data);
        $this->assign('smtp',$smtp);

        return $this->fetch();
    }

}