<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 定义数据目录
define('DATA_PATH', __DIR__ . '/../data/');
// 定插件目录
define('ADDONS_PATH', __DIR__ . '/../addons/');
// 定义配置目录
define('CONF_PATH', DATA_PATH . 'config/');
// 定义配置目录
define('CRET_PATH', DATA_PATH . 'cret/');
// 定义配置目录
define('TMPLATE_PATH', DATA_PATH . 'template/');
// 定义运行目录
define('RUNTIME_PATH', DATA_PATH . 'runtime/');
// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
