<?php
use layuiAdm\tools\Url;
use common\models\UserJobDaily;

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
        <th>用户名(ID)</th>
        <th>岗位名称(ID)</th>
        <th>用工日期</th>
        <th>当天工时</th>
        <th>当前状态</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\UserJobDaily[] */ ?>
    <?php foreach ($records as $uJob): ?>
        <?php $date = $uJob->dateStr(true) ?>
        <tr>
            <td><a class="layui-table-link clear"
                   href="<?= Url::selfLink(["uid" => $uJob->uid]) ?>"><?= $uJob->user->realname . '(' . $uJob->uid . ')' ?></a></td>
            <td><a class="layui-table-link clear"
                   href="<?= Url::selfLink(["jid" => $uJob->jid]) ?>"><?= $uJob->job->name . '(' . $uJob->job->id . ')' ?></a></td>
            <td><?= $date ?></td>
            <td><?= $uJob->numStr() ?></td>
            <td>
                <?php if ($uJob->status == UserJobDaily::PROVIDE): ?>
                    <span class="layui-badge layui-bg-orange">待审核</span>
                <?php elseif ($uJob->status == UserJobDaily::PASS): ?>
                    <span class="layui-badge layui-bg-green">已通过</span>
                <?php elseif ($uJob->status == UserJobDaily::REFUSE): ?>
                    <span class="layui-badge layui-bg-grey">被拒绝</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="layui-btn layui-btn-sm layui-btn-normal"
                      onclick="globalOpenIFrame('<?= Url::createLink("clock/list", ["uid" => $uJob->uid, "jid" => $uJob->jid, "start_date" => $date, "end_date" => $date]) ?>','打卡记录')">当天打卡记录</span>
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
