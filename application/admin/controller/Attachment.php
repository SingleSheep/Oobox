<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/13
 * Time: 21:02
 */

namespace app\admin\controller;


class Attachment extends Base
{

    public function index(){
        $Filelist = $this->getDir('');
        //dump($Filelist);
        $this->assign('dir',$Filelist['dir']);
        $this->assign('file',$Filelist['file']);
        return $this->fetch();
    }

    /**
     * 获取dir下的目录
     * @param $dir
     * @return array
     */
    function getDir($dir = '') {
        $TrueDir = UPLOAD_PATH.DS.$dir;
        $fileArray[] = NULL;
        $dirArray[]  = NULL;
        if (false != ($handle = opendir ( $TrueDir ))) {
            $i=0;
            while ( false !== ($dirs = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($dirs != "." && $dirs != ".."&&!strpos($dirs,".")) {
                    $dirArray[$i]=
                        [
                            'name'  => '文件名称',
                            'url'   => '/uploads' . DS . $dirs
                        ];
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        //获取文件
        if (false != ($handle = opendir ( $TrueDir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".."&&strpos($file,".")) {
                    $fileArray[$i]= [
                        'name'  => '文件名称',
                        'url'   => '/uploads/'. $file,
                    ];
                    if($i==100){
                        break;
                    }
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        $dirfile =  [
            'dir'   => $dirArray,
            'file'   => $fileArray,
        ];
        return $dirfile;
    }

    /**
     * 获取dir下的文件
     * @param $dir
     * @return array
     */
    function getFile($dir = '') {
        $fileArray[]=NULL;
        if (false != ($handle = opendir ( $dir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".."&&strpos($file,".")) {
                    $fileArray[$i]="./imageroot/current/".$file;
                    if($i==100){
                        break;
                    }
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        return $fileArray;
    }


//调用方法getDir("./dir")……
}