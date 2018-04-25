<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/1
 * Time: 20:38
 */

namespace app\admin\controller;
use think\Db;
use think\Request;

class User extends Base
{

    /**
     * 用户列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $data=Db::name('admin_auth_group_access')
            ->alias('aga')
            ->field('u.uid,u.last_login_ip,u.last_login_time,u.phone,u.email,u.avatar,u.status,u.username,u.nickname,u.email,aga.group_id,ag.title')
            ->join('__ADMIN_USERS__ u' , 'aga.uid=u.uid','RIGHT')
            ->join('__ADMIN_AUTH_GROUP__ ag' , 'aga.group_id=ag.id','LEFT')
            ->select();

        $first=$data[0];
        $first['title']=array();
        $user_data[$first['uid']]=$first;
        // 组合数组
        foreach ($data as $k => $v) {
            foreach ($user_data as $m => $n) {
                $uuids=array_map(function($a){return $a['uid'];}, $user_data);
                if (!in_array($v['uid'], $uuids)) {
                    $v['title']=array();
                    $user_data[$v['uid']]=$v;
                }
            }
        }
        // 组合管理员title数组
        foreach ($user_data as $k => $v) {
            foreach ($data as $m => $n) {
                if ($n['uid']==$k) {
                    $user_data[$k]['title'][]=$n['title'];
                }
            }
            $user_data[$k]['title']=implode('、', $user_data[$k]['title']);
        }
        $assign=array(
            'data'=>$user_data //用户数据
        );
        $this->assign($assign);
        $data=Db::name('admin_auth_group')->paginate();
        $assign=array(
            'auth'=>$data  //用户数据
        );
        $this->assign($assign);
        return $this->fetch();
    }


    public function info($uid){
        if(Request::instance()->isPost()){
                halt($_POST);
        }else{
            $data=Db::name('admin_auth_group_access')
                ->alias('aga')
                ->where('u.uid',$uid)
                ->field('u.uid,u.last_login_ip,u.last_login_time,u.phone,u.email,u.avatar,u.status,u.username,u.nickname,u.email,aga.group_id,ag.title')
                ->join('__ADMIN_USERS__ u' , 'aga.uid=u.uid','RIGHT')
                ->join('__ADMIN_AUTH_GROUP__ ag' , 'aga.group_id=ag.id','LEFT')
                ->find();

            $auth=Db::name('admin_auth_group')->paginate();
            $assign=array(
                'data'=>$data,  //用户数据
                'auth'=>$auth  //权限数据
            );
            $this->assign($assign);
            return $this->fetch();
        }
    }
}