<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/10/20
 * Time: 21:38
 */

namespace app\admin\controller;

use think\Db;

class Database extends Base
{
    public function index()
    {
        //获取所有活动表
        $list = db()->query('SHOW TABLE STATUS');
        $list = array_map('array_change_key_case', $list);
        $this->assign([
            'lists' => $list,
            'total' => sizeof($list),
        ]);
        return $this->fetch();
    }
    //查看表详情
    public function view($name = null)
    {

        if ($name) {
            $db = new Db();
            $info  = Db::table($name)->getTableInfo($name);
            $fields = [];
            $datas = [];
            foreach ($info['fields'] as $field){
                $datas['name']=$field;
                $datas['type'] = str_replace('unsigned','<span class="badge">无符号</span>',$info['type'][$field]);
                $fields[] = $datas;
            }
            $this->assign([
                'fields'=>$fields,
                'table'=>$name,
                'pk' => $info['pk']
            ]);
            halt($info);
            return $this->fetch();
        }
        return $this->error("请指定要查看的表");
    }
    //优化表
    public function optimize($name = null)
    {
        if ($name) {
            $Db = db();
            if (is_array($name)) {
                $name = implode('`,`', $name);
            }
            $list = $Db->query("OPTIMIZE TABLE `{$name}`");
            if ($list) {
                return $this->do_success("数据表优化成功");
            }
            return $this->do_error("数据表优化失败");
        }
        return $this->do_error("请指定要优化的表");
    }
    //修复表
    public function repair($name = null)
    {
        if ($name) {
            $Db = db();
            if (is_array($name)) {
                $name = implode('`,`', $name);
            }
            $list = $Db->query("REPAIR TABLE `{$name}`");
            if ($list) {
                return $this->do_success("数据表修复成功！");
            }
            return $this->do_error("数据表修复失败");
        }
        return $this->do_error("请指定要修复的表");
    }
}
