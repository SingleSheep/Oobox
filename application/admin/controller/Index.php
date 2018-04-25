<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/1
 * Time: 19:05
 */

namespace app\admin\controller;


class Index extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function userLock(){
        $this->view->engine->layout(false);
        return $this->fetch('lock');
    }
    //搜索  仅支持用户、订单
    public function search($keywords){
        if(empty($keywords)){
            echo "空值";
            return $this->fetch();
        }else {



            $Allsql = "SELECT `TABLE_NAME`,
                    `COLUMN_NAME` FROM `information_schema`.`COLUMNS`
                     WHERE `TABLE_SCHEMA` ='wxapp' AND DATA_TYPE in ('char', 'varchar', 'text', 'int')";
            $model =db();
            $data = $model->query($Allsql);
            halt($data);
            return $this->fetch();
        }

    }


}