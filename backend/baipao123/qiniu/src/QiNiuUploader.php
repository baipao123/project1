<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/4/13
 * Time: 下午7:20
 */

namespace backend\baipao123\qiniu\src;

use Yii;
use yii\web\View;

class QiNiuUploader extends \yii\base\Widget
{
    protected $jsFiles = [
        "/qiniu.min.js",
        "/qiniu.token.js"
    ];

    protected $asseturl;

    public $callBack = "uploadFile";

    public $isMulti = true;

    public $tokenUrl = "./qiniu-token";

    public $mineTypes = ["image/png", "image/jpeg", "image/gif"];

    public $ext = []; //额外字段

    public $useCdn = true;

    public $region = "";

    public function init() {
        parent::init();
        $this->asseturl = Yii::$app->assetManager->publish(dirname(__FILE__) . '/../assets')[1];

        // 华东区
        if (empty($this->region) && isset(Yii::$app->params['qiniu']) && isset(Yii::$app->params['qiniu']['region']))
            $this->region = Yii::$app->params['qiniu']['region'];

        foreach ($this->jsFiles as $jsFile) {
            Yii::$app->view->registerJsFile($this->asseturl . $jsFile, ['position' => View::POS_END]);
        }
    }

    public function run() {
        parent::run();
        $params = "{";
        foreach ($this->ext as $key => $val) {
            $params .= $key . ":" . json_encode($val);
        }
        $params .= "}";
        echo $this->renderFile(dirname(__FILE__) . '/../views/uploader.php', [
            'id'       => $this->id,
            'isMulti'  => $this->isMulti,
            'callBack' => $this->callBack,
            'mineTypes'=> empty($this->mineTypes) ? "null" : '["' . implode('", "', $this->mineTypes) . '"]',
            'params'   => $params,
            'useCdn'   => $this->useCdn ? "true" : "false",
            'tokenUrl' => $this->tokenUrl,
            'region'   => $this->region,
        ]);
    }
}