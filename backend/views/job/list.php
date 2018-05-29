<?php
    use layuiAdm\tools\Url;
?>
<style>
    img.img{
        max-width:60px;
    }
</style>

<!--<fieldset class="layui-elem-field">-->
<!--    <legend>检索</legend>-->
<!--    <div class="layui-field-box">-->
<!--        <form class="layui-form" method="get">-->
<!--            <div class="layui-form-item">-->
<!--                <div class="layui-inline">-->
<!--                    <label class="layui-form-label">企业名称</label>-->
<!--                    <div class="layui-input-inline" style="width: 100px;">-->
<!--                        <input type="text" name="name" autocomplete="off" class="layui-input" value="--><?//= $name ?><!--">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="layui-inline">-->
<!--                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </form>-->
<!--    </div>-->
<!--</fieldset>-->


<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th style="max-width: 100px;">岗位名称</th>
        <th style="min-width: 120px;">工作时间</th>
        <th style="width: 60px;">薪资</th>
        <th style="width: 100px;">联系人</th>
        <th style="width: 120px;">企业</th>
        <th style="width: 100px;">活跃度</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\Job[] */ ?>
    <?php foreach ($records as $job): ?>
        <tr>
            <td><?= $job->id ?></td>
            <td><?= $job->name ?></td>
            <td><?= $job->workDate() ?><br><?= $job->workTime() ?></td>
            <td><?= $job->prizeStr() ?></td>
            <td>
                <?= empty($job->contact_name) ? $job->owner->realname : $job->contact_name ?><br>
                <?= empty($job->phone) ? $job->owner->phone : $job->phone ?><br>
                <?= empty($job->tips) ? $job->company->tips : $job->tips ?>
            </td>
            <td class="icon-<?= $job->id ?>">
                <img class="img" src="<?= \common\tools\Img::format($job->company->icon, 0, 0, true) ?>"/>
                <br><?= $job->company->name ?>
            </td>
            <td>
                浏览量: <?= $job->view_num ?><br>
                关注数: <?= $job->follow_num ?><br>
                已招聘: <?= $job->getPassNum() . "/" . $job->num ?><br>
            </td>
            <td>
                <span class="layui-btn layui-btn-sm layui-btn-normal"
                      onclick="globalOpenIFrame('<?= Url::createLink("user/job-list", ["jid" => $job->id]) ?>','员工列表')">员工列表</span>
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
