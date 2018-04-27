<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午10:19
 */

namespace common\models;

use common\tools\Img;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property CompanyRecord $record
 * */
class Company extends \common\models\base\Company
{
    const STATUS_VERIFY = 0;
    const STATUS_PASS = 1;
    const STATUS_FORBID = 2;
    const STATUS_IGNORE = 3;

    const TYPE_COMPANY = 2;
    const TYPE_USER_BOSS = 3;

    public function getRecord() {
        return $this->hasOne(CompanyRecord::tableName(), ["uid" => "uid"])->andWhere(["status" => self::STATUS_VERIFY])->orderBy("created_at desc");
    }

    public static function Bind($uid, $data, $isAdd = false) {
        $company = static::findOne($uid);
        if ($company && $isAdd)
            return "您已经是企业用户了";
        $company or $company = new static;
        $name = ArrayHelper::getValue($data, "name", "");
        $icon = ArrayHelper::getValue($data, "icon", "");
        $cover = ArrayHelper::getValue($data, "cover", "");
        $position = ArrayHelper::getValue($data, "position", "");
        $description = ArrayHelper::getValue($data, "description", "");
        if ($company->status != self::STATUS_PASS) {
            $company->uid = $uid;
            $company->name = $name;
            $company->icon = $icon;
            $company->cover = $cover;
            $company->position = $position;
            $company->latitude = ArrayHelper::getValue($data, "latitude", "");
            $company->longitude = ArrayHelper::getValue($data, "longitude", "");
            $company->description = $description;
            $company->status = self::STATUS_VERIFY;
            if ($company->isNewRecord)
                $company->created_at = time();
            else
                $company->updated_at = time();
            if (!$company->save()) {
                Yii::warning($company->errors, "保存Company出错");
                return false;
            }
        }
        $oldRecords = CompanyRecord::find()->where(["uid" => $uid, "status" => self::STATUS_VERIFY])->all();
        /* @var $oldRecords CompanyRecord[] */
        $record = new CompanyRecord;
        $record->name = $name;
        $record->icon = $icon;
        $record->cover = $cover;
        $record->position = $position;
        $company->latitude = ArrayHelper::getValue($data, "latitude", "");
        $company->longitude = ArrayHelper::getValue($data, "longitude", "");
        $company->accuracy = ArrayHelper::getValue($data, "accuracy", "");
        $record->description = $description;
        $record->status = self::STATUS_VERIFY;
        $record->created_at = time();
        if (!$record->save()) {
            Yii::warning($record->errors, "保存CompanyRecord出错");
            return false;
        }
        foreach ($oldRecords as $r) {
            $r->status = self::STATUS_IGNORE;
            $r->updated_at = time();
            $r->save();
        }
        //attach
        $attaches = ArrayHelper::getValue($data, "attaches", []);
        foreach ($attaches as $path) {
            $attach = new Attach;
            $attach->type = Attach::COMPANY_RECORD;
            $attach->tid = $record->attributes['id'];
            $attach->path = $path;
            $attach->status = Attach::STATUS_ON;
            $attach->created_at = time();
            if (!$attach->save()) {
                Yii::warning($attach->errors, "保存CompanyAttaches出错");
                return false;
            }
        }
        return true;
    }

    public function jobs() {
        return [];
    }

    public function info() {
        return [
            "name"     => $this->name,
            "type"     => $this->type,
            "icon"     => Img::format($this->icon),
            "cover"    => Img::format($this->cover),
            "status"   => $this->status,
            "position" => [
                "longitude" => $this->longitude,
                "latitude"  => $this->latitude,
                "address"   => $this->position
            ]
        ];
    }
}