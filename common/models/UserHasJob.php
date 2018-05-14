<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-04-12
 * Time: 17:21:20
 */

namespace common\models;

/**
 * @property Job $job
 * @property User $user
 * @property UserClock[] $clocks
 * */
class UserHasJob extends \common\models\base\UserHasJob
{
    const APPLY = 1;
    const ON = 2;
    const REFUSE = 9;
    const END = 10;

    public function getJob() {
        return $this->hasOne(Job::className(), ["id" => "jid"]);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ["id" => "uid"]);
    }

    public function getClocks() {
        return $this->hasMany(UserClock::className(), ["uJid" => "id"]);
    }

    public function user() {
        $user = $this->user;
        return [
            "job"    => $this->job->name,
            "name"   => $user->realname,
            "phone"  => $user->phone,
            "time"   => date("Y-m-d H:i:s", $this->created_at),
            "status" => $this->status
        ];
    }

    public function info() {
        return [
            "id"         => $this->id,
            "jid"        => $this->jid,
            "uid"        => $this->uid,
            "status"     => $this->status,
            "key"        => $this->auth_key,
            "content"    => $this->content,
            "created_at" => date("Y-m-d H:i:s", $this->created_at),
            "auth_at"    => date("Y-m-d H:i:s", $this->auth_at),
            "end_at"     => date("Y-m-d H:i:s", $this->end_at),
            "refuse_at"  => date("Y-m-d H:i:s", $this->updated_at),
        ];
    }

    public function clocks() {
        $clocks = $this->clocks;
        $data = [];
        foreach ($clocks as $clock) {
            $info = $clock->info();
            $data[ $info['date'] ][] = $info;
        }
        return $data;
    }
}