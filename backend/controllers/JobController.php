<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/29
 * Time: 上午12:37
 */

namespace backend\controllers;

use Yii;
use common\models\Company;
use common\models\Job;
use yii\data\Pagination;

class JobController extends BaseController
{
    public $basicActions = ["off","del"];

    public function actionList($cid = 0, $status = 0, $start_date = "", $end_date = "") {
        $query = Job::find()->where(["<>", "status", Job::DEL]);
        if ($cid > 0)
            $query->andWhere(["uid" => $cid]);
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        if (!empty($start_date) || !empty($end_date)) {
            $start_date = empty($start_date) ? 0 : $start_date;
            $end_date = empty($end_date) ? date("Ymd") : $end_date;
            $query->andWhere(["<", "start_at", $end_date])->andWhere([">", "end_at", $start_date]);
        }

        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(10);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "status"     => [Job::ON, Job::END, Job::OFF],
                "created_at" => SORT_DESC
            ])
            ->all();
        return $this->render("list", [
            "records"    => $records,
            "pagination" => $pagination,
            "cid"        => $cid,
            "company"    => Company::findOne(["uid" => $cid]),
            "status"     => $status,
            "start_date" => $start_date,
            "end_date"   => $end_date
        ]);
    }


    public function actionOff($jid) {
        $job = Job::findOne($jid);
        Yii::warning($job->attributes);
        if (!$job)
            Yii::$app->session->setFlash("danger", "岗位不存在");
        else if ($job->status == Job::DEL)
            Yii::$app->session->setFlash("danger", "岗位已被删除");
        else if ($job->status != Job::ON)
            Yii::$app->session->setFlash("danger", "岗位不是上架状态，无需下架");
        else {
            $job->status = Job::OFF;
            $job->save();
            Yii::$app->session->setFlash("success", "岗位下架成功");
        }
        return $this->render("../layouts/none");
    }

    public function actionDel($jid){
        $job = Job::findOne($jid);
        Yii::warning($job->attributes);
        if (!$job)
            Yii::$app->session->setFlash("danger", "岗位不存在");
        else if ($job->status == Job::DEL)
            Yii::$app->session->setFlash("danger", "岗位已被删除");
        else if ($job->status != Job::OFF)
            Yii::$app->session->setFlash("danger", "岗位不是下架状态，请先下架");
        else {
            $job->status = Job::DEL;
            $job->save();
            Yii::$app->session->setFlash("success", "岗位删除成功");
        }
        return $this->render("../layouts/none");
    }
}