<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午6:42
 */

namespace frontend\modules\part\controllers;

use common\models\User;
use common\tools\StringHelper;
use common\tools\Tool;
use Yii;

class UserController extends \frontend\controllers\BaseController
{

    public function actionJoinUser() {
        if($this->getUser()->type == User::TYPE_COMPANY)
            return Tool::reJson(null,"您是企业用户",Tool::FAIL);
        $user = $this->getUser();
        $realName = $this->getPost("real_name");
        if(!StringHelper::isRealName($realName))
            return Tool::reJson(null,"请输入真实的姓名，2-7位汉字",Tool::FAIL);
        $user->realname = $realName;
        $user->real_at = time();
        $user->type = User::TYPE_USER;
        $user->save();
        return Tool::reJson($user->type);
    }

    public function actionJoinCompany(){
        if($this->getUser()->type == User::TYPE_USER)
            return Tool::reJson(null,"您是个人用户",Tool::FAIL);
    }

    public function actionEdit(){
       echo 1;
    }

}