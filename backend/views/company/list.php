<style>
    img.img{
        max-width:60px;
    }
</style>

<fieldset class="layui-elem-field">
    <legend>检索</legend>
    <div class="layui-field-box">
        <form class="layui-form" method="get">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">企业名称</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="name" autocomplete="off" class="layui-input" value="<?= $name ?>">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal login-btn" lay-submit>搜索</button>
                </div>
            </div>
        </form>
    </div>
</fieldset>


<table class="layui-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>Logo</th>
        <th>封面</th>
        <th>简介</th>
        <th>地址</th>
        <th>联系方式</th>
        <th>审核文件</th>
    </tr>
    </thead>
    <tbody>
    <?php /* @var $records \common\models\Company[] */ ?>
    <?php foreach ($records as $record): ?>
        <tr>
            <td><?= $record->uid ?></td>
            <td><?= $record->name ?></td>
            <td class="icon-<?= $record->uid ?>"><img class="img" src="<?= \common\tools\Img::format($record->icon, 0, 0, true) ?>"/></td>
            <td class="cover-<?= $record->uid ?>"><img class="img" src="<?= \common\tools\Img::format($record->cover, 0, 0, true) ?>"/></td>
            <td><?= $record->description ?></td>
            <td><?= $record->position ?></td>
            <td><?= $record->user->realname?> <br> <?= $record->user->phone?></td>
            <td class="attaches-<?= $record->uid ?>">
                <?php foreach ($record->lastRecord->attaches as $attach): ?>
                    <img src="<?= $attach->cover() ?>" class="img">
                <?php endforeach; ?>
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
