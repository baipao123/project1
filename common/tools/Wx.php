<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午6:20
 */

namespace common\tools;

use Yii;

class Wx {
    /**
     * @param string $url
     * @param array $get
     * @param array $post
     * @param int $timeout
     * @return string json
     */
    public static function http($url = '', $get = [], $post = [], $timeout = 3) {
        if (!empty($get)) {
            $url = self::getUrl($url, $get);
        }
        Yii::info("请求url","wx");
        Yii::info($url,"wx");
        if(!empty($post)){
            Yii::info("请求Post参数","wx");
            Yii::info($post, "wx");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//目标地址
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果，不输出
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);//超时时间
        //请求类型
        if (empty($post)) {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);
            //微信的post接口提交的参数为json
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }
        $response = curl_exec($ch);//获得返回值
        if (!$response) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            Yii::info("curl出错", "wx");
            Yii::info([$errno, $error], "wx");
        }
        Yii::info("返回值","wx");
        Yii::info($response,"wx");
        curl_close($ch);
        return $response;
    }
    
    /*
     * 拼接get参数到url
     */
    protected static function getUrl($url = '', $get = []) {
        if (!strpos("?", $url)) {
            $url .= "?";
        }
        else {
            $url .= "&";
        }
        
        foreach ($get as $key => $value) {
            $url .= $key . "=" . $value . "&";
        }
        return rtrim($url, "&");
    }
    
}