<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/5/29
 * Time: 下午8:59
 */

namespace backend\controllers;

use common\models\UserClock;
use Yii;
use yii\data\Pagination;

class ClockController extends BaseController
{
    public $basicActions = ["info"];

    public function actionList($uid = 0, $jid = 0, $start_date = '', $end_date = '') {
        $query = UserClock::find();
        if ($uid > 0)
            $query->andWhere(["uid" => $uid]);
        if ($jid > 0)
            $query->andWhere(["jid" => $jid]);
        if (!empty($start_date) || !empty($end_date)) {
            $start_at = empty($start_date) ? 0 : strtotime($start_date);
            $end_at = empty($end_date) ? time() : strtotime($end_date) + 24 * 3600 - 1;
            $query->andWhere(["BETWEEN", "created_at", $start_at, $end_at]);
        }
        $count = $query->count();
        $pagination = new Pagination(["totalCount" => $count]);
        $pagination->setPageSize(20);
        $records = $query->offset($pagination->getOffset())
            ->limit($pagination->getLimit())
            ->orderBy([
                "created_at" => SORT_DESC
            ])
            ->all();
        return $this->render("list", [
            "records"    => $records,
            "pagination" => $pagination,
            "uid"        => $uid,
            "jid"        => $jid,
            "start_date" => $start_date,
            "end_date"   => $end_date
        ]);
    }
}