<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/28
 * Time: 20:47
 */

namespace app\index\controller;
use Payment\Pay as PayService;

class Pay
{
    protected $config = [
        'wechat' => [
            'app_id' => 'wx1c32cda245563ee1',
            'mch_id' => '1493758822',
            'notify_url' => '回调地址',
            'key' => '06c56a89949d617def52f371c357b6db',
            'cert_client'   => CRET_PATH.'apiclient_cert.pem',
            'cert_key'      => CRET_PATH.'apiclient_key.pem',
        ],
    ];

    public function index()
    {
        $config_biz = [
            'out_trade_no' => 'e2',
            'total_fee' => '1', // **单位：￥分**
            'body' => 'test body',
            'spbill_create_ip' => '8.8.8.8',
            'openid' => 'obw730EFiL3c42aZeC4FQ2P_s1WU',
        ];

        $pay = new PayService($this->config);

        return $pay->driver('wechat')->gateway('wap')->pay($config_biz);
    }

    public function notify($request)
    {
        $pay = new PayService($this->config);
        $verify = $pay->driver('wechat')->gateway('scan')->verify($request->getContent());

        if ($verify) {
            file_put_contents('notify.txt', "收到来自微信的异步通知\r\n", FILE_APPEND);
            file_put_contents('notify.txt', '订单号：' . $verify['out_trade_no'] . "\r\n", FILE_APPEND);
            file_put_contents('notify.txt', '订单金额：' . $verify['total_fee'] . "\r\n\r\n", FILE_APPEND);
        } else {
            file_put_contents(storage_path('notify.txt'), "收到异步通知\r\n", FILE_APPEND);
        }

        echo "success";
    }

}