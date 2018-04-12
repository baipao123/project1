<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 15:07:15
 */

namespace common\tools;

use Yii;

class WxProgram
{
    const OK = 0;
    const IllegalAesKey = -41001;
    const IllegalIv = -41002;
    const IllegalBuffer = -41003;
    const DecodeBase64Error = -41004;

    protected static function getAppId() {
        return Yii::$app->params['appId'];
    }

    protected static function getAppSecret() {
        return Yii::$app->params['appSecret'];
    }

    public static function decryptUserCode($code) {
        $url = "https://api.weixin.qq.com/sns/jscode2session";
        $params = [
            "appid"      => self::getAppId(),
            "secret"     => self::getAppSecret(),
            "js_code"    => $code,
            "grant_type" => "authorization_code"
        ];

        return [
            "openId"      => "",
            "session_key" => "",
            "unionId"     => ""
        ];
    }

    public static function decryptData($encryptedData, $iv, $sessionKey, &$data) {
        if (strlen($sessionKey) != 24)
            return self::IllegalAesKey;
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24)
            return self::IllegalIv;
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL)
            return self::IllegalBuffer;
        if ($dataObj->watermark->appid != self::getAppId())
            return self::IllegalBuffer;
        $data = $result;
        return self::OK;
    }
}