<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/9
 * Time: 1:23
 */

namespace app\admin\controller;
use app\admin\model\Order as OrderModel;
use app\admin\model\Member as MemberModel;

class Order extends Base
{
    public function index(){

        $model = new OrderModel();
        $data = $model->paginate('10');
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 订单详情
     * @return mixed
     * @param $OrderId
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function OrderInfo($OrderId){

        $inData = (new OrderModel())
            ->where(['id'=>$OrderId])
            ->field('order_key',true)
            ->find();
        $userData = (new MemberModel())
            ->where(['id'=>$inData['uid']])
            ->field('nickName,province,county,phone')
            ->find();
        $Customer = (new MemberModel())
            ->where(['id'=>$inData['puid']])
            ->field('nickName,province,county,phone')
            ->find();
        $this->assign('data',$inData);
        $this->assign('user',$userData);
        $this->assign('customer',$Customer);
        return $this->fetch();
    }

    /**
     * 发票
     * @param $OrderId
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function invoice($OrderId){
        $inData = (new OrderModel())
            ->where(['id'=>$OrderId])
            ->field('order_key',true)
            ->find();
        $userData = (new MemberModel())
            ->where(['id'=>$inData['uid']])
            ->field('nickName,province,county,phone')
            ->find();
        $Customer = (new MemberModel())
            ->where(['id'=>$inData['puid']])
            ->field('nickName,province,county,phone')
            ->find();
        $this->assign('data',$inData);
        $this->assign('user',$userData);
        $this->assign('customer',$Customer);
        return $this->fetch();
    }
}