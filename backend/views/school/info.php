<?php
/**
 * Created by PhpStorm.
 * User: huangchen
 * Date: 2018/9/4
 * Time: 下午11:14
 */
use common\models\School;
/* @var $school School */
?>

<form class="layui-form" method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->getCsrfToken() ?>">
    <div class="layui-form-item">
        <div class="layui-form-mid">学校名称</div>
        <input type="text" name="name" lay-filter="name" lay-verify="required" autocomplete="off" class="layui-input"
               value="<?= $school->name ?>">
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">所属城市</div>
        <select  name="city_id" lay-filter="city_id" lay-verify="required" autocomplete="off" class="layui-input">
            <option value="0">-- 城市 --</option>
            <?php foreach ($cities as $city):?>
                <option value="<?=$city['id']?>" <?= $city['id']==$school->city_id ? "selected" : ""?>><?=$city['name']?></option>
            <?php endforeach;?>
        </select>
    </div>

    <div class="layui-form-item">
        <div class="layui-form-mid">状态</div>
        <div class="layui-col-xs12">
            <input type="radio" name="status" value="1" lay-filter="status"  lay-verify="status"
                   title="显示" checked>
            <input type="radio" name="status" value="0" lay-filter="status"  lay-verify="status"
                   title="不显示" <?= $school->status == School::OFF ? "checked" : "" ?>>
        </div>
    </div>

    <div class="layui-form-item m-login-btn">
        <button class="layui-btn layui-btn-normal login-btn" lay-submit lay-filter="submit">保存</button>
    </div>

<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        var form = layui.form,
            layer = layui.layer;
        form.render();
        //自定义验证规则
        form.verify({
            city_id: function (value) {
                if(value <= 0)
                    return "所属城市必选";
            },
        });
    });
</script>

