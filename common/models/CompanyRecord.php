<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;

use common\tools\Img;
use common\tools\WxApp;
use Yii;

/**
 * @property User $user
 * @property Attach[] $attaches
 * @property Company $company
 * */
class CompanyRecord extends \common\models\base\CompanyRecord
{
    public function getUser(){
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

    public function getAttaches() {
        return $this->hasMany(Attach::className(), ["tid" => "id"])->andWhere(["type" => Attach::COMPANY_RECORD, "status" => Attach::STATUS_ON]);
    }

    public function getCompany() {
        return $this->hasOne(Company::className(), ["uid" => "uid"]);
    }

    public function pass() {
        if ($this->status != Company::STATUS_VERIFY)
            return "已处理";
        $this->status = Company::STATUS_PASS;
        $this->updated_at = time();
        if (!$this->save()) {
            Yii::warning($this->errors, "保存CompanyRecord失败");
            return false;
        }
        $company = $this->company;
        if ($company->status == Company::STATUS_VERIFY) {
            $company->status = Company::STATUS_PASS;
            $company->updated_at = time();
            $company->save();
            if (!$company->save()) {
                Yii::warning($company->errors, "保存Company失败");
                return false;
            }
            //模板消息通知
            $this->user->sendTpl(WxApp::TPL_Company_Result, [
                $this->name, $company->typeStr(), date("Y-m-d H:i:s", $this->created_at), date("Y-m-d H:i:s"), "审核通过"
            ], $this->formId, "/pages/user/user");
            return true;
        }
        $company->name = $this->name;
        $company->icon = $this->icon;
        $company->cover = $this->cover;
        if (!empty($this->position))
            $company->position = $this->position;
        if (!empty($this->latitude) && !empty($this->longitude) && !empty($this->accuracy)) {
            $company->latitude = $this->latitude;
            $company->longitude = $this->longitude;
        }
        if (!empty($this->description))
            $company->description = $this->description;
        if (!$company->save()) {
            Yii::warning($company->errors, "保存Company失败");
            return false;
        }
        //模板消息通知
        $this->user->sendTpl(WxApp::TPL_Company_Result, [
            $this->name, $company->typeStr(), date("Y-m-d H:i:s", $this->created_at), date("Y-m-d H:i:s"), "审核通过"
        ], $this->formId, "/pages/user/user");
        return true;
    }

    public function icon() {
        $icon = empty($this->icon) ? "company/icon.jpg" : $this->icon;
        return Img::format($icon, 0, 0, true);
    }

    public function cover() {
        $cover = empty($this->cover) ? "company/cover.jpg" : $this->cover;
        return Img::format($cover, 0, 0, true);
    }

    public function covers() {
        if (empty($this->cover))
            return [];
        $arr = json_decode($this->cover, true);
        return is_array($arr) ? $arr : [$this->cover];
    }

}