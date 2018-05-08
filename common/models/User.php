<?php

namespace common\models;

use common\tools\Img;
use common\tools\Tool;
use common\tools\WxApp;
use Yii;

/**
 * @property Company $company
 * */
class User extends \common\models\base\User
{
    const TYPE_USER = 1;
    const TYPE_COMPANY = 2;
    const TYPE_USER_BOSS = 3;

    public function getCompany() {
        return $this->hasOne(Company::className(), ["uid" => "id"]);
    }

    /**
     * @param string $code
     * @return static
     */
    public static function findUserByAppCode($code) {
        $data = WxApp::decryptUserCode($code);
        if (!$data || !isset($data['openid']) || empty($data['openid']))
            return null;
        $user = static::findOne(["openId" => $data['openid']]);
        $user or $user = new static;
        $user->openId = $data['openid'];
        $user->unionId = isset($data['unionid']) ? $data['unionid'] : "";
        $user->session_key = isset($data['session_key']) ? $data['session_key'] : "";
        if ($user->isNewRecord)
            $user->created_at = time();
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

    public function info() {
        return [
            "uid"      => $this->id,
            "type"     => $this->type,
            "username" => empty($this->realname) ? $this->nickname : $this->realname,
            "avatar"   => Img::format($this->avatar),
            "phone"    => $this->phone(),
            "gender"   => $this->gender,
        ];
    }

    public function phone($full = false) {
        if (empty($this->phone) || $full)
            return $this->phone;
        return substr_replace($this->phone, '****', 3, 4);
    }
}
