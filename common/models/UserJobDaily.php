<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午7:59
 */

namespace common\models;


class UserJobDaily extends \common\models\base\UserJobDaily
{
    const NOTHING = 0;
    const PROVIDE = 1;
    const REFUSE = 2;
    const PASS = 3;
    const IGNORE = 4;

    public function dateStr() {
//        $format = substr($this->date, 0, 4) == date("Y") ? "m.d" : "Y.m.d";
        return date("Y-m-d", strtotime($this->date));
    }

    public function info() {
        return [
            "date"   => $this->dateStr(),
            "num"    => $this->num,
            "status" => $this->status,
            "msg"    => $this->msg,
        ];
    }
}