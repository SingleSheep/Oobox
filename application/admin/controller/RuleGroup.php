<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/13
 * Time: 19:02
 */

namespace app\admin\controller;
use think\Db;

class RuleGroup extends Base
{
    /**
     * 角色列表
     */
    public function index(){
        $data=Db::name('admin_auth_group')->paginate(5);
        $assign=array(
            'data'=>$data
        );
        $this->assign($assign);
        return $this->fetch();
    }


    /**
     * 添加角色
     */
    public function add_group(){
        $data=input('post.');
        unset($data['id']);
        $result=Db::name('auth_group')->insert($data);
        if ($result) {
            return show(
                config('app.success_code'),
                '添加用户组成功', "",
                200
            );
        }else{
            return show(
                config('app.error_code'),
                '添加用户组失败', "",
                200
            );
        }
    }

    /**
     * 修改角色
     */
    public function edit_group(){
        $data=input('post.');
        $result=Db::name('auth_group')->where(["id"=>$data['id']])->update(['title'=>$data['title']]);
        // $result=Db::name('auth_group')->editData($map,$data);
        if ($result) {
            $this->success('修改成功','Admin/Rule/rule_group');
        }else{
            $this->error('您没有做任何修改');
        }
    }

    /**
     * 删除角色
     */
    public function delete_group($id){
        if(request()->isPost()){
            if ($id!=1) {
                $result=Db::name('auth_group')->delete($id);
                if ($result) {
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            }
            $this->error('该用户组不能被删除');
        }
        $this->error("提交方式错误！");
    }


}