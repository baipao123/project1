<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
$assetUrl = \Yii::$app->assetManager->publish(dirname(__FILE__) . '/../../assets')[1];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layui/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layui/css/layui.hc.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $assetUrl ?>/layuicms2.0/css/public.css"/>
    <script type="text/javascript" charset="utf-8" src="<?= $assetUrl ?>/jQuery.3.3.1.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="<?= $assetUrl ?>/layui/layui.all.js"></script>
    <?php $this->head() ?>
</head>
<body class="layui-layout-body">
<?php $this->beginBody() ?>
<div class="container" style="padding: 15px;">
    <div class="layui-row">
        <?= $content ?>
    </div>
</div>
<script>
    //有无parent
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
