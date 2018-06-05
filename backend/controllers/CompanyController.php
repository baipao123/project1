<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午10:21
 */

namespace backend\controllers;

use backend\tools\Tool;
use common\tools\WxApp;
use Yii;
use common\models\Company;
use common\models\CompanyRecord;
use yii\data\Pagination;

class CompanyController extends BaseController
{
    public $basicActions = ["verify"];

    public function actionVerifyList() {
        $query = CompanyRecord::find()->where(["status" => Company::STATUS_VERIFY]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $records = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->all();
        return $this->render("verify-list", [
            "records"    => $records,
            "pagination" => $pagination,
        ]);
    }

    public function actionList($name = "", $status = 0) {
        $query = Company::find()->where(["<>", "status", Company::STATUS_VERIFY]);
        if (!empty($name))
            $query->andWhere(["like", "name", $name]);
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $records = $query->offset($pagination->getOffset())->limit($pagination->getLimit())->all();
        return $this->render("list", [
            "name"       => $name,
            "records"    => $records,
            "pagination" => $pagination,
        ]);
    }

    public function actionStatus($id, $status) {

    }

    public function actionVerify($id, $type) {
        $record = CompanyRecord::findOne($id);
        if (!$record || $record->status != Company::STATUS_VERIFY) {
            Yii::$app->session->setFlash("danger", "待审核记录不存在");
            return $this->render("../layouts/none");
        }
        if ($type == 1) {
            $res = $record->pass();
            if ($res === true)
                Yii::$app->session->setFlash("success", "已成功通过本条记录");
            else
                Yii::$app->session->setFlash("danger", $res === false ? "通过失败" : $res);
            return $this->render("../layouts/none");
        } else if ($type == 2) {
            if (Yii::$app->request->isPost) {
                $reason = Yii::$app->request->post("reason", "");
                $record->status = Company::STATUS_FORBID;
                $record->reason = $reason;
                $record->updated_at = time();
                $record->save();
                $company = $record->company;
                $company->status = Company::STATUS_FORBID;
                $company->updated_at = time();
                $company->save();
                // 模板消息
                $record->user->sendTpl(WxApp::TPL_Company_Result, [
                    $record->name, $company->typeStr(), date("Y-m-d H:i:s", $record->created_at), date("Y-m-d H:i:s"), "被拒绝：" . $reason
                ], $record->formId, "/pages/user/user");
                Yii::$app->session->setFlash("danger", "已成功拒绝本条记录");
                return $this->render("../layouts/none");
            }
        }
        return $this->render("verify-refuse", [
            "record" => $record
        ]);
    }

    public function actionQuery($name = "") {
        return \common\tools\Tool::reJson([
            "company" => Company::find()->where(["LIKE", "name", $name])->select(["id,name"])->asArray()->all()
        ]);
    }
}