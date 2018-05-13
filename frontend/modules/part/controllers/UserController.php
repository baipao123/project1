<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午6:42
 */

namespace frontend\modules\part\controllers;

use common\models\User;
use common\tools\Sms;
use common\tools\StringHelper;
use common\tools\Tool;
use common\tools\WxApp;
use Yii;

class UserController extends \frontend\controllers\BaseController
{

    public function actionPhoneDecrypt() {
        $user = $this->getUser();
        if (WxApp::decryptData($this->getPost("encryptedData"), $this->getPost("iv"), $user->session_key, $data) == WxApp::OK) {
            $data = json_decode($data, true);
            if (isset($data['phoneNumber']) && !empty($data['phoneNumber']) && isset($data['purePhoneNumber']) && !empty($data['purePhoneNumber'])) {
                Yii::$app->redis->set("USER-WX-PHONE-" . $user->id, $data['phoneNumber']);
                return Tool::reJson(["purePhoneNumber" => substr_replace($data['purePhoneNumber'], '****', 3, 4)]);
            } else
                return Tool::reJson(null, "您未绑定手机号", Tool::FAIL);
        } else
            return Tool::reJson(null, "解析手机号失败，请重新登录", Tool::FAIL);
    }


    public function actionJoinUser() {
        if ($this->getUser()->type == User::TYPE_COMPANY)
            return Tool::reJson(null, "您是企业用户", Tool::FAIL);
        $user = $this->getUser();
        $realName = $this->getPost("name");
        if (!StringHelper::isRealName($realName))
            return Tool::reJson(null, "请输入真实的姓名，2-7位汉字", Tool::FAIL);
        $type = $this->getPost("type", 1);
        if ($type == 1) {
            $phone = Yii::$app->redis->get("USER-WX-PHONE-" . $user->id);
            if (empty($phone))
                return Tool::reJson(null, "手机号不能为空", Tool::FAIL);
        } elseif ($type == 2) {
            $phone = $this->getPost("phone", "");
            $code = $this->getPost("code", "");
            if (!Sms::VerifyCode($phone, $code))
                return Tool::reJson(null, "验证码已失效", Tool::FAIL);
        } else
            return Tool::reJson(null, "非法请求", Tool::FAIL);
        $user->realname = $realName;
        $user->phone = $phone;
        $user->real_at = time();
        $user->type = User::TYPE_USER;
        $user->save();
        return Tool::reJson(["user" => $user->info()]);
    }

    public function actionChangePhone() {
        $user = $this->getUser();
        $type = $this->getPost("type", 1);
        if ($type == 1) {
            $phone = Yii::$app->redis->get("USER-WX-PHONE-" . $user->id);
            if (empty($phone))
                return Tool::reJson(null, "手机号不能为空", Tool::FAIL);
        } elseif ($type == 2) {
            $phone = $this->getPost("phone", "");
            $code = $this->getPost("code", "");
            if (!Sms::VerifyCode($phone, $code))
                return Tool::reJson(null, "验证码已失效", Tool::FAIL);
        } else
            return Tool::reJson(null, "非法请求", Tool::FAIL);
        $user->phone = $phone;
        $user->save();
        return Tool::reJson(["user" => $user->info()]);
    }

    public function actionJoinCompany() {
        if ($this->getUser()->type == User::TYPE_USER)
            return Tool::reJson(null, "您是个人用户", Tool::FAIL);


    }


    public function actionEdit() {
        $name = $this->getPost("name", "");
        $value = $this->getPost("value", "");
        $user = $this->getUser();

        if ($name == 'realname') {
            Yii::warning(StringHelper::isRealName($value));
            if (!StringHelper::isRealName($value))
                return Tool::reJson(null, "请输入正确的姓名", Tool::FAIL);
            $user->realname = $value;
            $user->save();
            return Tool::reJson(["user" => $user->info()]);
        } else if ($name == 'phone') {
            if (!StringHelper::isMobile($value))
                return Tool::reJson(null, "请输入正确的手机号", Tool::FAIL);
            $user->phone = $value;
            $user->save();
            return Tool::reJson(["user" => $user->info()]);
        } else if ($name == 'city') {
            $user->city_id = $this->getPost('cid', 0);
            $user->area_id = $this->getPost('aid', 0);
            $user->save();
            return Tool::reJson(["user" => $user->info()]);
        } else {
            if ($user->type <= User::TYPE_USER || !$company = $user->company)
                return Tool::reJson(null, "您还不是招聘者", Tool::FAIL);

            if (in_array($name, ["name", "description", "tips", "icon", "cover"])) {
                $company->$name = $value;
                $company->save();
                return Tool::reJson(["company" => $company->info()]);
            } else if ($name == 'position') {
                $company->position = $value;
                $latitude = $this->getPost('latitude', '');
                $longitude = $this->getPost('longitude', '');
                if (!empty($latitude) && !empty($longitude)) {
                    $company->latitude = $latitude;
                    $company->longitude = $longitude;
                }
                $company->save();
                return Tool::reJson(["company" => $company->info()]);
            } else
                return Tool::reJson(null, "无修改项目", Tool::FAIL);
        }

    }
}