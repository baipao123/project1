<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:01
 */

namespace frontend\modules\part\controllers;

use common\models\Job;
use common\models\User;
use common\models\UserHasJob;
use common\models\UserJobDaily;
use common\tools\Tool;
use Yii;

class JobController extends \frontend\controllers\BaseController
{
    public function actionApply(){
        if($this->getuser()->type != User::TYPE_USER)
            return Tool::reJson(null,"您无权报名",Tool::FAIL);
        $jid = $this->getPost("jid",0);
        $job = Job::findOne($jid);
        if(!$job || $job->status != Job::ON)
            return Tool::reJson(null,"工作不存在",Tool::FAIL);
        if(UserHasJob::isOn(Yii::$app->user->id,$jid))
            return Tool::reJson(null,"您已经报名该工作了",Tool::FAIL);
        $record = new UserHasJob;
        $record->uid = Yii::$app->user->id;
        $record->jid = $jid;
        $record->status = UserHasJob::STATUS_APPLY;
        $record->cretaed_at = time();
        if(!$record->save()){
            Yii::warning($record->errors,"添加UserHasJob失败");
            return Tool::reJson(null,"报名失败",Tool::FAIL);
        }
        //TODO 模板消息
        return Tool::reJson(1);
    }

    public function actionForbid(){
        $uJid = $this->getPost("uJid",0);
        $userJob = UserHasJob::findOne($uJid);
        if(!$userJob || $userJob->status !=  UserHasJob::Apply)
            return Tool::reJson(null,"工作不存在",Tool::FAIL);
        $job = $userJob->job;
        if(!$job || $job->status != Job::ON || $job->uid != Yii::$app->user->id)
            return Tool::reJson(null,"工作不存在",Tool::FAIL);

        $userJob->status = UserHasJob::REFUSE;
        $userJob->content =  $this->getPost("reason","");
        $userJob->updated_at = time();
        if(!$userJob->save()){
            Yii::warning($userJob->errors,"保存UserHasJob失败");
            return Tool::reJson(null,"拒绝失败",Tool::FAIL);
        }
        //TODO 模板消息
        return Tool::reJson(1);

    }

    public function actionCopy(){

    }


    public function actionTimeUp(){
        $uJid = $this->getPost("uJid",0);
        $date = $this->getPost("date",date("Ymd"));
        $daily = UserJobDaily::findOne(["uJid"=>$uJid,"date"=>$date]);
        if($daily && $daily->status == UserJobDaily::PASS)
            return Tool::reJson(null,"当天工时已审核通过，无法修改",Tool::FAIL);
        $uJob = UserHasJob::findOne($uJid);
        if(!$uJob || $uJob->uid != Yii::$app->user->id || $uJob->status != UserHasJob::ON)
            return Tool::reJson(null,"未查到工作记录",Tool::FAIL);

        $daily or $daily = new UserJobDaily;
        $daily->uid = Yii::$app->user->id;
        $daily->uJid = $uJid;
        $daily->jid = $uJob->jid;
        $daily->date = $date;
        $daily->num = $this->getPost("num",0);
        $daily->status = UserJobDaily::PROVIDE;
        $daily->isNewRecord ? $daily->created_at = time() : $daily->updated_at = time();
        $msg = $this->getPost("msg");
        $daily->msg = empty($msg) ? $daily->msg : $msg;
        $daily->save();
        //TODO 模板消息
        return Tool::reJson(1);
    }

    public function actionTimeRefuse(){
        if($this->getUser()->type != User::TYPE_COMPANY)
            return Tool::reJson(null,"无此操作权限",Tool::FAIL);
        $did = $this->getPost("did");
        $daily = UserJobDaily::findOne($did);
        if(!$daily || $daily->status != UserJobDaily::PROVIDE)
            return Tool::reJson(null,"无需处理",Tool::FAIL);
        $job = Job::findOne($daily->jid);
        if(!$job || $job->uid != Yii::$app->user->id)
            return Tool::reJson(null,"无需处理",Tool::FAIL);

        $daily->status = UserJobDaily::REFUSE ;
        $daily->updated_at = time();
        $daily->save();
        //TODO 模板消息
        return Tool::reJson(1);
    }

}