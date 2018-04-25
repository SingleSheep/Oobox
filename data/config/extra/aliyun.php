<?php
/**
 * Created by 小羊.
 * Author: 勇敢的小笨羊
 * 微博: http://weibo.com/xuzuxing
 * Date: 2017/12/18
 * Time: 22:39
 */

/*
 * 短信验证码 ：使用同一个签名，对同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天。
 * 短信通知： 使用同一个签名和同一个短信模板ID，对同一个手机号码发送短信通知，支持50条/日
 */
return [
    'signName'          =>  'Momo校园帮',          //短信签名
    'templateCode'      =>  'SMS_109495337',      //短信模板
    'accessKeyId'       =>  'LTAIZZvAxSB8pddC',   //应用accesskeyid
    'accessKeySecret'   =>  'rRmrBuFSHrHfNfvRGvBT3LcQwDPDsg'//应用accesskeysecret
];