<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\App;
use think\Hook;
use think\Config;
use think\Loader;
use think\Cache;
use think\Route;
use think\captcha\Captcha;



// 定义路由
Route::any('addons/execute/:route', "\\think\\addons\\Route@execute");

// 如果插件目录不存在则创建
if (!is_dir(ADDONS_PATH)) {
    @mkdir(ADDONS_PATH, 0777, true);
}

// 注册类的根命名空间
Loader::addNamespace('addons', ADDONS_PATH);

// 闭包自动识别插件目录配置
Hook::add('app_init', function () {
    // 获取开关
    $autoload = (bool)Config::get('addons.autoload', false);
    // 非正是返回
    if (!$autoload) {
        return;
    }
    // 当debug时不缓存配置
    $config = App::$debug ? [] : Cache::get('addons', []);
    if (empty($config)) {
        // 读取addons的配置
        $config = (array)Config::get('addons');
        // 读取插件目录及钩子列表
        $base = get_class_methods("\\think\\Addons");
        // 读取插件目录中的php文件
        foreach (glob(ADDONS_PATH . '*/*.php') as $addons_file) {
            // 格式化路径信息
            $info = pathinfo($addons_file);
            // 获取插件目录名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) == strtolower($name)) {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\addons\\" . $name . "\\" . $info['filename']);
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                // 循环将钩子方法写入配置中
                foreach ($hooks as $hook) {
                    if (!isset($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = [];
                    }
                    // 兼容手动配置项
                    if (is_string($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = explode(',', $config['hooks'][$hook]);
                    }
                    if (!in_array($name, $config['hooks'][$hook])) {
                        $config['hooks'][$hook][] = $name;
                    }
                }
            }
        }
        Cache::set('addons', $config);
    }
    Config::set('addons', $config);
});

// 闭包初始化行为
Hook::add('action_begin', function () {
    // 获取系统配置
    $data = App::$debug ? [] : Cache::get('hooks', []);
    $addons = (array)Config::get('addons.hooks');
    if (empty($data)) {
        // 初始化钩子
        foreach ($addons as $key => $values) {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else {
                $values = (array)$values;
            }
            $addons[$key] = array_filter(array_map('get_addon_class', $values));
            Hook::add($key, $addons[$key]);
        }
        Cache::set('hooks', $addons);
    } else {
        Hook::import($data, false);
    }
});

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param $name 插件名
 * @param string $type 返回命名空间类型
 * @param string $class 当前类名
 * @return string
 */
function get_addon_class($name, $type = 'hook', $class = null)
{
    $name = Loader::parseName($name);
    // 处理多级控制器情况
    if (!is_null($class) && strpos($class, '.')) {
        $class = explode('.', $class);
        foreach ($class as $key => $cls) {
            $class[$key] = Loader::parseName($cls, 1);
        }
        $class = implode('\\', $class);
    } else {
        $class = Loader::parseName(is_null($class) ? $name : $class, 1);
    }
    switch ($type) {
        case 'controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        default:
            $namespace = "\\addons\\" . $name . "\\" . $class;
    }

    return class_exists($namespace) ? $namespace : '';
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 * @return array
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return [];
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param $url
 * @param array $param
 * @return bool|string
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 */
function addon_url($url, $param = [], $suffix = true, $domain = false)
{
    $url = parse_url($url);
    $case = config('url_convert');
    $addons = $case ? Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? Loader::parseName($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    // 生成插件链接新规则
    $actions = "{$addons}-{$controller}-{$action}";

    return url("addons/execute/{$actions}", $param, $suffix, $domain);
}
/**
 * 检测是否登录
 * @return boolean 是否登录
 */
function check_login(){
    $uid = session(config('app.auth_key'));
    if (empty($uid)){
        return false;
    }else{
        return true;
    }
}

/**
 * 通用化API接口JSON数据输出
 * @param int $status 业务状态码   自定义
 * @param string $message 信息提示
 * @param [] $data  数据
 * @param int $httpCode http状态码
 * @return array
 */
function show($code, $message, $data=[], $httpCode=200) {

    $data = [
        'code' => $code,
        'msg' => $message,
        'data' => $data,
    ];

    return json($data, $httpCode);

}
function renderJSON($data=[], $msg ="ok", $code = 200){
    header('Content-type: application/json');//设置头部内容格式
    echo json_encode([
        "code" => $code,
        "msg"   =>  $msg,
        "data"  =>  $data,
        "req_id" =>  uniqid(),
    ]);
}

/**
 * 随机字符串
 * @param string $length
 * @param $format
 * @return null|string
 */
function getRandChar($length = '4',$format)
{
    switch($format){
        case 'ALL':
            $strPol='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
        case 'CHAR':
            $strPol='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            break;
        case 'NUMBER':
            $strPol='0123456789';
            break;
        default :
            $strPol='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }
    $str = null;
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}
/**
 * 验证码效验
 * @param $code
 * @return mixed
 */
function check_verify($code){
    $captcha = new Captcha();
    return $captcha->check($code);
}


/**
 * 系统邮件发送函数
 * @param $tomail
 * @param $name
 * @param string $subject
 * @param string $body
 * @param null $attachment
 * @return bool|string
 * @throws
 */
function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
    $mail = new PHPMailer();           //实例化PHPMailer对象
    $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();                    // 设定使用SMTP服务
    $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
    $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';          // 使用安全协议
    $mail->Host = "smtp.mxhichina.com"; // SMTP 服务器
    $mail->Port = 465;                  // SMTP服务器的端口号
    $mail->Username = "service@98imo.com";    // SMTP服务器用户名
    $mail->Password = "xzx595...";     // SMTP服务器密码
    $mail->SetFrom('service@98imo.com', 'OoBox');
    $replyEmail = '';                   //留空则为发件人EMAIL
    $replyName = '';                    //回复名称（留空则为发件人名称）
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($tomail, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}
