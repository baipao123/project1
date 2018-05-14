<?php /* @var $this \yii\web\View */ ?>
<form class="layui-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">

    <textarea name="reason" placeholder="拒绝理由" class="layui-textarea"></textarea>
    <div class="layui-form-item m-login-btn">
        <button class="layui-btn layui-btn-normal login-btn" lay-submit lay-filter="reset">确认拒绝</button>
    </div>
</form>
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        var form = layui.form,
            layer = layui.layer;
        form.render();
    });
</script>

