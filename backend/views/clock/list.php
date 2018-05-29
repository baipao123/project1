<?php
    use layuiAdm\tools\Url;
?>

<style>
    img.img {
        max-width: 60px;
    }
</style>

<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="number" name="uid" placeholder="用户ID" autocomplete="off" class="layui-input"
                               value="<?= $uid > 0 ? $uid : '' ?>">
                    </div>
                </div>

                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="number" name="jid" placeholder="岗位ID" autocomplete="off" class="layui-input"
                               value="<?= $jid > 0 ? $jid : '' ?>">
                    </div>
                </div>
                日期:
                <div class="layui-inline">
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="start_date" class="layui-input" id="start_date" placeholder="开始日期"
                               value="<?= $start_date ?>">
                    </div>
                </div>
                -
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 0;margin-left:-10px;"></label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="end_date" class="layui-input" id="end_date" placeholder="结束日期"
                               value="<?= $end_date ?>">
                    </div>
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
                </div>
            </div>
        </form>
    </div>
</fieldset>

<script>
    layui.use('laydate', function () {
        let laydate = layui.laydate;
        laydate.render({
            elem: '#start_date'
        });
        laydate.render({
            elem: '#end_date'
        });
    });
</script>

<table class="layui-table">
    <thead>
    <tr>
        <th>用户ID</th>
        <th>用户名</th>
        <th>岗位名称</th>
        <th>打卡时间</th>
        <th>打卡类型</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\UserClock[] */ ?>
    <?php foreach ($records as $clock): ?>
        <tr>
            <td><?= $clock->uid ?></td>
            <td><a class="layui-table-link clear" href="<?= Url::selfLink(["uid" => $clock->uid]) ?>"><?= $clock->user->realname ?></a></td>
            <td><a class="layui-table-link clear" href="<?= Url::selfLink(["jid" => $clock->jid]) ?>"><?= $clock->job->name ?></a></td>
            <td><?= date("Y-m-d H:i:s", $clock->created_at) ?></td>
            <td>
                <?php if ($clock->type == \common\models\UserClock::TYPE_START): ?>
                    <span class="layui-btn layui-btn-sm layui-btn">上班</span>
                <?php elseif ($clock->type == \common\models\UserClock::TYPE_END): ?>
                    <span class="layui-btn layui-btn-sm layui-btn-warm">下班</span>
                <?php endif; ?>
            </td>
            <td>
<!--                <span class="layui-btn layui-btn-sm layui-btn-normal">打卡地址</span>-->
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo \layuiAdm\widgets\PagesWidget::widget(["pagination" => $pagination]); ?>

<script>
    $(".img").click(function (e) {
        let classTxt = $(this).parent().eq(0).attr("class");
        globalLayer.photos({
            photos: "." + classTxt
        })
    })
</script>
