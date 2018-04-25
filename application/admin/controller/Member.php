<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/10/17
 * Time: 14:22
 */

namespace app\admin\controller;
use app\admin\model\Member as MemberModel;
class Member extends Base
{
    /**
     * 获取会员信息
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(){
        $data = (new MemberModel())
            ->paginate(10);

        $this->assign('data',$data);
        return $this->fetch();
    }
}