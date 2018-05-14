<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/23
 * Time: 下午8:29
 */

namespace common\models;


use common\tools\Img;

class Attach extends \common\models\base\Attach
{
    const COMPANY_RECORD = 1;

    const STATUS_ON = 1;
    const STATUS_DELETE = 2;

    public function cover() {
        return Img::format($this->path, 0, 0, true);
    }
}