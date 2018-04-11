<?php
/* @var $this \yii\web\View */
use yii\helpers\Html;
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
    <link rel="stylesheet" type="text/css" href="/plugin/layui/css/layui.css"/>
    <link rel="stylesheet" type="text/css" href="/plugin/layui/css/layui.hc.css"/>
    <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/plugin/layui/layui.all.js"></script>
    <?php $this->head() ?>
</head>
<body class="layui-layout-body">
<?php $this->beginBody() ?>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">layui 后台</div>
        <!-- 头部区域 -->
        <?php echo Yii::$app->controller->headerMenu(); ?>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <?php if(Yii::$app->user->isGuest):?>
                <a href="/site/login"><i class="layui-icon">&#xe612;</i></a>
                <?php else:?>
                <a href="javascript:;">
                    <!-- <img src="http://t.cn/RCzsdCq" class="layui-nav-img"> -->
                    <?= Yii::$app->user->identity->username ?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;" onclick="changePass();"><i class="layui-icon">&#xe60c;</i> 修改密码</a></dd>
                    <dd><a href="javascript:;"><i class="layui-icon">&#xe620;</i> 安全设置</a></dd>
                    <dd><a href="javascript:;" onclick="logOut();"><i class="my-icon">&#xe63a;</i> 安全退出</a></dd>
                </dl>
                <?php endif;?>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域 -->
            <?php echo Yii::$app->controller->sideMenu(); ?>
        </div>
    </div>

    <div class="layui-body">
        <!-- 内容主体区域 -->
        <div style="padding: 15px;">
            <?= $content ?>
        </div>
    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © layui.com - 底部固定区域
    </div>
</div>
<script>
    $(document).ready(function(){
        //JavaScript代码区域
        layui.use('element', function () {
            var element = layui.element;
            element.init();
        });

        layui.use(['form', 'layedit', 'laydate'], function () {
            var form = layui.form;
            form.render();
        });
    });

    var globalLayer = layui.layer;

    function logOut() {
        layui.layer.confirm("确认退出吗？", function () {
            window.location.href = "/site/logout";
        });
    }
    function changePass(){
        layui.layer.open({
            type: 2,
            title: '重置密码',
            shadeClose: true,
            shade: 0.6,
//            maxmin: true, //开启最大化最小化按钮
            area: ['324px'],
            content: '/admin/reset-pass'
        });
    }

    function layerConfirmUrl(url,text){
        if(text == undefined || text == '')
            return layerOpenIFrame(url);
        globalLayer.confirm(text,function(){
            layerOpenIFrame(url);
        });
    }

    function layerOpenIFrame(url, title, width) {
        var default_width = '380px';
        if (title == "" || title == undefined)
            default_width = '235px';
        width = width == undefined ? default_width : width;
        globalLayer.open({
            type: 2,
            title: title,
            shadeClose: false,
            area: width,
            content: url
        });
    }

</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>