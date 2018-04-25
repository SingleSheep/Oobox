<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/9
 * Time: 17:04
 */

namespace app\admin\controller;
use app\admin\model\Category as CateModel;
use think\Data;

class Category extends Base
{

    public function index(){
        $data = (new CateModel())
            ->alias('a')
            ->field('a.*,a.title as name,b.title,c.url')
            ->join('__CATEGORY_ITEM__ b','b.pid = a.id','LEFT')
            ->join('__IMAGES__ c','a.img_id = c.id','LEFT')
            ->order('a.create_time','desc')
            ->select();

        $this->assign('data',$data);

        return $this->fetch();
    }
}