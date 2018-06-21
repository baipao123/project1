<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/29
 * Time: 下午7:58
 */

namespace backend\controllers;

use common\models\District;
use common\models\User;
use common\models\UserHasJob;
use common\models\UserJobDaily;
use Yii;
use yii\data\Pagination;

class UserController extends BaseController
{
    public $basicActions = ["info","clear"];

    public function actionList($name = "", $phone = "", $gender = -1, $cid = 0, $aid = 0) {
        $query = User::find()->where(["type" => User::TYPE_USER]);
        if ($cid > 0)
            $query->andWhere(["city_id" => $cid]);
        if ($aid > 0)
            $query->andWhere(["area_id" => $aid]);
        if ($gender >= 0)
            $query->andWhere(["gender" => $gender]);
        if (!empty($name))
            $query->andWhere(["LIKE", "realname", $name]);
        if (!empty($phone))
            $query->andWhere(["LIKE", "phone", $phone]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(20);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "created_at" => SORT_ASC
            ])
            ->all();
        return $this->render("list", [
            "records"    => $records,
            "pagination" => $pagination,
            "cid"        => $cid,
            "aid"        => $aid,
            "gender"     => $gender,
            "name"       => $name,
            "phone"      => $phone,
            "cities"     => District::cities(),
            "areas"      => empty($cid) ? [] : District::areas($cid),
        ]);
    }


    public function actionJobList($uid = 0, $jid = 0, $start_date = "", $end_date = "", $status = 0) {
        $query = UserHasJob::find()->where(["<>", "status", UserHasJob::REFUSE]);
        if ($uid > 0)
            $query->andWhere(["uid" => $uid]);
        if ($jid > 0)
            $query->andWhere(["jid" => $jid]);
        if (!empty($start_date) || !empty($end_date)) {
            $start_at = empty($start_date) ? 0 : str_replace("-", "", $start_date);
            $end_at = empty($end_date) ? date("Ymd") : str_replace("-", "", $end_date);
            $query->andWhere(["BETWEEN", "created_at", $start_at, $end_at]);
        }
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(20);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "created_at" => SORT_DESC
            ])
            ->all();
        return $this->render("job-list", [
            "records"    => $records,
            "pagination" => $pagination,
            "uid"        => $uid,
            "jid"        => $jid,
            "start_date" => $start_date,
            "end_date"   => $end_date,
            "status"     => $status
        ]);
    }

    public function actionDailyList($status = 0, $uid = 0, $jid = 0, $start_date = "", $end_date = "") {
        $query = UserJobDaily::find();
        if ($uid > 0)
            $query->andWhere(["uid" => $uid]);
        if ($jid > 0)
            $query->andWhere(["jid" => $jid]);
        if (!empty($start_date) || !empty($end_date)) {
            $start_at = empty($start_date) ? 0 : str_replace("-", "", $start_date);
            $end_at = empty($end_date) ? date("Ymd") : str_replace("-", "", $end_date);
            $query->andWhere(["BETWEEN", "created_at", $start_at, $end_at]);
        }
        if ($status > 0)
            $query->andWhere(["status" => $status]);
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(20);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "created_at" => SORT_DESC
            ])
            ->all();
        return $this->render("daily-list", [
            "records"    => $records,
            "pagination" => $pagination,
            "uid"        => $uid,
            "jid"        => $jid,
            "start_date" => $start_date,
            "end_date"   => $end_date,
            "status"     => $status
        ]);
    }

    public function actionClear($uid){
        $user = User::findOne($uid);
        if (!$user)
            Yii::$app->session->setFlash("danger", "用户不存在");
        else if ($user->type != User::TYPE_USER)
            Yii::$app->session->setFlash("danger", "用户不是学生，无法清除注册信息");
        else {
            $user->type = 0;
            $user->save();
            Yii::$app->session->setFlash("success", "清除信息成功，用户可以重新注册了");
        }
        return $this->render("../layouts/none");
    }
}