<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/12
 * Time: 下午8:31
 */

namespace common\models;


class Job extends \common\models\base\Job
{
	const ON = 1;
	const OFF = 2;
	const DEL = 3;

	public function format(){
		return [];
	}
}