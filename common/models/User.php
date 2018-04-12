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
        if (!$data || !isset($data['openId']) || empty($data['openId']))
            return null;
        $user = static::findOne(["openId" => $data['openId']]);
        $user or $user = new static;
        $user->openId = $data['openId'];
        $user->unionId = isset($data['unionId']) ? $data['unionId'] : "";
        $user->session_key = isset($data['session_key']) ? $data['session_key'] : "";
        $user->save();
        return $user;
    }
}
