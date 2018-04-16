<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/16
 * Time: 下午10:19
 */

namespace common\models;


class Company extends \common\models\base\Company
{
    const STATUS_VERIFY = 0;

    const STATUS_PASS = 1;

    const STATUS_FORBID = 2;

}