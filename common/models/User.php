<?php
namespace common\models;

use common\tools\WxProgram;
use Yii;

class User extends \common\models\base\User
{

    /**
     * @param string $code
     * @return static
     */
    public static function findUserByProgramCode($code) {
        $data = WxProgram::decryptUserCode($code);
        if (!$data || !isset($data['openid']) || empty($data['openid']))
            return null;
        $user = static::findOne(["openid" => $data['openid']]);
        $user or $user = new static;
        $user->openId = $data['openId'];
        $user->unionId = isset($data['unionid']) ? $data['unionid'] : "";
        $user->session_key = isset($data['session_key']) ? $data['session_key'] : "";
        $user->save();
        return $user;
    }

    public function verifyUserInfo($rawData, $signature) {
        $sign = sha1($rawData . $this->session_key);
        if ($sign === $signature) {
            $userInfo = json_decode($rawData, true);
            $this->avatar = $userInfo['avatarUrl'];
            $this->nickname = $userInfo['nickName'];
            $this->gender = $userInfo['gender'];
            $this->city = $userInfo['city'];
            $this->province = $userInfo['province'];
            $this->country = $userInfo['country'];
            $this->save();
            return true;
        }
        return false;
    }
}
