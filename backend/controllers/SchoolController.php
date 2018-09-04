<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/9/4
 * Time: 下午10:36
 */

namespace backend\controllers;

use common\models\District;
use Yii;
use common\models\School;
use yii\data\Pagination;

class SchoolController extends BaseController
{
    public $basicActions = ["info", "toggle"];

    public function actionList($cid = 0, $name = "", $status = -1) {
        $query = School::find()->where(["<>","status",School::DEL]);
        if ($cid > 0)
            $query->andWhere(["city_id" => $cid]);
        if (!empty($name))
            $query->andWhere(["LIKE", "name", $name]);
        if ($status != -1)
            $query->andWhere(["status" => $status]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $list = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->all();
        return $this->render("list", [
            "data"       => $list,
            "pagination" => $pagination,
            "cid"        => $cid,
            "name"       => $name,
            "status"     => $status,
            "cities"     => District::cities(),
        ]);
    }

    public function actionInfo($id = 0) {
        if ($id > 0) {
            $school = School::findOne($id);
            if (!$school || $school->status == School::DEL) {
                Yii::$app->session->setFlash("danger", "未找到学校");
                return $this->render("../layouts/none");
            }
        } else
            $school = new School();
        if (Yii::$app->request->isPost) {
            $school->name = Yii::$app->request->post("name", "");
            $school->city_id = Yii::$app->request->post("city_id", 0);
            $school->status = Yii::$app->request->post("status", 0);
            if ($school->isNewRecord)
                $school->created_at = time();
            if (empty($school->name))
                Yii::$app->session->setFlash("info", "请填写学校名称");
            elseif (empty($school->city_id))
                Yii::$app->session->setFlash("info", "请选择学校所属城市");
            elseif (!$school->save()) {
                Yii::warning($school->errors, "保存school失败");
                Yii::$app->session->setFlash("danger", "保存学校失败");
            } else
                Yii::$app->session->setFlash("success", "保存成功");
        }
        return $this->render("info", [
            "school" => $school,
            "cities"     => District::cities(),
        ]);
    }

    public function actionToggle($id = 0, $status) {
        $school = School::findOne($id);
        if (!$school || $school->status == School::DEL)
            Yii::$app->session->setFlash("danger", "未找到学校");
        else if (!in_array($status, [School::ON, School::OFF, School::DEL]))
            Yii::$app->session->setFlash("danger", "非法操作");
        else if ($school->status == $status)
            Yii::$app->session->setFlash("danger", "无需操作");
        else {
            $school->status = $status;
            $school->save();
            Yii::$app->session->setFlash("success", "操作成功");
        }
        return $this->render("../layouts/none");


    }
}