<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/16
 * Time: 12:25
 */

namespace app\admin\controller;
use app\admin\model\Article as ArticleModel;

class Article extends Base
{

    /**
     * 文章列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $data = (new ArticleModel())
            ->alias('a')
            ->field('a.*,b.url')
            ->join('__IMAGES__ b','a.thumb = b.id','LEFT')
            ->order('a.create_time','desc')
            ->paginate(10);
        $this->assign('data',$data);
        return $this->fetch();
    }


    public function edit($id){
        $data   = (new ArticleModel())
            ->where([ 'id'   =>  $id ])
            ->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

}