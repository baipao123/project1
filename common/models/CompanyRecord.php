<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;

use Yii;

class CompanyRecord extends \common\models\base\CompanyRecord
{

    public function getAttaches() {
        return $this->hasMany(Attach::tableName(), ["id" => "tid"])->andWhere(["type" => Attach::COMPANY_RECORD, "status" => Attach::STATUS_ON]);
    }

    public function getCompany() {
        return $this->hasOne(Company::tableName(), ["uid" => "uid"]);
    }

    public function pass() {
        if ($this->Status != Company::STATUS_VERIFY)
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
            //TODO 模板消息通知
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
            $company->accuracy = $this->accuracy;
        }
        if (!empty($this->description))
            $company->description = $this->description;
        if (!$company->save()) {
            Yii::warning($company->errors, "保存Company失败");
            return false;
        }
        //TODO 模板消息通知
        return true;
    }

}