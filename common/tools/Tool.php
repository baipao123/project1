<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 16:10:38
 */

namespace common\tools;


class Tool
{
    const NEED_LOGIN  = -1;
    const SUCCESS = 0;
    const FAIL = 1;

    public static function reJson($data = null, $text = "", $code = Tool::SUCCESS) {
        return json_encode([
            "code" => (int)$code,
            "msg"  => $text,
            "data" => $data,
        ]);
    }

    public static function isRealName($str) {
        return (bool)preg_match('/^([\x80-\xff]){2,7}$/', $str);
    }

    public static function isMobile($mobile) {
        return (bool)preg_match('/1[1-9]([0-9]){9}/', $mobile);
    }
}