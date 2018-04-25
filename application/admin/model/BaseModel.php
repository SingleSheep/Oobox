<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/1
 * Time: 19:17
 */

namespace app\admin\model;
use think\Model;

class BaseModel extends Model
{
    /*登录验证*/
    public static function change($id,$data)
    {
        $changedata=db('auth_rule')->where($id)->update($data);
        if ($changedata) {
            return true;
        }else{
            return false;
        }
    }

}